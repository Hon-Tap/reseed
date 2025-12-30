<?php

declare(strict_types=1);

/**
 * Admin – Gallery Handler (Cloudinary Optimized)
 * Path: /backend/admin/handlers/gallery-handler.php
 */

/* -------------------------------------------------
 | 1. Bootstrap
 * ------------------------------------------------- */

$baseDir = dirname(__DIR__, 3);

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
    header('Location: ../gallery.php?error=cloudinary_missing');
    exit;
}

Configuration::instance(getenv('CLOUDINARY_URL'));

/* -------------------------------------------------
 | 3. Helpers
 * ------------------------------------------------- */

function uploadGalleryMedia(string $tmpFile, string $category): ?string
{
    try {
        $upload = new UploadApi();

        $result = $upload->upload($tmpFile, [
            'folder'        => 'reseed/gallery',
            'resource_type' => 'image',
            'quality'       => 'auto',
            'fetch_format'  => 'auto',
            'tags'          => ['gallery', strtolower($category)]
        ]);

        return $result['secure_url'] ?? null;

    } catch (Throwable $e) {
        error_log('Gallery Upload Error: ' . $e->getMessage());
        return null;
    }
}

/* -------------------------------------------------
 | 4. Guard
 * ------------------------------------------------- */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gallery.php');
    exit;
}

csrf_verify($_POST['csrf_token'] ?? null);

/* -------------------------------------------------
 | 5. Actions
 * ------------------------------------------------- */

try {

    /* -------- BULK ADD -------- */
    if (isset($_POST['bulk_add'])) {

        if (empty($_FILES['images']['tmp_name'][0])) {
            header('Location: ../gallery.php?error=no_files');
            exit;
        }

        $category     = trim($_POST['category'] ?? 'General');
        $baseCaption  = trim($_POST['caption'] ?? '');
        $mediaType    = 'image';
        $uploadCount  = 0;

        $pdo->beginTransaction();

        foreach ($_FILES['images']['tmp_name'] as $i => $tmpFile) {

            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $url = uploadGalleryMedia($tmpFile, $category);

            if (!$url) {
                continue;
            }

            $caption = $baseCaption !== ''
                ? $baseCaption
                : pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME);

            $stmt = $pdo->prepare(
                'INSERT INTO gallery (filename, caption, category, media_type, created_at)
                 VALUES (:filename, :caption, :category, :media_type, NOW())'
            );

            $stmt->execute([
                'filename'   => $url,
                'caption'    => $caption,
                'category'   => $category,
                'media_type' => $mediaType
            ]);

            $uploadCount++;
        }

        $pdo->commit();

        header("Location: ../gallery.php?success=uploaded&count=$uploadCount");
        exit;
    }

    /* -------- UPDATE -------- */
    if (isset($_POST['update'], $_POST['id'])) {

        $id       = (int) $_POST['id'];
        $caption  = trim($_POST['caption'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        $stmt = $pdo->prepare(
            'UPDATE gallery
             SET caption = :caption, category = :category
             WHERE id = :id'
        );

        $stmt->execute([
            'caption'  => $caption,
            'category' => $category,
            'id'       => $id
        ]);

        header('Location: ../gallery.php?success=updated');
        exit;
    }

    /* -------- DELETE -------- */
    if (isset($_POST['delete'], $_POST['id'])) {

        $id = (int) $_POST['id'];

        $stmt = $pdo->prepare('DELETE FROM gallery WHERE id = ?');
        $stmt->execute([$id]);

        header('Location: ../gallery.php?success=deleted');
        exit;
    }

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Gallery Handler Error: ' . $e->getMessage());
    header('Location: ../gallery.php?error=system_error');
    exit;
}

/* -------------------------------------------------
 | 6. Fallback
 * ------------------------------------------------- */

header('Location: ../gallery.php');
exit;
