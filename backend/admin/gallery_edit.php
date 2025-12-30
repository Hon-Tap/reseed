<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · GALLERY EDIT (DB + CLOUDINARY ALIGNED)
|--------------------------------------------------------------------------
*/

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

/* ----------------------------------------------------------------------
| Fetch Asset
|---------------------------------------------------------------------- */
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: gallery.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, filename, caption, category, created_at FROM gallery WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "<div class='p-10 text-center font-bold text-slate-500'>Asset not found.</div>";
    require_once $backendPath . '/admin/includes/admin_footer.php';
    exit;
}

/* ----------------------------------------------------------------------
| Resolve Preview URL
|---------------------------------------------------------------------- */
$previewUrl = $item['filename'];

if (str_contains($previewUrl, 'cloudinary.com')) {
    $previewUrl = str_replace(
        '/upload/',
        '/upload/w_900,c_limit,q_auto,f_auto/',
        $previewUrl
    );
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10">
<div class="max-w-5xl mx-auto">

    <!-- HEADER -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <a href="gallery.php"
               class="inline-flex items-center text-sm font-bold text-slate-400 hover:text-emerald-600 transition mb-2">
                <i class="fa-solid fa-chevron-left mr-2"></i>
                Back to Media Vault
            </a>

            <h1 class="text-4xl font-black text-slate-900 tracking-tight">
                Edit Asset
            </h1>
        </div>

        <!-- DELETE -->
        <form action="handlers/gallery-handler.php"
              method="POST"
              onsubmit="return confirm('Delete this asset permanently?');">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <input type="hidden" name="delete" value="1">

            <button type="submit"
                    class="bg-rose-50 text-rose-600 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition">
                <i class="fa-solid fa-trash-can mr-2"></i>
                Delete Asset
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">

        <!-- PREVIEW -->
        <div class="lg:col-span-3">
            <div class="bg-white p-4 rounded-[2.5rem] shadow-xl border border-slate-100">

                <div class="relative aspect-video rounded-[1.8rem] overflow-hidden bg-slate-100 group">
                    <img src="<?= htmlspecialchars($previewUrl) ?>"
                         class="w-full h-full object-cover">

                    <a href="<?= htmlspecialchars($item['filename']) ?>"
                       target="_blank"
                       class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <span class="bg-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest">
                            View Original
                        </span>
                    </a>
                </div>

                <div class="mt-4 px-2 flex items-center gap-3">
                    <input id="assetUrl"
                           type="text"
                           readonly
                           value="<?= htmlspecialchars($item['filename']) ?>"
                           class="flex-1 bg-slate-50 rounded-lg px-3 py-2 text-[10px] font-mono text-slate-500">

                    <button onclick="copyUrl(this)"
                            class="p-3 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl transition">
                        <i class="fa-solid fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- METADATA -->
        <div class="lg:col-span-2 space-y-6">

            <form action="handlers/gallery-handler.php"
                  method="POST"
                  class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">

                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <input type="hidden" name="update" value="1">

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">
                        Caption
                    </label>
                    <textarea name="caption"
                              rows="3"
                              required
                              class="w-full px-5 py-4 rounded-2xl bg-slate-50 focus:ring-2 focus:ring-emerald-500 outline-none font-bold resize-none"><?= htmlspecialchars($item['caption']) ?></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">
                        Category
                    </label>
                    <input type="text"
                           name="category"
                           required
                           value="<?= htmlspecialchars($item['category']) ?>"
                           class="w-full px-5 py-4 rounded-2xl bg-slate-50 focus:ring-2 focus:ring-emerald-500 outline-none font-bold">
                </div>

                <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-5 rounded-[1.8rem] shadow-xl shadow-emerald-200 transition active:scale-95 flex items-center justify-center gap-2">
                    Save Changes
                    <i class="fa-solid fa-check"></i>
                </button>
            </form>

            <div class="bg-slate-900 p-6 rounded-[2rem] text-center">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">
                    Uploaded
                </p>
                <p class="text-white font-bold">
                    <?= date('F d, Y', strtotime($item['created_at'])) ?>
                </p>
            </div>

        </div>

    </div>

</div>
</div>

<script>
function copyUrl(btn) {
    const input = document.getElementById('assetUrl');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value);

    const icon = btn.querySelector('i');
    icon.className = 'fa-solid fa-check text-emerald-600';

    setTimeout(() => {
        icon.className = 'fa-solid fa-copy';
    }, 1500);
}
</script>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>
