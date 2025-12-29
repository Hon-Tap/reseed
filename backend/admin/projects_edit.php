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

if (!$project) { die("Project not found."); }
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <a href="projects.php" class="text-sm font-bold text-slate-400 hover:text-emerald-600 transition mb-2 block">
                    <i class="fa-solid fa-arrow-left"></i> Project Management
                </a>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Edit Initiative</h1>
            </div>
        </div>

        <form action="handlers/project-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-400 mb-2">Project Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" required
                               class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 text-xl font-bold">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-400 mb-2">Location</label>
                            <input type="text" name="location" value="<?= htmlspecialchars($project['location']) ?>" required
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-400 mb-2">Slug</label>
                            <input type="text" name="slug" value="<?= htmlspecialchars($project['slug']) ?>"
                                   class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none font-mono text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase text-slate-400 mb-2">Short Summary</label>
                        <textarea name="summary" rows="2" class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-medium"><?= htmlspecialchars($project['summary']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase text-slate-400 mb-2">Description</label>
                        <textarea name="description" rows="10" class="w-full px-5 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500"><?= htmlspecialchars($project['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200">
                    <h3 class="text-xs font-black uppercase text-slate-400 mb-6 tracking-widest pb-4 border-b">Settings</h3>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold mb-1">Status</label>
                        <select name="status" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-bold">
                            <option value="Ongoing" <?= $project['status'] === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                            <option value="Completed" <?= $project['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="Planned" <?= $project['status'] === 'Planned' ? 'selected' : '' ?>>Planned</option>
                        </select>
                    </div>

                    <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="featured" value="1" <?= $project['featured'] ? 'checked' : '' ?> class="w-5 h-5 rounded border-none text-emerald-600">
                        <span class="text-sm font-black text-slate-700">Featured Project</span>
                    </label>
                </div>

                <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white">
                    <h3 class="text-xs font-black uppercase text-slate-500 mb-6 tracking-widest pb-4 border-b border-slate-800">Current Media</h3>
                    
                    <?php if($project['media_type'] === 'image'): ?>
                        <img src="<?= htmlspecialchars($project['media_url']) ?>" class="w-full h-40 object-cover rounded-2xl mb-4 border border-slate-700">
                    <?php elseif($project['media_type'] === 'video' || $project['media_type'] === 'url'): ?>
                        <div class="bg-slate-800 p-4 rounded-2xl mb-4 text-center text-xs text-slate-400">
                            Video Asset Attached
                        </div>
                    <?php endif; ?>

                    <label class="block text-xs font-bold text-slate-400 mb-2 uppercase">Replace Media (Optional)</label>
                    <input type="file" name="media_file" class="text-xs block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-slate-700 file:text-white hover:file:bg-emerald-600">
                </div>

                <button type="submit" name="update" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-5 rounded-[2rem] shadow-xl shadow-emerald-200/50 transition transform active:scale-95">
                    Apply Updates
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>