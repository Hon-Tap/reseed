<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| BOOTSTRAP
|--------------------------------------------------------------------------
*/

// Resolve project root safely
$ROOT = realpath(__DIR__ . '/../../../../');
if ($ROOT === false) {
    http_response_code(500);
    exit('Critical error: Cannot resolve application root.');
}

// Error reporting (disable in prod if needed)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Composer (Cloudinary SDK)
if (!file_exists($ROOT . '/vendor/autoload.php')) {
    http_response_code(500);
    exit('Composer autoload missing. Run composer install.');
}
require_once $ROOT . '/vendor/autoload.php';

// App config
require_once $ROOT . '/backend/includes/config.php';

// CSRF
require_once $ROOT . '/backend/admin/includes/csrf.php';

/*
|--------------------------------------------------------------------------
| CLOUDINARY CONFIG
|--------------------------------------------------------------------------
*/

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

if (!getenv('CLOUDINARY_URL')) {
    http_response_code(500);
    exit('CLOUDINARY_URL is not set.');
}

Configuration::instance(getenv('CLOUDINARY_URL'));

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function uploadGalleryImage(string $tmpFile, string $category): ?string
{
    try {
        $result = (new UploadApi())->upload(
            $tmpFile,
            [
                'folder'        => 'reseed/gallery',
                'resource_type' => 'image',
                'tags'          => ['gallery', strtolower($category)],
            ]
        );

        return $result['secure_url'] ?? null;
    } catch (Throwable $e) {
        return null;
    }
}

/*
|--------------------------------------------------------------------------
| BULK ADD IMAGES
|--------------------------------------------------------------------------
*/

if (isset($_POST['bulk_add'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    if (empty($_FILES['images']['tmp_name'][0])) {
        redirect('../gallery.php?error=nofiles');
    }

    $category = trim($_POST['category'] ?? 'General');
    $caption  = trim($_POST['caption'] ?? '');

    $success  = 0;

    foreach ($_FILES['images']['tmp_name'] as $i => $tmpFile) {

        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }

        $imageUrl = uploadGalleryImage($tmpFile, $category);

        if (!$imageUrl) {
            continue;
        }

        $finalCaption = $caption !== ''
            ? $caption
            : pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME);

        $stmt = $pdo->prepare("
            INSERT INTO gallery (
                filename,
                caption,
                category,
                created_at
            ) VALUES (
                :filename,
                :caption,
                :category,
                NOW()
            )
        ");

        $stmt->execute([
            'filename' => $imageUrl,
            'caption'  => $finalCaption,
            'category' => $category,
        ]);

        $success++;
    }

    if ($success > 0) {
        redirect("../gallery.php?success=uploaded&count={$success}");
    }

    redirect('../gallery.php?error=upload_failed');
}

/*
|--------------------------------------------------------------------------
| UPDATE METADATA
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'], $_POST['id'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $id       = (int) $_POST['id'];
    $caption  = trim($_POST['caption'] ?? '');
    $category = trim($_POST['category'] ?? '');

    $stmt = $pdo->prepare("
        UPDATE gallery
        SET caption = :caption,
            category = :category
        WHERE id = :id
    ");

    $stmt->execute([
        'caption'  => $caption,
        'category' => $category,
        'id'       => $id,
    ]);

    redirect('../gallery.php?success=updated');
}

/*
|--------------------------------------------------------------------------
| DELETE IMAGE
|--------------------------------------------------------------------------
*/

if (isset($_POST['delete'], $_POST['id'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $id = (int) $_POST['id'];

    // NOTE:
    // We intentionally do NOT delete from Cloudinary yet.
    // Once public_id is stored, cleanup can be added safely.

    $pdo->prepare('DELETE FROM gallery WHERE id = :id')
        ->execute(['id' => $id]);

    redirect('../gallery.php?success=deleted');
}

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/

redirect('../gallery.php');
