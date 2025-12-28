<?php
declare(strict_types=1);

// This ensures we get the absolute path to the root first
$basePath = realpath(__DIR__ . '/../../'); 
$backendFile = $basePath . '/backend/admin/handlers/post-handler.php';

if ($backendFile && file_exists($backendFile)) {
    require_once $backendFile;
} else {
    // This will show you the EXACT path being searched so you can see the error
    die("Error: Backend handler not found. Looking for: " . ($backendFile ?: 'nothing'));
}