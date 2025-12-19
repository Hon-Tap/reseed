<?php
declare(strict_types=1);

session_start();

/* Perform logout */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_destroy();
    header("Location: login.php");
    exit;
}

/* If accessed directly without session, redirect */
if (empty($_SESSION)) {
    header("Location: login.php");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Confirm Logout</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://cdn.tailwindcss.com"></script>

<style>
/* Blur background */
.bg-blur {
  filter: blur(6px) brightness(0.9);
  transform: scale(1.02);
}
</style>
</head>

<body class="bg-gray-100 min-h-screen">

<!-- ================= BACKGROUND (DASHBOARD SNAPSHOT) ================= -->
<div class="fixed inset-0 bg-gray-100 bg-blur"></div>

<!-- ================= MODAL OVERLAY ================= -->
<div class="fixed inset-0 flex items-center justify-center z-50">

  <!-- Overlay -->
  <div class="absolute inset-0 bg-black/40"></div>

  <!-- Modal -->
  <div class="relative bg-white rounded-2xl shadow-xl border border-gray-200 max-w-md w-full mx-4 p-8 text-center animate-fadeIn">

    <div class="mx-auto w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mb-4">
      <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
      </svg>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 mb-2">
      Sign out?
    </h2>

    <p class="text-gray-500 text-sm mb-6">
      You’ll be logged out of the admin dashboard.  
      You can sign back in anytime.
    </p>

    <form method="post" class="space-y-3">
      <button
        type="submit"
        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition"
      >
        Yes, Sign me out
      </button>

      <a
        href="dashboard.php"
        class="block w-full py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition"
      >
        Cancel and stay
      </a>
    </form>

  </div>
</div>

</body>
</html>
