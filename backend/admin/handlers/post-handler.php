<?php
declare(strict_types=1);

$baseDir = dirname(__DIR__, 3);
if (!file_exists($baseDir . '/vendor/autoload.php')) { $baseDir = dirname(__DIR__, 2); }

require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Api\Upload\UploadApi;
Configuration::instance(getenv('CLOUDINARY_URL'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
csrf_verify($_POST['csrf_token'] ?? null);

$id = $_POST['id'] ?? null;
$isAdd = isset($_POST['add']);

$coverMedia = null;
if (!empty($_FILES['media_file']['tmp_name'])) {
    $upload = new UploadApi();
    $res = $upload->upload($_FILES['media_file']['tmp_name'], ['folder' => 'reseed/posts']);
    $coverMedia = $res['secure_url'];
}

$params = [
    ':title'        => trim($_POST['title']),
    ':slug'         => strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $_POST['title']), '-')),
    ':author'       => $_POST['author'] ?: 'Team ReSEED',
    ':excerpt'      => $_POST['excerpt'],
    ':content'      => $_POST['content'],
    ':media_type'   => $_POST['media_type'] ?? 'image',
    ':featured'     => isset($_POST['featured']) ? 'true' : 'false',
    ':published_at' => !empty($_POST['published_at']) ? $_POST['published_at'] : date('Y-m-d H:i:s')
];

try {
    if ($isAdd) {
        $params[':cover_media'] = $coverMedia;
        $sql = "INSERT INTO posts (title, slug, author, excerpt, content, media_type, featured, published_at, cover_media, created_at) 
                VALUES (:title, :slug, :author, :excerpt, :content, :media_type, :featured, :published_at, :cover_media, NOW())";
    } else {
        $imgSql = $coverMedia ? ", cover_media = :cover_media" : "";
        if ($coverMedia) $params[':cover_media'] = $coverMedia;
        $params[':id'] = $id;
        $sql = "UPDATE posts SET title=:title, slug=:slug, author=:author, excerpt=:excerpt, content=:content, media_type=:media_type, featured=:featured, published_at=:published_at $imgSql WHERE id = :id";
    }

    $pdo->prepare($sql)->execute($params);
    header("Location: ../posts.php?success=saved");
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: ../posts.php?error=database_error");
}