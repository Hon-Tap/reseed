<?php
declare(strict_types=1);

/**
 * Admin - Project/Initiative Handler
 * Path: /backend/admin/handlers/project-handler.php
 */

// 1. Setup Environment
// We need to go up 3 levels to reach the root from /backend/admin/handlers/
$baseDir = dirname(__DIR__, 3); 

// Fallback logic to ensure we found the root
if (!file_exists($baseDir . '/vendor/autoload.php')) {
    // If 3 levels failed, try 2 (for different local setups)
    $baseDir = dirname(__DIR__, 2);
}

require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// 1. Initialize Cloudinary
if (!getenv('CLOUDINARY_URL')) {
    header('Location: ../projects.php?error=cloudinary_config_missing');
    exit;
}
Configuration::instance(getenv('CLOUDINARY_URL'));

/**
 * Slug generator with collision detection
 */
function createUniqueProjectSlug(string $title, PDO $pdo, int $id = 0): string 
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $id]);
    
    return ($stmt->fetchColumn() > 0) ? $slug . '-' . bin2hex(random_bytes(2)) : $slug;
}

/**
 * Cloudinary Upload Helper
 */
function uploadProjectMedia(array $file, string $type): ?string 
{
    if (empty($file['tmp_name'])) return null;
    try {
        $api = new UploadApi();
        $response = $api->upload($file['tmp_name'], [
            'folder'        => 'reseed/projects',
            'resource_type' => ($type === 'video' ? 'video' : 'image')
        ]);
        return $response['secure_url'];
    } catch (Exception $e) {
        error_log("Project Upload Error: " . $e->getMessage());
        return null;
    }
}

// 2. Security & Request Validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../projects.php');
    exit;
}

csrf_verify($_POST['csrf_token'] ?? '');

// 3. Input Sanitization
$id          = isset($_POST['id']) ? (int)$_POST['id'] : null;
$title       = trim($_POST['title'] ?? '');
$location    = trim($_POST['location'] ?? '');
$summary     = trim($_POST['summary'] ?? '');
$description = trim($_POST['description'] ?? '');
$status      = $_POST['status'] ?? 'Planned';
$start_date  = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date    = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
$featured    = isset($_POST['featured']) ? 1 : 0;
$media_type  = $_POST['media_type'] ?? 'image';

// 4. Handle Media Logic
$finalMediaUrl = null;

if ($media_type === 'url') {
    // Case: External Video URL
    $finalMediaUrl = filter_var($_POST['media_url'], FILTER_SANITIZE_URL);
} else {
    // Case: Cloudinary Upload
    $finalMediaUrl = uploadProjectMedia($_FILES['media_file'] ?? [], $media_type);
}

// 5. Database Operation
try {
    $pdo->beginTransaction();

    // Generate Slug
    $slugSource = !empty($_POST['slug']) ? $_POST['slug'] : $title;
    $finalSlug  = createUniqueProjectSlug($slugSource, $pdo, $id ?? 0);

    if (isset($_POST['add'])) {
        $sql = "INSERT INTO projects (
                    title, slug, location, summary, description, 
                    status, start_date, end_date, featured, 
                    cover_media, media_type, created_at
                ) VALUES (
                    :title, :slug, :location, :summary, :description, 
                    :status, :start_date, :end_date, :featured, 
                    :cover_media, :media_type, NOW()
                )";
    } else {
        // Update logic: Only change media if a new file was uploaded or new URL provided
        $mediaSql = ($finalMediaUrl) ? ", cover_media = :cover_media, media_type = :media_type" : "";
        $sql = "UPDATE projects SET 
                title = :title, slug = :slug, location = :location, 
                summary = :summary, description = :description, 
                status = :status, start_date = :start_date, 
                end_date = :end_date, featured = :featured 
                $mediaSql 
                WHERE id = :id";
    }

    $stmt = $pdo->prepare($sql);
    $params = [
        'title'       => $title,
        'slug'        => $finalSlug,
        'location'    => $location,
        'summary'     => $summary,
        'description' => $description,
        'status'      => $status,
        'start_date'  => $start_date,
        'end_date'    => $end_date,
        'featured'    => $featured
    ];

    if ($id) $params['id'] = $id;
    if ($finalMediaUrl || isset($_POST['add'])) {
        $params['cover_media'] = $finalMediaUrl;
        $params['media_type']  = $media_type;
    }

    $stmt->execute($params);
    $pdo->commit();

    $redirectStatus = isset($_POST['add']) ? 'created' : 'updated';
    header("Location: ../projects.php?success=$redirectStatus");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("Project Handler Error: " . $e->getMessage());
    header('Location: ../projects.php?error=db_failure');
    exit;
}