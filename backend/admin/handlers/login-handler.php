<?php
declare(strict_types=1);

/* =====================================================
    ERROR REPORTING (Debug Mode)
===================================================== */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/* =====================================================
    SESSION BOOTSTRAP
===================================================== */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* =====================================================
    DEPENDENCIES
===================================================== */
/**
 * PATH ANALYSIS:
 * Current: /var/www/html/backend/admin/handlers/login-handler.php
 * Target 1 (Config): /var/www/html/backend/includes/config.php 
 * Target 2 (CSRF):   /var/www/html/backend/admin/includes/csrf.php
 */

// Move up 2 levels: handlers -> admin -> backend. Then into includes/
require_once dirname(__DIR__, 2) . '/includes/config.php';

// Move up 1 level: handlers -> admin. Then into includes/
require_once dirname(__DIR__) . '/includes/csrf.php';

/* =====================================================
    REQUEST VALIDATION
===================================================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

/* =====================================================
    CSRF VERIFICATION
===================================================== */
if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Security check failed']);
        exit;
    }
    header('Location: /admin.php?error=csrf');
    exit;
}

/* =====================================================
    INPUT HANDLING
===================================================== */
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Missing credentials']);
        exit;
    }
    header('Location: /admin.php?error=empty');
    exit;
}

/* =====================================================
    BRUTE-FORCE THROTTLING
===================================================== */
$ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$key = 'login_throttle_' . md5($ip . '|' . $username);

$_SESSION[$key] ??= ['count' => 0, 'last' => time()];

if (time() - $_SESSION[$key]['last'] > 600) {
    $_SESSION[$key] = ['count' => 0, 'last' => time()];
}

if ($_SESSION[$key]['count'] >= 5) {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Too many attempts.']);
        exit;
    }
    header('Location: /admin.php?error=locked');
    exit;
}

/* =====================================================
    DATABASE LOOKUP
===================================================== */
try {
    // Ensure $pdo is defined in your backend/includes/config.php
    $stmt = $pdo->prepare(
        'SELECT id, username, password_hash, role 
         FROM users 
         WHERE username = :username 
         LIMIT 1'
    );
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        unset($_SESSION[$key]);
        session_regenerate_id(true);

        $_SESSION['admin_id']   = (int) $user['id'];
        $_SESSION['admin_name'] = (string) $user['username'];
        $_SESSION['admin_role'] = (string) ($user['role'] ?? 'admin');

        if ($isAjax) {
            echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
            exit;
        }

        header('Location: ../dashboard.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Login DB Error: " . $e->getMessage());
    http_response_code(500);
    die("Database connection error. Ensure Render environment variables are set.");
}

/* =====================================================
    FAILED LOGIN
==================================================== */
$_SESSION[$key]['count']++;
$_SESSION[$key]['last'] = time();

if ($isAjax) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    exit;
}

header('Location: /admin.php?error=invalid');
exit;