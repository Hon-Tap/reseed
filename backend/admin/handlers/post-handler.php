<?php
declare(strict_types=1);

/**
 * Admin - Post Handler (Cloudinary Optimized)
 * Path: /backend/admin/post-handler.php
 */

// 1. Setup Environment
$baseDir = dirname(__DIR__, 2); // Goes up to project root
require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// 2. Initialize Cloudinary
if (!getenv('CLOUDINARY_URL')) {
    header('Location: ../posts.php?error=cloudinary_missing');
    exit;
}
Configuration::instance(getenv('CLOUDINARY_URL'));

/**
 * Generates a URL-friendly slug and ensures uniqueness
 */
function generateUniqueSlug(string $title, PDO $pdo, int $excludeId = 0): string 
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    
    // Check if slug exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $excludeId]);
    
    if ($stmt->fetchColumn() > 0) {
        $slug .= '-' . bin2hex(random_bytes(2));
    }
    
    return $slug;
}

/**
 * Handles file upload to Cloudinary
 */
function uploadToCloudinary(array $file, string $type): ?string 
{
    if (empty($file['tmp_name'])) return null;

    try {
        $upload = new UploadApi();
        $response = $upload->upload($file['tmp_name'], [
            'folder'        => 'reseed/posts',
            'resource_type' => ($type === 'video' ? 'video' : 'image'),
            'quality'       => 'auto',
            'fetch_format'  => 'auto'
        ]);
        return $response['secure_url'];
    } catch (Exception $e) {
        error_log("Cloudinary Upload Error: " . $e->getMessage());
        return null;
    }
}

// 3. Process Request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../posts.php');
    exit;
}

// CSRF check
csrf_verify($_POST['csrf_token'] ?? '');

$id           = isset($_POST['id']) ? (int)$_POST['id'] : null;
$title        = trim($_POST['title'] ?? '');
$author       = trim($_POST['author'] ?? 'Team ReSEED');
$excerpt      = trim($_POST['excerpt'] ?? '');
$content      = trim($_POST['content'] ?? '');
$media_type   = $_POST['media_type'] ?? 'image';
$featured     = isset($_POST['featured']) ? 1 : 0;
$published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : date('Y-m-d H:i:s');

try {
    $pdo->beginTransaction();

    // --- DELETE ACTION ---
    if (isset($_POST['delete']) && $id) {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $pdo->commit();
        header('Location: ../posts.php?success=deleted');
        exit;
    }

    // --- ADD OR UPDATE ACTION ---
    // Handle Slug
    $slug = !empty($_POST['slug']) ? trim($_POST['slug']) : $title;
    $finalSlug = generateUniqueSlug($slug, $pdo, $id ?? 0);

    // Handle Image
    $uploadedUrl = uploadToCloudinary($_FILES['media_file'] ?? [], $media_type);

    if (isset($_POST['add'])) {
        $sql = "INSERT INTO posts (title, slug, author, excerpt, content, cover_image, media_type, featured, published_at, created_at) 
                VALUES (:title, :slug, :author, :excerpt, :content, :cover_image, :media_type, :featured, :published_at, NOW())";
    } else {
        // Build Update Query (only update image if a new one was uploaded)
        $imgSql = $uploadedUrl ? ", cover_image = :cover_image" : "";
        $sql = "UPDATE posts SET 
                title = :title, slug = :slug, author = :author, excerpt = :excerpt, 
                content = :content, media_type = :media_type, featured = :featured, 
                published_at = :published_at $imgSql 
                WHERE id = :id";
    }

    $stmt = $pdo->prepare($sql);
    $params = [
        'title'        => $title,
        'slug'         => $finalSlug,
        'author'       => $author,
        'excerpt'      => $excerpt,
        'content'      => $content,
        'media_type'   => $media_type,
        'featured'     => $featured,
        'published_at' => $published_at
    ];

    if ($id) $params['id'] = $id;
    if ($uploadedUrl || isset($_POST['add'])) $params['cover_image'] = $uploadedUrl;

    $stmt->execute($params);
    $pdo->commit();

    $msg = isset($_POST['add']) ? 'added' : 'updated';
    header("Location: ../posts.php?success=$msg");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("Post Handler Error: " . $e->getMessage());
    header('Location: ../posts.php?error=database_error');
    exit;
}