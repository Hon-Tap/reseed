<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM posts WHERE title ILIKE ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%"]);
$posts = $stmt->fetchAll();
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Project Updates</h1>
                <p class="text-gray-500 mt-2">Draft and publish field reports and news stories.</p>
            </div>
            <a href="posts_add.php" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition shadow-lg shadow-green-100">
                <i class="fa-solid fa-plus mr-2"></i> Create Update
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 text-[11px] uppercase font-bold tracking-widest">
                        <th class="px-8 py-5">Media</th>
                        <th class="px-8 py-5">Article Content</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if ($posts): foreach($posts as $p): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <?php if(!empty($p['cover_image'])): ?>
                                <img src="../uploads/posts/<?= $p['cover_image'] ?>" class="w-14 h-14 object-cover rounded-xl border border-gray-100 shadow-sm">
                            <?php else: ?>
                                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 border border-gray-200">
                                    <i class="fa-solid fa-newspaper text-xl"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-8 py-6">
                            <div class="font-bold text-gray-800 text-lg"><?= htmlspecialchars($p['title']) ?></div>
                            <div class="flex items-center gap-3 mt-1.5">
                                <span class="text-xs font-semibold text-gray-500"><i class="fa-regular fa-user mr-1"></i> <?= htmlspecialchars($p['author']) ?></span>
                                <span class="text-gray-300">•</span>
                                <span class="text-xs font-medium text-gray-400"><?= date('M d, Y', strtotime($p['published_at'])) ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="posts_edit.php?id=<?= $p['id'] ?>" class="w-10 h-10 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition"><i class="fa-solid fa-edit"></i></a>
                                <form action="handlers/post-handler.php" method="POST" onsubmit="return confirm('Delete this article?')" class="inline">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" name="delete" class="w-10 h-10 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-xl transition"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="3" class="p-16 text-center text-gray-400 font-medium">No results found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>