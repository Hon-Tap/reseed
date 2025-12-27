<?php
declare(strict_types=1);

$rootPath = dirname(__DIR__, 2); 

require_once $rootPath . '/includes/config.php';
require_once $rootPath . '/admin/includes/csrf.php';

/* ===================== UPLOAD CONFIG ===================== */
if (!defined('UPLOAD_ROOT')) {
    define('UPLOAD_ROOT', $rootPath . '/uploads');
}

$uploadDir = UPLOAD_ROOT . '/posts/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// ... Keep your MIME types and helper functions ...

/* ===================== ADD POST ===================== */
if (isset($_POST['add'])) {
    // ... your logic for trimming variables ...
    
    // Ensure you use $uploadDir in your move_uploaded_file call
    if (!empty($_FILES['media_file']['tmp_name'])) {
        // ... mime checking ...
        if (move_uploaded_file($_FILES['media_file']['tmp_name'], $uploadDir . $coverImage)) {
             // success
        }
    }
    // ... your SQL execution ...
    header('Location: ../posts.php?success=added');
    exit;
}