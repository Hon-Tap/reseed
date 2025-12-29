<?php
declare(strict_types=1);

/**
 * Admin — Post Editor (Optimized)
 * Path: /backend/admin/post_edit.php
 */

$backendPath = dirname(__DIR__, 1);
require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

// 1. Fetch Post Data
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) { 
    header("Location: posts.php?error=not_found"); 
    exit; 
}

// 2. Format Date for HTML5 Input
$publishedValue = date('Y-m-d\TH:i', strtotime($post['published_at'] ?? 'now'));
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10 text-slate-900">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex items-center gap-6">
                <a href="posts.php" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-slate-400 hover:text-emerald-600 transition group">
                    <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div>
                    <h1 class="text-4xl font-black tracking-tight">Edit Story</h1>
                    <p class="text-slate-400 font-medium">Article ID: <span class="font-mono text-emerald-600">#<?= $post['id'] ?></span></p>
                </div>
            </div>
            
            <form action="handlers/post-handler.php" method="POST" onsubmit="return confirm('Archive this story permanently?');">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                <input type="hidden" name="delete" value="1">
                <button type="submit" class="group flex items-center gap-2 text-rose-500 font-black text-[10px] uppercase tracking-widest px-6 py-3 rounded-xl bg-rose-50 hover:bg-rose-500 hover:text-white transition shadow-sm">
                    <i class="fa-solid fa-trash-can opacity-50 group-hover:opacity-100"></i>
                    Remove Draft
                </button>
            </form>
        </div>

        <form id="postForm" action="/admin/handlers/post-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Story Headline</label>
                        <input type="text" name="title" id="postTitle" value="<?= htmlspecialchars($post['title']) ?>" required
                               class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 text-2xl font-black transition-all outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Slug (Permalink)</label>
                            <input type="text" name="slug" id="postSlug" value="<?= htmlspecialchars($post['slug']) ?>"
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none font-mono text-xs text-slate-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Author Attribution</label>
                            <input type="text" name="author" value="<?= htmlspecialchars($post['author']) ?>" required
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Teaser Excerpt</label>
                        <textarea name="excerpt" rows="2" class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none font-medium focus:ring-2 focus:ring-emerald-500 outline-none leading-relaxed"><?= htmlspecialchars($post['excerpt']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Article Body</label>
                        <textarea name="content" rows="18" class="w-full px-8 py-6 rounded-3xl bg-slate-50 border-none leading-relaxed focus:ring-2 focus:ring-emerald-500 outline-none font-serif text-lg"><?= htmlspecialchars($post['content']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm sticky top-10">
                    <h3 class="text-xs font-black uppercase text-slate-400 mb-6 tracking-widest pb-4 border-b">Publishing</h3>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Release Date</label>
                        <input type="datetime-local" name="published_at" value="<?= $publishedValue ?>"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border-none font-bold text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 cursor-pointer mb-8 hover:bg-emerald-50 transition duration-300">
                        <input type="checkbox" name="featured" value="1" <?= $post['featured'] ? 'checked' : '' ?> class="w-5 h-5 rounded border-none text-emerald-600 focus:ring-0 transition">
                        <span class="text-sm font-black text-slate-700">Pin to Spotlight</span>
                    </label>

                    <button type="submit" name="update" id="submitBtn" class="w-full bg-slate-900 hover:bg-emerald-600 text-white font-black py-5 rounded-[2rem] shadow-xl shadow-slate-200 transition-all transform active:scale-95 flex items-center justify-center gap-3 group">
                        <span id="btnText">Publish Updates</span>
                        <i id="btnIcon" class="fa-solid fa-paper-plane text-xs group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                    </button>
                </div>

                <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl">
                    <h3 class="text-xs font-black uppercase text-slate-500 mb-6 tracking-widest pb-4 border-b border-slate-800 flex items-center justify-between">
                        Visual Identity
                        <i class="fa-solid fa-clapperboard text-slate-700"></i>
                    </h3>
                    
                    <div class="mb-6 space-y-4">
                        <?php if (!empty($post['cover_image'])): ?>
                            <div class="relative group rounded-3xl overflow-hidden border border-slate-800 bg-black aspect-video shadow-inner">
                                <img id="coverPreview" src="<?= htmlspecialchars($post['cover_image']) ?>" class="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition-opacity duration-700">
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <span class="text-[9px] font-black uppercase tracking-[0.3em] bg-white/10 backdrop-blur-md px-4 py-1.5 rounded-full border border-white/10">Active Cover</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase mb-3 tracking-widest">Switch Cover Image</label>
                            <div class="relative">
                                <input type="file" name="media_file" id="media_file" class="hidden" onchange="previewImage(this)">
                                <label for="media_file" class="flex items-center justify-center w-full py-4 rounded-2xl bg-slate-800 border-2 border-dashed border-slate-700 text-xs font-bold text-slate-300 hover:border-emerald-500 hover:text-emerald-400 transition cursor-pointer">
                                    <i class="fa-solid fa-camera mr-2"></i> Browse Files
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800">
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-3 tracking-widest flex items-center gap-2">
                            <i class="fa-brands fa-youtube text-rose-500"></i> Video Content (URL)
                        </label>
                        <input type="url" name="media_url" value="<?= htmlspecialchars($post['media_url'] ?? '') ?>"
                               placeholder="YouTube/Vimeo link" 
                               class="w-full px-4 py-4 rounded-xl bg-slate-800 border-none text-xs text-emerald-400 font-medium outline-none focus:ring-1 focus:ring-emerald-500 placeholder:text-slate-600 transition-all">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// 1. Slug Engine (Auto-generate from title until manually touched)
const titleInput = document.getElementById('postTitle');
const slugInput = document.getElementById('postSlug');
let isManualSlug = false;

slugInput.addEventListener('input', () => isManualSlug = true);

titleInput.addEventListener('input', function() {
    if (!isManualSlug) {
        slugInput.value = this.value
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
});

// 2. Image Preview Logic
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const preview = document.getElementById('coverPreview');
        
        reader.onload = (e) => {
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('opacity-60');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// 3. Form Submission State
document.getElementById('postForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');

    btn.disabled = true;
    btn.classList.add('opacity-70', 'cursor-not-allowed');
    btnText.innerText = 'Syncing with Cloudinary...';
    btnIcon.className = 'fa-solid fa-circle-notch animate-spin';
});
</script>

<?php require $backendPath . '/admin/includes/admin_footer.php'; ?>