<?php
declare(strict_types=1);

$baseDir = dirname(__DIR__, 3);
if (!file_exists($baseDir . '/vendor/autoload.php')) { $baseDir = dirname(__DIR__, 2); }

require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

Configuration::instance(getenv('CLOUDINARY_URL'));

function generateUniqueProjectSlug($title, $pdo, $id = 0) {
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM projects WHERE slug = ? AND id != ?');
    $stmt->execute([$slug, $id]);
    return ($stmt->fetchColumn() > 0) ? $slug . '-' . bin2hex(random_bytes(2)) : $slug;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
csrf_verify($_POST['csrf_token'] ?? null);

$id = $_POST['id'] ?? null;
$isAdd = isset($_POST['add']);

// 1. Handle Media
$mediaType = $_POST['media_type'] ?? 'image';
$coverMedia = null;

if ($mediaType === 'url') {
    $coverMedia = filter_var($_POST['media_url'] ?? '', FILTER_SANITIZE_URL);
} elseif (!empty($_FILES['media_file']['tmp_name'])) {
    $upload = new UploadApi();
    $res = $upload->upload($_FILES['media_file']['tmp_name'], ['folder' => 'reseed/projects']);
    $coverMedia = $res['secure_url'];
}

// 2. Build Params Dynamically
$params = [
    ':title'       => trim($_POST['title']),
    ':slug'        => generateUniqueProjectSlug($_POST['slug'] ?: $_POST['title'], $pdo, (int)$id),
    ':location'    => trim($_POST['location']),
    ':summary'     => trim($_POST['summary']),
    ':description' => trim($_POST['description']),
    ':status'      => $_POST['status'] ?? 'Planned',
    ':start_date'  => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
    ':end_date'    => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
    ':featured'    => isset($_POST['featured']) ? 'true' : 'false',
    ':media_type'  => $mediaType
];

try {
    if ($isAdd) {
        $params[':cover_media'] = $coverMedia;
        $sql = "INSERT INTO projects (title, slug, location, summary, description, status, start_date, end_date, featured, media_type, cover_media, created_at) 
                VALUES (:title, :slug, :location, :summary, :description, :status, :start_date, :end_date, :featured, :media_type, :cover_media, NOW())";
    } else {
        $imgSql = "";
        if ($coverMedia) {
            $imgSql = ", cover_media = :cover_media";
            $params[':cover_media'] = $coverMedia;
        }
        $params[':id'] = $id;
        $sql = "UPDATE projects SET title=:title, slug=:slug, location=:location, summary=:summary, description=:description, status=:status, start_date=:start_date, end_date=:end_date, featured=:featured, media_type=:media_type $imgSql WHERE id = :id";
    }

    $pdo->prepare($sql)->execute($params);
    header("Location: ../projects.php?success=saved");
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: ../projects.php?error=db_failure");
}