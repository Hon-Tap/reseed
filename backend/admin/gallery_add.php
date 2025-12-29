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
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <a href="gallery.php" class="inline-flex items-center text-sm font-bold text-emerald-600 hover:text-emerald-700 transition mb-2">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Back to Media Vault
                </a>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Upload Media</h1>
                <p class="text-slate-500 mt-1">Archive high-resolution project photography.</p>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-black text-slate-600 uppercase tracking-widest">Cloudinary Active</span>
            </div>
        </div>

        <form id="uploadForm" action="handlers/gallery-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="lg:col-span-2 space-y-6">
                <div id="drop-zone" class="bg-white p-12 rounded-[2.5rem] shadow-sm border-4 border-dashed border-slate-200 text-center transition-all duration-300 relative group overflow-hidden">
                    <input type="file" name="images[]" id="image-input" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div id="drop-zone-prompt" class="space-y-4">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-slate-50 text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                            <i class="fa-solid fa-images text-4xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Drop files to upload</h3>
                            <p class="text-slate-400 font-medium">Select multiple images at once</p>
                        </div>
                        <button type="button" class="px-6 py-2 bg-slate-900 text-white rounded-full text-sm font-bold pointer-events-none">Browse Computer</button>
                    </div>

                    <div id="preview-grid" class="hidden mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 relative z-20"></div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 sticky top-10">
                    <h3 class="text-[10px] font-black uppercase text-slate-400 mb-6 tracking-[0.2em] flex items-center gap-2">
                        <i class="fa-solid fa-tags"></i> Bulk Metadata
                    </h3>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wide mb-2">Target Category</label>
                            <select name="category" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 outline-none font-bold text-slate-700 transition">
                                <option value="Field Work">Field Work</option>
                                <option value="Events">Events</option>
                                <option value="Research">Research</option>
                                <option value="Team">Team</option>
                                <option value="Community">Community</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wide mb-2">Group Caption</label>
                            <textarea name="caption" rows="3" placeholder="Describe these photos..." 
                                      class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 outline-none text-slate-600 font-medium transition"></textarea>
                            <p class="text-[10px] text-slate-400 mt-3 italic leading-relaxed">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                If empty, filenames will be used as captions.
                            </p>
                        </div>

                        <div id="status-card" class="hidden p-4 rounded-2xl border-2 border-emerald-100 bg-emerald-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-black" id="file-count-badge">0</div>
                                <div>
                                    <p class="text-sm font-black text-emerald-900 leading-none">Files Selected</p>
                                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mt-1">Ready for Cloud Sync</p>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="bulk_add" id="submitBtn" 
                                class="w-full bg-slate-900 hover:bg-emerald-600 text-white font-black py-5 rounded-[2rem] shadow-lg transition-all active:scale-95 flex items-center justify-center gap-3">
                            <span id="btnText">Initiate Upload</span>
                            <i id="btnIcon" class="fa-solid fa-cloud-arrow-up"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const input = document.getElementById('image-input');
const grid = document.getElementById('preview-grid');
const prompt = document.getElementById('drop-zone-prompt');
const statusCard = document.getElementById('status-card');
const countBadge = document.getElementById('file-count-badge');
const dropZone = document.getElementById('drop-zone');

input.addEventListener('change', function() {
    handleFiles(this.files);
});

function handleFiles(files) {
    grid.innerHTML = '';
    
    if(files.length > 0) {
        prompt.classList.add('hidden');
        grid.classList.remove('hidden');
        statusCard.classList.remove('hidden');
        countBadge.innerText = files.length;
    } else {
        prompt.classList.remove('hidden');
        grid.classList.add('hidden');
        statusCard.classList.add('hidden');
    }

    Array.from(files).slice(0, 12).forEach(file => { // Preview first 12 for performance
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = "aspect-square rounded-2xl overflow-hidden border-2 border-white shadow-md bg-slate-100 animate-in fade-in zoom-in duration-300";
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            grid.appendChild(div);
        }
        reader.readAsDataURL(file);
    });

    if(files.length > 12) {
        const more = document.createElement('div');
        more.className = "aspect-square rounded-2xl flex items-center justify-center bg-slate-200 text-slate-500 font-black text-xs";
        more.innerText = `+${files.length - 12} MORE`;
        grid.appendChild(more);
    }
}

// Visual Drag feedback
['dragenter', 'dragover'].forEach(name => {
    dropZone.addEventListener(name, () => dropZone.classList.add('border-emerald-400', 'bg-emerald-50/20'), false);
});
['dragleave', 'drop'].forEach(name => {
    dropZone.addEventListener(name, () => dropZone.classList.remove('border-emerald-400', 'bg-emerald-50/20'), false);
});

// Form Submission State
document.getElementById('uploadForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const text = document.getElementById('btnText');
    const icon = document.getElementById('btnIcon');

    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-wait');
    text.innerText = "Syncing with Cloudinary...";
    icon.className = "fa-solid fa-circle-notch animate-spin";
});
</script>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>