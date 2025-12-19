<?php
// api/get-posts.php
// Fetches a list of posts/news articles and returns them as a JSON array.

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production

$response = [
    'success' => false,
    'data' => [],
    'message' => 'An unknown error occurred.'
];

try {
    // Adjust SELECT statement for your actual 'posts' table columns
    $stmt = $pdo->query("SELECT id, title, slug, summary, category, created_at, cover_image FROM posts WHERE is_published = 1 ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['data'] = $posts;
    $response['message'] = 'Posts fetched successfully.';
    
} catch(PDOException $e) {
    error_log("API Error (get-posts): " . $e->getMessage());
    $response['message'] = 'Database error: Could not retrieve posts.';
    http_response_code(500);
}

echo json_encode($response);
exit;