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
    DEPENDENCIES (Path Correction based on Logs)
===================================================== */
/**
 * Your Logs indicated:
 * 1. config.php is in: /var/www/html/includes/
 * 2. csrf.php is in:   /var/www/html/backend/admin/includes/
 */

// Moves up 3 levels: backend/admin/handlers -> root/includes/config.php
require_once dirname(__DIR__, 3) . '/includes/config.php';

// Moves up 1 level: backend/admin/handlers -> backend/admin/includes/csrf.php
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
    header('Location: /admin.php?error=csrf'); // Adjusted to your admin.php filename
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
    BRUTE-FORCE THROTTLING (IP + Username)
===================================================== */
$ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$key = 'login_throttle_' . md5($ip . '|' . $username);

$_SESSION[$key] ??= ['count' => 0, 'last' => time()];

// Reset after 10 minutes
if (time() - $_SESSION[$key]['last'] > 600) {
    $_SESSION[$key] = ['count' => 0, 'last' => time()];
}

if ($_SESSION[$key]['count'] >= 5) {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Too many attempts. Try again later.']);
        exit;
    }
    header('Location: /admin.php?error=locked');
    exit;
}

/* =====================================================
    DATABASE LOOKUP & AUTHENTICATION
===================================================== */
try {
    $stmt = $pdo->prepare(
        'SELECT id, username, password_hash, role 
         FROM users 
         WHERE username = :username 
         LIMIT 1'
    );
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Success: Clear throttle and refresh session
        unset($_SESSION[$key]);
        session_regenerate_id(true);

        $_SESSION['admin_id']   = (int) $user['id'];
        $_SESSION['admin_name'] = (string) $user['username'];
        $_SESSION['admin_role'] = (string) ($user['role'] ?? 'admin');

        if ($isAjax) {
            echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
            exit;
        }

        // Relative path to backend/admin/dashboard.php
        header('Location: ../dashboard.php');
        exit;
    }
} catch (PDOException $e) {
    // Log error and show 500
    error_log("Login DB Error: " . $e->getMessage());
    http_response_code(500);
    die("A database error occurred. Please check logs.");
}

/* =====================================================
    FAILED LOGIN
===================================================== */
$_SESSION[$key]['count']++;
$_SESSION[$key]['last'] = time();

if ($isAjax) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    exit;
}

header('Location: /admin.php?error=invalid');
exit;