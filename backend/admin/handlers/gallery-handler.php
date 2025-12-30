<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · GALLERY HANDLER (DB + CLOUDINARY ALIGNED)
| Path: /backend/admin/handlers/gallery-handler.php
|--------------------------------------------------------------------------
*/

/* ----------------------------------------------------------------------
| Bootstrap
|---------------------------------------------------------------------- */
$baseDir = dirname(__DIR__, 3);
if (!file_exists($baseDir . '/vendor/autoload.php')) {
    $baseDir = dirname(__DIR__, 2);
}

require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

/* ----------------------------------------------------------------------
| Cloudinary Init
|---------------------------------------------------------------------- */
if (!getenv('CLOUDINARY_URL')) {
    header('Location: ../gallery.php?error=cloudinary_missing');
    exit;
}

Configuration::instance(getenv('CLOUDINARY_URL'));

/* ----------------------------------------------------------------------
| Helpers
|---------------------------------------------------------------------- */
function uploadImage(string $tmp, string $category): ?string
{
    try {
        $api = new UploadApi();
        $res = $api->upload($tmp, [
            'folder'        => 'reseed/gallery',
            'resource_type' => 'image',
            'quality'       => 'auto',
            'fetch_format'  => 'auto',
            'tags'          => ['gallery', strtolower($category)]
        ]);

        return $res['secure_url'] ?? null;
    } catch (Throwable $e) {
        error_log('[Gallery Upload] ' . $e->getMessage());
        return null;
    }
}

/* ----------------------------------------------------------------------
| Guard
|---------------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gallery.php');
    exit;
}

csrf_verify($_POST['csrf_token'] ?? '');

/* ----------------------------------------------------------------------
| Actions
|---------------------------------------------------------------------- */
try {

    /* ================= BULK UPLOAD ================= */
    if (isset($_POST['bulk_add'])) {

        if (empty($_FILES['images']['tmp_name'][0])) {
            header('Location: ../gallery.php?error=no_files');
            exit;
        }

        $category = trim($_POST['category'] ?? 'General');
        $baseCaption = trim($_POST['caption'] ?? '');
        $count = 0;

        $pdo->beginTransaction();

        foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {

            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $url = uploadImage($tmp, $category);
            if (!$url) {
                continue;
            }

            $caption = $baseCaption !== ''
                ? $baseCaption
                : pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME);

            $stmt = $pdo->prepare(
                "INSERT INTO gallery (filename, caption, category, created_at)
                 VALUES (:filename, :caption, :category, NOW())"
            );

            $stmt->execute([
                'filename' => $url,
                'caption'  => $caption,
                'category' => $category
            ]);

            $count++;
        }

        $pdo->commit();
        header("Location: ../gallery.php?success=uploaded&count=$count");
        exit;
    }

    /* ================= UPDATE META ================= */
    if (isset($_POST['update'], $_POST['id'])) {

        $id = (int)$_POST['id'];
        $caption = trim($_POST['caption'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        $stmt = $pdo->prepare(
            "UPDATE gallery SET caption = :caption, category = :category WHERE id = :id"
        );

        $stmt->execute([
            'caption'  => $caption,
            'category' => $category,
            'id'       => $id
        ]);

        header('Location: ../gallery.php?success=updated');
        exit;
    }

    /* ================= DELETE ================= */
    if (isset($_POST['delete'], $_POST['id'])) {

        $id = (int)$_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: ../gallery.php?success=deleted');
        exit;
    }

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('[Gallery Handler] ' . $e->getMessage());
    header('Location: ../gallery.php?error=system');
    exit;
}

/* ----------------------------------------------------------------------
| Fallback
|---------------------------------------------------------------------- */
header('Location: ../gallery.php');
exit;
