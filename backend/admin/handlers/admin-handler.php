<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin Profile Handler
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/admin_auth.php';

/* ===================== METHOD ENFORCEMENT ===================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

/* ===================== AUTH ===================== */

$adminId = $_SESSION['admin_id'] ?? null;

if (!$adminId) {
    header('Location: /admin.php');
    exit;
}

/* ===================== UPDATE USERNAME ===================== */

if (isset($_POST['update_username'])) {

    $username = trim($_POST['username'] ?? '');

    if ($username === '') {
        $_SESSION['admin_error'] = 'Username cannot be empty.';
        header('Location: /admin/admin_profile.php');
        exit;
    }

    $stmt = $pdo->prepare(
        'UPDATE users SET username = ? WHERE id = ?'
    );
    $stmt->execute([$username, $adminId]);

    $_SESSION['admin_name'] = $username;
    $_SESSION['admin_success'] = 'Username updated successfully.';

    header('Location: /admin/admin_profile.php');
    exit;
}

/* ===================== UPDATE PASSWORD ===================== */

if (isset($_POST['update_password'])) {

    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($new === '' || $confirm === '') {
        $_SESSION['admin_error'] = 'Password fields cannot be empty.';
        header('Location: /admin/admin_profile.php');
        exit;
    }

    if ($new !== $confirm) {
        $_SESSION['admin_error'] = 'Passwords do not match.';
        header('Location: /admin/admin_profile.php');
        exit;
    }

    $stmt = $pdo->prepare(
        'SELECT password_hash FROM users WHERE id = ?'
    );
    $stmt->execute([$adminId]);
    $hash = $stmt->fetchColumn();

    if (!$hash || !password_verify($current, $hash)) {
        $_SESSION['admin_error'] = 'Current password is incorrect.';
        header('Location: /admin/admin_profile.php');
        exit;
    }

    $newHash = password_hash($new, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        'UPDATE users SET password_hash = ? WHERE id = ?'
    );
    $stmt->execute([$newHash, $adminId]);

    $_SESSION['admin_success'] = 'Password updated successfully.';

    header('Location: /admin/admin_profile.php');
    exit;
}

/* ===================== FALLBACK ===================== */

header('Location: /admin/admin_profile.php');
exit;
