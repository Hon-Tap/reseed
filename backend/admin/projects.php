<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · PROJECTS LIST
|--------------------------------------------------------------------------
*/

$ROOT = realpath(__DIR__ . '/..');
if ($ROOT === false) {
    http_response_code(500);
    exit('Cannot resolve backend root.');
}

require_once $ROOT . '/includes/config.php';
require_once $ROOT . '/admin/includes/admin_auth.php';
require_once $ROOT . '/admin/includes/admin_header.php';
require_once $ROOT . '/admin/includes/csrf.php';

/*
|--------------------------------------------------------------------------
| INPUT
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');

/*
|--------------------------------------------------------------------------
| FETCH PROJECTS (Postgres-safe)
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        title,
        location,
        cover_image,
        status,
        featured,
        start_date
    FROM projects
    WHERE (:search = '' OR title ILIKE :q OR location ILIKE :q)
    ORDER BY created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'search' => $search,
    'q'      => '%' . $search . '%',
]);

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function statusBadge(string $status): string
{
    return match (strtolower($status)) {
        'completed' => 'bg-emerald-100 text-emerald-700',
        'ongoing'   => 'bg-sky-100 text-sky-700',
        'planned'   => 'bg-amber-100 text-amber-700',
        default     => 'bg-slate-100 text-slate-600',
    };
}

function safeDate(?string $date): string
{
    return $date ? date('M d, Y', strtotime($date)) : '—';
}

function isRemoteImage(?string $url): bool
{
    return !empty($url) && str_starts_with($url, 'http');
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-[#f8fafc] min-h-screen">
<div class="max-w-7xl mx-auto">

    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Projects</h1>
            <p class="text-slate-500 mt-2 font-medium">
                Manage community initiatives and conservation efforts.
            </p>
        </div>

        <a href="project_add.php"
           class="inline-flex items-center px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl transition shadow-xl shadow-emerald-200 active:scale-95">
            <i class="fa-solid fa-plus mr-2"></i> New Initiative
        </a>
    </div>

    <div class="mb-8 max-w-md">
        <form method="GET" class="relative group">
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Filter by name or place…"
                class="w-full pl-12 pr-4 py-4 bg-white rounded-2xl focus:ring-4 focus:ring-emerald-500/10 outline-none shadow-sm font-medium"
            >
            <div class="absolute left-4 top-4.5 text-slate-400 group-focus-within:text-emerald-500">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase font-black tracking-[0.2em]">
                    <th class="px-8 py-6">Project & Location</th>
                    <th class="px-8 py-6">Timeline</th>
                    <th class="px-8 py-6">Status</th>
                    <th class="px-8 py-6 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-50">

            <?php if ($projects): foreach ($projects as $p): ?>

                <tr class="group hover:bg-slate-50/80 transition">

                    <!-- Project -->
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-5">

                            <?php if (isRemoteImage($p['cover_image'])): ?>
                                <div class="relative">
                                    <img
                                        src="<?= htmlspecialchars($p['cover_image']) ?>"
                                        class="w-16 h-16 object-cover rounded-2xl shadow-md group-hover:scale-105 transition"
                                        alt=""
                                    >
                                    <?php if (!empty($p['featured'])): ?>
                                        <div class="absolute -top-2 -right-2 bg-amber-400 text-white w-6 h-6 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                                            <i class="fa-solid fa-star text-[10px]"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-300 border">
                                    <i class="fa-solid fa-mountain-sun text-2xl"></i>
                                </div>
                            <?php endif; ?>

                            <div>
                                <div class="font-black text-slate-800 text-lg leading-tight">
                                    <?= htmlspecialchars($p['title']) ?>
                                </div>
                                <div class="text-sm text-slate-400 mt-1 font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-location-dot text-emerald-500/50"></i>
                                    <?= htmlspecialchars($p['location'] ?? 'Remote') ?>
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Timeline -->
                    <td class="px-8 py-6">
                        <div class="text-xs font-bold space-y-1">
                            <div class="text-slate-400 uppercase tracking-tighter">Launched</div>
                            <div class="text-slate-700"><?= safeDate($p['start_date'] ?? null) ?></div>
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="px-8 py-6">
                        <span class="px-4 py-1.5 text-[10px] font-black uppercase rounded-lg tracking-widest <?= statusBadge($p['status'] ?? '') ?>">
                            <?= htmlspecialchars($p['status'] ?? 'Unknown') ?>
                        </span>
                    </td>

                    <!-- Actions -->
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end gap-3">

                            <a href="project_edit.php?id=<?= (int) $p['id'] ?>"
                               class="p-3 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-xl transition"
                               title="Edit Project">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <form
                                action="handlers/project-handler.php"
                                method="POST"
                                onsubmit="return confirm('Delete this project permanently?');"
                            >
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                <input type="hidden" name="delete" value="1">
                                <button
                                    type="submit"
                                    class="p-3 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition"
                                    title="Delete Project"
                                >
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>

            <?php endforeach; else: ?>

                <tr>
                    <td colspan="4" class="py-24 text-center">
                        <div class="flex flex-col items-center opacity-30">
                            <i class="fa-solid fa-folder-open text-5xl mb-4"></i>
                            <p class="font-black uppercase tracking-widest text-sm">
                                No Projects Found
                            </p>
                        </div>
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>

    </div>
    </div>

</div>
</div>

<?php require_once $ROOT . '/admin/includes/admin_footer.php'; ?>
