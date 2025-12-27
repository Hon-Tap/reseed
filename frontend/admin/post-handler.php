<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Use absolute path for reliability
$backendFile = __DIR__ . '/../../backend/admin/handlers/post-handler.php';

if (file_exists($backendFile)) {
    require_once $backendFile;
} else {
    die("Error: Backend handler not found at: " . $backendFile);
}