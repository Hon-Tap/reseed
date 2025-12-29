<?php

declare(strict_types=1);

use Cloudinary\Api\Upload\UploadApi;

/*
|--------------------------------------------------------------------------
| BOOTSTRAP
|--------------------------------------------------------------------------
*/

$rootPath = dirname(__DIR__, 2);

require_once $rootPath . '/includes/config.php';
require_once $rootPath . '/admin/includes/csrf.php';
require_once dirname($rootPath) . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| HELPERS
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
| ADD PROJECT
|--------------------------------------------------------------------------
*/

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

    if (!empty($_FILES['media_file']['tmp_name'])) {
        $upload = (new UploadApi())->upload(
            $_FILES['media_file']['tmp_name'],
            [
                'folder'        => 'reseed/projects',
                'resource_type' => 'auto'
            ]
        );

        $coverImage = $upload['secure_url'];
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
        $title,
        $slug,
        $summary,
        $description,
        $location,
        $start_date,
        $end_date,
        $coverImage,
        $media_type,
        $media_url,
        $status,
        $featured
    ]);

    header('Location: ../projects.php?success=added');
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE PROJECT
|--------------------------------------------------------------------------
*/

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
        $upload = (new UploadApi())->upload(
            $_FILES['media_file']['tmp_name'],
            [
                'folder'        => 'reseed/projects',
                'resource_type' => 'auto'
            ]
        );

        $newImage = $upload['secure_url'];
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
            $title,
            $slug,
            $summary,
            $description,
            $location,
            $start_date,
            $end_date,
            $newImage,
            $media_type,
            $media_url,
            $status,
            $featured,
            $id
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
            $title,
            $slug,
            $summary,
            $description,
            $location,
            $start_date,
            $end_date,
            $media_type,
            $media_url,
            $status,
            $featured,
            $id
        ]);
    }

    header('Location: ../projects.php?success=updated');
    exit;
}

/*
|--------------------------------------------------------------------------
| DELETE PROJECT
|--------------------------------------------------------------------------
*/

if (isset($_POST['delete'], $_POST['id'])) {

    $id = (int) $_POST['id'];

    $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: ../projects.php?success=deleted');
    exit;
}

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/

header('Location: ../projects.php');
exit;
