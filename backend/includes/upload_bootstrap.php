<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(500);
    die('ROOT_PATH not defined');
}

define('UPLOAD_ROOT', ROOT_PATH . '/backend/uploads');
define('UPLOAD_URL', '/backend/uploads');

$dirs = [
    UPLOAD_ROOT,
    UPLOAD_ROOT . '/gallery',
    UPLOAD_ROOT . '/posts',
    UPLOAD_ROOT . '/projects',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
            http_response_code(500);
            die('Failed to create upload directory: ' . $dir);
        }
    }
}
