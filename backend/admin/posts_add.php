<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin — Add Post
|--------------------------------------------------------------------------
*/

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
*/
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50 min-h-screen p-6 md:p-10">
    <div class="max-w-4xl mx-auto">

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-slate-900">Create New Post</h2>
            <p class="text-slate-500 mt-1">
                Draft and publish news stories or field updates.
            </p>
        </div>

        <!-- FORM -->
        <form
            action="handlers/post-handler.php"
            method="POST"
            enctype="multipart/form-data"
            class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden"
        >

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="p-8 space-y-6">

                <!-- Title -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Title</label>
                    <input
                        type="text"
                        name="title"
                        required
                        class="w-full px-4 py-2 rounded-lg border
                               focus:ring-2 focus:ring-emerald-500 outline-none"
                    >
                </div>

                <!-- Slug / Author -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Slug (optional)</label>
                        <input
                            type="text"
                            name="slug"
                            class="w-full px-4 py-2 rounded-lg border bg-slate-50
                                   font-mono text-sm outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Author</label>
                        <input
                            type="text"
                            name="author"
                            required
                            class="w-full px-4 py-2 rounded-lg border
                                   focus:ring-2 focus:ring-emerald-500 outline-none"
                        >
                    </div>
                </div>

                <!-- Excerpt -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Excerpt</label>
                    <textarea
                        name="excerpt"
                        rows="2"
                        required
                        class="w-full px-4 py-2 rounded-lg border
                               focus:ring-2 focus:ring-emerald-500 outline-none"
                    ></textarea>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Full Content</label>
                    <textarea
                        name="content"
                        rows="8"
                        required
                        class="w-full px-4 py-2 rounded-lg border
                               focus:ring-2 focus:ring-emerald-500 outline-none"
                    ></textarea>
                </div>

                <!-- Meta -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t">

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Media Type</label>
                        <select
                            name="media_type"
                            class="w-full px-4 py-2 rounded-lg border bg-white outline-none"
                        >
                            <option value="image">Image</option>
                            <option value="video">Video</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Published At</label>
                        <input
                            type="datetime-local"
                            name="published_at"
                            class="w-full px-4 py-2 rounded-lg border outline-none"
                        >
                    </div>

                    <div class="flex items-end pb-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input
                                type="checkbox"
                                name="featured"
                                value="1"
                                class="w-5 h-5 text-emerald-600 rounded"
                            >
                            <span class="text-sm font-bold text-slate-700">
                                Mark as Featured
                            </span>
                        </label>
                    </div>

                </div>

                <!-- Media -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Upload File</label>
                        <input
                            type="file"
                            name="media_file"
                            class="text-sm text-slate-500
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-full file:border-0
                                   file:bg-emerald-50 file:text-emerald-700
                                   hover:file:bg-emerald-100"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">External Media URL</label>
                        <input
                            type="url"
                            name="media_url"
                            placeholder="https://..."
                            class="w-full px-4 py-2 rounded-lg border outline-none text-sm"
                        >
                    </div>

                </div>

            </div>

            <!-- Footer -->
            <div class="bg-slate-50 px-8 py-4 flex justify-end gap-4">
                <a href="posts.php" class="px-6 py-2 text-slate-600 hover:text-slate-800">
                    Cancel
                </a>

                <button
                    type="submit"
                    name="add"
                    class="bg-emerald-600 hover:bg-emerald-700
                           text-white font-bold px-8 py-2
                           rounded-lg shadow transition"
                >
                    Save Post
                </button>
            </div>

        </form>
    </div>
</div>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>
