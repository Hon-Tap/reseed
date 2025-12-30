<?php
declare(strict_types=1);

/**
 * PATH LOGIC:
 * Current: /backend/admin/handlers/forgot-handler.php
 * $backendPath (Up 2): /backend/
 * $rootPath (Up 3): / (Project Root for vendor)
 */
$backendPath = dirname(__DIR__, 2);
$rootPath    = dirname(__DIR__, 3);

// 1. Load Core Dependencies
require_once $backendPath . '/admin/includes/config.php';
require_once $backendPath . '/admin/includes/csrf.php';

// 2. Load PHPMailer via Composer
require_once $rootPath . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 3. Security Guard & CSRF Check
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['csrf_token'] ?? null)) {
    header('Location: /admin.php?error=csrf');
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

if ($email) {
    $token = bin2hex(random_bytes(32));
    
    try {
        // Update Database (PostgreSQL)
        $stmt = $pdo->prepare("
            UPDATE users 
            SET reset_token = :token, 
                reset_expires = NOW() + INTERVAL '1 hour' 
            WHERE email = :email
        ");
        $stmt->execute(['token' => $token, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your-email@gmail.com'; // Use Environment Variables here if possible
            $mail->Password   = 'your-app-password'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('noreply@reseed.com', 'ReSEED Admin');
            $mail->addAddress($email);

            // Content
            $resetLink = "https://reseed.onrender.com/reset-password.php?token=" . $token;
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click the link below to reset your password. Valid for 1 hour:<br><br>
                              <a href='$resetLink'>$resetLink</a>";

            $mail->send();
        }
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
    }
}

// Redirect back to login with success/sent message
header('Location: /admin.php?status=sent');
exit;