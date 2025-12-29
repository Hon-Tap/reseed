<?php
declare(strict_types=1);

$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/includes/config.php'; // Ensure Cloudinary SDK is initialized here
require_once $rootPath . '/admin/includes/csrf.php';

use Cloudinary\Api\Upload\UploadApi;

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
function slugify(string $value): string {
    return trim(strtolower(preg_replace('/[^a-z0-9]+/', '-', $value)), '-');
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
    $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
    $media_type   = $_POST['media_type'] ?? 'image';
    $featured     = isset($_POST['featured']) ? 1 : 0;

    $coverImageUrl = null;

    // Cloudinary Upload Logic
    if (!empty($_FILES['media_file']['tmp_name'])) {
        try {
            $upload = (new UploadApi())->upload($_FILES['media_file']['tmp_name'], [
                'folder' => 'reseed_blog',
                'resource_type' => ($media_type === 'video' ? 'video' : 'image')
            ]);
            $coverImageUrl = $upload['secure_url'];
        } catch (Exception $e) {
            header('Location: ../admin/posts.php?error=upload_failed');
            exit;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO posts (title, slug, author, excerpt, content, cover_image, media_type, featured, published_at, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([$title, $slug, $author, $excerpt, $content, $coverImageUrl, $media_type, $featured, $published_at]);

    header('Location: ../admin/posts.php?success=added');
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
    $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
    $media_type   = $_POST['media_type'] ?? 'image';
    $featured     = isset($_POST['featured']) ? 1 : 0;

    $updateImageUrl = null;

    if (!empty($_FILES['media_file']['tmp_name'])) {
        try {
            $upload = (new UploadApi())->upload($_FILES['media_file']['tmp_name'], [
                'folder' => 'reseed_blog',
                'resource_type' => ($media_type === 'video' ? 'video' : 'image')
            ]);
            $updateImageUrl = $upload['secure_url'];
        } catch (Exception $e) {
            header("Location: ../admin/posts_edit.php?id=$id&error=upload_failed");
            exit;
        }
    }

    $sql = "UPDATE posts SET title = ?, slug = ?, author = ?, excerpt = ?, content = ?, media_type = ?, featured = ?, published_at = ?";
    $params = [$title, $slug, $author, $excerpt, $content, $media_type, $featured, $published_at];

    if ($updateImageUrl) {
        $sql .= ", cover_image = ?";
        $params[] = $updateImageUrl;
    }

    $sql .= " WHERE id = ?";
    $params[] = $id;

    $pdo->prepare($sql)->execute($params);

    header('Location: ../admin/posts.php?success=updated');
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

    // Note: Cloudinary assets are usually kept or deleted via Public ID. 
    // For now, we simply remove the database record.
    $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$id]);

    header('Location: ../admin/posts.php?success=deleted');
    exit;
}

header('Location: ../admin/posts.php');
exit;