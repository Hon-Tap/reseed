<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin – Posts Management (Final, Clean, Polished)
|--------------------------------------------------------------------------
*/

$backendPath = dirname(__DIR__, 1);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

/*
|--------------------------------------------------------------------------
| Fetch Posts
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT id, title, author, cover_image, media_type, published_at, created_at
    FROM posts
    WHERE title ILIKE :search
    ORDER BY created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'search' => '%' . $search . '%'
]);

$posts = $stmt->fetchAll();
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="min-h-screen bg-slate-50 p-8">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                    Posts & Field Updates
                </h1>
                <p class="text-slate-500 mt-2 font-medium">
                    Create, edit, and publish news stories from the field.
                </p>
            </div>

            <a
                href="posts_add.php"
                class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700
                       text-white font-bold rounded-xl transition shadow-lg shadow-emerald-200"
            >
                <i class="fa-solid fa-plus mr-2"></i>
                New Post
            </a>
        </div>

        <!-- Search -->
        <div class="mb-6 max-w-md">
            <form method="GET" class="relative">
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search posts..."
                    class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-2xl
                           focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500
                           outline-none transition shadow-sm"
                >
                <div class="absolute left-4 top-3.5 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[11px]
                                   uppercase font-bold tracking-widest">
                            <th class="px-8 py-5">Media</th>
                            <th class="px-8 py-5">Post Details</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        <?php if ($posts): foreach ($posts as $p): ?>

                            <?php
                                $mediaUrl = !empty($p['cover_image'])
                                    ? UPLOADS_URL . '/posts/' . $p['cover_image']
                                    : null;

                                $isVideo = ($p['media_type'] ?? 'image') === 'video';
                            ?>

                            <tr class="hover:bg-slate-50/50 transition-colors">

                                <!-- Media -->
                                <td class="px-8 py-6">
                                    <?php if ($mediaUrl): ?>

                                        <?php if ($isVideo): ?>
                                            <video
                                                src="<?= htmlspecialchars($mediaUrl) ?>"
                                                class="w-16 h-16 object-cover rounded-xl border border-slate-100 shadow-sm"
                                                muted
                                                loop
                                                playsinline
                                            ></video>
                                        <?php else: ?>
                                            <img
                                                src="<?= htmlspecialchars($mediaUrl) ?>"
                                                class="w-16 h-16 object-cover rounded-xl border border-slate-100 shadow-sm"
                                                loading="lazy"
                                            >
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <div
                                            class="w-16 h-16 bg-slate-100 rounded-xl flex items-center
                                                   justify-center text-slate-400 border border-slate-200"
                                        >
                                            <i class="fa-solid fa-newspaper text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Details -->
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-800 text-lg">
                                        <?= htmlspecialchars($p['title']) ?>
                                    </div>

                                    <div class="flex items-center gap-3 mt-1.5 text-sm text-slate-500">
                                        <span class="font-semibold">
                                            <i class="fa-regular fa-user mr-1"></i>
                                            <?= htmlspecialchars($p['author'] ?? 'Editorial') ?>
                                        </span>
                                        <span class="text-slate-300">•</span>
                                        <span>
                                            <?= !empty($p['published_at'])
                                                ? date('M d, Y', strtotime($p['published_at']))
                                                : 'Unpublished'
                                            ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="posts_edit.php?id=<?= $p['id'] ?>"
                                            class="w-10 h-10 flex items-center justify-center
                                                   text-blue-500 hover:bg-blue-50 rounded-xl transition"
                                        >
                                            <i class="fa-solid fa-edit"></i>
                                        </a>

                                        <form
                                            action="handlers/post-handler.php"
                                            method="POST"
                                            onsubmit="return confirm('Delete this post?');"
                                        >
                                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="delete" value="1">

                                            <button
                                                type="submit"
                                                class="w-10 h-10 flex items-center justify-center
                                                       text-rose-500 hover:bg-rose-50 rounded-xl transition"
                                            >
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; else: ?>

                            <tr>
                                <td colspan="3" class="p-16 text-center text-slate-400 font-medium">
                                    No posts found.
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
