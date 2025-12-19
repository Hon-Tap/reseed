<?php
// backend/api/contact-handler.php
// Handles contact form submissions and stores them in the database

require_once __DIR__ . '/../includes/config.php';

/**
 * Standard API headers
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

/**
 * Handle CORS preflight
 */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Only allow POST
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

/**
 * Read input (supports JSON and form-data)
 */
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$name    = trim($data['name'] ?? '');
$email   = trim($data['email'] ?? '');
$phone   = trim($data['phone'] ?? '');
$message = trim($data['message'] ?? '');

/**
 * Validate input
 */
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
        'message' => 'Invalid email address.'
    ]);
    exit;
}

/**
 * Store message in database
 * Matches `contacts` table schema exactly
 */
try {
    $stmt = $pdo->prepare("
        INSERT INTO contacts (name, email, phone, message, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $name,
        $email,
        $phone ?: null,
        $message
    ]);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for contacting ReSEED. We will get back to you soon.'
    ]);

} catch (PDOException $e) {
    error_log('API Error (contact-handler): ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An internal server error occurred. Please try again later.'
    ]);
}

exit;
