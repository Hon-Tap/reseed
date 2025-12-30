<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · GALLERY HANDLER (STABLE, FLAG-FREE)
|--------------------------------------------------------------------------
*/

$backendDir = dirname(__DIR__, 2);
$rootDir    = dirname(__DIR__, 3);

require_once $rootDir . '/vendor/autoload.php';
require_once $backendDir . '/includes/config.php';
require_once $backendDir . '/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

/* ----------------------------------------------------------------------
| Guards
|---------------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gallery.php');
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

csrf_verify($_POST['csrf_token'] ?? null);

if (!getenv('CLOUDINARY_URL')) {
    header('Location: ../gallery.php?error=cloudinary_missing');
    exit;
}

Configuration::instance(getenv('CLOUDINARY_URL'));

/* ----------------------------------------------------------------------
| Helpers
|---------------------------------------------------------------------- */
function uploadGalleryImage(string $tmp, string $category): ?string
{
    try {
        $api = new UploadApi();
        $res = $api->upload($tmp, [
            'folder'        => 'reseed/gallery',
            'resource_type' => 'image',
            'quality'       => 'auto',
            'fetch_format'  => 'auto',
            'tags'          => ['gallery', strtolower($category)],
        ]);

        return $res['secure_url'] ?? null;
    } catch (Throwable $e) {
        error_log('[Gallery Upload] ' . $e->getMessage());
        return null;
    }
}

/* ----------------------------------------------------------------------
| Processing
|---------------------------------------------------------------------- */
try {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : null;

    /* ================= BULK INSERT ================= */
    if (!$id && isset($_FILES['images'])) {

        if (empty($_FILES['images']['tmp_name'][0])) {
            header('Location: ../gallery.php?error=no_files');
            exit;
        }

        $category    = trim($_POST['category'] ?? 'General');
        $baseCaption = trim($_POST['caption'] ?? '');
        $count       = 0;

        $pdo->beginTransaction();

        foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {

            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $url = uploadGalleryImage($tmp, $category);
            if (!$url) {
                continue;
            }

            $caption = $baseCaption !== ''
                ? $baseCaption
                : pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME);

            $stmt = $pdo->prepare("
                INSERT INTO gallery (filename, caption, category, created_at)
                VALUES (:filename, :caption, :category, NOW())
            ");

            $stmt->execute([
                'filename' => $url,
                'caption'  => $caption,
                'category' => $category,
            ]);

            $count++;
        }

        $pdo->commit();
        header("Location: ../gallery.php?success=uploaded&count={$count}");
        exit;
    }

    /* ================= UPDATE ================= */
    if ($id && !isset($_POST['delete'])) {

        $caption  = trim($_POST['caption'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        $stmt = $pdo->prepare("
            UPDATE gallery
            SET caption = :caption, category = :category
            WHERE id = :id
        ");

        $stmt->execute([
            'caption'  => $caption,
            'category' => $category,
            'id'       => $id,
        ]);

        header('Location: ../gallery.php?success=updated');
        exit;
    }

    /* ================= DELETE ================= */
    if ($id && isset($_POST['delete'])) {

        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = :id");
        $stmt->execute(['id' => $id]);

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

header('Location: ../gallery.php');
exit;
