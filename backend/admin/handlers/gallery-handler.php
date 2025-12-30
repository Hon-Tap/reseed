<?php
declare(strict_types=1);

$baseDir = dirname(__DIR__, 3);
if (!file_exists($baseDir . '/vendor/autoload.php')) { $baseDir = dirname(__DIR__, 2); }

require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Api\Upload\UploadApi;
Configuration::instance(getenv('CLOUDINARY_URL'));

if (isset($_POST['bulk_add'])) {
    $upload = new UploadApi();
    foreach ($_FILES['images']['tmp_name'] as $i => $tmpFile) {
        if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
            $res = $upload->upload($tmpFile, ['folder' => 'reseed/gallery']);
            $stmt = $pdo->prepare("INSERT INTO gallery (filename, caption, category, media_type, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$res['secure_url'], $_POST['caption'] ?: 'Gallery Image', $_POST['category'], 'image']);
        }
    }
    header("Location: ../gallery.php?success=uploaded");
}