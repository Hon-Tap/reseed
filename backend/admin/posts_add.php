<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

// Generate CSRF token if not already present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-gray-50 min-h-screen p-6 md:p-10">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">Create New Post</h2>
            <p class="text-gray-600">Fill in the details below to publish a new article.</p>
        </div>
        <form action="project-handler.php" 
              method="POST" 
              enctype="multipart/form-data" 
              class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Slug (Optional)</label>
                        <input type="text" name="slug" class="w-full px-4 py-2 rounded-lg border bg-gray-50 font-mono text-sm outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Author</label>
                        <input type="text" name="author" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Excerpt</label>
                    <textarea name="excerpt" rows="2" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Full Content</label>
                    <textarea name="content" rows="6" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Media Type</label>
                        <select name="media_type" class="w-full px-4 py-2 rounded-lg border bg-white outline-none">
                            <option value="image">Image</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Published At</label>
                        <input type="datetime-local" name="published_at" class="w-full px-4 py-2 rounded-lg border outline-none">
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" name="featured" value="1" class="w-5 h-5 text-green-600 rounded">
                            <span class="text-sm font-bold text-gray-700">Mark as Featured</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Upload File</label>
                        <input type="file" name="media_file" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">External Media URL</label>
                        <input type="url" name="media_url" placeholder="https://..." class="w-full px-4 py-2 rounded-lg border outline-none text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-8 py-4 flex justify-end space-x-4">
                <a href="posts.php" class="px-6 py-2 text-gray-600 hover:text-gray-800 transition">Cancel</a>
                <button type="submit" name="add" class="bg-green-600 hover:bg-green-700 text-white font-bold px-8 py-2 rounded-lg shadow transition">
                    Save Post
                </button>
            </div>
        </form>
    </div>
</div>

<?php include $backendPath . '/admin/includes/admin_footer.php'; ?>