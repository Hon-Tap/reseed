<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__, 1) . '/includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    header('Location: ../login.php?error=csrf');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: ../login.php?error=empty');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT id, username, password_hash, role
     FROM users
     WHERE username = :username
     LIMIT 1'
);
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);

    $_SESSION['admin_id']   = (int) $user['id'];
    $_SESSION['admin_name'] = $user['username'];
    $_SESSION['admin_role'] = $user['role'] ?? 'admin';

    header('Location: ../dashboard.php');
    exit;
}

header('Location: ../login.php?error=invalid');
exit;
