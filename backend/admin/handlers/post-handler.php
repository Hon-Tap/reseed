<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| PATHS & BOOTSTRAP
|--------------------------------------------------------------------------
*/
$rootPath = dirname(__DIR__, 2);

require_once $rootPath . '/includes/config.php';
require_once $rootPath . '/admin/includes/csrf.php';

/*
|--------------------------------------------------------------------------
| UPLOAD CONFIG
|--------------------------------------------------------------------------
*/
if (!defined('UPLOAD_ROOT')) {
    define('UPLOAD_ROOT', $rootPath . '/uploads');
}

$uploadDir = UPLOAD_ROOT . '/posts/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$imageMime = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
function slugify(string $value): string
{
    return trim(
        strtolower(preg_replace('/[^a-z0-9]+/', '-', $value)),
        '-'
    );
}

function generate_filename(string $ext): string
{
    return bin2hex(random_bytes(16)) . '.' . $ext;
}

/*
|--------------------------------------------------------------------------
| ADD POST
|--------------------------------------------------------------------------
*/
if (isset($_POST['add'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $title        = trim($_POST['title'] ?? '');
    $slug         = slugify($_POST['slug'] ?? $title);
    $author       = trim($_POST['author'] ?? '');
    $excerpt      = trim($_POST['excerpt'] ?? '');
    $content      = trim($_POST['content'] ?? '');
    $published_at = $_POST['published_at'] ?? null;
    $featured     = isset($_POST['featured']) ? true : false;

    $coverImage = null;

    if (!empty($_FILES['media_file']['tmp_name'])) {

        $mime = mime_content_type($_FILES['media_file']['tmp_name']);

        if (!isset($imageMime[$mime])) {
            header('Location: ../posts.php?error=type');
            exit;
        }

        $coverImage = generate_filename($imageMime[$mime]);

        if (!move_uploaded_file(
            $_FILES['media_file']['tmp_name'],
            $uploadDir . $coverImage
        )) {
            header('Location: ../posts.php?error=upload');
            exit;
        }
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO posts (
                title,
                slug,
                author,
                excerpt,
                content,
                cover_image,
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
                :featured,
                :published_at,
                NOW()
            )
        ");

        $stmt->execute([
            ':title'        => $title,
            ':slug'         => $slug,
            ':author'       => $author,
            ':excerpt'      => $excerpt,
            ':content'      => $content,
            ':cover_image'  => $coverImage,
            ':featured'     => $featured,
            ':published_at' => $published_at,
        ]);

        header('Location: ../posts.php?success=added');
        exit;

    } catch (PDOException $e) {
        die('Database Error: ' . $e->getMessage());
    }
}

/*
|--------------------------------------------------------------------------
| UPDATE POST
|--------------------------------------------------------------------------
*/
if (isset($_POST['update'], $_POST['id'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $id           = (int) $_POST['id'];
    $title        = trim($_POST['title'] ?? '');
    $slug         = slugify($_POST['slug'] ?? $title);
    $author       = trim($_POST['author'] ?? '');
    $excerpt      = trim($_POST['excerpt'] ?? '');
    $content      = trim($_POST['content'] ?? '');
    $published_at = $_POST['published_at'] ?? null;
    $featured     = isset($_POST['featured']) ? true : false;

    $newImage = null;

    if (!empty($_FILES['media_file']['tmp_name'])) {

        $mime = mime_content_type($_FILES['media_file']['tmp_name']);

        if (!isset($imageMime[$mime])) {
            header('Location: ../posts.php?error=type');
            exit;
        }

        $newImage = generate_filename($imageMime[$mime]);

        if (!move_uploaded_file(
            $_FILES['media_file']['tmp_name'],
            $uploadDir . $newImage
        )) {
            header('Location: ../posts.php?error=upload');
            exit;
        }

        $stmt = $pdo->prepare("SELECT cover_image FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $oldImage = $stmt->fetchColumn();

        if ($oldImage && file_exists($uploadDir . $oldImage)) {
            unlink($uploadDir . $oldImage);
        }
    }

    try {
        if ($newImage) {
            $stmt = $pdo->prepare("
                UPDATE posts SET
                    title = :title,
                    slug = :slug,
                    author = :author,
                    excerpt = :excerpt,
                    content = :content,
                    cover_image = :cover_image,
                    featured = :featured,
                    published_at = :published_at
                WHERE id = :id
            ");
        } else {
            $stmt = $pdo->prepare("
                UPDATE posts SET
                    title = :title,
                    slug = :slug,
                    author = :author,
                    excerpt = :excerpt,
                    content = :content,
                    featured = :featured,
                    published_at = :published_at
                WHERE id = :id
            ");
        }

        $params = [
            ':title'        => $title,
            ':slug'         => $slug,
            ':author'       => $author,
            ':excerpt'      => $excerpt,
            ':content'      => $content,
            ':featured'     => $featured,
            ':published_at' => $published_at,
            ':id'           => $id,
        ];

        if ($newImage) {
            $params[':cover_image'] = $newImage;
        }

        $stmt->execute($params);

        header('Location: ../posts.php?success=updated');
        exit;

    } catch (PDOException $e) {
        die('Database Error: ' . $e->getMessage());
    }
}

/*
|--------------------------------------------------------------------------
| DELETE POST
|--------------------------------------------------------------------------
*/
if (isset($_POST['delete'], $_POST['id'])) {

    csrf_verify($_POST['csrf_token'] ?? '');

    $id = (int) $_POST['id'];

    $stmt = $pdo->prepare("SELECT cover_image FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();

    if ($image && file_exists($uploadDir . $image)) {
        unlink($uploadDir . $image);
    }

    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: ../posts.php?success=deleted');
    exit;
}

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/
header('Location: ../posts.php');
exit;
