<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin – Posts Management (Cloudinary Optimized)
|--------------------------------------------------------------------------
*/

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

/*
|--------------------------------------------------------------------------
| Fetch Posts with Search logic
|--------------------------------------------------------------------------
*/
$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT id, title, author, cover_image, media_type, published_at, created_at, featured
    FROM posts
    WHERE title ILIKE :search
    ORDER BY COALESCE(published_at, created_at) DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => '%' . $search . '%']);
$posts = $stmt->fetchAll();

/**
 * Helper to determine the correct thumbnail URL
 */
function getAdminThumb(array $post): ?string {
    $img = $post['cover_image'];
    if (empty($img)) return null;
    // If it's a Cloudinary/External URL
    if (strpos($img, 'http') === 0) return $img;
    // Fallback to local
    return UPLOADS_URL . '/posts/' . $img;
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="min-h-screen bg-slate-50/50 p-4 md:p-10">
    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <nav class="flex text-sm text-slate-400 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="dashboard.php" class="hover:text-emerald-600">Dashboard</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                        <li class="text-slate-600 font-medium">Journal Posts</li>
                    </ol>
                </nav>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    Field Journal
                    <span class="bg-emerald-100 text-emerald-700 text-xs uppercase tracking-widest px-3 py-1 rounded-full font-bold">
                        <?= count($posts) ?> Total
                    </span>
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <a href="posts_add.php" 
                   class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 
                          text-white font-bold rounded-2xl transition-all shadow-lg shadow-emerald-200 active:scale-95">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Write New Story
                </a>
            </div>
        </div>

        <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-200 mb-8 flex flex-col md:flex-row gap-4 justify-between items-center">
            <form method="GET" class="relative w-full md:max-w-md">
                <input 
                    type="text" 
                    name="search" 
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search by title..."
                    class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-emerald-500 transition outline-none"
                >
                <div class="absolute left-4 top-3.5 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </form>
            
            <div class="flex gap-2 text-sm">
                <span class="text-slate-400">Quick Filter:</span>
                <button class="font-bold text-emerald-600 underline underline-offset-4">All</button>
                <button class="text-slate-500 hover:text-emerald-600 px-2">Published</button>
                <button class="text-slate-500 hover:text-emerald-600 px-2">Drafts</button>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-black tracking-[0.15em]">
                            <th class="px-8 py-5">Article Preview</th>
                            <th class="px-8 py-5">Details & Author</th>
                            <th class="px-8 py-5">Status</th>
                            <th class="px-8 py-5 text-right">Management</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        <?php if ($posts): foreach ($posts as $p): 
                            $thumb = getAdminThumb($p);
                            $isPublished = !empty($p['published_at']) && strtotime($p['published_at']) <= time();
                            $isScheduled = !empty($p['published_at']) && strtotime($p['published_at']) > time();
                        ?>
                            <tr class="group hover:bg-slate-50/50 transition-all">
                                
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="relative w-20 h-20 shrink-0">
                                            <?php if ($thumb): ?>
                                                <?php if (($p['media_type'] ?? 'image') === 'video'): ?>
                                                    <video src="<?= $thumb ?>" class="w-full h-full object-cover rounded-2xl shadow-sm bg-slate-200" muted></video>
                                                    <div class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-2xl">
                                                        <i class="fa-solid fa-play text-white text-xs"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <img src="<?= $thumb ?>" class="w-full h-full object-cover rounded-2xl shadow-sm bg-slate-200">
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <div class="w-full h-full bg-slate-100 rounded-2xl flex items-center justify-center text-slate-300">
                                                    <i class="fa-solid fa-image text-2xl"></i>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($p['featured']): ?>
                                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-amber-400 text-white rounded-full flex items-center justify-center shadow-sm border-2 border-white" title="Featured Post">
                                                    <i class="fa-solid fa-star text-[10px]"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="max-w-xs">
                                            <div class="font-black text-slate-900 leading-tight group-hover:text-emerald-700 transition-colors">
                                                <?= htmlspecialchars($p['title']) ?>
                                            </div>
                                            <div class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-bold">
                                                ID: #<?= $p['id'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="flex items-center gap-2 font-bold text-slate-700 text-sm">
                                            <i class="fa-solid fa-user-nib text-slate-300"></i>
                                            <?= htmlspecialchars($p['author'] ?: 'Team ReSEED') ?>
                                        </span>
                                        <span class="text-xs text-slate-400 mt-1">
                                            Created: <?= date('M d, Y', strtotime($p['created_at'])) ?>
                                        </span>
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <?php if ($isPublished): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Live
                                        </span>
                                    <?php elseif ($isScheduled): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-wide">
                                            <i class="fa-regular fa-clock"></i>
                                            Scheduled
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-wide">
                                            Draft
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($p['published_at']): ?>
                                        <div class="text-[10px] text-slate-400 mt-1 font-medium">
                                            <?= date('M d, Y', strtotime($p['published_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="/post.php?slug=<?= $p['slug'] ?>" target="_blank" 
                                           class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all"
                                           title="Preview Online">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                                        </a>

                                        <a href="posts_edit.php?id=<?= $p['id'] ?>" 
                                           class="w-9 h-9 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition-all"
                                           title="Edit Content">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>

                                        <form action="handlers/post-handler.php" method="POST" 
                                              onsubmit="return confirm('Archive this post? It will be permanently removed.');"
                                              class="inline">
                                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="delete" value="1">
                                            <button type="submit" 
                                                    class="w-9 h-9 flex items-center justify-center text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="4" class="py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mb-4">
                                            <i class="fa-solid fa-folder-open text-4xl"></i>
                                        </div>
                                        <h3 class="text-slate-900 font-bold">No stories found</h3>
                                        <p class="text-slate-400 text-sm max-w-xs mx-auto">Try adjusting your search or create your first journal entry today.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="bg-slate-50/50 px-8 py-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-400 font-bold uppercase tracking-widest">
                <span>Showing <?= count($posts) ?> Articles</span>
                <div class="flex gap-4">
                    <button disabled class="opacity-50 cursor-not-allowed">Previous</button>
                    <button disabled class="opacity-50 cursor-not-allowed">Next</button>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>