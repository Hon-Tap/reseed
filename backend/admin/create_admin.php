<?php
declare(strict_types=1);

// 1. Load the database connection
require_once dirname(__DIR__, 2) . '/includes/config.php';

// 2. Define your desired credentials
$username = 'admin'; 
$password = 'YourSecurePassword123'; // CHANGE THIS!
$role     = 'admin';

// 3. Hash the password securely
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO users (username, password_hash, role) 
        VALUES (:username, :password_hash, :role)
    ");
    
    $stmt->execute([
        'username'      => $username,
        'password_hash' => $passwordHash,
        'role'          => $role
    ]);

    echo "Admin user created successfully!";
} catch (PDOException $e) {
    die("Error creating admin: " . $e->getMessage());
}