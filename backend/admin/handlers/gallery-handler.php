<?php
declare(strict_types=1);

// Go up 3 levels to reach the root 'reseed' folder
$rootPath = dirname(__DIR__, 2); 

require_once $rootPath . '/includes/config.php';
require_once $rootPath . '/admin/includes/csrf.php';

/* ===================== UPLOAD CONFIG ===================== */
// Ensure UPLOAD_ROOT is defined
if (!defined('UPLOAD_ROOT')) {
    define('UPLOAD_ROOT', $rootPath . '/uploads');
}

$uploadDir = UPLOAD_ROOT . '/gallery/';

// Auto-create directory if missing
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

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
        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;

        $mime = mime_content_type($tmp);
        if (!isset($allowedMime[$mime])) continue;

        $filename = generate_filename($allowedMime[$mime]);

        if (move_uploaded_file($tmp, $uploadDir . $filename)) {
            $finalCaption = $caption ?: pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME);
            $stmt = $pdo->prepare('INSERT INTO gallery (filename, caption, category) VALUES (?, ?, ?)');
            $stmt->execute([$filename, $finalCaption, $category]);
        }
    }
    header('Location: ../gallery.php?success=uploaded');
    exit;
}

// ... Rest of the UPDATE and DELETE logic remains the same ...