<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · PROJECT HANDLER (ALIGNED WITH DB SCHEMA)
|--------------------------------------------------------------------------
*/

// 1. Get the path to the 'backend' directory
// From: backend/admin/handlers/project-handler.php
// Up 2 levels -> backend/
$backendDir = dirname(__DIR__, 2); 

// 2. Get the path to the project root (where vendor is)
// Up 3 levels -> Project Root/
$rootDir = dirname(__DIR__, 3);

// --- Fix the Requires ---

// Correct: Root -> vendor/autoload.php
require_once $rootDir . '/vendor/autoload.php';

// Correct: Backend -> includes/config.php
require_once $backendDir . '/includes/config.php';

// Correct: Backend -> admin/includes/csrf.php
require_once $backendDir . '/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

/*
|--------------------------------------------------------------------------
| CONFIGURATION & VALIDATION
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../projects.php');
    exit;
}

// Ensure session is started for CSRF and Auth
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

csrf_verify($_POST['csrf_token'] ?? null);

if (!getenv('CLOUDINARY_URL')) {
    header('Location: ../projects.php?error=cloudinary_missing');
    exit;
}

Configuration::instance(getenv('CLOUDINARY_URL'));

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function generateProjectSlug(string $source, PDO $pdo, int $excludeId = 0): string {
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $source), '-'));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $excludeId]);
    return ($stmt->fetchColumn() > 0) ? $slug . '-' . bin2hex(random_bytes(2)) : $slug;
}

function uploadProjectMedia(array $file, string $mediaType): ?string {
    if (empty($file['tmp_name'])) return null;
    try {
        $uploader = new UploadApi();
        $result = $uploader->upload($file['tmp_name'], [
            'folder'        => 'reseed/projects',
            'resource_type' => ($mediaType === 'video') ? 'video' : 'image',
        ]);
        return $result['secure_url'] ?? null;
    } catch (Throwable $e) {
        error_log('Cloudinary Error: ' . $e->getMessage());
        return null;
    }
}

/*
|--------------------------------------------------------------------------
| EXECUTION
|--------------------------------------------------------------------------
*/

try {
    // Inputs
    $id          = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $title       = trim($_POST['title'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $summary     = trim($_POST['summary'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status      = trim($_POST['status'] ?? 'Planned');
    $startDate   = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $endDate     = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $featured    = isset($_POST['featured']) ? 1 : 0;
    $mediaType   = in_array($_POST['media_type'], ['image', 'video']) ? $_POST['media_type'] : 'image';

    $uploadedMedia = uploadProjectMedia($_FILES['media_file'] ?? [], $mediaType);

    $pdo->beginTransaction();

    $slugSource = !empty($_POST['slug']) ? $_POST['slug'] : $title;
    $slug = generateProjectSlug($slugSource, $pdo, $id ?? 0);

    if (isset($_POST['add'])) {
        // CREATE - Note: using cover_media to match your DB image
        $stmt = $pdo->prepare("
            INSERT INTO projects (
                title, slug, summary, description, location, 
                start_date, end_date, status, featured, 
                media_type, cover_media, created_at
            ) VALUES (
                :title, :slug, :summary, :description, :location, 
                :start_date, :end_date, :status, :featured, 
                :media_type, :cover_media, NOW()
            )
        ");

        $stmt->execute([
            'title'       => $title,
            'slug'        => $slug,
            'summary'     => $summary,
            'description' => $description,
            'location'    => $location,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'status'      => $status,
            'featured'    => $featured,
            'media_type'  => $mediaType,
            'cover_media' => $uploadedMedia
        ]);
    } else {
        // UPDATE
        $mediaSql = $uploadedMedia ? ', media_type = :media_type, cover_media = :cover_media' : '';
        
        $stmt = $pdo->prepare("
            UPDATE projects SET 
                title = :title, slug = :slug, summary = :summary, 
                description = :description, location = :location, 
                start_date = :start_date, end_date = :end_date, 
                status = :status, featured = :featured
                $mediaSql
            WHERE id = :id
        ");

        $params = [
            'title'       => $title,
            'slug'        => $slug,
            'summary'     => $summary,
            'description' => $description,
            'location'    => $location,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'status'      => $status,
            'featured'    => $featured,
            'id'          => $id
        ];

        if ($uploadedMedia) {
            $params['media_type']  = $mediaType;
            $params['cover_media'] = $uploadedMedia;
        }

        $stmt->execute($params);
    }

    $pdo->commit();
    header('Location: ../projects.php?success=' . (isset($_POST['add']) ? 'created' : 'updated'));
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    
    // Debugging: Log the exact error to Render logs
    error_log('PROJECT HANDLER FATAL: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    
    header('Location: ../projects.php?error=save_failed_check_logs');
    exit;
}