<?php
declare(strict_types=1);

/**
 * Admin — Gallery Edit (Cloudinary Optimized)
 * Path: /backend/admin/gallery_edit.php
 */

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: gallery.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) { 
    echo "<div class='p-10 text-center font-bold text-slate-500'>Asset not found in the vault.</div>";
    exit; 
}

// Optimization: Use a smaller preview if it's a Cloudinary link
$displayUrl = $item['filename'];
if (strpos($displayUrl, 'cloudinary.com') !== false) {
    $displayUrl = str_replace('/upload/', '/upload/w_800,c_limit,q_auto,f_auto/', $item['filename']);
} elseif (strpos($displayUrl, 'http') !== 0) {
    $displayUrl = UPLOADS_URL . '/gallery/' . $item['filename'];
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10">
    <div class="max-w-5xl mx-auto">
        
        <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <a href="gallery.php" class="text-sm font-bold text-slate-400 hover:text-emerald-600 transition inline-flex items-center group mb-2">
                    <i class="fa-solid fa-chevron-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i> 
                    Back to Media Vault
                </a>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Modify Asset</h1>
            </div>
            
            <form action="handlers/gallery-handler.php" method="POST" onsubmit="return confirm('Delete this asset permanently from Cloudinary and Database?');">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <input type="hidden" name="delete" value="1">
                <button type="submit" class="bg-rose-50 text-rose-600 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition shadow-sm">
                    <i class="fa-solid fa-trash-can mr-2"></i> Delete Asset
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
            
            <div class="lg:col-span-3">
                <div class="bg-white p-4 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                    <div class="relative aspect-video rounded-[1.8rem] overflow-hidden bg-slate-100 group">
                        <img id="main-preview" src="<?= htmlspecialchars($displayUrl) ?>" class="w-full h-full object-cover">
                        
                        <a href="<?= htmlspecialchars($item['filename']) ?>" target="_blank" 
                           class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                           <span class="bg-white text-slate-900 px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-widest">View Full Resolution</span>
                        </a>
                    </div>
                    
                    <div class="mt-4 px-2 flex items-center justify-between">
                        <div class="flex-1 mr-4">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Direct Cloudinary Link</p>
                            <input type="text" readonly value="<?= htmlspecialchars($item['filename']) ?>" id="rawUrl"
                                   class="w-full bg-slate-50 border-none rounded-lg px-3 py-2 text-[10px] font-mono text-slate-500 focus:ring-0">
                        </div>
                        <button onclick="copyToClipboard()" class="mt-4 p-3 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl transition text-slate-500" title="Copy URL">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <form action="handlers/gallery-handler.php" method="POST" class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 pl-1">Image Caption</label>
                        <textarea name="caption" rows="3" 
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800 outline-none transition resize-none" 
                               required placeholder="What is happening in this photo?"><?= htmlspecialchars($item['caption']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 pl-1">Organizational Category</label>
                        <input type="text" name="category" value="<?= htmlspecialchars($item['category']) ?>" 
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800 outline-none transition" 
                               required placeholder="e.g. Construction, Community, Staff">
                    </div>

                    <div class="pt-4">
                        <button type="submit" name="update" class="group w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-5 rounded-[1.8rem] shadow-xl shadow-emerald-200/50 transition transform active:scale-95 flex items-center justify-center gap-2">
                            Update Metadata
                            <i class="fa-solid fa-check-circle transition-transform group-hover:scale-110"></i>
                        </button>
                    </div>
                </form>
                
                <div class="bg-slate-900 p-6 rounded-[2rem] text-center">
                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Date Logged</p>
                    <p class="text-white font-bold tracking-tight">
                        <?= date('l, F d, Y', strtotime($item['created_at'])) ?>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function copyToClipboard() {
        const copyText = document.getElementById("rawUrl");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        // Visual feedback
        const btn = event.currentTarget;
        const icon = btn.querySelector('i');
        icon.className = 'fa-solid fa-check text-emerald-500';
        setTimeout(() => {
            icon.className = 'fa-solid fa-copy';
        }, 2000);
    }
</script>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>