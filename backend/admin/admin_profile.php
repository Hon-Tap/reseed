<?php
declare(strict_types=1);

/**
 * Admin Profile Management
 * Update username & password securely
 */

require_once __DIR__ . '/includes/admin_header.php';

$adminId = $_SESSION['admin_id'] ?? null;
if (!$adminId) {
    header('Location: ' . admin_url('login.php'));
    exit;
}

// Fetch current admin
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die('Admin not found.');
}

// Flash messages
$success = $_SESSION['admin_success'] ?? null;
$error   = $_SESSION['admin_error'] ?? null;
unset($_SESSION['admin_success'], $_SESSION['admin_error']);
?>

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-xl mx-auto">

        <h2 class="text-3xl font-bold text-gray-800 mb-2">Admin Profile</h2>
        <p class="text-gray-500 text-sm mb-8">
            Manage your account details and change your password.
        </p>

        <?php if ($success): ?>
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- ================= UPDATE USERNAME ================= -->

        <form method="POST"
              action="<?= admin_url('handlers/admin-handler.php') ?>"
              class="bg-white p-6 rounded-xl shadow-sm border mb-8">

            <h3 class="text-lg font-semibold mb-4">Update Username</h3>

            <label class="block text-sm font-medium mb-1">Username</label>
            <input type="text"
                   name="username"
                   value="<?= htmlspecialchars($admin['username']) ?>"
                   required
                   class="w-full px-3 py-2 border rounded-lg mb-4">

            <button type="submit"
                    name="update_username"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                Save Username
            </button>
        </form>

        <!-- ================= CHANGE PASSWORD ================= -->

        <form method="POST"
              action="<?= admin_url('handlers/admin-handler.php') ?>"
              class="bg-white p-6 rounded-xl shadow-sm border">

            <h3 class="text-lg font-semibold mb-4">Change Password</h3>

            <label class="block text-sm font-medium mb-1">Current Password</label>
            <input type="password"
                   name="current_password"
                   required
                   class="w-full px-3 py-2 border rounded-lg mb-4">

            <label class="block text-sm font-medium mb-1">New Password</label>
            <input type="password"
                   name="new_password"
                   required
                   class="w-full px-3 py-2 border rounded-lg mb-4">

            <label class="block text-sm font-medium mb-1">Confirm New Password</label>
            <input type="password"
                   name="confirm_password"
                   required
                   class="w-full px-3 py-2 border rounded-lg mb-6">

            <button type="submit"
                    name="update_password"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                Update Password
            </button>
        </form>

    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
