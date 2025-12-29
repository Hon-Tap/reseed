<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

// --- Data Fetching ---
$search = $_GET['search'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));

// Adjust SQL based on your database (using ILIKE for Postgres as per your previous file)
$query = "SELECT * FROM projects 
          WHERE title ILIKE ? OR location ILIKE ? 
          ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%", "%$search%"]);
$projects = $stmt->fetchAll();

$uploadUrl = '../uploads/projects/'; // Adjust path as needed relative to this file
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-[#f8fafc] min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Projects</h1>
                <p class="text-slate-500 mt-2 font-medium">Track development and restoration initiatives.</p>
            </div>
            <a href="projects_add.php" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-200">
                <i class="fa-solid fa-plus mr-2"></i> New Project
            </a>
        </div>

        <div class="mb-8 max-w-md">
            <form method="GET" class="relative group">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search projects..." 
                       class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition shadow-sm">
                <div class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto"> <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-bold tracking-widest">
                            <th class="px-8 py-5">Project Details</th>
                            <th class="px-8 py-5">Timeline</th>
                            <th class="px-8 py-5">Status</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if ($projects): foreach($projects as $p): 
                            // Status Color Logic
                            $statusColor = match(strtolower($p['status'] ?? '')) {
                                'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'ongoing'   => 'bg-blue-50 text-blue-600 border-blue-100',
                                'planned'   => 'bg-amber-50 text-amber-600 border-amber-100',
                                default     => 'bg-slate-100 text-slate-600 border-slate-200',
                            };
                            
                            $hasImage = !empty($p['cover_image']);
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                   <?php
                                    $hasImage = !empty($p['cover_image']) && filter_var($p['cover_image'], FILTER_VALIDATE_URL);
                                    ?>

                                    <?php if ($hasImage): ?>
                                        <img 
                                            src="<?= htmlspecialchars($p['cover_image']) ?>" 
                                            class="w-16 h-16 object-cover rounded-xl shadow-sm border border-slate-100"
                                            loading="lazy"
                                            alt=""
                                        >
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 border border-slate-200">
                                            <i class="fa-solid fa-briefcase text-2xl"></i>
                                        </div>
                                    <?php endif; ?>

                                    
                                    <div>
                                        <div class="font-bold text-slate-800 text-lg hover:text-emerald-600 transition-colors cursor-pointer">
                                            <?= htmlspecialchars($p['title']) ?>
                                        </div>
                                        <div class="text-sm text-slate-500 mt-1 flex items-center gap-2">
                                            <i class="fa-solid fa-location-dot text-slate-400 text-xs"></i> 
                                            <?= htmlspecialchars($p['location'] ?? 'N/A') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1 text-sm">
                                    <span class="text-slate-500 font-medium">Start: <span class="text-slate-700"><?= htmlspecialchars($p['start_date']) ?></span></span>
                                    <span class="text-slate-500 font-medium">End: <span class="text-slate-700"><?= htmlspecialchars($p['end_date'] ?? 'Ongoing') ?></span></span>
                                </div>
                            </td>

                            <td class="px-8 py-6">
                                <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full border <?= $statusColor ?>">
                                    <?= htmlspecialchars($p['status']) ?>
                                </span>
                            </td>

                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="projects_edit.php?id=<?= $p['id'] ?>" class="w-10 h-10 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-xl transition">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    
                                    <form action="handlers/project-handler.php" method="POST" onsubmit="return confirm('Delete this project?');" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="delete" value="1">
                                        <button type="submit" class="w-10 h-10 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-xl transition">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" class="p-16 text-center text-slate-400 font-medium">No projects found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>