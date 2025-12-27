<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
*/

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| Pagination & Search
|--------------------------------------------------------------------------
*/

$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 15;
$offset = ($page - 1) * $limit;
$search = trim($_GET['search'] ?? '');

/*
|--------------------------------------------------------------------------
| Fetch projects
|--------------------------------------------------------------------------
*/

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
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search', $search);
$stmt->bindValue(':search_like', "%{$search}%");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$uploadUrl  = UPLOAD_URL . '/projects/';
$uploadRoot = UPLOAD_ROOT . '/projects/';

?>

<div class="page-card">

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Projects</h1>
            <p class="page-subtitle">Manage restoration and development initiatives.</p>
        </div>

        <a href="projects_add.php" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i>
            New Project
        </a>
    </div>

    <!-- Search -->
    <form method="get" class="search-bar">
        <i class="fa-solid fa-search"></i>
        <input
            type="text"
            name="search"
            value="<?= e($search) ?>"
            placeholder="Search projects…"
        >
    </form>

    <!-- Table -->
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Timeline</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($projects): foreach ($projects as $p): ?>

                <?php
                    $imagePath = $uploadRoot . ($p['cover_image'] ?? '');
                    $hasImage  = $p['cover_image'] && file_exists($imagePath);
                ?>

                <tr>
                    <!-- Cover -->
                    <td>
                        <?php if ($hasImage): ?>
                            <img
                                src="<?= $uploadUrl . e($p['cover_image']) ?>"
                                class="thumb"
                                alt=""
                            >
                        <?php else: ?>
                            <div class="thumb placeholder">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </td>

                    <!-- Details -->
                    <td>
                        <strong><?= e($p['title']) ?></strong>
                        <?php if ($p['featured']): ?>
                            <span class="badge badge-featured">Featured</span>
                        <?php endif; ?>

                        <div class="meta">
                            <span>
                                <i class="fa-solid fa-location-dot"></i>
                                <?= e($p['location']) ?>
                            </span>
                            <span class="sep">•</span>
                            <code><?= e($p['slug']) ?></code>
                        </div>
                    </td>

                    <!-- Status -->
                    <td>
                        <span class="badge badge-status">
                            <?= e($p['status']) ?>
                        </span>
                    </td>

                    <!-- Timeline -->
                    <td>
                        <div>Start: <strong><?= e($p['start_date']) ?></strong></div>
                        <div>End: <strong><?= e($p['end_date'] ?: 'Ongoing') ?></strong></div>
                    </td>

                    <!-- Actions -->
                    <td class="text-right">
                        <div class="actions">
                            <a href="projects_edit.php?id=<?= (int)$p['id'] ?>" class="icon-btn edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form
                                action="handlers/project-handler.php"
                                method="post"
                                onsubmit="return confirm('Delete this project permanently?')"
                            >
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <button name="delete" class="icon-btn delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

            <?php endforeach; else: ?>

                <tr>
                    <td colspan="5" class="empty-state">
                        No projects found.
                    </td>
                </tr>

            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
