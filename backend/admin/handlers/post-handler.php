<?php
declare(strict_types=1);

/**
 * PATH RESOLUTION
 * Current File: /backend/admin/handlers/post-handler.php
 * Root Directory: / (where includes/ and uploads/ live)
 */
$rootPath = dirname(__DIR__, 2); 

require_once $rootPath . '/includes/config.php';

// Verify CSRF
session_start();
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed.");
}

/* ===================== UPLOAD CONFIG ===================== */
// Define the absolute path to the uploads folder
$uploadDir = $rootPath . '/uploads/posts/';

// Ensure directory exists with correct permissions
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

/* ===================== ADD POST ===================== */
if (isset($_POST['add'])) {
    $title       = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $content     = trim($_POST['content']);
    $status      = $_POST['status'] ?? 'draft';
    $featured    = isset($_POST['featured']) ? 1 : 0;
    
    // Generate Slug if empty
    $slug = !empty($_POST['slug']) ? $_POST['slug'] : strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    $coverImage = null;

    // Handle File Upload
    if (!empty($_FILES['media_file']['tmp_name'])) {
        $fileTmpPath = $_FILES['media_file']['tmp_name'];
        $fileName    = $_FILES['media_file']['name'];
        $fileExt     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Create a unique filename to prevent overwriting
        $newFileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExt;
        
        // FIX: Combine Directory + Filename for the destination
        $destPath = $uploadDir . $newFileName;

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($fileExt, $allowedExts)) {
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $coverImage = $newFileName;
            }
        }
    }

    try {
        $sql = "INSERT INTO posts (title, slug, category_id, content, cover_image, status, featured, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $slug, $category_id, $content, $coverImage, $status, $featured]);

        // FIX: Correct Redirect Path
        // From /backend/admin/handlers/ to /admin/posts.php
        header('Location: ../../../admin/posts.php?success=added');
        exit;

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}

/* ===================== EDIT POST ===================== */
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    // ... same logic as above but with UPDATE SQL ...
    // ... use $uploadDir . $newFileName for move_uploaded_file ...
    
    header('Location: ../../../admin/posts.php?success=updated');
    exit;
}