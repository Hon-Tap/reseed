<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · EDIT POST (DB-ALIGNED)
|--------------------------------------------------------------------------
*/

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

/*
|--------------------------------------------------------------------------
| FETCH POST
|--------------------------------------------------------------------------
*/

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    header('Location: posts.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header("Location: posts.php?error=not_found");
    exit;
}

/*
|--------------------------------------------------------------------------
| FORMAT DATE
|--------------------------------------------------------------------------
*/

$publishedValue = $post['published_at']
    ? date('Y-m-d\TH:i', strtotime($post['published_at']))
    : '';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10 text-slate-900">
<div class="max-w-6xl mx-auto">

    <!-- HEADER -->
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">

        <div class="flex items-center gap-6">
            <a href="posts.php"
               class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-slate-400 hover:text-emerald-600 transition group">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </a>

            <div>
                <h1 class="text-4xl font-black tracking-tight">Edit Story</h1>
                <p class="text-slate-400 font-medium">
                    Article ID:
                    <span class="font-mono text-emerald-600">#<?= (int) $post['id'] ?></span>
                </p>
            </div>
        </div>

        <!-- DELETE -->
        <form action="handlers/post-handler.php"
              method="POST"
              onsubmit="return confirm('Delete this post permanently?');">

            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
            <input type="hidden" name="delete" value="1">

            <button type="submit"
                    class="group flex items-center gap-2 text-rose-500 font-black text-[10px] uppercase tracking-widest px-6 py-3 rounded-xl bg-rose-50 hover:bg-rose-500 hover:text-white transition shadow-sm">
                <i class="fa-solid fa-trash-can opacity-50 group-hover:opacity-100"></i>
                Remove Post
            </button>
        </form>

    </div>

    <!-- FORM -->
    <form id="postForm"
          action="/admin/handlers/post-handler.php"
          method="POST"
          enctype="multipart/form-data"
          class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">

        <!-- LEFT -->
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">
                        Story Headline
                    </label>
                    <input type="text" name="title" id="postTitle"
                           value="<?= htmlspecialchars($post['title']) ?>"
                           required
                           class="w-full px-6 py-4 rounded-2xl bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-2xl font-black outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">
                            Slug (Permalink)
                        </label>
                        <input type="text" name="slug" id="postSlug"
                               value="<?= htmlspecialchars($post['slug']) ?>"
                               class="w-full px-5 py-3 rounded-xl bg-slate-50 font-mono text-xs outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">
                            Author
                        </label>
                        <input type="text" name="author"
                               value="<?= htmlspecialchars($post['author']) ?>"
                               required
                               class="w-full px-5 py-3 rounded-xl bg-slate-50 font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">
                        Teaser Excerpt
                    </label>
                    <textarea name="excerpt" rows="2"
                              class="w-full px-5 py-3 rounded-xl bg-slate-50 focus:ring-2 focus:ring-emerald-500 outline-none"><?= htmlspecialchars($post['excerpt']) ?></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">
                        Article Body
                    </label>
                    <textarea name="content" rows="18"
                              class="w-full px-8 py-6 rounded-3xl bg-slate-50 focus:ring-2 focus:ring-emerald-500 outline-none font-serif text-lg"><?= htmlspecialchars($post['content']) ?></textarea>
                </div>

            </div>
        </div>

        <!-- RIGHT -->
        <div class="space-y-6">

            <!-- PUBLISH -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm sticky top-10">

                <h3 class="text-xs font-black uppercase text-slate-400 mb-6 tracking-widest pb-4 border-b">
                    Publishing
                </h3>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Release Date
                    </label>
                    <input type="datetime-local" name="published_at"
                           value="<?= $publishedValue ?>"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 font-bold focus:ring-2 focus:ring-emerald-500">
                </div>

                <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 hover:bg-emerald-50 transition mb-8">
                    <input type="checkbox" name="featured" value="1"
                           <?= $post['featured'] ? 'checked' : '' ?>
                           class="w-5 h-5 text-emerald-600">
                    <span class="text-sm font-black text-slate-700">Pin to Spotlight</span>
                </label>

                <button type="submit" name="update" id="submitBtn"
                        class="w-full bg-slate-900 hover:bg-emerald-600 text-white font-black py-5 rounded-[2rem] shadow-xl transition active:scale-95 flex items-center justify-center gap-3">
                    <span id="btnText">Publish Updates</span>
                    <i id="btnIcon" class="fa-solid fa-paper-plane text-xs"></i>
                </button>

            </div>

            <!-- COVER IMAGE -->
            <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl">

                <h3 class="text-xs font-black uppercase text-slate-500 mb-6 tracking-widest pb-4 border-b border-slate-800">
                    Cover Image
                </h3>

                <?php if (!empty($post['cover_image'])): ?>
                    <div class="mb-6 rounded-3xl overflow-hidden border border-slate-800 bg-black aspect-video">
                        <img id="coverPreview"
                             src="<?= htmlspecialchars($post['cover_image']) ?>"
                             class="w-full h-full object-cover opacity-90">
                    </div>
                <?php endif; ?>

                <label class="block text-[10px] font-black text-slate-500 uppercase mb-3 tracking-widest">
                    Replace Image
                </label>

                <input type="file"
                       name="media_file"
                       id="media_file"
                       accept="image/*"
                       onchange="previewImage(this)"
                       class="text-xs block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-emerald-500 file:text-white file:font-black hover:file:bg-emerald-400 cursor-pointer">

                <input type="hidden" name="media_type" value="image">
            </div>

        </div>
    </form>

</div>
</div>

<script>
// Slug sync
const titleInput = document.getElementById('postTitle');
const slugInput  = document.getElementById('postSlug');
let manualSlug = false;

slugInput.addEventListener('input', () => manualSlug = true);

titleInput.addEventListener('input', () => {
    if (!manualSlug) {
        slugInput.value = titleInput.value
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
});

// Image preview
function previewImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('coverPreview');
        if (img) img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}

// Submit state
document.getElementById('postForm').addEventListener('submit', () => {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    document.getElementById('btnText').innerText = 'Syncing...';
    document.getElementById('btnIcon').className = 'fa-solid fa-circle-notch animate-spin';
});
</script>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>
