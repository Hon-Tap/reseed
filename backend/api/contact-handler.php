<?php
// backend/api/contact-handler.php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
| IMPORTANT: Path corrected.
| backend/api → root/includes
*/
require_once dirname(__DIR__) . '/includes/config.php';



/*
|--------------------------------------------------------------------------
| Standard API Headers
|--------------------------------------------------------------------------
*/
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

/*
|--------------------------------------------------------------------------
| Preflight (CORS)
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/*
|--------------------------------------------------------------------------
| Method Guard
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Input Handling
|--------------------------------------------------------------------------
| Supports:
| - application/json
| - multipart/form-data
*/
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true);

$data = is_array($jsonData) ? $jsonData : $_POST;

$name    = trim($data['name']    ?? '');
$email   = trim($data['email']   ?? '');
$phone   = trim($data['phone']   ?? '');
$message = trim($data['message'] ?? '');

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
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

/*
|--------------------------------------------------------------------------
| Database Insert
|--------------------------------------------------------------------------
| Table: contacts
| Columns: name, email, phone, message, created_at
*/
try {
    $stmt = $pdo->prepare(
        "INSERT INTO contacts (name, email, phone, message, created_at)
         VALUES (:name, :email, :phone, :message, NOW())"
    );

    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':phone'   => $phone !== '' ? $phone : null,
        ':message' => $message
    ]);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for contacting ReSEED. We will get back to you soon.'
    ]);

} catch (Throwable $e) {
    // Never echo errors to client
    error_log('[CONTACT_API_ERROR] ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An internal server error occurred. Please try again later.'
    ]);
}

exit;
