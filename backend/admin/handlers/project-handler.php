<?php
// handlers/projects-handler.php

// Ensure configuration is loaded and PDO object ($pdo) is available
include "../../includes/config.php";

// Set the upload directory and ensure it exists
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/reseed/uploads/projects/';
if (!is_dir($uploadDir)) {
    // Attempt to create the directory recursively
    if (!mkdir($uploadDir, 0777, true)) {
        die("Failed to create upload directory.");
    }
}

// Function to safely delete a file
function delete_file_if_exists($fileName, $directory) {
    if ($fileName && file_exists($directory . $fileName)) {
        return unlink($directory . $fileName);
    }
    return true;
}

// Function to handle file upload and return the new file name
function handle_file_upload($fileArray, $uploadDir, $mediaType) {
    if (isset($fileArray) && $fileArray['error'] === 0) {
        $file = $fileArray;
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        $allowed = ($mediaType == 'image')
            ? ['jpg', 'jpeg', 'png', 'webp', 'gif'] // Added gif for completeness
            : ['mp4', 'webm', 'ogg', 'mov']; // Added mov
            
        if (!in_array($ext, $allowed)) {
            // Throw an exception or redirect with an error message
            throw new Exception("Invalid file type for $mediaType. Allowed extensions: " . implode(', ', $allowed));
        }
        
        $newFileName = time() . '_' . rand(1000, 9999) . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
            return $newFileName;
        } else {
            throw new Exception("Failed to move uploaded file.");
        }
    }
    return null;
}

// =================================================================
// 1. ADD PROJECT
// =================================================================
if (isset($_POST['add'])) {
    try {
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']) ?: strtolower(preg_replace('/[^a-z0-9]+/','-', $title));
        $summary = trim($_POST['summary']);
        $description = trim($_POST['description']);
        $location = trim($_POST['location']);
        $start_date = $_POST['start_date'] ?: null;
        $end_date = $_POST['end_date'] ?: null;
        $status = $_POST['status'];
        $media_type = $_POST['media_type'];
        $media_url = $_POST['media_url'] ?: null;
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        $cover_image = handle_file_upload($_FILES['media_file'], $uploadDir, $media_type);

        $sql = "INSERT INTO projects (title, slug, summary, description, location, start_date, end_date, cover_image, media_type, media_url, status, featured, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $title, $slug, $summary, $description, $location, 
            $start_date, $end_date, $cover_image, $media_type, $media_url, $status, $featured
        ]);

        header("Location: ../projects.php?status=success&action=added");
        exit();

    } catch (Exception $e) {
        error_log("Project Add Error: " . $e->getMessage());
        die("Error adding project: " . $e->getMessage());
    }
}

// =================================================================
// 2. UPDATE PROJECT
// =================================================================
if (isset($_POST['update'])) {
    try {
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']) ?: strtolower(preg_replace('/[^a-z0-9]+/','-', $title));
        $summary = trim($_POST['summary']);
        $description = trim($_POST['description']);
        $location = trim($_POST['location']);
        $start_date = $_POST['start_date'] ?: null;
        $end_date = $_POST['end_date'] ?: null;
        $status = $_POST['status'];
        $media_type = $_POST['media_type'];
        $media_url = $_POST['media_url'] ?: null;
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        // Handle file upload
        $new_cover_image = handle_file_upload($_FILES['media_file'], $uploadDir, $media_type);
        
        $params = [
            $title, $slug, $summary, $description, $location, $start_date, $end_date,
            $media_type, $media_url, $status, $featured
        ];
        
        $sql = "UPDATE projects SET 
                title=?, slug=?, summary=?, description=?, location=?, start_date=?, end_date=?, 
                media_type=?, media_url=?, status=?, featured=?";

        if ($new_cover_image) {
            // Get the old file name to delete it
            $stmt_old = $pdo->prepare("SELECT cover_image FROM projects WHERE id=?");
            $stmt_old->execute([$id]);
            $old_file = $stmt_old->fetchColumn();
            
            delete_file_if_exists($old_file, $uploadDir);

            $sql .= ", cover_image=?";
            $params[] = $new_cover_image;
        }

        $sql .= " WHERE id=?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: ../projects.php?status=success&action=updated");
        exit();
        
    } catch (Exception $e) {
        error_log("Project Update Error: " . $e->getMessage());
        die("Error updating project: " . $e->getMessage());
    }
}


// =================================================================
// 3. DELETE PROJECT (Refactored to check for POST as well)
// =================================================================
// Checks for both the old GET method and the new, preferred POST method.
if (isset($_POST['delete']) || isset($_GET['delete'])) {
    try {
        // Determine ID source
        $id = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['delete']) ? $_GET['delete'] : null);

        if (!$id) {
            header("Location: ../projects.php?status=error&message=No ID provided for deletion.");
            exit();
        }

        // Fetch the file name associated with the project
        $stmt = $pdo->prepare("SELECT cover_image FROM projects WHERE id=?");
        $stmt->execute([$id]);
        $file_to_delete = $stmt->fetchColumn();
        
        // Delete the file from the server
        delete_file_if_exists($file_to_delete, $uploadDir);
        
        // Delete the record from the database
        $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
        
        header("Location: ../projects.php?status=success&action=deleted");
        exit();

    } catch (Exception $e) {
        error_log("Project Delete Error: " . $e->getMessage());
        // Simple error handling: redirect with an error status
        header("Location: ../projects.php?status=error&message=Delete failed due to an internal error.");
        exit();
    }
}

// Fallback if no valid action is provided
header("Location: ../projects.php");
exit();