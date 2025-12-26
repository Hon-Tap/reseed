<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Gallery Handler (Admin)
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

$uploadDir = UPLOAD_ROOT . '/gallery/';
$allowedMime = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];

/* ===================== HELPERS ===================== */

function generate_filename(string $ext): string
{
    return bin2hex(random_bytes(16)) . '.' . $ext;
}

/* ===================== BULK ADD ===================== */

if (isset($_POST['bulk_add'])) {

    if (empty($_FILES['images']['name'][0])) {
        header('Location: ../gallery.php?error=nofiles');
        exit;
    }

    $category = trim($_POST['category'] ?? '');
    $caption  = trim($_POST['caption'] ?? '');

    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {

        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }

        $mime = mime_content_type($tmp);

        if (!isset($allowedMime[$mime])) {
            continue;
        }

        $filename = generate_filename($allowedMime[$mime]);

        if (!move_uploaded_file($tmp, $uploadDir . $filename)) {
            continue;
        }

        $finalCaption = $caption ?: pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME);

        $stmt = $pdo->prepare(
            'INSERT INTO gallery (filename, caption, category)
             VALUES (?, ?, ?)'
        );
        $stmt->execute([$filename, $finalCaption, $category]);
    }

    header('Location: ../gallery.php?success=uploaded');
    exit;
}

/* ===================== UPDATE ===================== */

if (isset($_POST['update'], $_POST['id'])) {

    $id       = (int) $_POST['id'];
    $caption  = trim($_POST['caption'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if (!empty($_FILES['image']['tmp_name'])) {

        $mime = mime_content_type($_FILES['image']['tmp_name']);

        if (!isset($allowedMime[$mime])) {
            header('Location: ../gallery.php?error=type');
            exit;
        }

        $newFile = generate_filename($allowedMime[$mime]);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFile)) {

            $stmt = $pdo->prepare('SELECT filename FROM gallery WHERE id = ?');
            $stmt->execute([$id]);
            $oldFile = $stmt->fetchColumn();

            if ($oldFile && file_exists($uploadDir . $oldFile)) {
                unlink($uploadDir . $oldFile);
            }

            $stmt = $pdo->prepare(
                'UPDATE gallery SET filename = ?, caption = ?, category = ? WHERE id = ?'
            );
            $stmt->execute([$newFile, $caption, $category, $id]);
        }

    } else {
        $stmt = $pdo->prepare(
            'UPDATE gallery SET caption = ?, category = ? WHERE id = ?'
        );
        $stmt->execute([$caption, $category, $id]);
    }

    header('Location: ../gallery.php?success=updated');
    exit;
}

/* ===================== DELETE ===================== */

if (isset($_POST['delete'], $_POST['id'])) {

    $id = (int) $_POST['id'];

    $stmt = $pdo->prepare('SELECT filename FROM gallery WHERE id = ?');
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();

    if ($file && file_exists($uploadDir . $file)) {
        unlink($uploadDir . $file);
    }

    $stmt = $pdo->prepare('DELETE FROM gallery WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: ../gallery.php?success=deleted');
    exit;
}

/* ===================== FALLBACK ===================== */

header('Location: ../gallery.php');
exit;
