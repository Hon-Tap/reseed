<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 2);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/csrf.php';


// 1. Method and CSRF Enforcement
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    header('Location: /frontend/admin/login.php?error=csrf');
    exit;
}

// 2. Input Validation
$token = $_POST['token'] ?? '';
$newPassword = $_POST['new_password'] ?? '';

if (empty($token) || strlen($newPassword) < 8) {
    header('Location: /frontend/admin/login.php?error=weak_password');
    exit;
}

try {
    /**
     * 3. Verify Token and Expiry in PostgreSQL
     * We check if the token exists and if NOW() is still before the expiry time.
     */
    $stmt = $pdo->prepare("
        SELECT id FROM users 
        WHERE reset_token = :token 
        AND reset_expires > NOW() 
        LIMIT 1
    ");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();

    if (!$user) {
        // Token is either fake or expired
        header('Location: /frontend/admin/login.php?error=invalid_token');
        exit;
    }

    // 4. Secure Password Hashing
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

    /**
     * 5. Update User & Clear Token
     * IMPORTANT: We set the token to NULL so it cannot be used a second time.
     */
    $updateStmt = $pdo->prepare("
        UPDATE users 
        SET password_hash = :hash, 
            reset_token = NULL, 
            reset_expires = NULL 
        WHERE id = :id
    ");
    
    $updateStmt->execute([
        'hash' => $hash,
        'id'   => $user['id']
    ]);

    // 6. Success! Redirect to login
    header('Location: /frontend/admin/login.php?success=reset_complete');
    exit;

} catch (PDOException $e) {
    error_log('Reset Error: ' . $e->getMessage());
    header('Location: /frontend/admin/login.php?error=server_error');
    exit;
}