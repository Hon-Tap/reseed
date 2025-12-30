<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · PROJECT HANDLER (DB-ALIGNED, CLOUDINARY ONLY)
|--------------------------------------------------------------------------
*/

// Resolve project root
$baseDir = dirname(__DIR__, 3);
if (!file_exists($baseDir . '/vendor/autoload.php')) {
    $baseDir = dirname(__DIR__, 2);
}

require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

/*
|--------------------------------------------------------------------------
| BOOTSTRAP
|--------------------------------------------------------------------------
*/

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

function generateProjectSlug(string $source, PDO $pdo, int $excludeId = 0): string
{
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $source), '-'));

    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM projects WHERE slug = ? AND id != ?"
    );
    $stmt->execute([$slug, $excludeId]);

    return ($stmt->fetchColumn() > 0)
        ? $slug . '-' . bin2hex(random_bytes(2))
        : $slug;
}

function uploadProjectMedia(array $file, string $mediaType): ?string
{
    if (empty($file['tmp_name'])) {
        return null;
    }

    try {
        $uploader = new UploadApi();
        $result = $uploader->upload($file['tmp_name'], [
            'folder'        => 'reseed/projects',
            'resource_type' => ($mediaType === 'video') ? 'video' : 'image',
        ]);

        return $result['secure_url'] ?? null;
    } catch (Throwable $e) {
        error_log('Cloudinary Project Upload: ' . $e->getMessage());
        return null;
    }
}

/*
|--------------------------------------------------------------------------
| REQUEST VALIDATION
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../projects.php');
    exit;
}

csrf_verify($_POST['csrf_token'] ?? null);

/*
|--------------------------------------------------------------------------
| INPUT
|--------------------------------------------------------------------------
*/

$id          = isset($_POST['id']) ? (int) $_POST['id'] : null;
$title       = trim($_POST['title'] ?? '');
$location    = trim($_POST['location'] ?? '');
$summary     = trim($_POST['summary'] ?? '');
$description = trim($_POST['description'] ?? '');
$status      = trim($_POST['status'] ?? 'Planned');
$startDate   = $_POST['start_date'] ?: null;
$endDate     = $_POST['end_date'] ?: null;
$featured    = isset($_POST['featured']) ? 1 : 0;

$mediaType = in_array($_POST['media_type'] ?? 'image', ['image', 'video'], true)
    ? $_POST['media_type']
    : 'image';

/*
|--------------------------------------------------------------------------
| MEDIA
|--------------------------------------------------------------------------
*/

$uploadedMedia = uploadProjectMedia($_FILES['media_file'] ?? [], $mediaType);

/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/

try {
    $pdo->beginTransaction();

    $slugSource = $_POST['slug'] ?: $title;
    $slug = generateProjectSlug($slugSource, $pdo, $id ?? 0);

    if (isset($_POST['add'])) {

        $stmt = $pdo->prepare("
            INSERT INTO projects (
                title,
                slug,
                summary,
                description,
                location,
                start_date,
                end_date,
                status,
                featured,
                media_type,
                cover_media,
                created_at
            ) VALUES (
                :title,
                :slug,
                :summary,
                :description,
                :location,
                :start_date,
                :end_date,
                :status,
                :featured,
                :media_type,
                :cover_media,
                NOW()
            )
        ");

        $stmt->execute([
            'title'        => $title,
            'slug'         => $slug,
            'summary'      => $summary,
            'description'  => $description,
            'location'     => $location,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'status'       => $status,
            'featured'     => $featured,
            'media_type'   => $mediaType,
            'cover_media'  => $uploadedMedia,
        ]);

    } else {

        $mediaSql = $uploadedMedia
            ? ', media_type = :media_type, cover_media = :cover_media'
            : '';

        $stmt = $pdo->prepare("
            UPDATE projects SET
                title        = :title,
                slug         = :slug,
                summary      = :summary,
                description  = :description,
                location     = :location,
                start_date   = :start_date,
                end_date     = :end_date,
                status       = :status,
                featured     = :featured
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
            'id'          => $id,
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
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Project Handler Failure: ' . $e->getMessage());
    header('Location: ../projects.php?error=save_failed');
    exit;
}
