<?php
declare(strict_types=1);

// Correct path to reach backend/includes/config.php
require_once dirname(__DIR__, 2) . '/includes/config.php';

$username = 'admin'; 
$password = 'admin123'; // Use this to test, you can change it later
$role     = 'admin';

// Hash the password securely
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    // 1. Clear any existing user with this name to avoid "Unique Constraint" errors
    $pdo->prepare("DELETE FROM users WHERE username = :username")->execute(['username' => $username]);

    // 2. Insert the fresh admin account
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
    echo "Admin user created. <br>Username: <b>admin</b> <br>Password: <b>admin123</b>";
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}