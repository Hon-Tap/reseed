<?php

declare(strict_types=1);

/**
 * Admin – Post Handler (Cloudinary Optimized)
 * Path: /backend/admin/handlers/post-handler.php
 */

/* -------------------------------------------------
 | 1. Bootstrap & Environment
 * ------------------------------------------------- */

$baseDir = dirname(__DIR__, 3);

// Fallback for different deployments
if (!file_exists($baseDir . '/vendor/autoload.php')) {
    $baseDir = dirname(__DIR__, 2);
}

require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

/* -------------------------------------------------
 | 2. Cloudinary Init
 * ------------------------------------------------- */

if (!getenv('CLOUDINARY_URL')) {
    header('Location: ../posts.php?error=cloudinary_missing');
    exit;
}

Configuration::instance(getenv('CLOUDINARY_URL'));

/* -------------------------------------------------
 | 3. Helpers
 * ------------------------------------------------- */

function generateUniqueSlug(string $title, PDO $pdo, int $excludeId = 0): string
{
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));

    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM posts WHERE slug = ? AND id != ?'
    );
    $stmt->execute([$slug, $excludeId]);

    if ($stmt->fetchColumn() > 0) {
        $slug .= '-' . bin2hex(random_bytes(2));
    }

    return $slug;
}

function uploadToCloudinary(array $file, string $mediaType): ?string
{
    if (empty($file['tmp_name'])) {
        return null;
    }

    try {
        $upload = new UploadApi();

        $result = $upload->upload($file['tmp_name'], [
            'folder'        => 'reseed/posts',
            'resource_type' => $mediaType === 'video' ? 'video' : 'image',
            'quality'       => 'auto',
            'fetch_format'  => 'auto'
        ]);

        return $result['secure_url'] ?? null;

    } catch (Throwable $e) {
        error_log('Cloudinary Upload Error: ' . $e->getMessage());
        return null;
    }
}

/* -------------------------------------------------
 | 4. Guard: POST only
 * ------------------------------------------------- */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../posts.php');
    exit;
}

csrf_verify($_POST['csrf_token'] ?? null);

/* -------------------------------------------------
 | 5. Input
 * ------------------------------------------------- */

$id           = isset($_POST['id']) ? (int) $_POST['id'] : null;
$title        = trim($_POST['title'] ?? '');
$author       = trim($_POST['author'] ?? 'Team ReSEED');
$excerpt      = trim($_POST['excerpt'] ?? '');
$content      = trim($_POST['content'] ?? '');
$mediaType    = $_POST['media_type'] ?? 'image';
$featured     = isset($_POST['featured']) ? 1 : 0;
$publishedAt  = !empty($_POST['published_at'])
    ? $_POST['published_at']
    : date('Y-m-d H:i:s');

/* -------------------------------------------------
 | 6. Transaction
 * ------------------------------------------------- */

try {
    $pdo->beginTransaction();

    /* -------- DELETE -------- */
    if (isset($_POST['delete']) && $id) {
        $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$id]);

        $pdo->commit();
        header('Location: ../posts.php?success=deleted');
        exit;
    }

    /* -------- ADD / UPDATE -------- */

    $slugInput = trim($_POST['slug'] ?? '');
    $slugBase  = $slugInput !== '' ? $slugInput : $title;
    $slug      = generateUniqueSlug($slugBase, $pdo, $id ?? 0);

    $coverMedia = uploadToCloudinary($_FILES['media_file'] ?? [], $mediaType);

    if (isset($_POST['add'])) {

        $sql = "
            INSERT INTO posts (
                title, slug, author, excerpt, content,
                cover_media, media_type, featured,
                published_at, created_at
            ) VALUES (
                :title, :slug, :author, :excerpt, :content,
                :cover_media, :media_type, :featured,
                :published_at, NOW()
            )
        ";

    } else {

        $imgSql = $coverMedia ? ', cover_media = :cover_media' : '';

        $sql = "
            UPDATE posts SET
                title = :title,
                slug = :slug,
                author = :author,
                excerpt = :excerpt,
                content = :content,
                media_type = :media_type,
                featured = :featured,
                published_at = :published_at
                $imgSql
            WHERE id = :id
        ";
    }

    $stmt = $pdo->prepare($sql);

    $params = [
        'title'        => $title,
        'slug'         => $slug,
        'author'       => $author,
        'excerpt'      => $excerpt,
        'content'      => $content,
        'media_type'   => $mediaType,
        'featured'     => $featured,
        'published_at' => $publishedAt
    ];

    if ($id) {
        $params['id'] = $id;
    }

    if ($coverMedia || isset($_POST['add'])) {
        $params['cover_media'] = $coverMedia;
    }

    $stmt->execute($params);

    $pdo->commit();

    $action = isset($_POST['add']) ? 'added' : 'updated';
    header("Location: ../posts.php?success=$action");
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Post Handler Error: ' . $e->getMessage());
    header('Location: ../posts.php?error=database_error');
    exit;
}
