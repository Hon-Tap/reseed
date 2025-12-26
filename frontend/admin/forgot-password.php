<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$backendPath = dirname(__DIR__, 2) . '/backend/admin';
require_once $backendPath . '/includes/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | ReSEED Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reuse the CSS from your login.php here */
        <?php include 'login-styles.css'; // Optional: move styles to a CSS file or paste here ?>
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="logo-area">
            <h2>Reset Password</h2>
            <p>Enter your email to receive a reset link</p>
        </div>

        <form method="POST" action="/backend/admin/handlers/forgot-handler.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" placeholder="admin@example.com" required autofocus>
                </div>
            </div>
            <button type="submit" class="btn-login">Send Reset Link</button>
        </form>

        <div class="login-footer">
            <a href="login.php" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>
</div>
</body>
</html>