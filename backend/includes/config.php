<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| DATABASE CONFIG
|--------------------------------------------------------------------------
*/

$dbHost = $_ENV['DB_HOST'] ?? null;
$dbName = $_ENV['DB_NAME'] ?? null;
$dbUser = $_ENV['DB_USER'] ?? null;
$dbPass = $_ENV['DB_PASS'] ?? null;

/*
|--------------------------------------------------------------------------
| FALLBACK FOR LOCAL DEVELOPMENT ONLY
|--------------------------------------------------------------------------
*/
if (!$dbHost && file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

/*
|--------------------------------------------------------------------------
| HARD FAIL IF STILL NOT SET
|--------------------------------------------------------------------------
*/
if (!$dbHost || !$dbName || !$dbUser) {
    die('Application misconfigured: database credentials missing.');
}

/*
|--------------------------------------------------------------------------
| PDO INIT
|--------------------------------------------------------------------------
*/
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

$pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
