<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 1);
require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

$today = date('Y-m-d');
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-10">
            <a href="projects.php" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 transition flex items-center gap-2 mb-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Projects
            </a>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">New Initiative</h1>
        </div>

        <form id="projectForm" action="/admin/handlers/project-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Project Title</label>
                        <input type="text" name="title" required placeholder="e.g. Solar Water Hub"
                               class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 text-xl font-bold outline-none transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Location</label>
                            <input type="text" name="location" required placeholder="City, Country"
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-medium outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">URL Slug</label>
                            <input type="text" name="slug" placeholder="leave-blank-to-auto-generate"
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none font-mono text-xs outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Short Hook (Summary)</label>
                        <textarea name="summary" rows="2" required placeholder="A brief one-sentence pitch for the cards..."
                                  class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 outline-none font-medium"></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Full Narrative</label>
                        <textarea name="description" rows="12" required placeholder="Describe the impact and goals..."
                                  class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 outline-none leading-relaxed"></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200">
                    <h3 class="text-xs font-black uppercase text-slate-400 mb-6 tracking-widest border-b pb-4">Lifecycle</h3>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Current Status</label>
                        <select name="status" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 outline-none font-bold">
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                            <option value="Planned">Planned</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Start Date</label>
                            <input type="date" name="start_date" value="<?= $today ?>" class="w-full px-3 py-2 rounded-lg bg-slate-50 border-none text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">End Date</label>
                            <input type="date" name="end_date" class="w-full px-3 py-2 rounded-lg bg-slate-50 border-none text-xs font-bold">
                        </div>
                    </div>

                    <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 cursor-pointer hover:bg-emerald-50 transition group mb-6">
                        <input type="checkbox" name="featured" value="1" class="w-5 h-5 rounded border-none text-emerald-600 focus:ring-0">
                        <span class="text-sm font-black text-slate-700 group-hover:text-emerald-700">Feature Project</span>
                    </label>

                    <button type="submit" name="add" id="submitBtn" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-5 rounded-[2rem] shadow-xl shadow-emerald-200/50 transition-all flex items-center justify-center gap-2">
                        <span id="btnText">Launch Project</span>
                        <i id="btnIcon" class="fa-solid fa-rocket"></i>
                    </button>
                </div>

                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white">
                    <h3 class="text-xs font-black uppercase text-slate-500 mb-6 tracking-widest border-b border-slate-800 pb-4">Cover Media</h3>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-bold mb-2 text-slate-300">Media Type</label>
                        <select name="media_type" id="media_type" class="w-full px-4 py-3 rounded-xl bg-slate-800 border-none text-white outline-none focus:ring-2 focus:ring-emerald-500 transition">
                            <option value="image">Upload Image</option>
                            <option value="video">Upload Video</option>
                            <option value="url">External Video (Link)</option>
                        </select>
                    </div>

                    <div id="media-preview" class="hidden w-full aspect-video rounded-2xl bg-slate-800 border border-slate-700 mb-4 overflow-hidden">
                        </div>

                    <div id="file-input-wrapper">
                        <input type="file" name="media_file" id="media_file" accept="image/*,video/*"
                               class="text-xs block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-emerald-500 file:text-white file:font-black hover:file:bg-emerald-400 cursor-pointer">
                    </div>

                    <div id="url-input-wrapper" class="hidden">
                        <input type="url" name="media_url" placeholder="YouTube or Vimeo Link" 
                               class="w-full px-4 py-3 rounded-xl bg-slate-800 border-none text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// 1. Toggle media inputs and handle previews
const mediaType = document.getElementById('media_type');
const fileInput = document.getElementById('media_file');
const mediaPreview = document.getElementById('media-preview');

mediaType.addEventListener('change', function() {
    const isUrl = this.value === 'url';
    document.getElementById('file-input-wrapper').classList.toggle('hidden', isUrl);
    document.getElementById('url-input-wrapper').classList.toggle('hidden', !isUrl);
    
    // Clear preview when switching to URL mode
    if (isUrl) mediaPreview.classList.add('hidden');
    
    // Update accept attributes
    fileInput.accept = this.value === 'video' ? 'video/*' : 'image/*';
});

fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    mediaPreview.classList.remove('hidden');
    mediaPreview.innerHTML = '<div class="w-full h-full flex items-center justify-center"><i class="fa-solid fa-circle-notch animate-spin"></i></div>';

    reader.onload = function(event) {
        if (file.type.startsWith('image/')) {
            mediaPreview.innerHTML = `<img src="${event.target.result}" class="w-full h-full object-cover">`;
        } else if (file.type.startsWith('video/')) {
            mediaPreview.innerHTML = `<video src="${event.target.result}" class="w-full h-full object-cover" muted autoplay loop></video>`;
        }
    };
    reader.readAsDataURL(file);
});

// 2. Loading state on submit
document.getElementById('projectForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const text = document.getElementById('btnText');
    const icon = document.getElementById('btnIcon');

    btn.disabled = true;
    btn.classList.add('opacity-70', 'cursor-not-allowed');
    text.innerText = 'Uploading to Cloudinary...';
    icon.className = 'fa-solid fa-spinner animate-spin';
});
</script>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>