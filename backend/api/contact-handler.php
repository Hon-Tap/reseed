<?php
declare(strict_types=1);

/**
 * Contact Form Handler — ReSEED
 * Accepts POST only
 * Supports FormData and JSON
 */

// ---------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------
require_once dirname(__DIR__) . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

// ---------------------------------------------------------------------
// Method guard
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);
    exit;
}

// ---------------------------------------------------------------------
// Input handling (JSON or FormData)
// ---------------------------------------------------------------------
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (str_contains($contentType, 'application/json')) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
} else {
    $data = $_POST;
}

// ---------------------------------------------------------------------
// Normalize & sanitize input
// ---------------------------------------------------------------------
$name    = trim((string) ($data['name'] ?? ''));
$email   = trim((string) ($data['email'] ?? ''));
$phone   = trim((string) ($data['phone'] ?? ''));
$message = trim((string) ($data['message'] ?? ''));

// ---------------------------------------------------------------------
// Validation
// ---------------------------------------------------------------------
if ($name === '' || $email === '' || $message === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Name, email, and message are required.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a valid email address.'
    ]);
    exit;
}

// ---------------------------------------------------------------------
// Database write
// ---------------------------------------------------------------------
try {
    $stmt = $pdo->prepare("
        INSERT INTO contacts (name, email, phone, message)
        VALUES (:name, :email, :phone, :message)
    ");

    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':phone'   => $phone !== '' ? $phone : null,
        ':message' => $message,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Thank you for contacting ReSEED! We will get back to you shortly.'
    ]);
} catch (Throwable $e) {
    error_log('[CONTACT_HANDLER] ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An internal error occurred. Please try again later.'
    ]);
}
