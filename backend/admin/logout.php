<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/includes/csrf.php';

/* Guard */
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

/* Logout */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        header('Location: login.php?error=csrf');
        exit;
    }

    $_SESSION = [];
    session_unset();
    session_destroy();

    header('Location: login.php');
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
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center">

<div class="bg-white rounded-3xl shadow-xl p-8 max-w-md w-full text-center">
    <img src="<?= BASE_URL ?>/assets/images/reseed-logo.svg" class="h-14 mx-auto mb-6" alt="ReSEED">

    <h2 class="text-xl font-bold mb-2">Sign out?</h2>
    <p class="text-gray-500 mb-6">You will be logged out of the admin panel.</p>

    <form method="post" class="space-y-3">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <button class="w-full bg-red-600 text-white py-3 rounded-xl hover:bg-red-700">
            Yes, sign me out
        </button>

        <a href="dashboard.php" class="block w-full py-3 rounded-xl bg-gray-100 hover:bg-gray-200">
            Stay logged in
        </a>
    </form>
</div>

</body>
</html>
