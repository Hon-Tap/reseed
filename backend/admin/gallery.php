<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 2);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';


$stmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
$uploadPath = UPLOAD_URL . '/gallery/';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Gallery Library</h1>
                <p class="text-slate-500 mt-2 font-medium">Manage the visual storytelling assets for ReSEED.</p>
            </div>
            <a href="gallery_add.php" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-200">
                <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload Images
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200 overflow-hidden">
            <?php if (empty($images)): ?>
                <div class="py-24 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                        <i class="fa-solid fa-images text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">No images found</h3>
                    <p class="text-slate-400 mt-2">Start your collection by uploading your first impact photo.</p>
                </div>
            <?php else: ?>
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold tracking-[0.15em]">
                            <th class="px-8 py-5">Preview</th>
                            <th class="px-8 py-5">Details</th>
                            <th class="px-8 py-5">Category</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($images as $g): ?>
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <div class="relative w-24 h-16 overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                                    <img src="<?= $uploadPath . htmlspecialchars($g['filename']) ?>" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="font-bold text-slate-800 text-base"><?= $g['caption'] ?: 'Untitled Image' ?></div>
                                <div class="text-[11px] font-mono text-slate-400 mt-1 uppercase tracking-tighter"><?= htmlspecialchars($g['filename']) ?></div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="inline-block px-3 py-1 text-[10px] font-black uppercase rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100">
                                    <?= htmlspecialchars($g['category']) ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="gallery_edit.php?id=<?= $g['id'] ?>" class="w-10 h-10 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="handlers/gallery-handler.php?delete=<?= $g['id'] ?>" 
                                       onclick="return confirm('Permanently delete this image?')"
                                       class="w-10 h-10 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-xl transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>