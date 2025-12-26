<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin Login Handler
|--------------------------------------------------------------------------
*/

// __DIR__ is .../backend/admin/handlers
// dirname(__DIR__) goes up one level to .../backend/admin
$basePath = dirname(__DIR__); 

require_once $basePath . '/includes/config.php';
require_once $basePath . '/includes/csrf.php';


/* ===================== SESSION ===================== */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* ===================== METHOD ENFORCEMENT ===================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

/* ===================== REQUEST TYPE ===================== */

$isAjax =
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

/* ===================== CSRF ===================== */

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Security check failed']);
        exit;
    }
    header('Location: /admin.php?error=csrf');
    exit;
}

/* ===================== INPUT ===================== */

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

/* ===================== BRUTE FORCE THROTTLE ===================== */

$ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$key = 'login_throttle_' . hash('sha256', $ip . '|' . $username);

$_SESSION[$key] ??= ['count' => 0, 'last' => time()];

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

/* ===================== AUTH ===================== */

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

        unset($_SESSION[$key]);
        session_regenerate_id(true);

        $_SESSION['admin_id']   = (int) $user['id'];
        $_SESSION['admin_name'] = (string) $user['username'];
        $_SESSION['admin_role'] = (string) ($user['role'] ?? 'admin');

        if ($isAjax) {
            echo json_encode([
                'success'  => true,
                'redirect' => '/dashboard.php'
            ]);
            exit;
        }

        header('Location: /dashboard.php');
        exit;
    }

} catch (PDOException $e) {
    error_log('Login DB Error: ' . $e->getMessage());
    http_response_code(500);

    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Server error']);
        exit;
    }

    die('Database error');
}

/* ===================== FAILED LOGIN ===================== */

$_SESSION[$key]['count']++;
$_SESSION[$key]['last'] = time();

if ($isAjax) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    exit;
}

header('Location: /admin.php?error=invalid');
exit;
