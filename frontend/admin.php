<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require ROOT_PATH . '/backend/admin/handlers/login-handler.php';
    exit;
}

require ROOT_PATH . '/backend/admin/login.php';
