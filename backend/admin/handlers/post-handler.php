<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| BOOTSTRAP
|--------------------------------------------------------------------------
*/

// Resolve project root safely (Render / Docker / local)
$ROOT = realpath(__DIR__ . '/../../../../');
if ($ROOT === false) {
    http_response_code(500);
    exit('Critical error: Cannot resolve application root.');
}

// Error reporting (disable in prod if desired)
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

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    return trim($value, '-');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function uploadPostMedia(array $file, string $mediaType): ?string
{
    if (empty($file['tmp_name'])) {
        return null;
    }

    try {
        $result = (new UploadApi())->upload(
            $file['tmp_name'],
            [
                'folder'        => 'reseed/posts',
                'resource_type' => ($mediaType === 'video' ? 'video' : 'image'),
            ]
        );

        return $result['secure_url'] ?? null;
    } catch (Throwable $e) {
        http_response_code(500);
        exit('Cloudinary upload failed: ' . $e->getMessage());
    }
}

/*
|--------------------------------------------------------------------------
| INPUT NORMALIZATION
|--------------------------------------------------------------------------
*/

$action = $_POST['add'] ?? $_POST['update'] ?? $_POST['delete'] ?? null;

$title        = trim($_POST['title'] ?? '');
$slug         = slugify($_POST['slug'] ?? $title);
$author       = trim($_POST['author'] ?? '');
$excerpt      = trim($_POST['excerpt'] ?? '');
$content      = trim($_POST['content'] ?? '');
$published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
$media_type   = $_POST['media_type'] ?? 'image';
$featured     = isset($_POST['featured']) ? 1 : 0;

/*
|--------------------------------------------------------------------------
| ADD POST
|--------------------------------------------------------------------------
*/

if (isset($_POST['add'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $coverImage = uploadPostMedia($_FILES['media_file'] ?? [], $media_type);

    $stmt = $pdo->prepare("
        INSERT INTO posts (
            title,
            slug,
            author,
            excerpt,
            content,
            cover_image,
            media_type,
            featured,
            published_at,
            created_at
        ) VALUES (
            :title,
            :slug,
            :author,
            :excerpt,
            :content,
            :cover_image,
            :media_type,
            :featured,
            :published_at,
            NOW()
        )
    ");

    $stmt->execute([
        'title'        => $title,
        'slug'         => $slug,
        'author'       => $author,
        'excerpt'      => $excerpt,
        'content'      => $content,
        'cover_image'  => $coverImage,
        'media_type'   => $media_type,
        'featured'     => $featured,
        'published_at' => $published_at,
    ]);

    redirect('../posts.php?success=added');
}

/*
|--------------------------------------------------------------------------
| UPDATE POST
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'], $_POST['id'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $id        = (int) $_POST['id'];
    $newMedia  = uploadPostMedia($_FILES['media_file'] ?? [], $media_type);

    if ($newMedia) {
        $sql = "
            UPDATE posts SET
                title=:title,
                slug=:slug,
                author=:author,
                excerpt=:excerpt,
                content=:content,
                cover_image=:cover_image,
                media_type=:media_type,
                featured=:featured,
                published_at=:published_at
            WHERE id=:id
        ";
    } else {
        $sql = "
            UPDATE posts SET
                title=:title,
                slug=:slug,
                author=:author,
                excerpt=:excerpt,
                content=:content,
                media_type=:media_type,
                featured=:featured,
                published_at=:published_at
            WHERE id=:id
        ";
    }

    $stmt = $pdo->prepare($sql);

    $params = [
        'title'        => $title,
        'slug'         => $slug,
        'author'       => $author,
        'excerpt'      => $excerpt,
        'content'      => $content,
        'media_type'   => $media_type,
        'featured'     => $featured,
        'published_at' => $published_at,
        'id'           => $id,
    ];

    if ($newMedia) {
        $params['cover_image'] = $newMedia;
    }

    $stmt->execute($params);

    redirect('../posts.php?success=updated');
}

/*
|--------------------------------------------------------------------------
| DELETE POST
|--------------------------------------------------------------------------
*/

if (isset($_POST['delete'], $_POST['id'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $id = (int) $_POST['id'];

    // Note: Cloudinary assets intentionally retained (no public_id stored yet)
    $pdo->prepare('DELETE FROM posts WHERE id = :id')
        ->execute(['id' => $id]);

    redirect('../posts.php?success=deleted');
}

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/

redirect('../posts.php');
