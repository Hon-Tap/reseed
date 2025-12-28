<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Automatic Current Time for the form
$currentTime = date('Y-m-d\TH:i');
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50 min-h-screen p-6 md:p-10">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900">Create New Post</h2>
                <p class="text-slate-500 mt-1">Draft and publish news stories or field updates.</p>
            </div>
            <a href="posts.php" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-xmark text-xl"></i>
            </a>
        </div>
       
        <form action="post-handler.php" method="POST" enctype="multipart/form-data" 
              class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Title</label>
                        <input type="text" name="title" required placeholder="Article headline..."
                               class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Slug (URL)</label>
                        <input type="text" name="slug" placeholder="auto-generated-if-empty"
                               class="w-full px-4 py-2 rounded-lg border bg-slate-50 font-mono text-sm outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Author</label>
                        <input type="text" name="author" required value="<?= $_SESSION['admin_name'] ?? '' ?>"
                               class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Published Date (Auto-set)</label>
                        <input type="datetime-local" name="published_at" value="<?= $currentTime ?>"
                               class="w-full px-4 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Excerpt</label>
                    <textarea name="excerpt" rows="2" required placeholder="Brief summary for cards..."
                              class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Full Content</label>
                    <textarea name="content" rows="10" required placeholder="Write your story here..."
                              class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-slate-100">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Cover Media</label>
                        <div class="flex gap-4 mb-3">
                            <select name="media_type" class="px-3 py-2 rounded-lg border bg-white text-sm outline-none">
                                <option value="image">Image</option>
                                <option value="video">Video</option>
                            </select>
                            <input type="file" name="media_file" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-emerald-50 file:text-emerald-700">
                        </div>
                        <input type="url" name="media_url" placeholder="Or paste YouTube/Vimeo link"
                               class="w-full px-4 py-2 rounded-lg border text-sm outline-none">
                    </div>

                    <div class="flex items-center">
                        <label class="group flex items-center space-x-4 cursor-pointer bg-slate-50 p-4 rounded-xl border border-dashed border-slate-300 hover:border-emerald-500 transition w-full">
                            <input type="checkbox" name="featured" value="1" class="w-6 h-6 text-emerald-600 rounded-md border-slate-300 focus:ring-emerald-500">
                            <div>
                                <span class="block text-sm font-bold text-slate-700">Mark as Featured</span>
                                <span class="block text-xs text-slate-500">Highlight this at the top of the blog.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-8 py-5 flex justify-end gap-4 border-t border-slate-200">
                <a href="posts.php" class="px-6 py-2.5 text-slate-600 font-medium hover:bg-slate-200 rounded-lg transition">Cancel</a>
                <button type="submit" name="add" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-10 py-2.5 rounded-lg shadow-lg shadow-emerald-200 transition">
                    Publish Article
                </button>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>