<?php
declare(strict_types=1);

/**
 * Admin Header (REAL FILE)
 * --------------------------------------------------
 * Shared layout + navigation for Admin Panel
 * Executed via proxy: /frontend/admin/*.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/admin_auth.php';

/* ==================================================
   CONSTANTS & HELPERS
================================================== */

/**
 * IMPORTANT:
 * This must point to the PROXY URL, not the real folder
 */
if (!defined('ADMIN_BASE_URL')) {
    define('ADMIN_BASE_URL', '/admin');
}

$currentPage = basename($_SERVER['PHP_SELF']);

function isActive(string $file): string
{
    return basename($_SERVER['PHP_SELF']) === $file ? 'active' : '';
}

function admin_url(string $path): string
{
    return ADMIN_BASE_URL . '/' . ltrim($path, '/');
}

/* ==================================================
   NOTIFICATIONS
================================================== */

try {
    $contactCount = (int) $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
} catch (Throwable $e) {
    $contactCount = 0;
}

/* ==================================================
   USER CONTEXT
================================================== */

$adminName     = $_SESSION['admin_name'] ?? 'Admin';
$adminInitials = strtoupper(substr($adminName, 0, 2));
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ReSEED | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --primary: #166534;
            --accent: #22c55e;
            --danger: #ef4444;
            --radius-lg: 16px;
            --radius-md: 12px;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
            --nav-width: 280px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* ===================== SIDEBAR ===================== */

        .sidebar {
            width: var(--nav-width);
            background: #0f172a;
            color: #f8fafc;
            position: fixed;
            inset: 0 auto 0 0;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .3s ease;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 12px 32px;
            border-bottom: 1px solid rgba(255,255,255,.1);
            margin-bottom: 24px;
        }

        .brand img {
            width: 38px;
            height: 38px;
            background: #fff;
            border-radius: 8px;
            padding: 4px;
        }

        .brand h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
        }

        .nav-group { margin-bottom: 24px; }

        .nav-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            padding: 0 16px 12px;
            font-weight: 700;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 600;
            border-radius: var(--radius-md);
            transition: all .2s;
        }

        .nav-link i { width: 22px; text-align: center; }

        .nav-link:hover {
            background: rgba(255,255,255,.06);
            color: #fff;
        }

        .nav-link.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 6px 16px rgba(22,101,52,.4);
        }

        .badge {
            margin-left: auto;
            background: var(--danger);
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            color: #fff;
        }

        .logout-btn {
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,.1);
            padding-top: 24px;
        }

        .logout-link:hover {
            background: rgba(239,68,68,.15);
            color: var(--danger);
        }

        /* ===================== MAIN ===================== */

        .main-wrapper {
            margin-left: var(--nav-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-header {
            height: 72px;
            background: var(--surface);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f1f5f9;
            padding: 6px 16px 6px 6px;
            border-radius: 50px;
            font-weight: 600;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 12px;
        }

        .container-fluid {
            padding: 32px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <div class="brand">
        <img src="/assets/images/Re-logo.png" alt="ReSEED">
        <div>
            <h1>ReSEED</h1>
            <div style="font-size:10px;opacity:.6;font-weight:600;">ADMIN PANEL</div>
        </div>
    </div>

    <nav>
        <div class="nav-group">
            <div class="nav-label">Overview</div>
            <a href="<?= admin_url('dashboard.php') ?>" class="nav-link <?= isActive('dashboard.php') ?>">
                <i class="fa-solid fa-grid-2"></i> Dashboard
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-label">Content</div>
            <a href="<?= admin_url('projects.php') ?>" class="nav-link <?= isActive('projects.php') ?>">
                <i class="fa-solid fa-leaf"></i> Projects
            </a>
            <a href="<?= admin_url('posts.php') ?>" class="nav-link <?= isActive('posts.php') ?>">
                <i class="fa-solid fa-pen-nib"></i> Blog Posts
            </a>
            <a href="<?= admin_url('gallery.php') ?>" class="nav-link <?= isActive('gallery.php') ?>">
                <i class="fa-solid fa-layer-group"></i> Gallery
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-label">System</div>
            <a href="<?= admin_url('contacts.php') ?>" class="nav-link <?= isActive('contacts.php') ?>">
                <i class="fa-solid fa-envelope"></i> Contacts
                <?php if ($contactCount > 0): ?>
                    <span class="badge"><?= $contactCount ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= admin_url('create_admin.php') ?>" class="nav-link <?= isActive('create_admin.php') ?>">
                <i class="fa-solid fa-shield-halved"></i> Administrators
            </a>
        </div>
    </nav>

    <div class="logout-btn">
        <a href="<?= admin_url('logout.php') ?>" class="nav-link logout-link">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
        </a>
    </div>
</aside>

<div class="main-wrapper">
    <header class="top-header">
        <div>
            <span style="color:var(--text-muted);font-size:14px;">Welcome back,</span>
            <strong><?= htmlspecialchars($adminName) ?></strong>
        </div>
        <div class="user-pill">
            <div class="user-avatar"><?= $adminInitials ?></div>
            <span>Admin Account</span>
        </div>
    </header>

    <div class="container-fluid">
