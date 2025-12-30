<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ADMIN · POSTS MANAGEMENT (DB-ALIGNED)
|--------------------------------------------------------------------------
*/

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

/*
|--------------------------------------------------------------------------
| INPUT
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');

/*
|--------------------------------------------------------------------------
| FETCH POSTS (POSTGRES SAFE)
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        title,
        slug,
        author,
        cover_media,
        media_type,
        published_at,
        created_at,
        featured
    FROM posts
    WHERE (:search = '' OR title ILIKE :q)
    ORDER BY COALESCE(published_at, created_at) DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'search' => $search,
    'q'      => '%' . $search . '%',
]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function postThumb(?string $img): ?string
{
    if (!$img) return null;
    return str_starts_with($img, 'http')
        ? $img
        : rtrim(UPLOADS_URL, '/') . '/posts/' . ltrim($img, '/');
}

function postStatus(?string $publishedAt): array
{
    if (!$publishedAt) {
        return ['Draft', 'bg-slate-100 text-slate-500'];
    }

    return (strtotime($publishedAt) <= time())
        ? ['Live', 'bg-emerald-50 text-emerald-600']
        : ['Scheduled', 'bg-amber-50 text-amber-600'];
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="min-h-screen bg-slate-50/50 p-4 md:p-10">
<div class="max-w-7xl mx-auto">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">

        <div>
            <nav class="flex text-sm text-slate-400 mb-2">
                <ol class="flex items-center space-x-2">
                    <li><a href="dashboard.php" class="hover:text-emerald-600">Dashboard</a></li>
                    <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                    <li class="text-slate-600 font-medium">Journal Posts</li>
                </ol>
            </nav>

            <h1 class="text-4xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                Field Journal
                <span class="bg-emerald-100 text-emerald-700 text-xs uppercase tracking-widest px-3 py-1 rounded-full font-bold">
                    <?= count($posts) ?> Total
                </span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="posts_add.php"
               class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700
                      text-white font-bold rounded-2xl transition shadow-lg shadow-emerald-200 active:scale-95">
                <i class="fa-solid fa-plus mr-2"></i>
                Write New Story
            </a>
        </div>

    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-400 text-[11px] uppercase font-black tracking-[0.15em]">
                    <th class="px-8 py-5">Article Preview</th>
                    <th class="px-8 py-5">Details & Author</th>
                    <th class="px-8 py-5">Status</th>
                    <th class="px-8 py-5 text-right">Management</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">

            <?php if ($posts): foreach ($posts as $p): ?>

                <?php
                    [$label, $badge] = postStatus($p['published_at']);
                    $thumb = postThumb($p['cover_image']);
                ?>

                <tr class="group hover:bg-slate-50/50 transition">

                    <!-- PREVIEW -->
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">

                            <div class="relative w-20 h-20 shrink-0">
                                <?php if ($thumb): ?>
                                    <img src="<?= htmlspecialchars($thumb) ?>"
                                         class="w-full h-full object-cover rounded-2xl shadow-sm bg-slate-200">
                                <?php else: ?>
                                    <div class="w-full h-full bg-slate-100 rounded-2xl flex items-center justify-center text-slate-300">
                                        <i class="fa-solid fa-image text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="max-w-xs">
                                <div class="font-black text-slate-900 leading-tight group-hover:text-emerald-700 transition">
                                    <?= htmlspecialchars($p['title']) ?>
                                </div>
                                <div class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-bold">
                                    ID: #<?= (int) $p['id'] ?>
                                </div>
                            </div>

                        </div>
                    </td>

                    <!-- AUTHOR -->
                    <td class="px-8 py-6">
                        <span class="font-bold text-slate-700 text-sm">
                            <?= htmlspecialchars($p['author'] ?: 'Team ReSEED') ?>
                        </span>
                    </td>

                    <!-- STATUS -->
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $badge ?>">
                            <?= $label ?>
                        </span>
                    </td>

                    <!-- ACTIONS -->
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end items-center gap-2">

                            <a href="/post.php?slug=<?= urlencode($p['slug']) ?>" target="_blank"
                               class="p-2 text-slate-400 hover:text-emerald-600">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <a href="posts_edit.php?id=<?= (int) $p['id'] ?>"
                               class="p-2 text-blue-500 hover:bg-blue-50 rounded-xl transition">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <form action="post-handler.php" method="POST" class="inline"
                                  onsubmit="return confirm('Archive this post?');">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                <input type="hidden" name="delete" value="1">
                                <button type="submit" class="p-2 text-rose-400 hover:text-rose-600">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>

            <?php endforeach; else: ?>

                <tr>
                    <td colspan="4" class="py-24 text-center text-slate-400 font-black uppercase tracking-widest">
                        No posts found
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>
    </div>

</div>
</div>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>
