<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-gray-50 min-h-screen p-6">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border">
        <h2 class="text-2xl font-bold mb-6">Add New Project</h2>
         <form action="post-handler.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-bold mb-1">Title</label>
                    <input type="text" name="title" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Slug (optional)</label>
                    <input type="text" name="slug" class="w-full p-2 border rounded font-mono text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Location</label>
                    <input type="text" name="location" class="w-full p-2 border rounded">
                </div>
            </div>

            <label class="block text-sm font-bold mb-1">Summary</label>
            <textarea name="summary" rows="2" class="w-full p-2 border rounded"></textarea>

            <label class="block text-sm font-bold mb-1">Description</label>
            <textarea name="description" rows="5" class="w-full p-2 border rounded"></textarea>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Start Date</label>
                    <input type="date" name="start_date" class="w-full p-2 border rounded text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">End Date</label>
                    <input type="date" name="end_date" class="w-full p-2 border rounded text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Status</label>
                    <select name="status" class="w-full p-2 border rounded text-sm">
                        <option value="Ongoing">Ongoing</option>
                        <option value="Completed">Completed</option>
                        <option value="Planned">Planned</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t pt-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Media Type</label>
                    <select name="media_type" class="w-full p-2 border rounded">
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Upload File</label>
                    <input type="file" name="media_file" class="text-xs">
                </div>
            </div>

            <label class="flex items-center space-x-2 py-2">
                <input type="checkbox" name="featured" value="1" class="w-4 h-4 text-green-600">
                <span class="text-sm font-bold">Mark as Featured Project</span>
            </label>

            <div class="flex justify-end pt-4">
                <button type="submit" name="add" class="bg-green-600 text-white px-8 py-2 rounded font-bold hover:bg-green-700">Save Project</button>
            </div>
        </form>
    </div>
</div>

<?php include $backendPath . '/admin/includes/admin_footer.php'; ?>