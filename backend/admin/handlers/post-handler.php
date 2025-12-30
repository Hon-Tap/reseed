<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · POST HANDLER (CLOUDINARY + POSTGRES)
|--------------------------------------------------------------------------
*/

// Resolve project root safely
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
| CLOUDINARY INIT
|--------------------------------------------------------------------------
*/

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

    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM posts WHERE slug = ? AND id != ?"
    );
    $stmt->execute([$slug, $excludeId]);

    if ((int) $stmt->fetchColumn() > 0) {
        $slug .= '-' . bin2hex(random_bytes(2));
    }

    return $slug;
}

function uploadCoverImage(array $file): ?string
{
    if (empty($file['tmp_name'])) {
        return null;
    }

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
| REQUEST VALIDATION
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../posts.php');
    exit;
}

csrf_verify($_POST['csrf_token'] ?? null);

/*
|--------------------------------------------------------------------------
| INPUT NORMALIZATION
|--------------------------------------------------------------------------
*/

$id           = isset($_POST['id']) ? (int) $_POST['id'] : null;
$title        = trim($_POST['title'] ?? '');
$author       = trim($_POST['author'] ?? 'Team ReSEED');
$excerpt      = trim($_POST['excerpt'] ?? '');
$content      = trim($_POST['content'] ?? '');
$featured     = isset($_POST['featured']) ? 1 : 0;
$published_at = !empty($_POST['published_at'])
    ? $_POST['published_at']
    : date('Y-m-d H:i:s');

/*
|--------------------------------------------------------------------------
| TRANSACTION
|--------------------------------------------------------------------------
*/

try {
    $pdo->beginTransaction();

    /* ---------------- DELETE ---------------- */
    if (isset($_POST['delete']) && $id) {

        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);

        $pdo->commit();
        header('Location: ../posts.php?success=deleted');
        exit;
    }

    /* ---------------- SLUG ---------------- */
    $slugSource = !empty($_POST['slug']) ? $_POST['slug'] : $title;
    $slug = uniquePostSlug($slugSource, $pdo, $id ?? 0);

    /* ---------------- IMAGE ---------------- */
    $newCover = uploadCoverImage($_FILES['media_file'] ?? []);

    /* ---------------- INSERT / UPDATE ---------------- */
    if (isset($_POST['add'])) {

        $stmt = $pdo->prepare("
            INSERT INTO posts (
                title, slug, author, excerpt, content,
                cover_image, media_type,
                featured, published_at, created_at
            ) VALUES (
                :title, :slug, :author, :excerpt, :content,
                :cover_image, 'image',
                :featured, :published_at, NOW()
            )
        ");

        $stmt->execute([
            'title'        => $title,
            'slug'         => $slug,
            'author'       => $author,
            'excerpt'      => $excerpt,
            'content'      => $content,
            'cover_image'  => $newCover,
            'featured'     => $featured,
            'published_at' => $published_at,
        ]);

    } else {

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

        if ($newCover) {
            $sql .= ", cover_image = :cover_image";
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

        if ($newCover) {
            $params['cover_image'] = $newCover;
        }

        $stmt->execute($params);
    }

    $pdo->commit();

    $status = isset($_POST['add']) ? 'created' : 'updated';
    header("Location: ../posts.php?success={$status}");
    exit;

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Post handler failure: ' . $e->getMessage());
    header('Location: ../posts.php?error=database_failure');
    exit;
}
