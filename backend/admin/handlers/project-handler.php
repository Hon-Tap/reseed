<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| BOOTSTRAP & CONFIGURATION
|--------------------------------------------------------------------------
*/

// 1. Define the Absolute Path for Docker/Render Environment
// This points directly to /var/www/html
$basePath = '/var/www/html';

// 2. Enable Error Reporting (Helps debug if something else breaks)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 3. Load Dependencies using Absolute Paths
// We check if files exist to prevent fatal crashes if paths are wrong
if (file_exists($basePath . '/vendor/autoload.php')) {
    require_once $basePath . '/vendor/autoload.php';
} else {
    die("Critical Error: /vendor/autoload.php not found. Did 'composer install' run?");
}

if (file_exists($basePath . '/backend/includes/config.php')) {
    require_once $basePath . '/backend/includes/config.php';
} else {
    die("Critical Error: Config file not found at $basePath/backend/includes/config.php");
}

if (file_exists($basePath . '/backend/admin/includes/csrf.php')) {
    require_once $basePath . '/backend/admin/includes/csrf.php';
} else {
    // If missing, define a dummy function to prevent crash, but warn user
    function csrf_token() { return ''; } 
}

// 4. Import Cloudinary Classes
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// 5. Configure Cloudinary
// IMPORTANT: You must set the 'CLOUDINARY_URL' environment variable in Render Dashboard
// Format: cloudinary://API_KEY:API_SECRET@CLOUD_NAME
if (getenv('CLOUDINARY_URL')) {
    Configuration::instance(getenv('CLOUDINARY_URL'));
}

/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

function slugify(string $value): string
{
    return trim(
        strtolower(preg_replace('/[^a-z0-9]+/', '-', $value)),
        '-'
    );
}

/*
|--------------------------------------------------------------------------
| HANDLE REQUESTS
|--------------------------------------------------------------------------
*/

// --- ADD PROJECT ---
if (isset($_POST['add'])) {

    $title        = trim($_POST['title'] ?? '');
    $slug         = slugify($_POST['slug'] ?? $title);
    $summary      = trim($_POST['summary'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $location     = trim($_POST['location'] ?? '');
    $start_date   = $_POST['start_date'] ?: null;
    $end_date     = $_POST['end_date'] ?: null;
    $status       = $_POST['status'] ?? 'planned';
    $media_type   = $_POST['media_type'] ?? 'image';
    $media_url    = trim($_POST['media_url'] ?? '') ?: null;
    $featured     = isset($_POST['featured']) ? 1 : 0;

    $coverImage = null;

    // Handle File Upload via Cloudinary
    if (!empty($_FILES['media_file']['tmp_name'])) {
        try {
            $upload = (new UploadApi())->upload(
                $_FILES['media_file']['tmp_name'],
                [
                    'folder'        => 'reseed/projects',
                    'resource_type' => 'auto'
                ]
            );
            $coverImage = $upload['secure_url'];
        } catch (Exception $e) {
            die("Cloudinary Upload Failed: " . $e->getMessage());
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO projects (
            title, slug, summary, description, location,
            start_date, end_date, cover_image,
            media_type, media_url, status,
            featured, created_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
        )
    ");

    $stmt->execute([
        $title, $slug, $summary, $description, $location,
        $start_date, $end_date, $coverImage,
        $media_type, $media_url, $status, $featured
    ]);

    header('Location: ../projects.php?success=added');
    exit;
}

// --- UPDATE PROJECT ---
if (isset($_POST['update'], $_POST['id'])) {

    $id           = (int) $_POST['id'];
    $title        = trim($_POST['title'] ?? '');
    $slug         = slugify($_POST['slug'] ?? $title);
    $summary      = trim($_POST['summary'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $location     = trim($_POST['location'] ?? '');
    $start_date   = $_POST['start_date'] ?: null;
    $end_date     = $_POST['end_date'] ?: null;
    $status       = $_POST['status'] ?? 'planned';
    $media_type   = $_POST['media_type'] ?? 'image';
    $media_url    = trim($_POST['media_url'] ?? '') ?: null;
    $featured     = isset($_POST['featured']) ? 1 : 0;

    $newImage = null;

    if (!empty($_FILES['media_file']['tmp_name'])) {
        try {
            $upload = (new UploadApi())->upload(
                $_FILES['media_file']['tmp_name'],
                [
                    'folder'        => 'reseed/projects',
                    'resource_type' => 'auto'
                ]
            );
            $newImage = $upload['secure_url'];
        } catch (Exception $e) {
            die("Cloudinary Upload Failed: " . $e->getMessage());
        }
    }

    if ($newImage) {
        $stmt = $pdo->prepare("
            UPDATE projects SET
                title=?, slug=?, summary=?, description=?, location=?,
                start_date=?, end_date=?, cover_image=?,
                media_type=?, media_url=?, status=?, featured=?
            WHERE id=?
        ");
        $stmt->execute([
            $title, $slug, $summary, $description, $location,
            $start_date, $end_date, $newImage,
            $media_type, $media_url, $status, $featured, $id
        ]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE projects SET
                title=?, slug=?, summary=?, description=?, location=?,
                start_date=?, end_date=?,
                media_type=?, media_url=?, status=?, featured=?
            WHERE id=?
        ");
        $stmt->execute([
            $title, $slug, $summary, $description, $location,
            $start_date, $end_date,
            $media_type, $media_url, $status, $featured, $id
        ]);
    }

    header('Location: ../projects.php?success=updated');
    exit;
}

// --- DELETE PROJECT ---
if (isset($_POST['delete'], $_POST['id'])) {
    $id = (int) $_POST['id'];
    $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: ../projects.php?success=deleted');
    exit;
}

// Fallback
header('Location: ../projects.php');
exit;