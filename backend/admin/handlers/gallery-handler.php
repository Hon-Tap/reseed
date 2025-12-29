<?php
declare(strict_types=1);

/**
 * Admin - Gallery Media Handler
 * Path: /backend/admin/handlers/gallery-handler.php
 */

$baseDir = dirname(__DIR__, 3); 
require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// 1. Initialize Cloudinary
if (!getenv('CLOUDINARY_URL')) {
    header('Location: ../gallery.php?error=cloudinary_config');
    exit;
}
Configuration::instance(getenv('CLOUDINARY_URL'));

/**
 * Upload single image to Cloudinary
 */
function uploadToGallery(string $tmpFile, string $category): ?string 
{
    try {
        $api = new UploadApi();
        $response = $api->upload($tmpFile, [
            'folder'         => 'reseed/gallery',
            'resource_type'  => 'image',
            'quality'        => 'auto',
            'fetch_format'   => 'auto',
            'tags'           => ['gallery', strtolower($category)]
        ]);
        return $response['secure_url'];
    } catch (Exception $e) {
        error_log("Gallery Upload Error: " . $e->getMessage());
        return null;
    }
}

// 2. Security Check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gallery.php');
    exit;
}

csrf_verify($_POST['csrf_token'] ?? '');

// 3. Process Actions
try {
    // --- BULK ADD ---
    if (isset($_POST['bulk_add'])) {
        if (empty($_FILES['images']['tmp_name'][0])) {
            header('Location: ../gallery.php?error=no_files_selected');
            exit;
        }

        $category = trim($_POST['category'] ?? 'General');
        $baseCaption = trim($_POST['caption'] ?? '');
        $uploadCount = 0;

        $pdo->beginTransaction();
        
        foreach ($_FILES['images']['tmp_name'] as $index => $tmpFile) {
            if ($_FILES['images']['error'][$index] !== UPLOAD_ERR_OK) continue;

            $url = uploadToGallery($tmpFile, $category);
            
            if ($url) {
                // If user didn't provide a bulk caption, use the original filename
                $finalCaption = !empty($baseCaption) 
                    ? $baseCaption 
                    : pathinfo($_FILES['images']['name'][$index], PATHINFO_FILENAME);

                $stmt = $pdo->prepare("INSERT INTO gallery (filename, caption, category, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$url, $finalCaption, $category]);
                $uploadCount++;
            }
        }

        $pdo->commit();
        header("Location: ../gallery.php?success=uploaded&count=$uploadCount");
        exit;
    }

    // --- UPDATE METADATA ---
    if (isset($_POST['update'], $_POST['id'])) {
        $id = (int)$_POST['id'];
        $caption = trim($_POST['caption'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        $stmt = $pdo->prepare("UPDATE gallery SET caption = ?, category = ? WHERE id = ?");
        $stmt->execute([$caption, $category, $id]);

        header('Location: ../gallery.php?success=updated');
        exit;
    }

    // --- DELETE ---
    if (isset($_POST['delete'], $_POST['id'])) {
        $id = (int)$_POST['id'];

        // Optional: In the future, fetch the URL and delete from Cloudinary here
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: ../gallery.php?success=deleted');
        exit;
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("Gallery Handler Error: " . $e->getMessage());
    header('Location: ../gallery.php?error=system_error');
    exit;
}

// Fallback
header('Location: ../gallery.php');