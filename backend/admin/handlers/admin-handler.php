<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 2);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/csrf.php';

/* ===================== METHOD ===================== */

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

/* ===================== CSRF ===================== */

csrf_verify($_POST['csrf_token'] ?? null);

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

    $_SESSION['admin_name']    = $username;
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

    // Password strength enforcement
    if (
        strlen($new) < 8 ||
        !preg_match('/[A-Z]/', $new) ||
        !preg_match('/[a-z]/', $new) ||
        !preg_match('/[0-9]/', $new) ||
        !preg_match('/[\W]/', $new)
    ) {
        $_SESSION['admin_error'] =
            'Password must include upper, lower, number, symbol and be 8+ characters.';
        header('Location: /admin/admin_profile.php');
        exit;
    }

    // Verify current password
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

    // Update password
    $newHash = password_hash($new, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        'UPDATE users 
         SET password_hash = ?, password_updated_at = NOW() 
         WHERE id = ?'
    );
    $stmt->execute([$newHash, $adminId]);

    $_SESSION['admin_success'] = 'Password updated successfully.';
    header('Location: /admin/admin_profile.php');
    exit;
}

/* ===================== ENABLE 2FA ===================== */

if (isset($_POST['enable_2fa'])) {

    $pdo->prepare(
        'UPDATE users SET two_factor_enabled = TRUE WHERE id = ?'
    )->execute([$adminId]);

    $_SESSION['admin_success'] = 'Two-factor authentication enabled.';
    header('Location: /admin/admin_profile.php');
    exit;
}

/* ===================== DISABLE 2FA ===================== */

if (isset($_POST['disable_2fa'])) {

    $pdo->prepare(
        'UPDATE users SET two_factor_enabled = FALSE WHERE id = ?'
    )->execute([$adminId]);

    $_SESSION['admin_success'] = 'Two-factor authentication disabled.';
    header('Location: /admin/admin_profile.php');
    exit;
}

/* ===================== FALLBACK ===================== */

header('Location: /admin/admin_profile.php');
exit;
