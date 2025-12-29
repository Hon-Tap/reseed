<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 1);
require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) { header("Location: posts.php"); exit; }

$publishedValue = date('Y-m-d\TH:i', strtotime($post['published_at'] ?? 'now'));
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex items-center gap-6">
                <a href="posts.php" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-slate-400 hover:text-emerald-600 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight">Edit Story</h1>
                    <p class="text-slate-400 font-medium">Currently modifying: <span class="text-slate-600"><?= htmlspecialchars($post['title']) ?></span></p>
                </div>
            </div>
            
            <form action="handlers/post-handler.php" method="POST" onsubmit="return confirm('Archive this post permanently?');">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                <input type="hidden" name="delete" value="1">
                <button type="submit" class="text-rose-500 font-black text-xs uppercase tracking-widest px-6 py-3 rounded-xl bg-rose-50 hover:bg-rose-500 hover:text-white transition">Delete Post</button>
            </form>
        </div>

        <form action="handlers/post-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Article Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required
                               class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 text-xl font-black">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">URL Slug</label>
                            <input type="text" name="slug" value="<?= htmlspecialchars($post['slug']) ?>"
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Author</label>
                            <input type="text" name="author" value="<?= htmlspecialchars($post['author']) ?>" required
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Excerpt</label>
                        <textarea name="excerpt" rows="3" class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none font-medium"><?= htmlspecialchars($post['excerpt']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Full Content</label>
                        <textarea name="content" rows="15" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none leading-relaxed"><?= htmlspecialchars($post['content']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-slate-400 mb-6 tracking-widest pb-4 border-b">Publishing</h3>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-bold mb-2">Published At</label>
                        <input type="datetime-local" name="published_at" value="<?= $publishedValue ?>"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border-none font-bold text-sm">
                    </div>

                    <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 cursor-pointer mb-6">
                        <input type="checkbox" name="featured" value="1" <?= $post['featured'] ? 'checked' : '' ?> class="w-5 h-5 rounded border-none text-emerald-600">
                        <span class="text-sm font-black text-slate-700">Featured Post</span>
                    </label>

                    <button type="submit" name="update" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-5 rounded-[2rem] shadow-xl shadow-emerald-100 transition transform active:scale-95">
                        Save Changes
                    </button>
                </div>

                <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white">
                    <h3 class="text-xs font-black uppercase text-slate-500 mb-6 tracking-widest pb-4 border-b border-slate-800">Media Assets</h3>
                    
                    <?php if (!empty($post['cover_image'])): ?>
                        <div class="mb-4 rounded-2xl overflow-hidden border border-slate-800 shadow-2xl">
                            <img src="<?= htmlspecialchars($post['cover_image']) ?>" class="w-full aspect-video object-cover">
                        </div>
                    <?php endif; ?>

                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Replace Cover Image</label>
                    <input type="file" name="media_file" class="text-xs block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-slate-800 file:text-emerald-400 file:font-black cursor-pointer mb-6">

                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest">Video URL</label>
                    <input type="url" name="media_url" value="<?= htmlspecialchars($post['media_url'] ?? '') ?>"
                           placeholder="https://youtube.com/..." class="w-full px-4 py-3 rounded-xl bg-slate-800 border-none text-xs outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>
        </form>
    </div>
</div>

<?php require $backendPath . '/admin/includes/admin_footer.php'; ?>