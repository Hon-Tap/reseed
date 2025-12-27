<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/csrf.php';
require_once $backendPath . '/admin/includes/admin_header.php';

/*
|--------------------------------------------------------------------------
| Fetch projects
|--------------------------------------------------------------------------
*/
$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT id, title, slug, location, status, featured,
           cover_image, start_date, end_date, created_at
    FROM projects
    WHERE (:search = '' OR title ILIKE :search_like)
    ORDER BY created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'search'      => $search,
    'search_like' => "%{$search}%"
]);

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
$uploadPath = UPLOAD_URL . '/projects/';
?>
<style>
    .page-card {
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 16px;
    margin-bottom: 24px;
}

.page-title {
    margin: 0;
    font-size: 28px;
}

.page-subtitle {
    margin: 6px 0 0;
    color: var(--text-muted);
}

.search-bar {
    position: relative;
    margin-bottom: 20px;
}

.search-bar i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.search-bar input {
    width: 100%;
    padding: 10px 14px 10px 40px;
    border-radius: 10px;
    border: 1px solid var(--border);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 14px;
    border-bottom: 1px solid var(--border);
    vertical-align: top;
}

.thumb {
    width: 56px;
    height: 56px;
    border-radius: 10px;
    object-fit: cover;
}

.thumb.placeholder {
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
}

.actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.icon-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.icon-btn.edit { color: #2563eb; }
.icon-btn.delete { color: #dc2626; }

.empty-state {
    text-align: center;
    padding: 48px;
    color: var(--text-muted);
}

</style>

<!-- Page content -->
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
            value="<?= htmlspecialchars($search) ?>"
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
                <tr>
                    <td>
                        <?php if (!empty($p['cover_image'])): ?>
                            <img
                                src="<?= $uploadPath . htmlspecialchars($p['cover_image']) ?>"
                                alt=""
                                class="thumb"
                            >
                        <?php else: ?>
                            <div class="thumb placeholder">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td>
                        <div class="title-row">
                            <strong><?= htmlspecialchars($p['title']) ?></strong>
                            <?php if ($p['featured']): ?>
                                <span class="badge badge-featured">Featured</span>
                            <?php endif; ?>
                        </div>
                        <div class="meta">
                            <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($p['location']) ?></span>
                            <span class="sep">•</span>
                            <code><?= htmlspecialchars($p['slug']) ?></code>
                        </div>
                    </td>

                    <td>
                        <span class="badge badge-status">
                            <?= htmlspecialchars($p['status']) ?>
                        </span>
                    </td>

                    <td>
                        <div class="dates">
                            <div>Start: <strong><?= htmlspecialchars($p['start_date']) ?></strong></div>
                            <div>End: <strong><?= $p['end_date'] ?: 'Ongoing' ?></strong></div>
                        </div>
                    </td>

                    <td class="text-right">
                        <div class="actions">
                            <a href="projects_edit.php?id=<?= $p['id'] ?>" class="icon-btn edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form
                                action="handlers/project-handler.php"
                                method="post"
                                onsubmit="return confirm('Delete this project permanently?')"
                            >
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
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

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>
