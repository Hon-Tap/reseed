<?php
// backend/api/get-projects.php
// Fetches a list of public projects and returns them as JSON

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$response = [
    'success' => false,
    'data' => [],
    'message' => 'An unknown error occurred.'
];

try {
    /**
     * Schema-aligned query
     * status ENUM: ('ongoing','completed','planned')
     */
    $stmt = $pdo->query("
        SELECT
            id,
            title,
            slug,
            summary,
            description,
            location,
            start_date,
            end_date,
            cover_image,
            media_type,
            media_url,
            status,
            featured,
            created_at
        FROM projects
        WHERE status IN ('ongoing','completed','planned')
        ORDER BY created_at DESC
    ");

    $response['success'] = true;
    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['message'] = 'Projects fetched successfully.';

} catch (PDOException $e) {
    error_log('API Error (get-projects): ' . $e->getMessage());
    http_response_code(500);
    $response['message'] = 'Database error: Could not retrieve projects.';
}

echo json_encode($response);
exit;
