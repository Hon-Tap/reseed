<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Adjust paths based on your structure: backend/admin/includes/admin_header.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/admin_auth.php';

/*
|--------------------------------------------------------------------------
| Admin URL Helpers
|--------------------------------------------------------------------------
*/
if (!defined('ADMIN_BASE_URL')) {
    define('ADMIN_BASE_URL', '/admin');
}

function admin_url(string $path = ''): string {
    return ADMIN_BASE_URL . '/' . ltrim($path, '/');
}

function isActive(string $file): string {
    return basename($_SERVER['PHP_SELF']) === $file ? 'active' : '';
}

/*
|--------------------------------------------------------------------------
| Data Fetching
|--------------------------------------------------------------------------
*/
try {
    // specific to your DB wrapper
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
    <title>ReSEED — Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            /* --- Theme Colors (ReSEED Green) --- */
            --primary: #166534;       /* Deep Green */
            --primary-hover: #14532d; /* Darker Green */
            --accent: #22c55e;        /* Bright Green for badges/active states */
            
            /* --- UI Colors --- */
            --bg-body: #f1f5f9;       /* Light Gray Background */
            --bg-surface: #ffffff;    /* White cards/header */
            --text-main: #0f172a;     /* Dark Slate */
            --text-muted: #64748b;    /* Muted Text */
            --border-color: #e2e8f0;  /* Light Border */
            --danger: #ef4444;

            /* --- Dimensions --- */
            --sidebar-width: 260px;
            --sidebar-mini-width: 72px;
            --header-height: 64px;
            
            /* --- Transitions --- */
            --ease: cubic-bezier(0.4, 0, 0.2, 1);
            --duration: 0.3s;
        }

        /* --- Reset & Base --- */
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            font-size: 14px;
            overflow-x: hidden;
        }

        /* --- Layout Grid --- */
        .wrapper {
            display: flex;
            min-height: 100vh;
            transition: all var(--duration) var(--ease);
        }

        /* --- Sidebar Styling --- */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(170deg, var(--primary), var(--primary-hover));
            color: white;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            display: flex;
            flex-direction: column;
            transition: width var(--duration) var(--ease), transform var(--duration) var(--ease);
            box-shadow: 4px 0 24px rgba(0,0,0,0.05);
            overflow: hidden; /* Important for clean collapsing */
        }

        .brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            white-space: nowrap;
        }

        .brand img {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: white;
            padding: 4px;
            flex-shrink: 0;
        }

        .brand-text {
            margin-left: 12px;
            opacity: 1;
            transition: opacity 0.2s;
        }

        .brand-text h4 { margin: 0; font-weight: 700; font-size: 16px; letter-spacing: -0.01em; }
        .brand-text small { font-size: 11px; opacity: 0.7; font-weight: 500; text-transform: uppercase; }

        /* Navigation */
        .nav-scroller {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }

        .nav-header {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255,255,255,0.5);
            margin: 16px 12px 8px;
            font-weight: 700;
            white-space: nowrap;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s;
            white-space: nowrap;
            position: relative;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            font-weight: 600;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            height: 20px;
            width: 4px;
            background: var(--accent);
            border-radius: 0 4px 4px 0;
        }

        .nav-link i {
            width: 24px;
            text-align: center;
            font-size: 16px;
            margin-right: 12px;
            flex-shrink: 0; /* Prevents icon squishing */
        }

        .badge {
            margin-left: auto;
            background: var(--accent);
            color: var(--primary);
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
        }

        /* --- Main Content Area --- */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0; /* Prevents flex overflow issues */
            transition: margin-left var(--duration) var(--ease);
        }

        /* Top Header */
        .topbar {
            height: var(--header-height);
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .toggle-btn {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .toggle-btn:hover {
            background: var(--bg-body);
            color: var(--primary);
            border-color: var(--primary);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
        }

        /* --- DESKTOP: Collapsed Sidebar (.sidebar-collapsed) --- */
        @media (min-width: 992px) {
            body.sidebar-collapsed .sidebar {
                width: var(--sidebar-mini-width);
            }
            
            body.sidebar-collapsed .main-content {
                margin-left: var(--sidebar-mini-width);
            }

            body.sidebar-collapsed .brand {
                padding: 0 0 0 18px; /* Center logo visually */
            }

            body.sidebar-collapsed .brand-text,
            body.sidebar-collapsed .nav-header,
            body.sidebar-collapsed .nav-link span,
            body.sidebar-collapsed .badge,
            body.sidebar-collapsed .nav-link.active::before {
                opacity: 0;
                pointer-events: none;
                display: none;
            }

            body.sidebar-collapsed .nav-link {
                justify-content: center;
                padding: 12px 0;
            }

            body.sidebar-collapsed .nav-link i {
                margin-right: 0;
            }
            
            /* Tooltip on hover for collapsed items */
            body.sidebar-collapsed .nav-link:hover {
                background: rgba(255,255,255,0.2);
            }
        }

        /* --- MOBILE: Off-canvas Sidebar --- */
        .mobile-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 45;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            backdrop-filter: blur(2px);
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }

            /* State: Mobile Open */
            body.mobile-open .sidebar {
                transform: translateX(0);
            }

            body.mobile-open .mobile-overlay {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>
<body>

<div class="mobile-overlay" id="mobileOverlay"></div>

<div class="wrapper">
    <nav class="sidebar">
        <div class="brand">
            <img src="/assets/images/Re-logo.png" alt="Logo">
            <div class="brand-text">
                <h4>ReSEED</h4>
                <small>Admin Portal</small>
            </div>
        </div>

        <div class="nav-scroller">
            <div class="nav-header">Overview</div>
            <a href="<?= admin_url('dashboard.php') ?>" class="nav-link <?= isActive('dashboard.php') ?>" title="Dashboard">
                <i class="fa-solid fa-grid-2"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-header">Content Management</div>
            <a href="<?= admin_url('projects.php') ?>" class="nav-link <?= isActive('projects.php') ?>" title="Projects">
                <i class="fa-solid fa-briefcase"></i>
                <span>Projects</span>
            </a>
            <a href="<?= admin_url('posts.php') ?>" class="nav-link <?= isActive('posts.php') ?>" title="Posts">
                <i class="fa-solid fa-newspaper"></i>
                <span>Posts</span>
            </a>
            <a href="<?= admin_url('gallery.php') ?>" class="nav-link <?= isActive('gallery.php') ?>" title="Gallery">
                <i class="fa-solid fa-image"></i>
                <span>Gallery</span>
            </a>

            <div class="nav-header">Connect</div>
            <a href="<?= admin_url('contacts.php') ?>" class="nav-link <?= isActive('contacts.php') ?>" title="Messages">
                <i class="fa-solid fa-inbox"></i>
                <span>Messages</span>
                <?php if ($contactCount > 0): ?>
                    <span class="badge"><?= $contactCount ?></span>
                <?php endif; ?>
            </a>

            <div class="nav-header">System</div>
            <a href="<?= admin_url('admin_profile.php') ?>" class="nav-link <?= isActive('admin_profile.php') ?>" title="Profile">
                <i class="fa-solid fa-user-shield"></i>
                <span>Profile</span>
            </a>
            
            <a href="<?= admin_url('logout.php') ?>" class="nav-link" style="margin-top: 12px; color: #fca5a5;" title="Logout">
                <i class="fa-solid fa-power-off"></i>
                <span>Sign Out</span>
            </a>
        </div>
    </nav>

    <main class="main-content">
        <header class="topbar">
            <button class="toggle-btn" id="sidebarToggle" aria-label="Toggle Sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="user-menu">
                <div style="text-align: right; line-height: 1.2;">
                    <span style="display:block; font-weight: 600; font-size: 13px;"><?= htmlspecialchars($adminName) ?></span>
                    <span style="display:block; font-size: 11px; color: var(--text-muted);">Administrator</span>
                </div>
                <div class="avatar-circle">
                    <?= $adminInitials ?>
                </div>
            </div>
        </header>

        <div style="padding: 24px; flex: 1;">