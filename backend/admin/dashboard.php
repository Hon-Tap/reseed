<?php
declare(strict_types=1);

// 1. Correct Pathing: Go up one level to reach 'backend' root
$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

/* ==================================================
   DASHBOARD METRICS (Optimized for PostgreSQL)
================================================== */
try {
    // Single trip to the database for all counts
    $sql = "SELECT 
        (SELECT COUNT(*) FROM projects) as projects_count,
        (SELECT COUNT(*) FROM posts) as posts_count,
        (SELECT COUNT(*) FROM contacts) as contacts_count,
        (SELECT COUNT(*) FROM users) as admins_count";
    
    $statsData = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    
    $stats = [
        'projects' => (int)($statsData['projects_count'] ?? 0),
        'posts'    => (int)($statsData['posts_count'] ?? 0),
        'contacts' => (int)($statsData['contacts_count'] ?? 0),
        'admins'   => (int)($statsData['admins_count'] ?? 0),
    ];

    // Fetch 5 most recent inquiries
    $recentInquiries = $pdo->query("SELECT name, email, created_at FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll();

} catch (Throwable $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $stats = ['projects' => 0, 'posts' => 0, 'contacts' => 0, 'admins' => 0];
    $recentInquiries = [];
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="min-h-screen bg-gray-50 p-4 md:p-8">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Welcome back, <span class="text-green-600"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
                </h1>
                <p class="text-gray-500 mt-1">Here is what is happening with <span class="font-semibold text-gray-700">ReSEED</span> today.</p>
            </div>
            <div class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-600">
                <i class="fa-regular fa-calendar mr-2 text-green-500"></i>
                <?= date('F d, Y') ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-green-100 rounded-lg text-green-600"><i class="fa-solid fa-leaf text-xl"></i></div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Projects</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['projects'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 rounded-lg text-blue-600"><i class="fa-solid fa-pen-nib text-xl"></i></div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Blog Posts</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['posts'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-amber-100 rounded-lg text-amber-600"><i class="fa-solid fa-envelope text-xl"></i></div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Inquiries</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['contacts'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-purple-100 rounded-lg text-purple-600"><i class="fa-solid fa-user-shield text-xl"></i></div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Team</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['admins'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fa-solid fa-bolt mr-2 text-yellow-500"></i> Quick Actions
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="projects.php" class="group p-5 bg-white border border-gray-100 rounded-2xl shadow-sm hover:border-green-500 hover:bg-green-50 transition-all">
                        <i class="fa-solid fa-folder-plus text-green-500 text-2xl mb-3 block"></i>
                        <h4 class="font-bold text-gray-900 group-hover:text-green-700">Manage Projects</h4>
                        <p class="text-sm text-gray-500">Add or edit restoration progress.</p>
                    </a>
                    <a href="posts.php" class="group p-5 bg-white border border-gray-100 rounded-2xl shadow-sm hover:border-blue-500 hover:bg-blue-50 transition-all">
                        <i class="fa-solid fa-plus text-blue-500 text-2xl mb-3 block"></i>
                        <h4 class="font-bold text-gray-900 group-hover:text-blue-700">New Blog Post</h4>
                        <p class="text-sm text-gray-500">Share your latest success story.</p>
                    </a>
                    <a href="contacts.php" class="group p-5 bg-white border border-gray-100 rounded-2xl shadow-sm hover:border-amber-500 hover:bg-amber-50 transition-all">
                        <i class="fa-solid fa-inbox text-amber-500 text-2xl mb-3 block"></i>
                        <h4 class="font-bold text-gray-900 group-hover:text-amber-700">Check Inbox</h4>
                        <p class="text-sm text-gray-500">Read and respond to messages.</p>
                    </a>
                    <a href="users.php" class="group p-5 bg-white border border-gray-100 rounded-2xl shadow-sm hover:border-purple-500 hover:bg-purple-50 transition-all">
                        <i class="fa-solid fa-users-cog text-purple-500 text-2xl mb-3 block"></i>
                        <h4 class="font-bold text-gray-900 group-hover:text-purple-700">Team Settings</h4>
                        <p class="text-sm text-gray-500">Manage administrative permissions.</p>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-1">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left mr-2 text-gray-400"></i> Recent Messages
                </h3>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <?php if (empty($recentInquiries)): ?>
                        <div class="p-8 text-center">
                            <i class="fa-solid fa-ghost text-gray-200 text-4xl mb-2"></i>
                            <p class="text-gray-400 text-sm">No recent inquiries.</p>
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-gray-50">
                            <?php foreach($recentInquiries as $msg): ?>
                                <div class="p-4 hover:bg-gray-50 transition-colors">
                                    <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars((string)$msg['name']) ?></p>
                                    <p class="text-xs text-gray-500 mb-1"><?= htmlspecialchars((string)$msg['email']) ?></p>
                                    <span class="text-[10px] font-medium px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full">
                                        <?= date('M d, g:i a', strtotime((string)$msg['created_at'])) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="contacts.php" class="block text-center py-3 bg-gray-50 text-xs font-bold text-gray-500 hover:text-green-600 transition-colors uppercase tracking-widest">
                            View All Messages
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Fixed footer requirement using $backendPath to avoid the previous error
require_once $backendPath . '/admin/includes/admin_footer.php'; 
?>