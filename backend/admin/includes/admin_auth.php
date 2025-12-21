<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    empty($_SESSION['admin_id']) ||
    empty($_SESSION['admin_role'])
) {
    header('Location: login.php');
    exit;
}

/* Optional role enforcement */
function require_role(string $role): void
{
    if ($_SESSION['admin_role'] !== $role) {
        http_response_code(403);
        exit('Forbidden');
    }
}
