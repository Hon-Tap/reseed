<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../includes/config.php";

/* ----------------------------------------
   INPUT SANITIZATION
---------------------------------------- */
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: ../login.php?error=empty");
    exit;
}

/* ----------------------------------------
   FETCH USER (MATCHES TABLE EXACTLY)
---------------------------------------- */
$stmt = $pdo->prepare("
    SELECT id, username, password_hash, role
    FROM users
    WHERE username = ?
    LIMIT 1
");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

/* ----------------------------------------
   VERIFY PASSWORD
---------------------------------------- */
if ($user && password_verify($password, $user['password_hash'])) {

    // ✅ CONSISTENT SESSION CONTRACT
    $_SESSION['admin_id']   = (int) $user['id'];
    $_SESSION['admin_name'] = $user['username']; // display name
    $_SESSION['admin_role'] = $user['role'];     // future-proof

    header("Location: ../dashboard.php");
    exit;
}

/* ----------------------------------------
   LOGIN FAILED
---------------------------------------- */
header("Location: ../login.php?error=invalid");
exit;
