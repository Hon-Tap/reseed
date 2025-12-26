<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Posts Handler (Admin)
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/admin_auth.php';

/* ===================== METHOD ENFORCEMENT ===================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

/* ===================== UPLOAD CONFIG ===================== */

$uploadDir = UPLOAD_ROOT . '/posts/';

$imageMime = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];

$videoMime = [
    'video/mp4'  => 'mp4',
    'video/webm' => 'webm',
    'video/ogg'  => 'ogg',
];

/* ===================== HELPERS ===================== */

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

/* ===================== ADD POST ===================== */

if (isset($_POST['add'])) {

    $title        = trim($_POST['title'] ?? '');
    $slug         = slugify($_POST['slug'] ?? $title);
    $excerpt      = trim($_POST['excerpt'] ?? '');
    $content      = trim($_POST['content'] ?? '');
    $author       = trim($_POST['author'] ?? '');
    $published_at = $_POST['published_at'] ?: null;
    $media_type   = $_POST['media_type'] ?? 'image';
    $media_url    = trim($_POST['media_url'] ?? '') ?: null;
    $featured     = isset($_POST['featured']) ? 1 : 0;

    $coverImage = null;

    if (!empty($_FILES['media_file']['tmp_name'])) {

        $mime = mime_content_type($_FILES['media_file']['tmp_name']);

        $map = $media_type === 'image' ? $imageMime : $videoMime;

        if (!isset($map[$mime])) {
            header('Location: ../posts.php?error=type');
            exit;
        }

        $coverImage = generate_filename($map[$mime]);

        if (!move_uploaded_file(
            $_FILES['media_file']['tmp_name'],
            $uploadDir . $coverImage
        )) {
            header('Location: ../posts.php?error=upload');
            exit;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO posts (
            title, slug, excerpt, content, author,
            published_at, cover_image, media_type,
            media_url, featured, created_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
        )
    ");

    $stmt->execute([
        $title,
        $slug,
        $excerpt,
        $content,
        $author,
        $published_at,
        $coverImage,
        $media_type,
        $media_url,
        $featured
    ]);

    header('Location: ../posts.php?success=added');
    exit;
}

/* ===================== UPDATE POST ===================== */

if (isset($_POST['update'], $_POST['id'])) {

    $id           = (int) $_POST['id'];
    $title        = trim($_POST['title'] ?? '');
    $slug         = slugify($_POST['slug'] ?? $title);
    $excerpt      = trim($_POST['excerpt'] ?? '');
    $content      = trim($_POST['content'] ?? '');
    $author       = trim($_POST['author'] ?? '');
    $published_at = $_POST['published_at'] ?: null;
    $media_type   = $_POST['media_type'] ?? 'image';
    $media_url    = trim($_POST['media_url'] ?? '') ?: null;
    $featured     = isset($_POST['featured']) ? 1 : 0;

    $newImage = null;

    if (!empty($_FILES['media_file']['tmp_name'])) {

        $mime = mime_content_type($_FILES['media_file']['tmp_name']);
        $map  = $media_type === 'image' ? $imageMime : $videoMime;

        if (!isset($map[$mime])) {
            header('Location: ../posts.php?error=type');
            exit;
        }

        $newImage = generate_filename($map[$mime]);

        if (!move_uploaded_file(
            $_FILES['media_file']['tmp_name'],
            $uploadDir . $newImage
        )) {
            header('Location: ../posts.php?error=upload');
            exit;
        }

        $stmt = $pdo->prepare('SELECT cover_image FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetchColumn();

        if ($old && file_exists($uploadDir . $old)) {
            unlink($uploadDir . $old);
        }
    }

    if ($newImage) {
        $stmt = $pdo->prepare("
            UPDATE posts SET
                title=?, slug=?, excerpt=?, content=?, author=?,
                published_at=?, cover_image=?, media_type=?,
                media_url=?, featured=?
            WHERE id=?
        ");

        $stmt->execute([
            $title, $slug, $excerpt, $content, $author,
            $published_at, $newImage, $media_type,
            $media_url, $featured, $id
        ]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE posts SET
                title=?, slug=?, excerpt=?, content=?, author=?,
                published_at=?, media_type=?, media_url=?, featured=?
            WHERE id=?
        ");

        $stmt->execute([
            $title, $slug, $excerpt, $content, $author,
            $published_at, $media_type, $media_url, $featured, $id
        ]);
    }

    header('Location: ../posts.php?success=updated');
    exit;
}

/* ===================== DELETE POST ===================== */

if (isset($_POST['delete'], $_POST['id'])) {

    $id = (int) $_POST['id'];

    $stmt = $pdo->prepare('SELECT cover_image FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();

    if ($file && file_exists($uploadDir . $file)) {
        unlink($uploadDir . $file);
    }

    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: ../posts.php?success=deleted');
    exit;
}

/* ===================== FALLBACK ===================== */

header('Location: ../posts.php');
exit;
