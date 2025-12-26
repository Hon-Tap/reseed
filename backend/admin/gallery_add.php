<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';


?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-gray-50 min-h-screen p-6 md:p-10">
    <div class="max-w-4xl mx-auto">
        
        <div class="mb-8">
            <a href="gallery.php" class="text-sm text-green-600 hover:underline flex items-center mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
                Back to Library
            </a>
            <h2 class="text-3xl font-extrabold text-gray-900">Upload Media</h2>
            <p class="text-gray-500">You can upload a single image or multiple files at once.</p>
        </div>

        <form action="<?= admin_url('handlers/gallery-handler.php') ?>"
      method="POST"
      enctype="multipart/form-data"
      class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-2xl shadow-sm border-2 border-dashed border-gray-200 text-center transition hover:border-green-400 group">
                    <input type="file" name="images[]" id="image-input" multiple accept="image/*" class="hidden" onchange="handleFiles(this.files)">
                    <label for="image-input" class="cursor-pointer">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 text-green-600 mb-4 group-hover:scale-110 transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Select Files</h3>
                        <p class="text-sm text-gray-500">Click to browse or drag and drop multiple images here</p>
                    </label>
                    
                    <div id="preview-grid" class="mt-8 grid grid-cols-3 sm:grid-cols-4 gap-4"></div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="text-xs font-bold uppercase text-gray-400 mb-4 tracking-widest">Upload Details</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Common Category</label>
                        <input type="text" name="category" placeholder="e.g. Field Work" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Base Caption (Optional)</label>
                        <input type="text" name="caption" placeholder="Defaults to filename" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none">
                    </div>

                    <div id="file-count" class="text-xs text-gray-500 mb-6 italic">No files selected</div>

                    <button type="submit" name="bulk_add" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-md transition transform active:scale-95">
                        Start Upload
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function handleFiles(files) {
    const grid = document.getElementById('preview-grid');
    const countLabel = document.getElementById('file-count');
    grid.innerHTML = '';
    
    countLabel.innerText = `${files.length} file(s) selected for upload`;

    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = "aspect-square rounded-lg overflow-hidden border bg-gray-50 relative group";
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            grid.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}
</script>

<?php include "includes/admin_footer.php"; ?>