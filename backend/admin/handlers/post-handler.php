<?php
declare(strict_types=1);

// 1. Correct the path to reach backend/includes/config.php
$rootPath = dirname(__DIR__, 2); 

require_once $rootPath . '/includes/config.php';
require_once $rootPath . '/admin/includes/csrf.php';

// 2. SAFETY CHECK: Ensure UPLOAD_ROOT is defined
if (!defined('UPLOAD_ROOT')) {
    define('UPLOAD_ROOT', $rootPath . '/uploads');
}

$uploadDir = UPLOAD_ROOT . '/posts/';

// 3. Ensure the folder exists on the server
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

/* ===================== UPLOAD CONFIG ===================== */

$imageMime = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
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

    $title    = trim($_POST['title'] ?? '');
    $slug     = slugify($_POST['slug'] ?? $title);
    $content  = trim($_POST['content'] ?? '');
    $status   = $_POST['status'] ?? 'draft';
    $featured = isset($_POST['featured']) ? 1 : 0;

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
        // Removed category_id to match your database error deduction
        $stmt = $pdo->prepare("
            INSERT INTO posts (
                title, slug, content, cover_image, 
                status, featured, created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, NOW()
            )
        ");

        $stmt->execute([
            $title,
            $slug,
            $content,
            $coverImage,
            $status,
            $featured
        ]);

        header('Location: ../posts.php?success=added');
        exit;
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}

/* ===================== UPDATE POST ===================== */

if (isset($_POST['update'], $_POST['id'])) {

    $id       = (int) $_POST['id'];
    $title    = trim($_POST['title'] ?? '');
    $slug     = slugify($_POST['slug'] ?? $title);
    $content  = trim($_POST['content'] ?? '');
    $status   = $_POST['status'] ?? 'draft';
    $featured = isset($_POST['featured']) ? 1 : 0;

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

        // Clean up old image
        $stmt = $pdo->prepare('SELECT cover_image FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetchColumn();

        if ($old && file_exists($uploadDir . $old)) {
            unlink($uploadDir . $old);
        }
    }

    try {
        if ($newImage) {
            $stmt = $pdo->prepare("
                UPDATE posts SET
                    title=?, slug=?, content=?, cover_image=?,
                    status=?, featured=?
                WHERE id=?
            ");
            $stmt->execute([$title, $slug, $content, $newImage, $status, $featured, $id]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE posts SET
                    title=?, slug=?, content=?, status=?, featured=?
                WHERE id=?
            ");
            $stmt->execute([$title, $slug, $content, $status, $featured, $id]);
        }

        header('Location: ../posts.php?success=updated');
        exit;
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
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