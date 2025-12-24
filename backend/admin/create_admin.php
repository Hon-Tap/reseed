<?php
declare(strict_types=1);

// PATH CORRECTION: 
// From: backend/admin/create_admin.php
// To:   backend/includes/config.php
// Moves up 1 level to 'admin', then into 'includes'
require_once dirname(__DIR__) . '/includes/config.php';

$username = 'admin'; 
$password = 'admin123'; 
$role     = 'admin';

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    // 1. Clear existing
    $pdo->prepare("DELETE FROM users WHERE username = :username")->execute(['username' => $username]);

    // 2. Insert fresh
    $stmt = $pdo->prepare("
        INSERT INTO users (username, password_hash, role) 
        VALUES (:username, :password_hash, :role)
    ");
    
    $stmt->execute([
        'username'      => $username,
        'password_hash' => $passwordHash,
        'role'          => $role
    ]);

    echo "<h1>Success!</h1>";
    echo "Admin user created. Username: <b>admin</b>";
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}