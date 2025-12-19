<?php
declare(strict_types=1);

include "includes/admin_auth.php";
include "../includes/config.php";
include "includes/admin_header.php";

$error = $success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = "An admin with this username already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare(
                "INSERT INTO users (username, password_hash, role, created_at)
                 VALUES (?, ?, 'admin', NOW())"
            )->execute([$username, $hash]);

            $success = "Admin account created successfully.";
        }
    }
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-6 bg-gray-50 min-h-screen">
  <div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-3xl font-bold text-gray-800">Create Admin</h2>
      <p class="text-gray-500 text-sm">
        Add a trusted user with full administrative access.
      </p>
    </div>

    <!-- Alerts -->
    <?php if ($error): ?>
      <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700 text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 text-sm">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <!-- Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <form method="post" class="space-y-5">

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Username
          </label>
          <input
            type="text"
            name="username"
            required
            class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none"
          >
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">
            Password
          </label>
          <input
            type="password"
            name="password"
            required
            class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none"
          >
          <p class="text-xs text-gray-400 mt-1">
            Choose a strong password. Admins have full system access.
          </p>
        </div>

        <button
          type="submit"
          class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition"
        >
          Create Admin
        </button>

      </form>
    </div>

  </div>
</div>

<?php include "includes/admin_footer.php"; ?>
