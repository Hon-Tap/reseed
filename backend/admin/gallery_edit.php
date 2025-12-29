<?php
declare(strict_types=1);

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

if (!$item) { die("Asset not found."); }

// Handle Cloudinary/Legacy URLs
$imageUrl = (strpos($item['filename'], 'http') === 0) 
    ? $item['filename'] 
    : UPLOADS_URL . '/gallery/' . $item['filename'];
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10">
    <div class="max-w-4xl mx-auto">
        
        <div class="mb-10 flex items-center justify-between">
            <div>
                <a href="gallery.php" class="text-sm font-bold text-slate-400 hover:text-emerald-600 transition block mb-2">
                    <i class="fa-solid fa-chevron-left mr-1"></i> Back to Archive
                </a>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Edit Media Info</h1>
            </div>
            
            <form action="handlers/gallery-handler.php" method="POST" onsubmit="return confirm('Delete this asset permanently?');">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <input type="hidden" name="delete" value="1">
                <button type="submit" class="bg-rose-50 text-rose-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition">
                    Delete Asset
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div>
                <div class="bg-white p-4 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                    <div class="aspect-[4/5] rounded-[2rem] overflow-hidden bg-slate-100">
                        <img id="main-preview" src="<?= htmlspecialchars($imageUrl) ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <p class="text-[10px] font-mono text-slate-400 truncate uppercase tracking-tighter">
                            Source: <?= htmlspecialchars(basename($item['filename'])) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col justify-center">
                <form action="handlers/gallery-handler.php" method="POST" class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">

                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.15em] mb-2">Caption</label>
                        <input type="text" name="caption" value="<?= htmlspecialchars($item['caption']) ?>" 
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800 outline-none transition" required>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.15em] mb-2">Category</label>
                        <input type="text" name="category" value="<?= htmlspecialchars($item['category']) ?>" 
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-bold text-slate-800 outline-none transition" required>
                    </div>

                    <div class="pt-4">
                        <button type="submit" name="update" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-emerald-100 transition active:scale-95">
                            Save Changes
                        </button>
                    </div>
                </form>
                
                <p class="text-center text-slate-400 text-xs mt-6">
                    Added on <?= date('F d, Y', strtotime($item['created_at'])) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include "includes/admin_footer.php"; ?>