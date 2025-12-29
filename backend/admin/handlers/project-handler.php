<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| BOOTSTRAP
|--------------------------------------------------------------------------
*/

// Always resolve from THIS file location (works on Render, Docker, local)
$ROOT = realpath(__DIR__ . '/../../../../');
if ($ROOT === false) {
    http_response_code(500);
    exit('Critical error: Unable to resolve project root.');
}

// Error reporting (disable in production if needed)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Composer (Cloudinary)
if (!file_exists($ROOT . '/vendor/autoload.php')) {
    http_response_code(500);
    exit('Composer autoload not found. Run composer install.');
}
require_once $ROOT . '/vendor/autoload.php';

// App config
require_once $ROOT . '/backend/includes/config.php';

// CSRF (optional but safe)
$csrfPath = $ROOT . '/backend/admin/includes/csrf.php';
if (file_exists($csrfPath)) {
    require_once $csrfPath;
}

/*
|--------------------------------------------------------------------------
| CLOUDINARY CONFIG
|--------------------------------------------------------------------------
*/

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

if (!getenv('CLOUDINARY_URL')) {
    http_response_code(500);
    exit('CLOUDINARY_URL is not set in environment.');
}

Configuration::instance(getenv('CLOUDINARY_URL'));

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    return trim($value, '-');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

/*
|--------------------------------------------------------------------------
| INPUT NORMALIZATION
|--------------------------------------------------------------------------
*/

$action = $_POST['add'] ?? $_POST['update'] ?? $_POST['delete'] ?? null;

$title       = trim($_POST['title'] ?? '');
$slug        = slugify($_POST['slug'] ?? $title);
$summary     = trim($_POST['summary'] ?? '');
$description = trim($_POST['description'] ?? '');
$location    = trim($_POST['location'] ?? '');
$start_date  = $_POST['start_date'] ?: null;
$end_date    = $_POST['end_date'] ?: null;
$status      = $_POST['status'] ?? 'planned';
$media_type  = $_POST['media_type'] ?? 'image';
$media_url   = trim($_POST['media_url'] ?? '') ?: null;
$featured    = isset($_POST['featured']) ? 1 : 0;

/*
|--------------------------------------------------------------------------
| FILE UPLOAD (CLOUDINARY ONLY)
|--------------------------------------------------------------------------
*/

function uploadProjectImage(array $file): ?string
{
    if (empty($file['tmp_name'])) {
        return null;
    }

    try {
        $result = (new UploadApi())->upload(
            $file['tmp_name'],
            [
                'folder'        => 'reseed/projects',
                'resource_type' => 'auto'
            ]
        );

        return $result['secure_url'] ?? null;
    } catch (Throwable $e) {
        http_response_code(500);
        exit('Cloudinary upload failed: ' . $e->getMessage());
    }
}

/*
|--------------------------------------------------------------------------
| ADD PROJECT
|--------------------------------------------------------------------------
*/

if (isset($_POST['add'])) {

    $coverImage = uploadProjectImage($_FILES['media_file'] ?? []);

    $stmt = $pdo->prepare("
        INSERT INTO projects (
            title, slug, summary, description, location,
            start_date, end_date, cover_image,
            media_type, media_url, status,
            featured, created_at
        ) VALUES (
            :title, :slug, :summary, :description, :location,
            :start_date, :end_date, :cover_image,
            :media_type, :media_url, :status,
            :featured, NOW()
        )
    ");

    $stmt->execute([
        'title'       => $title,
        'slug'        => $slug,
        'summary'     => $summary,
        'description' => $description,
        'location'    => $location,
        'start_date'  => $start_date,
        'end_date'    => $end_date,
        'cover_image' => $coverImage,
        'media_type'  => $media_type,
        'media_url'   => $media_url,
        'status'      => $status,
        'featured'    => $featured,
    ]);

    redirect('../projects.php?success=added');
}

/*
|--------------------------------------------------------------------------
| UPDATE PROJECT
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'], $_POST['id'])) {

    $id = (int) $_POST['id'];
    $newImage = uploadProjectImage($_FILES['media_file'] ?? []);

    if ($newImage) {
        $sql = "
            UPDATE projects SET
                title=:title, slug=:slug, summary=:summary,
                description=:description, location=:location,
                start_date=:start_date, end_date=:end_date,
                cover_image=:cover_image,
                media_type=:media_type, media_url=:media_url,
                status=:status, featured=:featured
            WHERE id=:id
        ";
    } else {
        $sql = "
            UPDATE projects SET
                title=:title, slug=:slug, summary=:summary,
                description=:description, location=:location,
                start_date=:start_date, end_date=:end_date,
                media_type=:media_type, media_url=:media_url,
                status=:status, featured=:featured
            WHERE id=:id
        ";
    }

    $stmt = $pdo->prepare($sql);

    $params = [
        'title'       => $title,
        'slug'        => $slug,
        'summary'     => $summary,
        'description' => $description,
        'location'    => $location,
        'start_date'  => $start_date,
        'end_date'    => $end_date,
        'media_type'  => $media_type,
        'media_url'   => $media_url,
        'status'      => $status,
        'featured'    => $featured,
        'id'          => $id,
    ];

    if ($newImage) {
        $params['cover_image'] = $newImage;
    }

    $stmt->execute($params);

    redirect('../projects.php?success=updated');
}

/*
|--------------------------------------------------------------------------
| DELETE PROJECT
|--------------------------------------------------------------------------
*/

if (isset($_POST['delete'], $_POST['id'])) {

    $id = (int) $_POST['id'];
    $stmt = $pdo->prepare('DELETE FROM projects WHERE id = :id');
    $stmt->execute(['id' => $id]);

    redirect('../projects.php?success=deleted');
}

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/

redirect('../projects.php');
