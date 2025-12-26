<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * FIXED PATHS:
 * We use the ROOT_PATH constant defined in admin.php 
 * or calculate it relative to this file.
 */
if (!defined('ROOT_PATH')) {
    // This file is in /backend/admin/, so we go up 2 levels to reach the root
    define('ROOT_PATH', dirname(__DIR__, 2));
}

// Config is in /backend/includes/
require_once ROOT_PATH . '/backend/includes/config.php';
// CSRF is usually also in /backend/includes/ or /backend/admin/includes/
// Check your file tree and adjust the path below if needed:
require_once ROOT_PATH . '/backend/includes/csrf.php'; 

/* Redirect if already logged in */
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

/* Error handling */
$errorCode = $_GET['error'] ?? null;
$errorMessages = [
    'empty'    => 'Please fill in all required fields.',
    'invalid'  => 'Invalid username or password.',
    'csrf'     => 'Security check failed. Please try again.',
    'locked'   => 'Too many failed attempts. Please wait and try again.',
    'sent'     => 'If that account exists, a reset link has been sent.',
];

$errorText = $errorMessages[$errorCode] ?? null;
$successText = ($errorCode === 'sent') ? $errorMessages['sent'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Secure Login | ReSEED Admin</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root{
            --primary:#099227;
            --primary-hover:#077a20;
            --bg-body:#f1f5f9;
            --text-main:#1e293b;
            --text-muted:#64748b;
            --white:#ffffff;
            --shadow:0 20px 25px -5px rgba(0,0,0,.1),0 10px 10px -5px rgba(0,0,0,.04);
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{
            font-family:'Inter',sans-serif;
            background:linear-gradient(135deg,#f1f5f9 0%,#e2e8f0 100%);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
            color:var(--text-main);
        }
        .login-container{width:100%;max-width:420px}
        .login-card{
            background:var(--white);
            padding:40px;
            border-radius:24px;
            box-shadow:var(--shadow);
            position:relative;
        }
        .login-card::before{
            content:'';
            position:absolute;
            top:0;left:0;right:0;
            height:5px;
            background:var(--primary);
        }
        .logo-area{text-align:center;margin-bottom:32px}
        .logo-area img{
            width:80px;height:80px;
            border-radius:50%;
            margin-bottom:16px;
            object-fit: cover;
        }
        .logo-area h2{
            font-family:'Plus Jakarta Sans',sans-serif;
            font-weight:800;
            font-size:1.75rem;
        }
        .logo-area p{color:var(--text-muted);font-size:.9rem}
        .form-group{margin-bottom:20px}
        .form-label-flex{
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .form-group label{font-size:.85rem;font-weight:600;}
        .forgot-link{
            font-size: 0.8rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover{text-decoration: underline;}
        .input-wrapper{position:relative}
        .input-wrapper i{
            position:absolute;
            left:16px;
            top:50%;
            transform:translateY(-50%);
            color:var(--text-muted);
        }
        .input-wrapper input{
            width:100%;
            padding:14px 16px 14px 48px;
            border:1.5px solid #e2e8f0;
            border-radius:12px;
            background:#f8fafc;
            font-size:1rem;
            transition: border-color 0.2s;
        }
        .input-wrapper input:focus{
            outline: none;
            border-color: var(--primary);
        }
        .btn-login{
            width:100%;
            padding:14px;
            border:none;
            border-radius:12px;
            background:var(--primary);
            color:#fff;
            font-size:1rem;
            font-weight:700;
            cursor:pointer;
            transition: background 0.2s;
        }
        .btn-login:hover{background: var(--primary-hover);}
        .banner{
            padding:12px;
            border-radius:8px;
            font-size:.85rem;
            margin-bottom:20px;
            display:flex;
            gap:10px;
            align-items: center;
        }
        .error-banner{ background:#fef2f2; color:#dc2626; }
        .success-banner{ background:#f0fdf4; color:#15803d; }
        .login-footer{
            margin-top:24px;
            text-align:center;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
        .back-link{
            text-decoration:none;
            color:var(--text-muted);
            font-size:.9rem;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">

        <div class="logo-area">
            <img src="/assets/images/Re-logo.jpeg" alt="ReSEED Logo">
            <h2>ReSEED Admin</h2>
            <p>Secure access portal</p>
        </div>

        <?php if ($errorText && !$successText): ?>
            <div class="banner error-banner">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($errorText, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($successText): ?>
            <div class="banner success-banner">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars($successText, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/backend/admin/handlers/login-handler.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="form-group">
                <label>Username</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" placeholder="Enter username" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <div class="form-label-flex">
                    <label>Password</label>
                    <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
                </div>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="login-footer">
            <a href="/" class="back-link">
                <i class="fa-solid fa-house"></i> Back to Home
            </a>
        </div>
    </div>
</div>

</body>
</html>