<?php
declare(strict_types=1);

$backendFile = dirname(__DIR__, 3) . '/backend/admin/handlers/project-handler.php';

if (file_exists($backendFile)) {
    require_once $backendFile;
} else {
    die("Error: Backend Project Handler not found at: " . $backendFile);
}