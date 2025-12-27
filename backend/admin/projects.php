<?php

declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

/*
|--------------------------------------------------------------------------
| Fetch projects
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT 
        id,
        title,
        slug,
        location,
        status,
        featured,
        cover_image,
        start_date,
        end_date,
        created_at
    FROM projects
    WHERE (:search = '' OR title ILIKE :search_like)
    ORDER BY created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'search'      => $search,
    'search_like' => "%{$search}%"
]);

$projects   = $stmt->fetchAll(PDO::FETCH_ASSOC);
$uploadPath = UPLOAD_URL . '/projects/';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                    Projects
                </h1>
                <p class="text-slate-500 mt-2 font-medium">
                    Manage restoration and development initiatives.
                </p>
            </div>

            <a
                href="projects_add.php"
                class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-200"
            >
                <i class="fa-solid fa-plus mr-2"></i>
                New Project
            </a>
        </div>

        <!-- Search -->
        <div class="mb-8 max-w-md">
            <form method="get" class="relative group">
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search projects..."
                    class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-2xl
                           focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500
                           outline-none transition shadow-sm"
                >
                <div class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold tracking-widest">
                        <th class="px-8 py-5">Cover</th>
                        <th class="px-8 py-5">Project Details</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5">Timeline</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                <?php if ($projects): foreach ($projects as $p): ?>

                    <?php
                        $statusColor = match (strtolower($p['status'] ?? '')) {
                            'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                            'ongoing'   => 'bg-blue-50 text-blue-600 border-blue-100',
                            default     => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                    ?>

                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <!-- Cover -->
                        <td class="px-8 py-6">
                            <?php if (!empty($p['cover_image'])): ?>
                                <img
                                    src="<?= $uploadPath . htmlspecialchars($p['cover_image']) ?>"
                                    alt=""
                                    class="w-14 h-14 object-cover rounded-xl shadow-sm border border-slate-100"
                                >
                            <?php else: ?>
                                <div class="w-14 h-14 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 border border-slate-200">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Details -->
                        <td class="px-8 py-6">
                            <div class="font-bold text-slate-800 text-lg">
                                <?= htmlspecialchars($p['title']) ?>
                                <?php if ($p['featured']): ?>
                                    <span class="ml-2 px-2 py-0.5 text-[10px] font-black uppercase rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200">
                                        Featured
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs text-slate-500">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-location-dot text-[10px]"></i>
                                    <?= htmlspecialchars($p['location']) ?>
                                </span>
                                <span class="text-slate-300">•</span>
                                <code class="text-slate-400">
                                    <?= htmlspecialchars($p['slug']) ?>
                                </code>
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full border <?= $statusColor ?>">
                                <?= htmlspecialchars($p['status']) ?>
                            </span>
                        </td>

                        <!-- Timeline -->
                        <td class="px-8 py-6 text-sm text-slate-500">
                            <div>Start: <strong><?= htmlspecialchars($p['start_date']) ?></strong></div>
                            <div>End: <strong><?= $p['end_date'] ?: 'Ongoing' ?></strong></div>
                        </td>

                        <!-- Actions -->
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a
                                    href="projects_edit.php?id=<?= $p['id'] ?>"
                                    class="w-10 h-10 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition"
                                >
                                    <i class="fa-solid fa-edit"></i>
                                </a>

                                <form
                                    action="handlers/project-handler.php"
                                    method="post"
                                    onsubmit="return confirm('Delete this project permanently?')"
                                >
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <button
                                        name="delete"
                                        class="w-10 h-10 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-xl transition"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                <?php endforeach; else: ?>

                    <tr>
                        <td colspan="5" class="p-16 text-center text-slate-400 font-medium">
                            No projects found.
                        </td>
                    </tr>

                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
