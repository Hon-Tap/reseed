<?php
// api/get-projects.php
// Fetches a list of projects and returns them as a JSON array.

// Ensure configuration is loaded
require_once __DIR__ . '/../includes/config.php';

// Set standard headers for API response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // IMPORTANT: Adjust this in production for security!

$response = [
    'success' => false,
    'data' => [],
    'message' => 'An unknown error occurred.'
];

try {
    // Select only essential public fields
    $stmt = $pdo->query("SELECT id, title, slug, summary, cover_image, media_type, status, location, start_date, end_date FROM projects WHERE status != 'Draft' ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['data'] = $projects;
    $response['message'] = 'Projects fetched successfully.';
    
} catch(PDOException $e) {
    // Log error for server-side debugging
    error_log("API Error (get-projects): " . $e->getMessage());
    
    $response['message'] = 'Database error: Could not retrieve projects.';
    http_response_code(500); // Set HTTP status code to 500 (Internal Server Error)
}

// Return the final JSON response
echo json_encode($response);
exit;