<?php
// backend/api/get-posts.php

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$response = [
    'success' => false,
    'data' => [],
    'message' => 'An unknown error occurred.'
];

try {
    $stmt = $pdo->query("
        SELECT 
            id,
            title,
            slug,
            excerpt,
            cover_image,
            media_type,
            media_url,
            published_at
        FROM posts
        WHERE published_at IS NOT NULL
        ORDER BY published_at DESC
    ");

    $response['success'] = true;
    $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['message'] = 'Posts fetched successfully.';

} catch (PDOException $e) {
    error_log('API Error (get-posts): ' . $e->getMessage());
    http_response_code(500);
    $response['message'] = 'Database error: Could not retrieve posts.';
}

echo json_encode($response);
exit;
