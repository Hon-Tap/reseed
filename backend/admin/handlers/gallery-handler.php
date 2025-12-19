<?php
// admin/handlers/gallery-handler.php
require "../../includes/config.php";

// Define the relative upload path
$uploadDir = "../../uploads/gallery/";

// Create folder if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Allowed image extensions
$allowed = ['jpg', 'jpeg', 'png', 'webp'];

/* ==========================================
   BULK ADD IMAGES
   ========================================== */
if (isset($_POST['bulk_add'])) {
    $base_cat = trim($_POST['category']);
    $base_cap = trim($_POST['caption']);

    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        die("No files selected for upload.");
    }

    $files = $_FILES['images'];
    $count = count($files['name']);

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] === 0) {
            $originalName = $files['name'][$i];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                // Generate unique filename
                $newName = time() . "_" . rand(1000, 9999) . "." . $ext;
                $targetPath = $uploadDir . $newName;

                if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                    // Logic: Use provided caption, or fall back to the original filename
                    $finalCaption = !empty($base_cap) ? $base_cap : pathinfo($originalName, PATHINFO_FILENAME);
                    
                    $stmt = $pdo->prepare("INSERT INTO gallery (filename, caption, category) VALUES (?, ?, ?)");
                    $stmt->execute([$newName, $finalCaption, $base_cat]);
                }
            }
        }
    }
    header("Location: ../gallery.php?success=uploaded");
    exit();
}

/* ==========================================
   UPDATE EXISTING IMAGE
   ========================================== */
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $cap = trim($_POST['caption']);
    $cat = trim($_POST['category']);

    // Check if a new file is being uploaded
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Invalid file type.");
        }

        $newName = time() . "_" . rand(1000, 9999) . "." . $ext;
        $targetPath = $uploadDir . $newName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Remove the old file from server
            $stmt = $pdo->prepare("SELECT filename FROM gallery WHERE id = ?");
            $stmt->execute([$id]);
            $oldFile = $stmt->fetchColumn();
            
            if ($oldFile && file_exists($uploadDir . $oldFile)) {
                unlink($uploadDir . $oldFile);
            }

            // Update database with new filename
            $stmt = $pdo->prepare("UPDATE gallery SET filename = ?, caption = ?, category = ? WHERE id = ?");
            $stmt->execute([$newName, $cap, $cat, $id]);
        }
    } else {
        // Just update text metadata
        $stmt = $pdo->prepare("UPDATE gallery SET caption = ?, category = ? WHERE id = ?");
        $stmt->execute([$cap, $cat, $id]);
    }

    header("Location: ../gallery.php?success=updated");
    exit();
}

/* ==========================================
   DELETE IMAGE
   ========================================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // 1. Get filename to delete from server
    $stmt = $pdo->prepare("SELECT filename FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();

    if ($file && file_exists($uploadDir . $file)) {
        unlink($uploadDir . $file);
    }

    // 2. Delete from database
    $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: ../gallery.php?success=deleted");
    exit();
}