<?php
// api/contact-handler.php
// Handles POST requests from the contact form, saves data, and sends a notification.

require_once __DIR__ . '/../includes/config.php';

// Set standard headers for API response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
// Allow POST requests specifically
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$response = [
    'success' => false,
    'message' => 'Invalid request method.'
];

// Handle pre-flight OPTIONS request (common for CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. Check for POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Capture and Sanitize Input
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // 3. Simple Validation
    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
        $response['message'] = 'Please fill out all required fields and provide a valid email.';
        http_response_code(400); // Bad Request
    } else {
        try {
            // 4. Save to Database (Recommended for reliability)
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, received_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $subject, $message]);

            // 5. Send Email Notification (Example, requires external mail library or php mail() config)
            $to = "your.admin.email@example.com";
            $email_subject = "NEW CONTACT FORM: " . $subject;
            $email_body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
            $headers = "From: webmaster@yourdomain.com\r\n";
            $headers .= "Reply-To: $email\r\n";
            
            // mail($to, $email_subject, $email_body, $headers); // Uncomment this line if PHP mail is configured

            $response['success'] = true;
            $response['message'] = 'Your message has been sent successfully!';
            http_response_code(200);

        } catch(PDOException $e) {
            error_log("API Error (contact-handler): " . $e->getMessage());
            $response['message'] = 'An internal server error occurred while saving your message.';
            http_response_code(500);
        }
    }
}

echo json_encode($response);
exit;