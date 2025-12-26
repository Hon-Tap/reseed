<?php
declare(strict_types=1);

/* =========================================================
 * ADMIN HEADER – SINGLE SOURCE OF TRUTH
 * ======================================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/admin_auth.php';

/* ------------------------------
   ADMIN ROUTING CONTRACT
------------------------------ */
if (!defined('ADMIN_BASE_URL')) {
    define('ADMIN_BASE_URL', '/admin');
}

function admin_url(string $path): string
{
    return ADMIN_BASE_URL . '/' . ltrim($path, '/');
}

function isActive(string $file): string
{
    return basename($_SERVER['PHP_SELF']) === $file ? 'active' : '';
}

/* ------------------------------
   DATA FOR UI
------------------------------ */
try {
    $contactCount = (int) $pdo
        ->query("SELECT COUNT(*) FROM contacts")
        ->fetchColumn();
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

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #166534;
            --primary-dark: #14532d;
            --accent: #22c55e;

            --bg-body: #f3f4f6;
            --bg-surface: #ffffff;

            --text-main: #1e293b;
            --text-muted: #64748b;
            --danger: #ef4444;
            --border: #e2e8f0;

            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;

            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
        }
        a { text-decoration: none; color: inherit; }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        .brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .brand img {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: white;
            padding: 2px;
            object-fit: contain;
        }

        .brand-text {
            margin-left: 12px;
            white-space: nowrap;
            overflow: hidden;
        }

        .brand-text h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .nav-menu {
            flex: 1;
            padding: 20px 12px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-header {
            font-size: 11px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
            margin: 16px 12px 6px;
            font-weight: 600;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 14px;
            border-radius: 10px;
            color: rgba(255,255,255,0.85);
            font-size: 14px;
            transition: 0.2s;
            position: relative;
        }

        .nav-link i {
            width: 24px;
            font-size: 16px;
            text-align: center;
            margin-right: 12px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: #fff;
            box-shadow: inset 3px 0 0 var(--accent);
        }

        .badge {
            margin-left: auto;
            background: var(--danger);
            color: #fff;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 6px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            background: rgba(0,0,0,0.2);
            font-size: 13px;
            transition: 0.2s;
        }

        /* ================= MAIN ================= */

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
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .toggle-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: var(--text-muted);
            padding: 8px;
            border-radius: 8px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* ================= STATES ================= */

        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed
        .brand-text,
        body.sidebar-collapsed
        .nav-header,
        body.sidebar-collapsed
        .nav-link span,
        body.sidebar-collapsed
        .badge,
        body.sidebar-collapsed
        .btn-logout span {
            display: none;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 950;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0 !important;
            }
            body.mobile-open .sidebar {
                transform: translateX(0);
            }
            body.mobile-open .overlay {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>
<body>

<div class="overlay" id="overlay"></div>

<div class="wrapper">

    <!-- ================= SIDEBAR ================= -->
    <nav class="sidebar" id="sidebar">

        <div class="brand">
            <img src="/assets/images/Re-logo.png" alt="ReSEED Logo">
            <div class="brand-text">
                <h4>ReSEED</h4>
                <span>Admin Panel</span>
            </div>
        </div>

        <div class="nav-menu">

            <div class="nav-header">Overview</div>

            <a href="<?= admin_url('dashboard.php') ?>"
               class="nav-link <?= isActive('dashboard.php') ?>">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-header">Content</div>

            <a href="<?= admin_url('projects.php') ?>"
               class="nav-link <?= isActive('projects.php') ?>">
                <i class="fa-solid fa-diagram-project"></i>
                <span>Projects</span>
            </a>

            <a href="<?= admin_url('posts.php') ?>"
               class="nav-link <?= isActive('posts.php') ?>">
                <i class="fa-solid fa-newspaper"></i>
                <span>Posts</span>
            </a>

            <a href="<?= admin_url('gallery.php') ?>"
               class="nav-link <?= isActive('gallery.php') ?>">
                <i class="fa-solid fa-images"></i>
                <span>Gallery</span>
            </a>

            <a href="<?= admin_url('contacts.php') ?>"
               class="nav-link <?= isActive('contacts.php') ?>">
                <i class="fa-solid fa-envelope"></i>
                <span>Contacts</span>
                <?php if ($contactCount > 0): ?>
                    <span class="badge"><?= $contactCount ?></span>
                <?php endif; ?>
            </a>

        </div>

        <div class="sidebar-footer">
            <a href="<?= admin_url('logout.php') ?>" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>

    </nav>

    <!-- ================= MAIN ================= -->
    <main class="main-content">

        <header class="topbar">
            <button class="toggle-btn" id="sidebarToggle" type="button">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="user-profile">
                <strong><?= htmlspecialchars($adminName) ?></strong>
                <div class="avatar"><?= $adminInitials ?></div>
            </div>
        </header>

        <div class="container-fluid">
