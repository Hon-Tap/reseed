<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 2);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';




$search = $_GET['search'] ?? '';
$query = "SELECT * FROM posts WHERE title ILIKE ? ORDER BY created_at DESC"; // ILIKE for Postgres
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%"]);
$posts = $stmt->fetchAll();
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Project Updates</h1>
                <p class="text-slate-500 mt-2 font-medium">Draft and publish field reports and news stories.</p>
            </div>
            <a href="posts_add.php" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-200">
                <i class="fa-solid fa-plus mr-2"></i> Create Update
            </a>
        </div>

        <div class="mb-8 max-w-md">
            <form method="GET" class="relative group">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search updates..." 
                       class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition shadow-sm">
                <div class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold tracking-widest">
                        <th class="px-8 py-5">Media</th>
                        <th class="px-8 py-5">Article Content</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($posts): foreach($posts as $p): 
                        $statusColor = match(strtolower($p['status'] ?? '')) {
                            'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                            'ongoing'   => 'bg-blue-50 text-blue-600 border-blue-100',
                            default     => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <?php if($p['media_type'] == 'image'): ?>
                                <img src="../uploads/posts/<?= $p['cover_image'] ?>" class="w-14 h-14 object-cover rounded-xl shadow-sm border border-slate-100">
                            <?php else: ?>
                                <div class="w-14 h-14 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 border border-slate-200">
                                    <i class="fa-solid fa-newspaper text-xl"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-8 py-6">
                            <div class="font-bold text-slate-800 text-lg hover:text-emerald-600 transition-colors cursor-pointer"><?= htmlspecialchars($p['title']) ?></div>
                            <div class="flex items-center gap-3 mt-1.5">
                                <span class="text-xs font-semibold text-slate-500 flex items-center gap-1">
                                    <i class="fa-regular fa-user text-[10px]"></i> <?= htmlspecialchars($p['author']) ?>
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="text-xs font-medium text-slate-400">
                                    <?= date('M d, Y', strtotime($p['published_at'])) ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full border <?= $statusColor ?>">
                                <?= $p['status'] ?>
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="posts_edit.php?id=<?= $p['id'] ?>" class="w-10 h-10 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <a href="handlers/post-handler.php?delete=<?= $p['id'] ?>" 
                                   onclick="return confirm('Delete this article?')" 
                                   class="w-10 h-10 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-xl transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4" class="p-16 text-center text-slate-400 font-medium">No results found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>