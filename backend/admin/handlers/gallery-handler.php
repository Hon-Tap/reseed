<?php
declare(strict_types=1);

/**
 * Admin – Gallery Handler (Cloudinary Optimized)
 */

$rootPath = dirname(__DIR__, 2); 

require_once $rootPath . '/includes/config.php'; // Ensure Cloudinary SDK is initialized here
require_once $rootPath . '/admin/includes/csrf.php';

use Cloudinary\Api\Upload\UploadApi;

/* ===================== BULK ADD ===================== */
if (isset($_POST['bulk_add'])) {
    // Verify CSRF for security
    csrf_verify($_POST['csrf_token'] ?? '');

    if (empty($_FILES['images']['name'][0])) {
        header('Location: ../gallery.php?error=nofiles');
        exit;
    }

    $category = trim($_POST['category'] ?? 'General');
    $caption  = trim($_POST['caption'] ?? '');
    $uploadApi = new UploadApi();
    $successCount = 0;

    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;

        try {
            // Upload to Cloudinary
            $upload = $uploadApi->upload($tmp, [
                'folder' => 'reseed_gallery',
                'tags'   => ['gallery', $category]
            ]);

            $finalUrl = $upload['secure_url'];
            
            // If user didn't provide a global caption, use the original filename
            $finalCaption = $caption ?: pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME);

            $stmt = $pdo->prepare('INSERT INTO gallery (filename, caption, category) VALUES (?, ?, ?)');
            $stmt->execute([$finalUrl, $finalCaption, $category]);
            
            $successCount++;
        } catch (Exception $e) {
            // In a real environment, you might log $e->getMessage()
            continue; 
        }
    }

    if ($successCount > 0) {
        header("Location: ../gallery.php?success=uploaded&count=$successCount");
    } else {
        header('Location: ../gallery.php?error=upload_failed');
    }
    exit;
}

/* ===================== DELETE LOGIC ===================== */
if (isset($_POST['delete'], $_POST['id'])) {
    csrf_verify($_POST['csrf_token'] ?? '');
    
    $id = (int)$_POST['id'];

    // Optional: You could extract the public_id from the URL to delete from Cloudinary too
    // For now, we remove the database entry to keep it simple
    $stmt = $pdo->prepare('DELETE FROM gallery WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: ../gallery.php?success=deleted');
    exit;
}

/* ===================== UPDATE LOGIC ===================== */
if (isset($_POST['update'], $_POST['id'])) {
    csrf_verify($_POST['csrf_token'] ?? '');
    
    $id       = (int)$_POST['id'];
    $caption  = trim($_POST['caption'] ?? '');
    $category = trim($_POST['category'] ?? '');

    $stmt = $pdo->prepare('UPDATE gallery SET caption = ?, category = ? WHERE id = ?');
    $stmt->execute([$caption, $category, $id]);

    header('Location: ../gallery.php?success=updated');
    exit;
}

header('Location: ../gallery.php');
exit;