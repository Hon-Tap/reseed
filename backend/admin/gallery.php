<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM gallery WHERE caption ILIKE ? OR category ILIKE ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%", "%$search%"]);
$images = $stmt->fetchAll();

$uploadUrl = '../uploads/gallery/';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Media Gallery</h1>
                <p class="text-gray-500 mt-2 font-medium">Manage visual assets and photographs.</p>
            </div>
            <a href="gallery_add.php" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition shadow-lg shadow-green-100">
                <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload Image
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 text-[11px] uppercase font-bold tracking-widest">
                        <th class="px-8 py-5">Preview</th>
                        <th class="px-8 py-5">Image Details</th>
                        <th class="px-8 py-5">Category</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if ($images): foreach($images as $img): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="w-20 h-20 rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                                <img src="<?= $uploadUrl . htmlspecialchars($img['filename']) ?>" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="font-bold text-gray-800"><?= htmlspecialchars($img['caption'] ?: 'Untitled') ?></div>
                            <div class="text-[10px] font-mono text-gray-400 mt-1 uppercase"><?= htmlspecialchars($img['filename']) ?></div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 text-xs font-bold text-gray-600 bg-gray-100 rounded-lg"><?= htmlspecialchars($img['category'] ?: 'General') ?></span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="gallery_edit.php?id=<?= $img['id'] ?>" class="w-10 h-10 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition"><i class="fa-solid fa-pen"></i></a>
                                <form action="handlers/gallery-handler.php" method="POST" onsubmit="return confirm('Delete image?')" class="inline">
                                    <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                    <button type="submit" name="delete" class="w-10 h-10 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-xl transition"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4" class="p-16 text-center text-gray-400 font-medium">No images found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>