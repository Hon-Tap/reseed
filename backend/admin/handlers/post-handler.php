<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| BOOTSTRAP
|--------------------------------------------------------------------------
*/

$rootPath = dirname(__DIR__, 2);

require_once $rootPath . '/includes/config.php';
require_once $rootPath . '/admin/includes/csrf.php';

/*
|--------------------------------------------------------------------------
| UPLOAD CONFIG
|--------------------------------------------------------------------------
*/

if (!defined('UPLOAD_ROOT')) {
    define('UPLOAD_ROOT', $rootPath . '/uploads');
}

$uploadDir = UPLOAD_ROOT . '/posts/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$imageMime = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];

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

function generateFilename(string $ext): string
{
    return bin2hex(random_bytes(16)) . '.' . $ext;
}

/*
|--------------------------------------------------------------------------
| ADD POST
|--------------------------------------------------------------------------
*/

if (isset($_POST['add'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $title        = trim($_POST['title'] ?? '');
    $slug         = slugify($_POST['slug'] ?? $title);
    $author       = trim($_POST['author'] ?? '');
    $excerpt      = trim($_POST['excerpt'] ?? '');
    $content      = trim($_POST['content'] ?? '');
    $published_at = $_POST['published_at'] ?: null;
    $media_type   = $_POST['media_type'] ?? 'image';
    $featured     = isset($_POST['featured']) ? 1 : 0;

    $coverImage = null;

    if (!empty($_FILES['media_file']['tmp_name'])) {

        $mime = mime_content_type($_FILES['media_file']['tmp_name']);

        if (!isset($imageMime[$mime])) {
            // Corrected: Redirect to posts.php in the same admin folder
            header('Location: posts.php?error=invalid_media');
            exit;
        }

        $coverImage = generateFilename($imageMime[$mime]);

        if (!move_uploaded_file(
            $_FILES['media_file']['tmp_name'],
            $uploadDir . $coverImage
        )) {
            header('Location: posts.php?error=upload_failed');
            exit;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO posts (
            title, slug, author, excerpt, content, 
            cover_image, media_type, featured, published_at, created_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
        )
    ");

    $stmt->execute([
        $title, $slug, $author, $excerpt, $content, 
        $coverImage, $media_type, $featured, $published_at
    ]);

    header('Location: posts.php?success=added');
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE POST
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'], $_POST['id'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $id           = (int) $_POST['id'];
    $title        = trim($_POST['title'] ?? '');
    $slug         = slugify($_POST['slug'] ?? $title);
    $author       = trim($_POST['author'] ?? '');
    $excerpt      = trim($_POST['excerpt'] ?? '');
    $content      = trim($_POST['content'] ?? '');
    $published_at = $_POST['published_at'] ?: null;
    $media_type   = $_POST['media_type'] ?? 'image';
    $featured     = isset($_POST['featured']) ? 1 : 0;

    $newImage = null;

    if (!empty($_FILES['media_file']['tmp_name'])) {

        $mime = mime_content_type($_FILES['media_file']['tmp_name']);

        if (!isset($imageMime[$mime])) {
            header('Location: posts.php?error=invalid_media');
            exit;
        }

        $newImage = generateFilename($imageMime[$mime]);

        if (!move_uploaded_file(
            $_FILES['media_file']['tmp_name'],
            $uploadDir . $newImage
        )) {
            header('Location: posts.php?error=upload_failed');
            exit;
        }

        $old = $pdo->prepare("SELECT cover_image FROM posts WHERE id = ?");
        $old->execute([$id]);
        $oldImage = $old->fetchColumn();

        if ($oldImage && file_exists($uploadDir . $oldImage)) {
            unlink($uploadDir . $oldImage);
        }
    }

    $sql = "UPDATE posts SET title = ?, slug = ?, author = ?, excerpt = ?, content = ?, media_type = ?, featured = ?, published_at = ?";
    $params = [$title, $slug, $author, $excerpt, $content, $media_type, $featured, $published_at];

    if ($newImage) {
        $sql .= ", cover_image = ?";
        $params[] = $newImage;
    }

    $sql .= " WHERE id = ?";
    $params[] = $id;

    $pdo->prepare($sql)->execute($params);

    header('Location: posts.php?success=updated');
    exit;
}

/*
|--------------------------------------------------------------------------
| DELETE POST
|--------------------------------------------------------------------------
*/

if (isset($_POST['delete'], $_POST['id'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $id = (int) $_POST['id'];

    $stmt = $pdo->prepare("SELECT cover_image FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();

    if ($image && file_exists($uploadDir . $image)) {
        unlink($uploadDir . $image);
    }

    $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$id]);

    header('Location: posts.php?success=deleted');
    exit;
}

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/

header('Location: posts.php');
exit;