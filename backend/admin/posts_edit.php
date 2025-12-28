<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header("Location: posts.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Format the date for the HTML input
$publishedValue = !empty($post['published_at']) 
    ? date('Y-m-d\TH:i', strtotime($post['published_at'])) 
    : date('Y-m-d\TH:i');
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex items-center gap-4">
            <a href="posts.php" class="bg-white p-2 rounded-lg border hover:shadow-sm transition text-slate-400 hover:text-emerald-600">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900">Edit: <?= htmlspecialchars($post['title']) ?></h2>
                <p class="text-slate-500">Updating ID #<?= $post['id'] ?></p>
            </div>
        </div>

        <form action="post-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Article Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required
                               class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 outline-none text-lg font-semibold">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">URL Slug</label>
                            <input type="text" name="slug" value="<?= htmlspecialchars($post['slug']) ?>"
                                   class="w-full px-3 py-2 rounded-lg border bg-slate-50 font-mono text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Author</label>
                            <input type="text" name="author" value="<?= htmlspecialchars($post['author']) ?>" required
                                   class="w-full px-3 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Excerpt</label>
                        <textarea name="excerpt" rows="3" required class="w-full px-4 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-emerald-500"><?= htmlspecialchars($post['excerpt']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Content</label>
                        <textarea name="content" rows="12" required class="w-full px-4 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-emerald-500"><?= htmlspecialchars($post['content']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-xs font-bold uppercase text-slate-400 mb-4 tracking-widest">Publishing</h3>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Published At</label>
                        <input type="datetime-local" name="published_at" value="<?= $publishedValue ?>"
                               class="w-full px-3 py-2 rounded-lg border outline-none text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <label class="flex items-center space-x-3 cursor-pointer p-3 mb-6 rounded-xl border border-dashed border-slate-200 hover:bg-emerald-50 transition">
                        <input type="checkbox" name="featured" value="1" class="w-5 h-5 text-emerald-600 rounded" <?= !empty($post['featured']) ? 'checked' : '' ?>>
                        <span class="text-sm font-bold text-slate-700">Featured Post</span>
                    </label>

                    <button type="submit" name="update" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-emerald-100 transition">
                        Update Changes
                    </button>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-xs font-bold uppercase text-slate-400 mb-4 tracking-widest">Media Assets</h3>
                    
                    <select name="media_type" class="w-full mb-4 px-3 py-2 rounded-lg border bg-slate-50 text-sm">
                        <option value="image" <?= $post['media_type'] === 'image' ? 'selected' : '' ?>>Image</option>
                        <option value="video" <?= $post['media_type'] === 'video' ? 'selected' : '' ?>>Video</option>
                    </select>

                    <?php if (!empty($post['cover_image'])): ?>
                        <div class="mb-4 rounded-xl overflow-hidden border border-slate-100 shadow-inner">
                            <img src="<?= UPLOADS_URL ?>/posts/<?= $post['cover_image'] ?>" class="w-full h-40 object-cover">
                            <p class="text-[10px] text-center p-1 text-slate-400 bg-slate-50">Current Image</p>
                        </div>
                    <?php endif; ?>

                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Replace File</label>
                    <input type="file" name="media_file" class="text-xs block w-full mb-4">

                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">External Link</label>
                    <input type="url" name="media_url" value="<?= htmlspecialchars($post['media_url'] ?? '') ?>"
                           placeholder="https://youtube.com/..." class="w-full px-3 py-2 rounded-lg border text-xs outline-none">
                </div>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>