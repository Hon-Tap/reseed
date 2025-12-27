<?php

declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

/*
|--------------------------------------------------------------------------
| Fetch gallery items
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT id, filename, caption, category, created_at
    FROM gallery
    ORDER BY created_at DESC
");

$images     = $stmt->fetchAll(PDO::FETCH_ASSOC);
$uploadPath = UPLOAD_URL . '/gallery/';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                    Gallery
                </h1>
                <p class="text-slate-500 mt-2 font-medium">
                    Manage images and visual media.
                </p>
            </div>

            <a
                href="gallery_add.php"
                class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700
                       text-white font-bold rounded-xl transition shadow-lg shadow-emerald-200"
            >
                <i class="fa-solid fa-cloud-arrow-up mr-2"></i>
                Upload Image
            </a>
        </div>

        <!-- Content -->
        <?php if (!$images): ?>

            <!-- Empty State -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/60 p-16 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <i class="fa-solid fa-images text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-700 mb-1">
                    No images yet
                </h3>
                <p class="text-slate-400">
                    Upload your first gallery image to get started.
                </p>
            </div>

        <?php else: ?>

            <!-- Table -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold tracking-widest">
                            <th class="px-8 py-5">Preview</th>
                            <th class="px-8 py-5">Details</th>
                            <th class="px-8 py-5">Category</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                    <?php foreach ($images as $g): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Preview -->
                            <td class="px-8 py-6">
                                <img
                                    src="<?= $uploadPath . htmlspecialchars($g['filename']) ?>"
                                    alt=""
                                    class="w-20 h-14 object-cover rounded-xl shadow-sm border border-slate-100"
                                >
                            </td>

                            <!-- Details -->
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-800 text-lg">
                                    <?= htmlspecialchars($g['caption'] ?: 'Untitled Image') ?>
                                </div>
                                <div class="mt-1 text-xs text-slate-400">
                                    <code><?= htmlspecialchars($g['filename']) ?></code>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full
                                             bg-slate-100 text-slate-600 border border-slate-200">
                                    <?= htmlspecialchars($g['category'] ?: 'Uncategorized') ?>
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <a
                                        href="gallery_edit.php?id=<?= $g['id'] ?>"
                                        class="w-10 h-10 flex items-center justify-center
                                               text-blue-500 hover:bg-blue-50 rounded-xl transition"
                                    >
                                        <i class="fa-solid fa-edit"></i>
                                    </a>

                                    <form
                                        action="handlers/gallery-handler.php"
                                        method="post"
                                        onsubmit="return confirm('Delete this image permanently?')"
                                    >
                                        <input type="hidden" name="id" value="<?= $g['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <button
                                            name="delete"
                                            class="w-10 h-10 flex items-center justify-center
                                                   text-rose-500 hover:bg-rose-50 rounded-xl transition"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </div>
</div>
