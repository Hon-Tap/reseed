<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: projects.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex items-center gap-4">
            <a href="projects.php" class="bg-white p-2 rounded-lg border hover:text-emerald-600 transition shadow-sm text-slate-400">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900">Update Project</h2>
                <p class="text-slate-500">Editing: <?= htmlspecialchars($project['title']) ?></p>
            </div>
        </div>

        <form action="project-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Project Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" required
                               class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 outline-none transition text-lg font-semibold">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Slug</label>
                            <input type="text" name="slug" value="<?= htmlspecialchars($project['slug']) ?>"
                                   class="w-full px-3 py-2 rounded-lg border bg-slate-50 font-mono text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Location</label>
                            <input type="text" name="location" value="<?= htmlspecialchars($project['location']) ?>" required
                                   class="w-full px-3 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Summary</label>
                        <textarea name="summary" rows="3" required class="w-full px-4 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-emerald-500"><?= htmlspecialchars($project['summary']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Full Description</label>
                        <textarea name="description" rows="12" required class="w-full px-4 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-emerald-500"><?= htmlspecialchars($project['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-xs font-bold uppercase text-slate-400 mb-4 tracking-widest">Settings</h3>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 rounded-lg border bg-white focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="Ongoing" <?= $project['status'] === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                            <option value="Completed" <?= $project['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="Planned" <?= $project['status'] === 'Planned' ? 'selected' : '' ?>>Planned</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Start Date</label>
                            <input type="date" name="start_date" value="<?= $project['start_date'] ?>" class="w-full text-xs px-2 py-2 rounded border">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">End Date</label>
                            <input type="date" name="end_date" value="<?= $project['end_date'] ?>" class="w-full text-xs px-2 py-2 rounded border">
                        </div>
                    </div>

                    <label class="flex items-center space-x-3 cursor-pointer p-3 mb-6 rounded-xl border border-dashed border-slate-200 hover:bg-emerald-50 transition">
                        <input type="checkbox" name="featured" value="1" class="w-5 h-5 text-emerald-600 rounded" <?= !empty($project['featured']) ? 'checked' : '' ?>>
                        <span class="text-sm font-bold text-slate-700">Featured Project</span>
                    </label>

                    <button type="submit" name="update" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-lg transition">
                        Update Project
                    </button>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-xs font-bold uppercase text-slate-400 mb-4 tracking-widest">Media Asset</h3>
                    
                    <select name="media_type" class="w-full mb-4 px-3 py-2 rounded-lg border bg-slate-50 text-sm">
                        <option value="image" <?= $project['media_type'] === 'image' ? 'selected' : '' ?>>Image</option>
                        <option value="video" <?= $project['media_type'] === 'video' ? 'selected' : '' ?>>Video</option>
                    </select>

                    <?php if (!empty($project['cover_image'])): ?>
                        <div class="mb-4 rounded-xl overflow-hidden border">
                            <img src="../uploads/projects/<?= $project['cover_image'] ?>" class="w-full h-32 object-cover">
                        </div>
                    <?php endif; ?>

                    <input type="file" name="media_file" class="text-xs block w-full mb-4">
                    <input type="url" name="media_url" value="<?= htmlspecialchars($project['media_url'] ?? '') ?>" 
                           placeholder="External URL" class="w-full px-3 py-2 rounded-lg border text-xs outline-none">
                </div>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>