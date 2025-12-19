<?php
include "includes/admin_auth.php";
include "../includes/config.php";
include "includes/admin_header.php";

$stmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
$images = $stmt->fetchAll();
$uploadPath = '../uploads/gallery/';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Gallery Manager</h2>
                <p class="text-gray-500 text-sm">Organize and manage your site's visual library.</p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-3">
                <a href="gallery_add.php" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Upload Images
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <?php if (empty($images)): ?>
                <div class="py-20 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900">Gallery is empty</h3>
                    <p class="text-sm text-gray-500">Start uploading images to showcase your impact.</p>
                </div>
            <?php else: ?>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-200 text-gray-600 text-[11px] uppercase font-bold tracking-widest">
                            <th class="px-6 py-4">Preview</th>
                            <th class="px-6 py-4">Image Details</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php foreach ($images as $g): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <img src="<?= $uploadPath . htmlspecialchars($g['filename']) ?>" 
                                     class="h-14 w-20 object-cover rounded shadow-sm border border-gray-200">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800"><?= $g['caption'] ?: 'Untitled' ?></div>
                                <div class="text-[10px] font-mono text-gray-400"><?= htmlspecialchars($g['filename']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-[10px] font-bold uppercase rounded bg-blue-50 text-blue-600 border border-blue-100">
                                    <?= htmlspecialchars($g['category']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-1">
                                    <a href="gallery_edit.php?id=<?= $g['id'] ?>" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <a href="handlers/gallery-handler.php?delete=<?= $g['id'] ?>" 
                                       onclick="return confirm('Delete this image?')"
                                       class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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

<?php include "includes/admin_footer.php"; ?>