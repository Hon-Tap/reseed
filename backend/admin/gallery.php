<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

// --- Data Fetching ---
$search = $_GET['search'] ?? '';

// Using ILIKE for Postgres
$query = "SELECT * FROM gallery 
          WHERE caption ILIKE ? OR category ILIKE ? 
          ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%", "%$search%"]);
$images = $stmt->fetchAll();

$uploadUrl = '../uploads/gallery/';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Media Gallery</h1>
                <p class="text-slate-500 mt-2 font-medium">Manage visual assets and photographs.</p>
            </div>
            <a href="gallery_add.php" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-200">
                <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload Image
            </a>
        </div>

        <div class="mb-8 max-w-md">
            <form method="GET" class="relative group">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search caption or category..." 
                       class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition shadow-sm">
                <div class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold tracking-widest">
                            <th class="px-8 py-5">Preview</th>
                            <th class="px-8 py-5">Image Details</th>
                            <th class="px-8 py-5">Category</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if ($images): foreach($images as $img): 
                            $hasImage = !empty($img['filename']);
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-6 w-32">
                                <?php if($hasImage): ?>
                                    <div class="w-24 h-24 rounded-xl overflow-hidden shadow-sm border border-slate-100 group relative">
                                        <img src="<?= $uploadUrl . htmlspecialchars($img['filename']) ?>" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                    </div>
                                <?php else: ?>
                                    <div class="w-24 h-24 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 border border-slate-200">
                                        <i class="fa-solid fa-image text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-800 text-lg">
                                    <?= htmlspecialchars($img['caption'] ?: 'Untitled Image') ?>
                                </div>
                                <div class="text-xs font-mono text-slate-400 mt-1.5 bg-slate-100 inline-block px-2 py-1 rounded-md">
                                    <?= htmlspecialchars($img['filename']) ?>
                                </div>
                                <div class="text-xs text-slate-400 mt-2">
                                    Uploaded: <?= date('M d, Y', strtotime($img['created_at'])) ?>
                                </div>
                            </td>

                            <td class="px-8 py-6">
                                <span class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 rounded-lg">
                                    <?= htmlspecialchars($img['category'] ?: 'Uncategorized') ?>
                                </span>
                            </td>

                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="gallery_edit.php?id=<?= $img['id'] ?>" class="w-10 h-10 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    
                                    <form action="handlers/gallery-handler.php" method="POST" onsubmit="return confirm('Delete this image permanently?');" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                        <input type="hidden" name="delete" value="1">
                                        <button type="submit" class="w-10 h-10 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-xl transition">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" class="p-16 text-center text-slate-400 font-medium">No images found in gallery.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>