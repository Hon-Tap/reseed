<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · POST HANDLER (FIXED FOR DB SCHEMA)
|--------------------------------------------------------------------------
*/

// Direct path resolution for Render/Standard environments
$baseDir = dirname(__DIR__, 2); 
// Navigates up from 'handlers' -> 'admin' -> 'backend' -> to project root
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

/*
|--------------------------------------------------------------------------
| INITIALIZATION & VALIDATION
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../posts.php');
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

csrf_verify($_POST['csrf_token'] ?? null);

if (!getenv('CLOUDINARY_URL')) {
    header('Location: ../posts.php?error=cloudinary_missing');
    exit;
}

Configuration::instance(getenv('CLOUDINARY_URL'));

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function uniquePostSlug(string $source, PDO $pdo, int $excludeId = 0): string
{
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $source), '-'));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $excludeId]);

    if ((int) $stmt->fetchColumn() > 0) {
        $slug .= '-' . bin2hex(random_bytes(2));
    }
    return $slug;
}

function uploadCoverImage(array $file): ?string
{
    if (empty($file['tmp_name'])) return null;

    try {
        $api = new UploadApi();
        $result = $api->upload($file['tmp_name'], [
            'folder'        => 'reseed/posts',
            'resource_type' => 'image',
            'quality'       => 'auto',
            'fetch_format'  => 'auto',
        ]);
        return $result['secure_url'] ?? null;
    } catch (Throwable $e) {
        error_log('Post image upload failed: ' . $e->getMessage());
        return null;
    }
}

/*
|--------------------------------------------------------------------------
| PROCESSING
|--------------------------------------------------------------------------
*/

try {
    $id           = isset($_POST['id']) ? (int) $_POST['id'] : null;
    $title        = trim($_POST['title'] ?? '');
    $author       = trim($_POST['author'] ?? 'Team ReSEED');
    $excerpt      = trim($_POST['excerpt'] ?? '');
    $content      = trim($_POST['content'] ?? '');
    $featured     = isset($_POST['featured']) ? 1 : 0;
    $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : date('Y-m-d H:i:s');

    $pdo->beginTransaction();

    // DELETE Action
    if (isset($_POST['delete']) && $id) {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $pdo->commit();
        header('Location: ../posts.php?success=deleted');
        exit;
    }

    $slugSource = !empty($_POST['slug']) ? $_POST['slug'] : $title;
    $slug = uniquePostSlug($slugSource, $pdo, $id ?? 0);
    $newMedia = uploadCoverImage($_FILES['media_file'] ?? []);

    if (isset($_POST['add'])) {
        // INSERT - Using 'cover_media' as per your DB screenshot
        $stmt = $pdo->prepare("
            INSERT INTO posts (
                title, slug, author, excerpt, content,
                cover_media, media_type,
                featured, published_at, created_at
            ) VALUES (
                :title, :slug, :author, :excerpt, :content,
                :cover_media, 'image',
                :featured, :published_at, NOW()
            )
        ");

        $stmt->execute([
            'title'        => $title,
            'slug'         => $slug,
            'author'       => $author,
            'excerpt'      => $excerpt,
            'content'      => $content,
            'cover_media'  => $newMedia,
            'featured'     => $featured,
            'published_at' => $published_at,
        ]);

    } else {
        // UPDATE - Using 'cover_media' as per your DB screenshot
        $sql = "
            UPDATE posts SET
                title = :title,
                slug = :slug,
                author = :author,
                excerpt = :excerpt,
                content = :content,
                featured = :featured,
                published_at = :published_at
        ";

        if ($newMedia) {
            $sql .= ", cover_media = :cover_media";
        }

        $sql .= " WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        $params = [
            'title'        => $title,
            'slug'         => $slug,
            'author'       => $author,
            'excerpt'      => $excerpt,
            'content'      => $content,
            'featured'     => $featured,
            'published_at' => $published_at,
            'id'           => $id,
        ];

        if ($newMedia) {
            $params['cover_media'] = $newMedia;
        }

        $stmt->execute($params);
    }

    $pdo->commit();
    $status = isset($_POST['add']) ? 'created' : 'updated';
    header("Location: ../posts.php?success={$status}");
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Post handler failure: ' . $e->getMessage());
    header('Location: ../posts.php?error=database_failure&msg=' . urlencode($e->getMessage()));
    exit;
}