<?php
declare(strict_types=1);
// Check if this path actually reaches the backend file
$backendFile = __DIR__ . '/../../backend/admin/handlers/project-handler.php';

if (file_exists($backendFile)) {
    require_once $backendFile;
} else {
    die("Error: Backend handler not found at " . $backendFile);
}