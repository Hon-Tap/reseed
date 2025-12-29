<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10">
    <div class="max-w-5xl mx-auto">
        
        <div class="mb-10">
            <a href="gallery.php" class="inline-flex items-center text-sm font-bold text-emerald-600 hover:text-emerald-700 transition mb-4">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Media Vault
            </a>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Upload Media</h1>
            <p class="text-slate-500 mt-2">Add high-resolution photographs to the ReSEED archive.</p>
        </div>

        <form action="handlers/gallery-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="lg:col-span-2 space-y-6">
                <div id="drop-zone" class="bg-white p-10 rounded-[2rem] shadow-sm border-4 border-dashed border-slate-200 text-center transition-all hover:border-emerald-400 group relative">
                    <input type="file" name="images[]" id="image-input" multiple accept="image/*" class="hidden" onchange="handleFiles(this.files)">
                    
                    <label for="image-input" class="cursor-pointer block">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-emerald-50 text-emerald-600 mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-800">Drop images here</h3>
                        <p class="text-slate-400 mt-2 font-medium">or click to browse your computer</p>
                    </label>

                    <div id="preview-grid" class="mt-10 grid grid-cols-3 sm:grid-cols-4 gap-4"></div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 sticky top-10">
                    <h3 class="text-[10px] font-black uppercase text-slate-400 mb-6 tracking-[0.2em]">Bulk Metadata</h3>
                    
                    <div class="mb-5">
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wide mb-2">Category</label>
                        <select name="category" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 outline-none font-bold text-slate-700">
                            <option value="Field Work">Field Work</option>
                            <option value="Events">Events</option>
                            <option value="Research">Research</option>
                            <option value="Team">Team</option>
                            <option value="Community">Community</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wide mb-2">Global Caption</label>
                        <textarea name="caption" rows="3" placeholder="Describe these photos..." class="w-full px-4 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 outline-none text-slate-600 font-medium"></textarea>
                        <p class="text-[10px] text-slate-400 mt-2 leading-relaxed">Leave blank to use the original filenames as captions.</p>
                    </div>

                    <div id="file-count" class="bg-emerald-50 text-emerald-700 text-xs font-bold p-3 rounded-xl mb-6 text-center hidden">
                        0 files selected
                    </div>

                    <button type="submit" name="bulk_add" class="w-full bg-slate-900 hover:bg-emerald-600 text-white font-black py-4 rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rocket"></i>
                        Push to Cloud
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
    
    if(files.length > 0) {
        countLabel.classList.remove('hidden');
        countLabel.innerText = `${files.length} image(s) ready for upload`;
    }

    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = "aspect-square rounded-2xl overflow-hidden border-2 border-white shadow-sm bg-slate-100 relative group animate-in fade-in zoom-in duration-300";
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            grid.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}

// Drag & Drop Styling
const dropZone = document.getElementById('drop-zone');
['dragenter', 'dragover'].forEach(name => {
    dropZone.addEventListener(name, e => {
        e.preventDefault();
        dropZone.classList.add('border-emerald-400', 'bg-emerald-50/30');
    });
});
['dragleave', 'drop'].forEach(name => {
    dropZone.addEventListener(name, e => {
        e.preventDefault();
        dropZone.classList.remove('border-emerald-400', 'bg-emerald-50/30');
    });
});
</script>

<?php include "includes/admin_footer.php"; ?>