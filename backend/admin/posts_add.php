<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 1);
require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

$currentTime = date('Y-m-d\TH:i');
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-10">
            <a href="posts.php" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 transition flex items-center gap-2 mb-2">
                <i class="fa-solid fa-chevron-left"></i> Back to Stories
            </a>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Draft New Story</h1>
        </div>

        <form id="postForm" action="/admin/handlers/post-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Headline</label>
                        <input type="text" name="title" required placeholder="Enter a compelling title..."
                               class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 text-2xl font-black outline-none transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Author Name</label>
                            <input type="text" name="author" required value="<?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?>"
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-bold outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">URL Slug (Optional)</label>
                            <input type="text" name="slug" placeholder="auto-generated-if-blank"
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none font-mono text-xs outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Short Excerpt</label>
                        <textarea name="excerpt" rows="2" required placeholder="A brief summary for the blog cards..."
                                  class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 outline-none font-medium"></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Post Content</label>
                        <textarea name="content" rows="15" required placeholder="Write your story here..."
                                  class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 outline-none leading-relaxed"></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200">
                    <h3 class="text-xs font-black uppercase text-slate-400 mb-6 tracking-widest border-b pb-4">Publishing</h3>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Schedule Date</label>
                        <input type="datetime-local" name="published_at" value="<?= $currentTime ?>"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border-none font-bold outline-none">
                    </div>

                    <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 cursor-pointer hover:bg-emerald-50 transition mb-6">
                        <input type="checkbox" name="featured" value="1" class="w-5 h-5 rounded border-none text-emerald-600 focus:ring-0">
                        <span class="text-sm font-black text-slate-700">Feature this post</span>
                    </label>

                    <button type="submit" name="add" id="submitBtn" 
                            class="w-full bg-slate-900 hover:bg-emerald-600 text-white font-black py-5 rounded-[2rem] shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                        <span id="btnText">Publish Story</span>
                        <i id="btnIcon" class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200">
                    <h3 class="text-xs font-black uppercase text-slate-400 mb-6 tracking-widest border-b pb-4">Cover Image</h3>
                    
                    <div id="image-preview" class="w-full aspect-video rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 mb-4 flex items-center justify-center overflow-hidden">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">No Image Selected</span>
                    </div>

                    <input type="file" name="media_file" accept="image/*" onchange="previewImage(event)"
                           class="text-xs block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-black hover:file:bg-emerald-100 cursor-pointer">
                    
                    <input type="hidden" name="media_type" value="image">
                </div>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * Visual feedback for image selection
 */
function previewImage(event) {
    const reader = new FileReader();
    const output = document.getElementById('image-preview');
    
    reader.onload = function() {
        output.innerHTML = `<img src="${reader.result}" class="w-full h-full object-cover">`;
        output.classList.remove('border-dashed', 'bg-slate-50');
    }
    
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

/**
 * Handle Loading State on Submit
 */
document.getElementById('postForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const text = document.getElementById('btnText');
    const icon = document.getElementById('btnIcon');

    // Disable button to prevent double submission
    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-not-allowed');
    
    // Change text and icon
    text.innerText = 'Uploading to Cloud...';
    icon.className = 'fa-solid fa-circle-notch animate-spin';
});
</script>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>