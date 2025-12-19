<?php
declare(strict_types=1);

// Configuration
require_once dirname(__DIR__) . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Support both JSON and Form Data inputs
$input = json_decode(file_get_contents('php://input'), true);
$data = $input ?? $_POST;

$name    = htmlspecialchars(trim($data['name'] ?? ''));
$email   = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone   = htmlspecialchars(trim($data['phone'] ?? ''));
$message = htmlspecialchars(trim($data['message'] ?? ''));

// Validation
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, message, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $phone ?: null, $message]);

    echo json_encode([
        'success' => true,
        'message' => 'Thank you for contacting ReSEED! We will get back to you soon.'
    ]);
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong on our end.']);
}