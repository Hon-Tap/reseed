<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 2);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/csrf.php';


/* ===================== METHOD ENFORCEMENT ===================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

/* ===================== UPLOAD CONFIG ===================== */

$uploadDir = UPLOAD_ROOT . '/projects/';

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

/* ===================== ADD PROJECT ===================== */

if (isset($_POST['add'])) {

    $title        = trim($_POST['title'] ?? '');
    $slug         = slugify($_POST['slug'] ?? $title);
    $summary      = trim($_POST['summary'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $location     = trim($_POST['location'] ?? '');
    $start_date   = $_POST['start_date'] ?: null;
    $end_date     = $_POST['end_date'] ?: null;
    $status       = $_POST['status'] ?? 'planned';
    $media_type   = $_POST['media_type'] ?? 'image';
    $media_url    = trim($_POST['media_url'] ?? '') ?: null;
    $featured     = isset($_POST['featured']) ? 1 : 0;

    $coverImage = null;

    if (!empty($_FILES['media_file']['tmp_name'])) {

        $mime = mime_content_type($_FILES['media_file']['tmp_name']);
        $map  = $media_type === 'image' ? $imageMime : $videoMime;

        if (!isset($map[$mime])) {
            header('Location: ../projects.php?error=type');
            exit;
        }

        $coverImage = generate_filename($map[$mime]);

        if (!move_uploaded_file(
            $_FILES['media_file']['tmp_name'],
            $uploadDir . $coverImage
        )) {
            header('Location: ../projects.php?error=upload');
            exit;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO projects (
            title, slug, summary, description, location,
            start_date, end_date, cover_image,
            media_type, media_url, status,
            featured, created_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
        )
    ");

    $stmt->execute([
        $title,
        $slug,
        $summary,
        $description,
        $location,
        $start_date,
        $end_date,
        $coverImage,
        $media_type,
        $media_url,
        $status,
        $featured
    ]);

    header('Location: ../projects.php?success=added');
    exit;
}

/* ===================== UPDATE PROJECT ===================== */

if (isset($_POST['update'], $_POST['id'])) {

    $id           = (int) $_POST['id'];
    $title        = trim($_POST['title'] ?? '');
    $slug         = slugify($_POST['slug'] ?? $title);
    $summary      = trim($_POST['summary'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $location     = trim($_POST['location'] ?? '');
    $start_date   = $_POST['start_date'] ?: null;
    $end_date     = $_POST['end_date'] ?: null;
    $status       = $_POST['status'] ?? 'planned';
    $media_type   = $_POST['media_type'] ?? 'image';
    $media_url    = trim($_POST['media_url'] ?? '') ?: null;
    $featured     = isset($_POST['featured']) ? 1 : 0;

    $newImage = null;

    if (!empty($_FILES['media_file']['tmp_name'])) {

        $mime = mime_content_type($_FILES['media_file']['tmp_name']);
        $map  = $media_type === 'image' ? $imageMime : $videoMime;

        if (!isset($map[$mime])) {
            header('Location: ../projects.php?error=type');
            exit;
        }

        $newImage = generate_filename($map[$mime]);

        if (!move_uploaded_file(
            $_FILES['media_file']['tmp_name'],
            $uploadDir . $newImage
        )) {
            header('Location: ../projects.php?error=upload');
            exit;
        }

        $stmt = $pdo->prepare('SELECT cover_image FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetchColumn();

        if ($old && file_exists($uploadDir . $old)) {
            unlink($uploadDir . $old);
        }
    }

    if ($newImage) {
        $stmt = $pdo->prepare("
            UPDATE projects SET
                title=?, slug=?, summary=?, description=?, location=?,
                start_date=?, end_date=?, cover_image=?,
                media_type=?, media_url=?, status=?, featured=?
            WHERE id=?
        ");

        $stmt->execute([
            $title,
            $slug,
            $summary,
            $description,
            $location,
            $start_date,
            $end_date,
            $newImage,
            $media_type,
            $media_url,
            $status,
            $featured,
            $id
        ]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE projects SET
                title=?, slug=?, summary=?, description=?, location=?,
                start_date=?, end_date=?,
                media_type=?, media_url=?, status=?, featured=?
            WHERE id=?
        ");

        $stmt->execute([
            $title,
            $slug,
            $summary,
            $description,
            $location,
            $start_date,
            $end_date,
            $media_type,
            $media_url,
            $status,
            $featured,
            $id
        ]);
    }

    header('Location: ../projects.php?success=updated');
    exit;
}

/* ===================== DELETE PROJECT ===================== */

if (isset($_POST['delete'], $_POST['id'])) {

    $id = (int) $_POST['id'];

    $stmt = $pdo->prepare('SELECT cover_image FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();

    if ($file && file_exists($uploadDir . $file)) {
        unlink($uploadDir . $file);
    }

    $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: ../projects.php?success=deleted');
    exit;
}

/* ===================== FALLBACK ===================== */

header('Location: ../projects.php');
exit;
