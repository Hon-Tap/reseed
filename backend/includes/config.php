<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| APPLICATION ENVIRONMENT
|--------------------------------------------------------------------------
*/
// getenv() is more reliable on hosted platforms like Render
define('APP_ENV', getenv('APP_ENV') ?: 'production');

/*
|--------------------------------------------------------------------------
| BASE URL & PATHS
|--------------------------------------------------------------------------
*/
define('BASE_URL', '');
define('ROOT_PATH', dirname(__DIR__, 2));

/*
|--------------------------------------------------------------------------
| ERROR HANDLING
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/upload_bootstrap.php';

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    
    // Ensure logs directory exists or fallback to system log to avoid 500 error
    $logPath = ROOT_PATH . '/logs/php-error.log';
    if (is_dir(dirname($logPath)) && is_writable(dirname($logPath))) {
        ini_set('error_log', $logPath);
    }
}

/*
|--------------------------------------------------------------------------
| DATABASE CONFIGURATION
|--------------------------------------------------------------------------
*/
$dbHost = getenv('DB_HOST') ?: null;
$dbName = getenv('DB_NAME') ?: null;
$dbUser = getenv('DB_USER') ?: null;
$dbPass = getenv('DB_PASS') ?: null;
$dbPort = getenv('DB_PORT') ?: '5432';

/*
|--------------------------------------------------------------------------
| LOCAL DEVELOPMENT FALLBACK
|--------------------------------------------------------------------------
*/
if (
    APP_ENV === 'development'
    && (!$dbHost || !$dbName || !$dbUser)
    && file_exists(__DIR__ . '/config.local.php')
) {
    require __DIR__ . '/config.local.php';
}

/*
|--------------------------------------------------------------------------
| PDO INITIALIZATION (PostgreSQL)
|--------------------------------------------------------------------------
*/
if (!$dbHost || !$dbName || !$dbUser) {
    error_log("Missing DB configuration. Host: $dbHost, Name: $dbName, User: $dbUser");
    http_response_code(500);
    die('Application misconfigured: database credentials missing.');
}

try {
    /**
     * Render's managed PostgreSQL requires sslmode=require in production.
     * We append it here to ensure a secure connection.
     */
    $sslMode = (APP_ENV === 'production') ? '?sslmode=require' : '';
    
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s%s',
        $dbHost,
        $dbPort,
        $dbName,
        $sslMode
    );

    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    
} catch (PDOException $e) {
    // Log the actual error to Render's log tab
    error_log("PDO Connection Error: " . $e->getMessage());

    http_response_code(500);
    if (APP_ENV === 'development') {
        die('Database connection failed: ' . $e->getMessage());
    }
    die('Database connection failed. Please check server logs.');
}