<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin — Gallery Management (Cloudinary Optimized)
|--------------------------------------------------------------------------
*/

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT id, filename, caption, category, created_at
    FROM gallery
    WHERE caption ILIKE :search
       OR category ILIKE :search
    ORDER BY created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => '%' . $search . '%']);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

/**
 * Cloudinary-aware URL helper
 */
function getGalleryUrl(string $filename): string {
    return (strpos($filename, 'http') === 0) 
        ? $filename 
        : UPLOADS_URL . '/gallery/' . $filename;
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="min-h-screen bg-slate-50/50 p-4 md:p-10">
    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Media Vault</h1>
                <p class="text-slate-500 font-medium mt-1">Manage Cloudinary assets and gallery categories.</p>
            </div>

            <a href="gallery_add.php" 
               class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl transition shadow-lg shadow-emerald-200">
                <i class="fa-solid fa-plus-circle mr-2"></i>
                Bulk Upload
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="md:col-span-2">
                <form method="GET" class="relative">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                           placeholder="Search gallery..."
                           class="w-full pl-12 pr-4 py-4 bg-white border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    <div class="absolute left-4 top-4 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                </form>
            </div>
            <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                <span class="text-slate-500 font-bold uppercase text-[10px] tracking-widest">Total Assets</span>
                <span class="text-2xl font-black text-emerald-600"><?= count($images) ?></span>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-black tracking-widest">
                            <th class="px-8 py-5 text-center">Preview</th>
                            <th class="px-8 py-5">Metadata</th>
                            <th class="px-8 py-5">Category</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if ($images): foreach ($images as $img): 
                            $url = getGalleryUrl($img['filename']);
                        ?>
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6 w-32">
                                    <div class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-white shadow-md bg-slate-100 rotate-1 group-hover:rotate-0 transition-transform">
                                        <img src="<?= htmlspecialchars($url) ?>" class="w-full h-full object-cover">
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-800 text-lg"><?= htmlspecialchars($img['caption'] ?: 'Untitled Asset') ?></div>
                                    <div class="text-[10px] text-slate-400 mt-1 font-mono break-all max-w-xs">
                                        <?= basename($img['filename']) ?>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-black uppercase tracking-tighter">
                                        <?= htmlspecialchars($img['category'] ?: 'General') ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="gallery_edit.php?id=<?= $img['id'] ?>" 
                                           class="w-10 h-10 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="handlers/gallery-handler.php" method="POST" onsubmit="return confirm('Delete permanently?');">
                                            <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                            <input type="hidden" name="delete" value="1">
                                            <button class="w-10 h-10 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-xl transition">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="4" class="p-20 text-center text-slate-400 font-bold italic">No media found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>