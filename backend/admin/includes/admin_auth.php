<?php
declare(strict_types=1);

/* =====================================================
   SESSION BOOTSTRAP & SECURITY
===================================================== */
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Set secure cookie params before starting session
    session_start([
        'cookie_httponly' => true,
        'cookie_secure'   => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true,
    ]);
}

/* =====================================================
   DEPENDENCIES
==================================================== */
require_once dirname(__DIR__, 2) . '/includes/config.php';

/* =====================================================
   BASE AUTH CHECK
===================================================== */
// Check if user is logged in
if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_role'])) {
    // Use a relative path or a defined constant to avoid 404s
    header('Location: login.php'); 
    exit;
}

// Session Hijacking Protection: Check User Agent
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
} else if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header('Location: login.php?error=session_invalid');
    exit;
}

/* =====================================================
   ROLE HELPERS
===================================================== */

/**
 * Require a specific role
 */
function require_role(string $role): void
{
    if (($_SESSION['admin_role'] ?? '') !== $role) {
        http_response_code(403);
        include __DIR__ . '/../403.php'; // Optional: show a nice error page
        exit('Forbidden: Insufficient permissions.');
    }
}

/**
 * Require one of multiple roles
 */
function require_any_role(array $roles): void
{
    if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], $roles, true)) {
        http_response_code(403);
        exit('Forbidden: Insufficient permissions.');
    }
}

/**
 * Require at least admin privileges
 */
function require_admin(): void
{
    require_any_role(['admin', 'super']);
}