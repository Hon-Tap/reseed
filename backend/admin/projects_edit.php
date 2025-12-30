<?php

declare(strict_types=1);

$backendPath = dirname(__DIR__, 1);
require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    header('Location: projects.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "<div class='p-10 text-center font-bold'>Project not found.</div>";
    require_once $backendPath . '/admin/includes/admin_footer.php';
    exit;
}

$startDate = $project['start_date'] ? date('Y-m-d', strtotime($project['start_date'])) : '';
$endDate   = $project['end_date'] ? date('Y-m-d', strtotime($project['end_date'])) : '';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50/50 min-h-screen p-6 md:p-10">
<div class="max-w-6xl mx-auto">

    <div class="mb-10">
        <a href="projects.php"
           class="text-sm font-bold text-slate-400 hover:text-emerald-600 transition mb-2 inline-flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Back to Archive
        </a>

        <div class="flex items-center gap-4">
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Edit Initiative</h1>
            <span class="px-4 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-black uppercase tracking-widest">
                ID: #<?= (int) $project['id'] ?>
            </span>
        </div>
    </div>

    <form id="editForm"
          action="/admin/handlers/project-handler.php"
          method="POST"
          enctype="multipart/form-data"
          class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">

        <!-- LEFT -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6">

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">
                        Project Title
                    </label>
                    <input type="text" name="title" id="projectTitle"
                           value="<?= htmlspecialchars($project['title']) ?>"
                           required
                           class="w-full px-6 py-4 rounded-2xl bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xl font-bold outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">
                            Location
                        </label>
                        <input type="text" name="location"
                               value="<?= htmlspecialchars($project['location']) ?>"
                               required
                               class="w-full px-5 py-3 rounded-xl bg-slate-50 focus:ring-2 focus:ring-emerald-500 font-medium outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">
                            Permalink Slug
                        </label>
                        <input type="text" name="slug" id="projectSlug"
                               value="<?= htmlspecialchars($project['slug']) ?>"
                               class="w-full px-5 py-3 rounded-xl bg-slate-100 font-mono text-xs outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">
                        Executive Summary
                    </label>
                    <textarea name="summary" rows="2" required
                              class="w-full px-5 py-3 rounded-xl bg-slate-50 focus:ring-2 focus:ring-emerald-500 font-medium outline-none"><?= htmlspecialchars($project['summary']) ?></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">
                        Full Narrative
                    </label>
                    <textarea name="description" rows="12" required
                              class="w-full px-5 py-3 rounded-xl bg-slate-50 focus:ring-2 focus:ring-emerald-500 leading-relaxed outline-none"><?= htmlspecialchars($project['description']) ?></textarea>
                </div>

            </div>
        </div>

        <!-- RIGHT -->
        <div class="space-y-6">

            <!-- Lifecycle -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200">

                <h3 class="text-xs font-black uppercase text-slate-400 mb-6 tracking-widest border-b pb-4">
                    Lifecycle
                </h3>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Status</label>
                    <select name="status"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 focus:ring-2 focus:ring-emerald-500 font-bold outline-none">
                        <?php foreach (['Ongoing', 'Completed', 'Planned'] as $s): ?>
                            <option value="<?= $s ?>" <?= $project['status'] === $s ? 'selected' : '' ?>>
                                <?= $s ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">
                            Start Date
                        </label>
                        <input type="date" name="start_date" value="<?= $startDate ?>"
                               class="w-full px-3 py-2 rounded-lg bg-slate-50 text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">
                            End Date
                        </label>
                        <input type="date" name="end_date" value="<?= $endDate ?>"
                               class="w-full px-3 py-2 rounded-lg bg-slate-50 text-xs font-bold">
                    </div>
                </div>

                <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 cursor-pointer hover:bg-emerald-50 transition mb-6">
                    <input type="checkbox" name="featured" value="1"
                           <?= $project['featured'] ? 'checked' : '' ?>
                           class="w-5 h-5 text-emerald-600">
                    <span class="text-sm font-black text-slate-700">Feature on Home</span>
                </label>

                <button type="submit" id="submitBtn"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-5 rounded-[2rem] shadow-xl shadow-emerald-200 flex items-center justify-center gap-2">
                    <span id="btnText">Save Changes</span>
                    <i id="btnIcon" class="fa-solid fa-check-double"></i>
                </button>
            </div>

            <!-- Media -->
            <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white">

                <h3 class="text-xs font-black uppercase text-slate-500 mb-6 tracking-widest border-b border-slate-800 pb-4">
                    Current Media
                </h3>

                <div class="mb-6 aspect-video rounded-2xl overflow-hidden bg-slate-800 border border-slate-700">
                    <?php if ($project['cover_media']): ?>
                        <?php if ($project['media_type'] === 'video'): ?>
                            <video src="<?= htmlspecialchars($project['cover_media']) ?>" controls class="w-full h-full object-cover"></video>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($project['cover_media']) ?>" class="w-full h-full object-cover">
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="flex items-center justify-center h-full text-slate-500">
                            <i class="fa-regular fa-image text-4xl"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <label class="block text-xs font-bold mb-2 text-slate-400 uppercase tracking-widest">
                    Replace Media
                </label>

                <select name="media_type" id="media_type"
                        class="w-full mb-4 px-4 py-3 rounded-xl bg-slate-800 text-white focus:ring-2 focus:ring-emerald-500">
                    <option value="image">New Image</option>
                    <option value="video">New Video</option>
                </select>

                <input type="file" name="media_file" id="media_file"
                       accept="image/*,video/*"
                       class="text-xs block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-emerald-500 file:text-white file:font-black hover:file:bg-emerald-400 cursor-pointer">
            </div>

        </div>
    </form>
</div>
</div>

<script>
const titleInput = document.getElementById('projectTitle');
const slugInput  = document.getElementById('projectSlug');
let manualSlug   = false;

slugInput.addEventListener('input', () => manualSlug = true);

titleInput.addEventListener('input', () => {
    if (!manualSlug) {
        slugInput.value = titleInput.value
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
});

document.getElementById('editForm').addEventListener('submit', () => {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('btnText').innerText = 'Updating Archive...';
    document.getElementById('btnIcon').className = 'fa-solid fa-spinner animate-spin';
});
</script>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>
