<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · GALLERY MANAGEMENT (DB + CLOUDINARY ALIGNED)
|--------------------------------------------------------------------------
*/

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

/*
|--------------------------------------------------------------------------
| INPUT
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');

/*
|--------------------------------------------------------------------------
| FETCH GALLERY (POSTGRES SAFE)
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        filename,
        caption,
        category,
        created_at
    FROM gallery
    WHERE (:search = '' 
           OR caption ILIKE :q 
           OR category ILIKE :q)
    ORDER BY created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'search' => $search,
    'q'      => '%' . $search . '%',
]);

$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function galleryThumb(string $url): string
{
    if (str_contains($url, 'cloudinary.com')) {
        return str_replace(
            '/upload/',
            '/upload/w_200,h_200,c_fill,g_auto,q_auto,f_auto/',
            $url
        );
    }
    return $url;
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="min-h-screen bg-slate-50/50 p-4 md:p-10">
<div class="max-w-7xl mx-auto">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Media Vault</h1>
            <p class="text-slate-500 font-medium mt-1">
                Manage Cloudinary assets and gallery categories.
            </p>
        </div>

        <a href="gallery_add.php"
           class="inline-flex items-center px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl transition shadow-xl shadow-emerald-200/50 active:scale-95">
            <i class="fa-solid fa-cloud-arrow-up mr-2"></i>
            Bulk Upload
        </a>
    </div>

    <!-- SEARCH + COUNT -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

        <div class="md:col-span-3">
            <form method="GET" class="relative">
                <input type="text"
                       name="search"
                       value="<?= htmlspecialchars($search) ?>"
                       placeholder="Search by caption or category…"
                       class="w-full pl-14 pr-4 py-4 bg-white rounded-2xl shadow-sm focus:ring-2 focus:ring-emerald-500 outline-none font-medium">
                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </div>
            </form>
        </div>

        <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <span class="text-slate-400 font-black uppercase text-[10px] tracking-[0.2em] block">
                    Stored Assets
                </span>
                <span class="text-2xl font-black text-slate-900">
                    <?= count($images) ?>
                </span>
            </div>
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                <i class="fa-solid fa-photo-film"></i>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-black tracking-widest">
                    <th class="px-8 py-6 text-center">Thumbnail</th>
                    <th class="px-8 py-6">Metadata</th>
                    <th class="px-8 py-6">Category</th>
                    <th class="px-8 py-6 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">

            <?php if ($images): foreach ($images as $img): ?>

                <tr class="group hover:bg-slate-50/80 transition">

                    <!-- THUMB -->
                    <td class="px-8 py-6 w-32">
                        <div class="relative w-20 h-20 rounded-2xl overflow-hidden border-4 border-white shadow-md bg-slate-100">
                            <img src="<?= htmlspecialchars(galleryThumb($img['filename'])) ?>"
                                 class="w-full h-full object-cover">
                            <a href="<?= htmlspecialchars($img['filename']) ?>"
                               target="_blank"
                               class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition">
                                <i class="fa-solid fa-expand text-xs"></i>
                            </a>
                        </div>
                    </td>

                    <!-- META -->
                    <td class="px-8 py-6">
                        <div class="font-black text-slate-800 text-lg">
                            <?= htmlspecialchars($img['caption'] ?: 'Untitled Asset') ?>
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                Added
                            </span>
                            <span class="text-[10px] text-slate-500 font-medium">
                                <?= date('M d, Y', strtotime($img['created_at'])) ?>
                            </span>
                        </div>
                    </td>

                    <!-- CATEGORY -->
                    <td class="px-8 py-6">
                        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest border border-slate-200">
                            <i class="fa-solid fa-tag text-[8px]"></i>
                            <?= htmlspecialchars($img['category'] ?: 'General') ?>
                        </span>
                    </td>

                    <!-- ACTIONS -->
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end gap-3">

                            <a href="<?= htmlspecialchars($img['filename']) ?>"
                               target="_blank"
                               class="w-11 h-11 flex items-center justify-center bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white rounded-2xl transition shadow-sm">
                                <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                            </a>

                            <a href="gallery_edit.php?id=<?= (int) $img['id'] ?>"
                               class="w-11 h-11 flex items-center justify-center bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white rounded-2xl transition shadow-sm">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </a>

                            <form action="handlers/gallery-handler.php"
                                  method="POST"
                                  onsubmit="return confirm('Delete this asset permanently?');">

                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="<?= (int) $img['id'] ?>">
                                <input type="hidden" name="delete" value="1">

                                <button class="w-11 h-11 flex items-center justify-center bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white rounded-2xl transition shadow-sm">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>

            <?php endforeach; else: ?>

                <tr>
                    <td colspan="4" class="p-32 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200">
                                <i class="fa-solid fa-box-open text-4xl"></i>
                            </div>
                            <p class="text-slate-400 font-black uppercase text-xs tracking-[0.2em]">
                                No Media Found
                            </p>
                        </div>
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>
    </div>

</div>
</div>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>
