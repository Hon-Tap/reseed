<?php
declare(strict_types=1);

/**
 * Frontend Proxy for Dashboard
 * Path: frontend/dashboard.php -> backend/admin/dashboard.php
 */

// Move up one level out of frontend, then into backend
require_once __DIR__ . '/../backend/admin/dashboard.php';