<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/admin_auth.php';

if (!defined('ADMIN_BASE_URL')) {
    define('ADMIN_BASE_URL', '/admin');
}

function admin_url(string $path): string {
    return ADMIN_BASE_URL . '/' . ltrim($path, '/');
}

function isActive(string $file): string {
    return basename($_SERVER['PHP_SELF']) === $file ? 'active' : '';
}

try {
    $contactCount = (int) $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
} catch (Throwable) {
    $contactCount = 0;
}

$adminName     = $_SESSION['admin_name'] ?? 'Admin';
$adminInitials = strtoupper(substr($adminName, 0, 2));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ReSEED Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #166534;
            --primary-dark: #14532d;
            --accent: #22c55e;
            --bg-body: #f8fafc;
            --bg-surface: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --danger: #ef4444;
            --border: #e2e8f0;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Layout Structure */
        .wrapper { display: flex; min-height: 100vh; }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }

        .brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .brand img {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: white;
            padding: 4px;
            object-fit: contain;
        }

        .brand-text { margin-left: 14px; transition: var(--transition); }
        .brand-text h4 { margin: 0; font-size: 18px; letter-spacing: -0.5px; }
        .brand-text span { font-size: 12px; opacity: 0.7; }

        .nav-menu { flex: 1; padding: 24px 16px; overflow-y: auto; gap: 4px; display: flex; flex-direction: column; }
        .nav-header { font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,0.4); margin: 12px 12px 8px; font-weight: 700; letter-spacing: 0.05em; }

        .nav-link {
            display: flex; align-items: center;
            padding: 12px 16px;
            border-radius: 12px;
            color: rgba(255,255,255,0.7);
            font-size: 14px; font-weight: 500;
            transition: var(--transition);
            margin-bottom: 2px;
        }

        .nav-link i { width: 24px; font-size: 18px; margin-right: 12px; transition: var(--transition); }
        .nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .nav-link.active { background: #fff; color: var(--primary); box-shadow: var(--shadow-md); }

        .badge { margin-left: auto; background: var(--accent); color: var(--primary-dark); font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px; }

        /* Main Content area */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: var(--transition);
        }

        .topbar {
            height: var(--header-height);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky; top: 0; z-index: 1000;
        }

        .toggle-btn {
            background: #f1f5f9; border: none; cursor: pointer;
            width: 40px; height: 40px; border-radius: 10px;
            color: var(--text-main); display: flex; align-items: center; justify-content: center;
            transition: var(--transition);
        }
        .toggle-btn:hover { background: var(--border); }

        .user-profile { display: flex; align-items: center; gap: 12px; }
        .avatar {
            width: 40px; height: 40px; background: var(--primary); color: white;
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px;
        }

        /* Collapsed States (Desktop) */
        body.sidebar-collapsed .sidebar { width: var(--sidebar-collapsed-width); }
        body.sidebar-collapsed .main-content { margin-left: var(--sidebar-collapsed-width); }
        body.sidebar-collapsed .brand-text, 
        body.sidebar-collapsed .nav-link span, 
        body.sidebar-collapsed .nav-header,
        body.sidebar-collapsed .badge { display: none; }
        body.sidebar-collapsed .nav-link i { margin-right: 0; margin: auto; }

        /* Responsive Mobile Styles */
        .overlay {
            position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px); z-index: 1040; opacity: 0; visibility: hidden; transition: 0.3s;
        }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0 !important; }
            body.mobile-open .sidebar { transform: translateX(0); }
            body.mobile-open .overlay { opacity: 1; visibility: visible; }
        }
    </style>
</head>
<body>

<div class="overlay" id="overlay"></div>

<div class="wrapper">
    <nav class="sidebar" id="sidebar">
        <div class="brand">
            <img src="/assets/images/Re-logo.png" alt="Logo">
            <div class="brand-text">
                <h4>ReSEED</h4>
                <span>Admin Portal</span>
            </div>
        </div>

        <div class="nav-menu">
            <div class="nav-header">Overview</div>
            <a href="<?= admin_url('dashboard.php') ?>" class="nav-link <?= isActive('dashboard.php') ?>">
                <i class="fa-solid fa-house-chimney-window"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-header">Management</div>
            <a href="<?= admin_url('projects.php') ?>" class="nav-link <?= isActive('projects.php') ?>">
                <i class="fa-solid fa- layer-group"></i>
                <span>Projects</span>
            </a>
            <a href="<?= admin_url('posts.php') ?>" class="nav-link <?= isActive('posts.php') ?>">
                <i class="fa-solid fa-pen-nib"></i>
                <span>Posts</span>
            </a>
            <a href="<?= admin_url('contacts.php') ?>" class="nav-link <?= isActive('contacts.php') ?>">
                <i class="fa-solid fa-comment-dots"></i>
                <span>Messages</span>
                <?php if ($contactCount > 0): ?>
                    <span class="badge"><?= $contactCount ?></span>
                <?php endif; ?>
            </a>
            
            <div class="nav-header">Settings</div>
            <a href="<?= admin_url('admin_profile.php') ?>" class="nav-link <?= isActive('admin_profile.php') ?>">
                <i class="fa-solid fa-circle-user"></i>
                <span>Profile</span>
            </a>
        </div>

        <div style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.08);">
            <a href="<?= admin_url('logout.php') ?>" class="nav-link" style="color: #fda4af;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Sign Out</span>
            </a>
        </div>
    </nav>

    <main class="main-content">
        <header class="topbar">
            <button class="toggle-btn" id="sidebarToggle">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>

            <div class="user-profile">
                <span style="font-size: 14px; font-weight: 500; color: var(--text-muted);" class="d-none d-md-block">
                    Welcome, <strong><?= htmlspecialchars($adminName) ?></strong>
                </span>
                <div class="avatar"><?= $adminInitials ?></div>
            </div>
        </header>
        
        <div class="container-fluid" style="padding: 24px;">