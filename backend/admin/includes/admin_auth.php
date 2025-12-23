<?php
declare(strict_types=1);

/* =====================================================
   SESSION BOOTSTRAP
===================================================== */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* =====================================================
   BASE AUTH CHECK
===================================================== */
if (
    empty($_SESSION['admin_id']) ||
    empty($_SESSION['admin_role'])
) {
    header('Location: /admin/login.php');
    exit;
}

/* =====================================================
   ROLE HELPERS
===================================================== */

/**
 * Require a specific role
 * Example: require_role('super');
 */
function require_role(string $role): void
{
    if ($_SESSION['admin_role'] !== $role) {
        http_response_code(403);
        exit('Forbidden');
    }
}

/**
 * Require one of multiple roles
 * Example: require_any_role(['admin', 'editor']);
 */
function require_any_role(array $roles): void
{
    if (!in_array($_SESSION['admin_role'], $roles, true)) {
        http_response_code(403);
        exit('Forbidden');
    }
}

/**
 * Require at least admin privileges
 * (admin OR super)
 */
function require_admin(): void
{
    require_any_role(['admin', 'super']);
}
