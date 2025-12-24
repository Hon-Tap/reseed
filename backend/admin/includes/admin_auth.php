<?php
declare(strict_types=1);

/* =====================================================
   SESSION BOOTSTRAP
===================================================== */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* =====================================================
   DEPENDENCIES
===================================================== */
/**
 * Current Path: /backend/admin/includes/admin_auth.php
 * Config Path:  /backend/includes/config.php
 */
require_once dirname(__DIR__, 2) . '/includes/config.php';

/* =====================================================
   BASE AUTH CHECK
===================================================== */
if (
    empty($_SESSION['admin_id']) ||
    empty($_SESSION['admin_role'])
) {
    // Redirect to the public-facing login page at the root
    header('Location: /admin.php');
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