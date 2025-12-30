<?php
declare(strict_types=1);

/**
 * Admin Profile & Security Settings
 */

require_once __DIR__ . '/includes/admin_header.php';

$adminId = $_SESSION['admin_id'] ?? null;
if (!$adminId) {
    header('Location: ' . admin_url('login.php'));
    exit;
}

// Fetch admin
$stmt = $pdo->prepare("
    SELECT 
        username,
        password_updated_at,
        two_factor_enabled
    FROM users 
    WHERE id = ?
");
$stmt->execute([$adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die('Admin not found.');
}

// Avatar initials
$initials = strtoupper(substr($admin['username'], 0, 2));

// Password last updated
$lastPasswordChange = $admin['password_updated_at']
    ? date('F j, Y \a\t H:i', strtotime($admin['password_updated_at']))
    : 'Never updated';

// Flash messages
$success = $_SESSION['admin_success'] ?? null;
$error   = $_SESSION['admin_error'] ?? null;
unset($_SESSION['admin_success'], $_SESSION['admin_error']);
?>

<div class="min-h-screen bg-slate-100 py-10 px-4">
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- HEADER -->
        <div class="flex items-center gap-5">
            <div class="h-16 w-16 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xl font-bold shadow">
                <?= htmlspecialchars($initials) ?>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Admin Profile</h1>
                <p class="text-slate-500">
                    Manage identity, security, and authentication settings
                </p>
            </div>
        </div>

        <!-- ALERTS -->
        <?php if ($success): ?>
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-800">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- ================= ACCOUNT ================= -->
        <section class="bg-white rounded-2xl border shadow-sm p-6">
            <h2 class="text-xl font-semibold text-slate-800 mb-6">
                Account Information
            </h2>

            <form method="POST" action="<?= admin_url('handlers/admin-handler.php') ?>">
                <label class="block text-sm font-medium text-slate-600 mb-1">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    value="<?= htmlspecialchars($admin['username']) ?>"
                    required
                    class="w-full max-w-md rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                >

                <div class="mt-4 flex justify-end">
                    <button
                        type="submit"
                        name="update_username"
                        class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition"
                    >
                        Save Username
                    </button>
                </div>
            </form>
        </section>

        <!-- ================= SECURITY ================= -->
        <section class="bg-white rounded-2xl border shadow-sm p-6">
            <h2 class="text-xl font-semibold text-slate-800 mb-2">
                Security
            </h2>

            <p class="text-sm text-slate-500 mb-4">
                Last password change:
                <span class="font-medium text-slate-700">
                    <?= $lastPasswordChange ?>
                </span>
            </p>

            <form method="POST" action="<?= admin_url('handlers/admin-handler.php') ?>" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        Current Password
                    </label>
                    <input
                        type="password"
                        name="current_password"
                        required
                        class="w-full max-w-md rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        New Password
                    </label>
                    <input
                        type="password"
                        name="new_password"
                        required
                        class="w-full max-w-md rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500"
                    >

                    <!-- Password strength hint -->
                    <p class="mt-2 text-xs text-slate-500 max-w-md">
                        Password must be at least <strong>8 characters</strong>,
                        include <strong>uppercase</strong>, <strong>lowercase</strong>,
                        <strong>numbers</strong>, and <strong>symbols</strong>.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        Confirm New Password
                    </label>
                    <input
                        type="password"
                        name="confirm_password"
                        required
                        class="w-full max-w-md rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500"
                    >
                </div>

                <div class="pt-4 flex justify-end">
                    <button
                        type="submit"
                        name="update_password"
                        class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-black transition"
                    >
                        Update Password
                    </button>
                </div>
            </form>
        </section>

        <!-- ================= 2FA ================= -->
        <section class="bg-white rounded-2xl border shadow-sm p-6">
            <h2 class="text-xl font-semibold text-slate-800 mb-2">
                Two-Factor Authentication (2FA)
            </h2>

            <p class="text-sm text-slate-500 mb-4">
                Add an extra layer of protection to your admin account.
            </p>

            <form method="POST" action="<?= admin_url('handlers/admin-handler.php') ?>" class="flex items-center justify-between max-w-xl">

                <div>
                    <p class="font-medium text-slate-700">
                        Status:
                        <?php if ($admin['two_factor_enabled']): ?>
                            <span class="text-emerald-600">Enabled</span>
                        <?php else: ?>
                            <span class="text-red-600">Disabled</span>
                        <?php endif; ?>
                    </p>

                    <p class="text-xs text-slate-500 mt-1">
                        Recommended for all admin users.
                    </p>
                </div>

                <button
                    type="submit"
                    name="<?= $admin['two_factor_enabled'] ? 'disable_2fa' : 'enable_2fa' ?>"
                    class="rounded-lg px-4 py-2 text-sm font-semibold text-white
                        <?= $admin['two_factor_enabled']
                            ? 'bg-red-600 hover:bg-red-700'
                            : 'bg-emerald-600 hover:bg-emerald-700' ?>
                        transition"
                >
                    <?= $admin['two_factor_enabled'] ? 'Disable 2FA' : 'Enable 2FA' ?>
                </button>
            </form>
        </section>

    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
