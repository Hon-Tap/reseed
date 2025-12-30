<?php
declare(strict_types=1);

/**
 * ADMIN BOOTSTRAP SCRIPT
 * Run once, then DELETE this file.
 */

require_once dirname(__DIR__) . '/includes/config.php';

/* ===================== CONFIG ===================== */

$username = 'admin';
$password = 'ChangeMeNow!123'; // MUST CHANGE
$role     = 'admin';

/* ===================== VALIDATION ===================== */

if (strlen($password) < 10) {
    die('Password too weak. Minimum 10 characters.');
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo->beginTransaction();

    // Remove existing admin with same username
    $pdo->prepare(
        'DELETE FROM users WHERE username = :username'
    )->execute(['username' => $username]);

    // Insert admin
    $stmt = $pdo->prepare("
        INSERT INTO users (
            username,
            password_hash,
            role,
            password_updated_at,
            two_factor_enabled,
            created_at
        ) VALUES (
            :username,
            :password_hash,
            :role,
            NOW(),
            FALSE,
            NOW()
        )
    ");

    $stmt->execute([
        'username'      => $username,
        'password_hash' => $passwordHash,
        'role'          => $role,
    ]);

    $pdo->commit();

    echo "<h1>Admin Created Successfully</h1>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>IMPORTANT:</strong> Delete this file immediately.</p>";

} catch (Throwable $e) {
    $pdo->rollBack();
    die('Setup failed: ' . htmlspecialchars($e->getMessage()));
}
