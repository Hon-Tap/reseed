<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$backendPath = dirname(__DIR__, 2) . '/backend/admin';
require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/includes/csrf.php';

$token = $_GET['token'] ?? '';
$isValid = false;

if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW() LIMIT 1");
    $stmt->execute([$token]);
    if ($stmt->fetch()) { $isValid = true; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password | ReSEED</title>
    </head>
<body>
<div class="login-container">
    <div class="login-card">
        <?php if ($isValid): ?>
            <h2>New Password</h2>
            <form method="POST" action="/backend/admin/handlers/reset-handler.php">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <div class="form-group">
                    <label>Enter New Password</label>
                    <input type="password" name="new_password" required minlength="8">
                </div>
                <button type="submit" class="btn-login">Update Password</button>
            </form>
        <?php else: ?>
            <div class="error-banner">Invalid or expired reset link.</div>
            <a href="forgot-password.php" class="back-link">Request a new link</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>