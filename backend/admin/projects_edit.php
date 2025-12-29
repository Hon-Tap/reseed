<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__, 1);
require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: projects.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) { 
    echo "<div class='p-10 text-center font-bold'>Project not found.</div>";
    exit; 
}

// Format dates for the HTML5 date picker
$startDate = $project['start_date'] ? date('Y-m-d', strtotime($project['start_date'])) : '';
$endDate = $project['end_date'] ? date('Y-m-d', strtotime($project['end_date'])) : '';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-10">
            <a href="projects.php" class="text-sm font-bold text-slate-400 hover:text-emerald-600 transition mb-2 inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Archive
            </a>
            <div class="flex items-center gap-4">
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Edit Initiative</h1>
                <span class="px-4 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-black uppercase tracking-widest">
                    ID: #<?= $project['id'] ?>
                </span>
            </div>
        </div>

        <form id="editForm" action="/admin/handlers/project-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Project Title</label>
                        <input type="text" name="title" id="projectTitle" value="<?= htmlspecialchars($project['title']) ?>" required
                               class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 text-xl font-bold transition outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Location</label>
                            <input type="text" name="location" value="<?= htmlspecialchars($project['location']) ?>" required
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-medium transition outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Permalink Slug</label>
                            <input type="text" name="slug" id="projectSlug" value="<?= htmlspecialchars($project['slug']) ?>"
                                   class="w-full px-5 py-3 rounded-xl bg-slate-100 border-none font-mono text-xs text-slate-500 focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Executive Summary</label>
                        <textarea name="summary" rows="2" required class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-medium outline-none"><?= htmlspecialchars($project['summary']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Full Narrative</label>
                        <textarea name="description" rows="12" required class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 leading-relaxed outline-none"><?= htmlspecialchars($project['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200">
                    <h3 class="text-xs font-black uppercase text-slate-400 mb-6 tracking-widest border-b pb-4">Lifecycle</h3>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-bold outline-none">
                            <option value="Ongoing" <?= $project['status'] === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                            <option value="Completed" <?= $project['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="Planned" <?= $project['status'] === 'Planned' ? 'selected' : '' ?>>Planned</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Start Date</label>
                            <input type="date" name="start_date" value="<?= $startDate ?>" class="w-full px-3 py-2 rounded-lg bg-slate-50 border-none text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">End Date</label>
                            <input type="date" name="end_date" value="<?= $endDate ?>" class="w-full px-3 py-2 rounded-lg bg-slate-50 border-none text-xs font-bold">
                        </div>
                    </div>

                    <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 cursor-pointer hover:bg-emerald-50 transition group mb-6">
                        <input type="checkbox" name="featured" value="1" <?= $project['featured'] ? 'checked' : '' ?> class="w-5 h-5 rounded border-none text-emerald-600 focus:ring-0">
                        <span class="text-sm font-black text-slate-700 group-hover:text-emerald-700 transition-colors">Feature on Home</span>
                    </label>

                    <button type="submit" name="update" id="submitBtn" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-5 rounded-[2rem] shadow-xl shadow-emerald-200/50 transition transform active:scale-95 flex items-center justify-center gap-2">
                        <span id="btnText">Save Changes</span>
                        <i id="btnIcon" class="fa-solid fa-check-double"></i>
                    </button>
                </div>

                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white">
                    <h3 class="text-xs font-black uppercase text-slate-500 mb-6 tracking-widest border-b border-slate-800 pb-4">Live Media</h3>
                    
                    <div class="mb-6">
                        <?php if($project['media_type'] === 'image'): ?>
                            <div class="relative group rounded-2xl overflow-hidden aspect-video bg-slate-800 border border-slate-700">
                                <img src="<?= htmlspecialchars($project['media_url']) ?>" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="bg-black/50 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase">Current Image</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-col items-center justify-center aspect-video rounded-2xl bg-slate-800 border border-slate-700 border-dashed text-slate-500">
                                <i class="fa-solid fa-film text-3xl mb-2"></i>
                                <span class="text-[10px] font-bold uppercase tracking-widest">Video Content</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold mb-2 text-slate-400 uppercase tracking-widest">Update Content</label>
                            <select name="media_type" id="media_type" class="w-full px-4 py-3 rounded-xl bg-slate-800 border-none text-white outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                <option value="image" <?= $project['media_type'] === 'image' ? 'selected' : '' ?>>New Image</option>
                                <option value="video" <?= $project['media_type'] === 'video' ? 'selected' : '' ?>>New Video Upload</option>
                                <option value="url" <?= $project['media_type'] === 'url' ? 'selected' : '' ?>>External URL</option>
                            </select>
                        </div>

                        <div id="file-wrapper">
                            <input type="file" name="media_file" class="text-xs block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-emerald-500 file:text-white file:font-black hover:file:bg-emerald-400 cursor-pointer">
                        </div>

                        <div id="url-wrapper" class="hidden">
                            <input type="url" name="media_url" value="<?= $project['media_type'] === 'url' ? htmlspecialchars($project['media_url']) : '' ?>" 
                                   placeholder="YouTube/Vimeo Link" class="w-full px-4 py-3 rounded-xl bg-slate-800 border-none text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// 1. Slug Auto-generation (Syncs with title unless edited)
const titleInput = document.getElementById('projectTitle');
const slugInput = document.getElementById('projectSlug');
let isManualSlug = false;

slugInput.addEventListener('input', () => { isManualSlug = true; });

titleInput.addEventListener('input', function() {
    if (!isManualSlug) {
        slugInput.value = this.value
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
});

// 2. Media Toggle
const mediaTypeSelect = document.getElementById('media_type');
const fileWrapper = document.getElementById('file-wrapper');
const urlWrapper = document.getElementById('url-wrapper');

function toggleMediaInputs() {
    if (mediaTypeSelect.value === 'url') {
        fileWrapper.classList.add('hidden');
        urlWrapper.classList.remove('hidden');
    } else {
        fileWrapper.classList.remove('hidden');
        urlWrapper.classList.add('hidden');
    }
}

mediaTypeSelect.addEventListener('change', toggleMediaInputs);
toggleMediaInputs(); // Initial run

// 3. Loading State
document.getElementById('editForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.classList.add('opacity-70', 'cursor-not-allowed');
    document.getElementById('btnText').innerText = 'Updating Archive...';
    document.getElementById('btnIcon').className = 'fa-solid fa-circle-notch animate-spin';
});
</script>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>