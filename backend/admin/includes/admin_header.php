<?php
// backend/admin/includes/admin_header.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/admin_auth.php';

/* ===================== HELPERS ===================== */
$currentPage = basename($_SERVER['PHP_SELF']);

function isActive(string $fileName): string {
    $current = basename($_SERVER['PHP_SELF']);
    return ($current === $fileName) ? 'active' : '';
}

/* ===================== NOTIFICATIONS ===================== */
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM contacts");
    $contactCount = (int)$stmt->fetchColumn();
} catch (Throwable $e) {
    $contactCount = 0;
}

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
            --primary-light: #dcfce7;
            --accent: #22c55e;
            --danger: #ef4444;
            --radius-lg: 16px;
            --radius-md: 12px;
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --nav-width: 280px;
        }

        * { box-sizing: border-box; -webkit-font-smoothing: antialiased; }
        
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--nav-width);
            background: #0f172a; /* Dark sleek professional look */
            color: #f8fafc;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            padding: 24px 16px;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 12px 32px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 24px;
        }

        .brand img {
            width: 38px;
            height: 38px;
            object-fit: contain;
            background: white;
            border-radius: 8px;
            padding: 4px;
        }

        .brand h1 { font-size: 18px; font-weight: 800; margin: 0; letter-spacing: -0.5px; }

        /* Nav Links */
        .nav-group { margin-bottom: 24px; }
        .nav-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
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
            font-size: 14px;
            border-radius: var(--radius-md);
            transition: all 0.2s;
            margin-bottom: 4px;
        }

        .nav-link i { font-size: 18px; width: 24px; text-align: center; }

        .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(22, 101, 52, 0.3);
        }

        .badge {
            margin-left: auto;
            background: var(--danger);
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            color: white;
        }

        /* Main Content Area */
        .main-wrapper {
            flex: 1;
            margin-left: var(--nav-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            padding: 6px 16px 6px 6px;
            background: #f1f5f9;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 12px;
        }

        .container-fluid { padding: 32px; max-width: 1400px; margin: 0 auto; width: 100%; }

        .logout-btn {
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 24px;
        }

        .logout-link:hover { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
            .sidebar.open { transform: translateX(0); }
        }
    </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="brand">
        <img src="/assets/images/Re-logo.png" alt="ReSEED">
        <div>
            <h1>ReSEED</h1>
            <div style="font-size: 10px; opacity: 0.6; font-weight: 600;">ADMIN PANEL</div>
        </div>
    </div>

    <nav style="flex: 1">
        <div class="nav-group">
            <div class="nav-label">Overview</div>
            <a href="dashboard.php" class="nav-link <?= isActive('dashboard.php') ?>">
                <i class="fa-solid fa-grid-2"></i> Dashboard
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-label">Content Management</div>
            <a href="projects.php" class="nav-link <?= isActive('projects.php') ?>">
                <i class="fa-solid fa-leaf"></i> Projects
            </a>
            <a href="posts.php" class="nav-link <?= isActive('posts.php') ?>">
                <i class="fa-solid fa-pen-nib"></i> Blog Posts
            </a>
            <a href="gallery.php" class="nav-link <?= isActive('gallery.php') ?>">
                <i class="fa-solid fa-layer-group"></i> Gallery
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-label">System</div>
            <a href="contacts.php" class="nav-link <?= isActive('contacts.php') ?>">
                <i class="fa-solid fa-envelope"></i> Contacts
                <?php if ($contactCount > 0): ?>
                    <span class="badge"><?= $contactCount ?></span>
                <?php endif; ?>
            </a>
            <a href="create_admin.php" class="nav-link <?= isActive('create_admin.php') ?>">
                <i class="fa-solid fa-shield-halved"></i> Administrators
            </a>
        </div>
    </nav>

    <div class="logout-btn">
        <a href="logout.php" class="nav-link logout-link">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
        </a>
    </div>
</aside>

<div class="main-wrapper">
    <header class="top-header">
        <button id="mobile-toggle" style="display: none; background: none; border: none; font-size: 20px; cursor: pointer;">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>
        <div class="page-context">
            <span style="color: var(--text-muted); font-size: 14px;">Welcome back,</span>
            <span style="font-weight: 700; color: var(--text-main);"> <?= htmlspecialchars($adminName) ?></span>
        </div>
        
        <div class="user-pill">
            <div class="user-avatar"><?= $adminInitials ?></div>
            <span>Admin Account</span>
        </div>
    </header>
    
    <div class="container-fluid">