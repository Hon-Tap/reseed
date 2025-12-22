<?php
declare(strict_types=1);

/* =====================================================
   SESSION BOOTSTRAP
===================================================== */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* =====================================================
   DEPENDENCIES
===================================================== */

require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__, 2) . '/includes/csrf.php';


/* =====================================================
   REQUEST + MODE DETECTION
===================================================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

/* =====================================================
   CSRF CHECK
===================================================== */
if (!csrf_verify($_POST['csrf_token'] ?? null)) {

    if ($isAjax) {
        echo json_encode([
            'success' => false,
            'message' => 'Security check failed'
        ]);
        exit;
    }

    header('Location: ../login.php?error=csrf');
    exit;
}

/* =====================================================
   INPUT SANITIZATION
===================================================== */
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {

    if ($isAjax) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing credentials'
        ]);
        exit;
    }

    header('Location: ../login.php?error=empty');
    exit;
}

/* =====================================================
   BRUTE-FORCE THROTTLING
===================================================== */
$ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$key = 'login_throttle_' . md5($ip . '|' . $username);

$_SESSION[$key] ??= [
    'count' => 0,
    'last'  => time()
];

// Reset window after 10 minutes
if (time() - $_SESSION[$key]['last'] > 600) {
    $_SESSION[$key] = ['count' => 0, 'last' => time()];
}

// Block after 5 attempts
if ($_SESSION[$key]['count'] >= 5) {

    if ($isAjax) {
        echo json_encode([
            'success' => false,
            'message' => 'Too many failed attempts. Try again later.'
        ]);
        exit;
    }

    header('Location: ../login.php?error=locked');
    exit;
}

/* =====================================================
   USER LOOKUP
===================================================== */
$stmt = $pdo->prepare(
    'SELECT id, username, password_hash, role
     FROM users
     WHERE username = :username
     LIMIT 1'
);
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

/* =====================================================
   AUTHENTICATION
===================================================== */
if ($user && password_verify($password, $user['password_hash'])) {

    // Success → clear throttle + harden session
    unset($_SESSION[$key]);
    session_regenerate_id(true);

    $_SESSION['admin_id']   = (int) $user['id'];
    $_SESSION['admin_name'] = (string) $user['username'];
    $_SESSION['admin_role'] = (string) ($user['role'] ?? 'admin');

    if ($isAjax) {
        echo json_encode([
            'success'  => true,
            'redirect' => 'dashboard.php'
        ]);
        exit;
    }

    header('Location: ../dashboard.php');
    exit;
}

/* =====================================================
   FAILED LOGIN
===================================================== */
$_SESSION[$key]['count']++;
$_SESSION[$key]['last'] = time();

if ($isAjax) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid username or password'
    ]);
    exit;
}

header('Location: ../login.php?error=invalid');
exit;
