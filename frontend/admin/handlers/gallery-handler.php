<?php
declare(strict_types=1);

// We are now in /frontend/admin/handlers/, so we need to go up THREE levels
// to reach the root: ../../../
$backendFile = dirname(__DIR__, 3) . '/backend/admin/handlers/gallery-handler.php';

if (file_exists($backendFile)) {
    require_once $backendFile;
} else {
    die("Error: Backend Gallery Handler not found at: " . $backendFile);
}