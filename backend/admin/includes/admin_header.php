<?php
declare(strict_types=1);

/**
 * Admin Header — Unified & Stable
 * Uses proxy routing (/admin/*)
 * UI inspired by original version (collapsible sidebar + hamburger)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/admin_auth.php';

/* ===================== ROUTING ===================== */

if (!defined('ADMIN_BASE_URL')) {
    define('ADMIN_BASE_URL', '/admin');
}

function admin_url(string $path): string {
    return ADMIN_BASE_URL . '/' . ltrim($path, '/');
}

$currentPage = basename($_SERVER['PHP_SELF']);
function isActive(string $file): string {
    return $file === basename($_SERVER['PHP_SELF']) ? 'active' : '';
}

/* ===================== DATA ===================== */

try {
    $contactCount = (int)$pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
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
<title>ReSEED Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ===================== DESIGN TOKENS ===================== */
:root{
  --bg:#f4f7fb;
  --surface:#ffffff;
  --text:#0f172a;
  --muted:#6b7280;

  --primary:#166534;
  --accent:#22c55e;
  --danger:#ef4444;

  --radius:14px;
  --shadow:0 10px 30px rgba(0,0,0,.08);

  --nav-width:260px;
  --nav-collapsed:72px;
}

/* ===================== RESET ===================== */
*{box-sizing:border-box}
html,body{
  margin:0;
  height:100%;
  font-family:Inter,system-ui;
  background:var(--bg);
  color:var(--text);
}
a{text-decoration:none;color:inherit}

/* ===================== APP ===================== */
.app{display:flex;min-height:100vh}

/* ===================== SIDEBAR ===================== */
.sidebar{
  position:fixed;
  inset:0 auto 0 0;
  width:var(--nav-width);
  background:linear-gradient(180deg,var(--accent),var(--primary));
  color:#fff;
  padding:18px 10px;
  display:flex;
  flex-direction:column;
  z-index:1000;
  transition:width .3s ease, transform .3s ease;
}

.sidebar.collapsed{
  width:var(--nav-collapsed);
}

.sidebar.collapsed .brand-text,
.sidebar.collapsed .nav span,
.sidebar.collapsed .nav-title,
.sidebar.collapsed .badge{
  display:none;
}

.sidebar.collapsed .nav a{
  justify-content:center;
}

/* ===================== BRAND ===================== */
.brand{
  display:flex;
  align-items:center;
  gap:12px;
  padding:8px 10px;
  margin-bottom:20px;
}
.brand img{
  width:40px;
  height:40px;
  background:#fff;
  border-radius:10px;
  object-fit:contain;
}
.brand-text h1{
  margin:0;
  font-size:15px;
  font-weight:800;
}
.brand-text small{
  font-size:11px;
  opacity:.8;
}

/* ===================== NAV ===================== */
.nav{display:flex;flex-direction:column;gap:8px}
.nav-title{
  padding:8px 14px;
  font-size:11px;
  font-weight:700;
  letter-spacing:.08em;
  text-transform:uppercase;
  opacity:.7;
}

.nav a{
  display:flex;
  align-items:center;
  gap:14px;
  padding:12px 14px;
  border-radius:12px;
  font-weight:600;
  font-size:14px;
  transition:.2s;
}
.nav a i{
  width:22px;
  text-align:center;
}
.nav a:hover{
  background:rgba(255,255,255,.15);
}
.nav a.active{
  background:rgba(255,255,255,.28);
  box-shadow:inset 4px 0 0 #fff;
}

.badge{
  margin-left:auto;
  background:var(--danger);
  color:#fff;
  font-size:11px;
  font-weight:800;
  padding:2px 8px;
  border-radius:999px;
}

.logout{
  margin-top:16px;
  background:rgba(255,255,255,.18);
}
.logout:hover{
  background:rgba(239,68,68,.35);
}

/* ===================== MAIN ===================== */
.main{
  flex:1;
  margin-left:var(--nav-width);
  display:flex;
  flex-direction:column;
  transition:margin .3s ease;
}

.sidebar.collapsed ~ .main{
  margin-left:var(--nav-collapsed);
}

/* ===================== TOPBAR ===================== */
.topbar{
  height:72px;
  background:var(--surface);
  box-shadow:var(--shadow);
  display:flex;
  align-items:center;
  padding:0 20px;
  gap:16px;
  position:sticky;
  top:0;
  z-index:900;
}

.hamburger{
  background:none;
  border:0;
  font-size:20px;
  cursor:pointer;
}

.spacer{flex:1}

.user{
  display:flex;
  align-items:center;
  gap:10px;
}
.avatar{
  width:38px;
  height:38px;
  border-radius:12px;
  background:linear-gradient(135deg,var(--accent),var(--primary));
  display:grid;
  place-items:center;
  color:#fff;
  font-weight:800;
}

/* ===================== CONTENT ===================== */
.container{
  padding:24px;
  max-width:1400px;
  width:100%;
}

/* ===================== MOBILE ===================== */
.overlay{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.45);
  display:none;
  z-index:800;
}
.overlay.show{display:block}

@media(max-width:900px){
  .sidebar{
    transform:translateX(-100%);
    width:var(--nav-width);
  }
  .sidebar.open{
    transform:translateX(0);
  }
  .main{
    margin-left:0!important;
  }
}
</style>
</head>

<body>

<div class="overlay" id="overlay"></div>

<div class="app">

<nav class="sidebar" id="sidebar">
  <div>
    <div class="brand">
      <img src="/assets/images/Re-logo.png" alt="ReSEED">
      <div class="brand-text">
        <h1>ReSEED Admin</h1>
        <small>Control Panel</small>
      </div>
    </div>

    <div class="nav">
      <div class="nav-title">Overview</div>
      <a href="<?= admin_url('dashboard.php') ?>" class="<?= isActive('dashboard.php') ?>">
        <i class="fa-solid fa-chart-pie"></i><span>Dashboard</span>
      </a>

      <div class="nav-title">Content</div>
      <a href="<?= admin_url('projects.php') ?>" class="<?= isActive('projects.php') ?>">
        <i class="fa-solid fa-diagram-project"></i><span>Projects</span>
      </a>
      <a href="<?= admin_url('posts.php') ?>" class="<?= isActive('posts.php') ?>">
        <i class="fa-solid fa-newspaper"></i><span>Posts</span>
      </a>
      <a href="<?= admin_url('gallery.php') ?>" class="<?= isActive('gallery.php') ?>">
        <i class="fa-solid fa-images"></i><span>Gallery</span>
      </a>

      <div class="nav-title">System</div>
      <a href="<?= admin_url('contacts.php') ?>" class="<?= isActive('contacts.php') ?>">
        <i class="fa-solid fa-envelope"></i><span>Contacts</span>
        <?php if($contactCount>0): ?><span class="badge"><?= $contactCount ?></span><?php endif; ?>
      </a>
      <a href="<?= admin_url('create_admin.php') ?>" class="<?= isActive('create_admin.php') ?>">
        <i class="fa-solid fa-user-shield"></i><span>Admins</span>
      </a>

      <a href="<?= admin_url('logout.php') ?>" class="logout">
        <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
      </a>
    </div>
  </div>
</nav>

<main class="main">
<header class="topbar">
  <button class="hamburger" id="hamburger">
    <i class="fa-solid fa-bars"></i>
  </button>
  <div class="spacer"></div>
  <div class="user">
    <div class="avatar"><?= $adminInitials ?></div>
    <strong><?= htmlspecialchars($adminName) ?></strong>
  </div>
</header>

<section class="container">
