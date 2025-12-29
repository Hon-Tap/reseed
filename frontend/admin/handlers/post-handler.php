<?php
declare(strict_types=1);

$backendFile = dirname(__DIR__, 3) . '/backend/admin/handlers/post-handler.php';

if (file_exists($backendFile)) {
    require_once $backendFile;
} else {
    die("Error: Backend Post Handler not found at: " . $backendFile);
}