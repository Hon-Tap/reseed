<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/csrf.php';   // ← REQUIRED
require_once $backendPath . '/admin/includes/admin_header.php';



$search = $_GET['search'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM projects WHERE title ILIKE ? ORDER BY created_at DESC");
$stmt->execute(["%{$search}%"]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
$uploadPath = UPLOAD_URL . '/projects/';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="max-w-7xl mx-auto">

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Restoration Projects</h1>
                <p class="text-slate-500 mt-2 font-medium">Track and manage large-scale environmental initiatives.</p>
            </div>
            <a href="projects_add.php" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-200">
                <i class="fa-solid fa-leaf mr-2"></i> New Project
            </a>
        </div>

        <div class="mb-8 max-w-md">
            <form method="GET" class="relative group">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search projects..." 
                       class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition shadow-sm">
                <div class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                    <i class="fa-solid fa-search"></i>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold tracking-widest">
                        <th class="px-8 py-5">Cover</th>
                        <th class="px-8 py-5">Initiative Details</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5">Timeline</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                <?php if ($projects): foreach ($projects as $p): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <?php if ($p['cover_image']): ?>
                                <img src="<?= $uploadPath . $p['cover_image'] ?>" class="w-16 h-16 object-cover rounded-xl border border-slate-100 shadow-sm">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-100 text-slate-300">
                                    <i class="fa-solid fa-mountain-sun text-2xl"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800 text-lg"><?= htmlspecialchars($p['title']) ?></span>
                                <?php if ($p['featured']): ?>
                                    <span class="text-amber-400 bg-amber-50 px-2 py-0.5 rounded text-[10px] font-black uppercase border border-amber-100">Featured</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-[10px]"></i> <?= htmlspecialchars($p['location']) ?>
                                <span class="text-slate-200">|</span>
                                <span class="font-mono"><?= htmlspecialchars($p['slug']) ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                <?= $p['status'] ?>
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-[11px] font-bold text-slate-500 uppercase">Started: <span class="text-slate-800"><?= $p['start_date'] ?></span></div>
                            <div class="text-[11px] font-bold text-slate-400 uppercase">End: <span class="text-slate-600"><?= $p['end_date'] ?: 'Ongoing' ?></span></div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="projects_edit.php?id=<?= $p['id'] ?>" class="w-10 h-10 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="handlers/project-handler.php" method="POST" onsubmit="return confirm('Delete permanently?')" class="inline">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <button name="delete" class="w-10 h-10 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-xl transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5" class="p-16 text-center text-slate-400">No projects listed.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>