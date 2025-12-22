<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| APPLICATION ENVIRONMENT
|--------------------------------------------------------------------------
| Change only if you know why.
*/
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');

/*
|--------------------------------------------------------------------------
| BASE URL (CRITICAL)
|--------------------------------------------------------------------------
| This MUST match how the app is deployed.
| Render deployment lives under /reseed
*/
if (!defined('BASE_URL')) {
    if (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'onrender.com')) {
        define('BASE_URL', '/reseed');
    } else {
        // Local development (XAMPP)
        define('BASE_URL', '/reseed'); // keep consistent
    }
}

/*
|--------------------------------------------------------------------------
| ROOT PATH (FILE SYSTEM)
|--------------------------------------------------------------------------
*/
define('ROOT_PATH', dirname(__DIR__, 2));

/*
|--------------------------------------------------------------------------
| ERROR HANDLING
|--------------------------------------------------------------------------
*/
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT_PATH . '/logs/php-error.log');
}

/*
|--------------------------------------------------------------------------
| DATABASE CONFIG
|--------------------------------------------------------------------------
| Uses environment variables in production
*/
$dbHost = $_ENV['DB_HOST'] ?? null;
$dbName = $_ENV['DB_NAME'] ?? null;
$dbUser = $_ENV['DB_USER'] ?? null;
$dbPass = $_ENV['DB_PASS'] ?? null;
$dbPort = $_ENV['DB_PORT'] ?? '5432';

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
| HARD FAIL IF DB CONFIG IS MISSING
|--------------------------------------------------------------------------
*/
if (!$dbHost || !$dbName || !$dbUser) {
    http_response_code(500);
    die('Application misconfigured: database credentials missing.');
}

/*
|--------------------------------------------------------------------------
| PDO INITIALIZATION (PostgreSQL)
|--------------------------------------------------------------------------
*/
try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        $dbHost,
        $dbPort,
        $dbName
    );

    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);

    if (APP_ENV === 'development') {
        die('Database connection failed: ' . $e->getMessage());
    }

    error_log($e->getMessage());
    die('Database connection failed.');
}
