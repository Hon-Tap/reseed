<?php
declare(strict_types=1);

// 1. Setup paths
// Since logout.php is in /backend/admin/, dirname(__DIR__) gets you to /backend/
$backendPath = dirname(__DIR__); 

// 2. Load dependencies
require_once $backendPath . '/admin/includes/config.php'; // Corrected path
require_once $backendPath . '/admin/includes/csrf.php';   // Corrected path

// ... rest of your code ...

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* Guard: If not logged in, they shouldn't even see this page */
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

/* Logout Logic: Process only on POST */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        header('Location: login.php?error=csrf');
        exit;
    }

    // Completely clear the session
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    header('Location: login.php?status=logged_out');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sign out — ReSEED Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen bg-[#f1f5f9] flex items-center justify-center p-6">

    <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-200 p-10 max-w-md w-full text-center">
        <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </div>

        <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Sign out?</h2>
        <p class="text-slate-500 mb-8 font-medium">Are you sure you want to end your current session? You will need to login again to access the dashboard.</p>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <button type="submit" class="w-full bg-red-600 text-white font-bold py-4 rounded-2xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
                Yes, sign me out
            </button>

            <a href="dashboard.php" class="block w-full py-4 rounded-2xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors">
                Cancel, stay logged in
            </a>
        </form>
    </div>

</body>
</html>