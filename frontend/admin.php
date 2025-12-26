<?php
declare(strict_types=1);

// 1. Define the absolute path to the config file
$configPath = __DIR__ . '/../backend/includes/config.php';

// 2. Safety check: If the file is missing, show a clear error instead of a 500 crash
if (!file_exists($configPath)) {
    header("HTTP/1.1 500 Internal Server Error");
    die("Critical Error: Configuration file not found at " . htmlspecialchars($configPath));
}

require_once $configPath;

// 3. Handle Login Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handlerPath = ROOT_PATH . '/backend/admin/handlers/login-handler.php';
    
    if (file_exists($handlerPath)) {
        require $handlerPath;
    } else {
        die("Error: Login handler missing.");
    }
    exit;
}

// 4. Load Login View
$viewPath = ROOT_PATH . '/backend/admin/login.php';
if (file_exists($viewPath)) {
    require $viewPath;
} else {
    die("Error: Login view file missing.");
}