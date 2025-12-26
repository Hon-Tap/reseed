<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 2);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/csrf.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Adjust path to your vendor/autoload.php
require_once dirname(__DIR__, 3) . '/vendor/autoload.php'; 
$basePath = dirname(__DIR__); 
require_once $basePath . '/includes/config.php';
require_once $basePath . '/includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['csrf_token'] ?? null)) {
    header('Location: /frontend/admin/login.php?error=csrf');
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

if ($email) {
    $token = bin2hex(random_bytes(32));
    
    try {
        // 1. Update Database (PostgreSQL syntax)
        $stmt = $pdo->prepare("
            UPDATE users 
            SET reset_token = :token, 
                reset_expires = NOW() + INTERVAL '1 hour' 
            WHERE email = :email
        ");
        $stmt->execute(['token' => $token, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            // 2. PHPMailer Configuration
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP provider
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your-email@gmail.com'; 
            $mail->Password   = 'your-app-password'; // Not your login password!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('noreply@reseed.com', 'ReSEED Admin');
            $mail->addAddress($email);

            // Content
            $resetLink = "https://yourdomain.com/frontend/admin/reset-password.php?token=" . $token;
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

// Redirect to login with a "check your email" message
header('Location: /frontend/admin/login.php?error=sent');
exit;