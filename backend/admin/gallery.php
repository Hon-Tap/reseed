<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/csrf.php';
require_once $backendPath . '/admin/includes/admin_header.php';

/*
|--------------------------------------------------------------------------
| Fetch gallery items
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT id, filename, caption, category, created_at
    FROM gallery
    ORDER BY id DESC
");

$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
$uploadPath = UPLOAD_URL . '/gallery/';
?>

<style>
    .empty-state {
    text-align: center;
    padding: 64px 24px;
    color: var(--text-muted);
}

.empty-icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.thumb {
    width: 80px;
    height: 56px;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid var(--border);
}

.thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

</style>

<div class="page-card">

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Gallery</h1>
            <p class="page-subtitle">Manage images and visual media.</p>
        </div>

        <a href="gallery_add.php" class="btn btn-primary">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            Upload Image
        </a>
    </div>

    <?php if (!$images): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fa-solid fa-images"></i>
            </div>
            <h3>No images yet</h3>
            <p>Upload your first gallery image to get started.</p>
        </div>
    <?php else: ?>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Details</th>
                    <th>Category</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($images as $g): ?>
                <tr>
                    <td>
                        <div class="thumb">
                            <img
                                src="<?= $uploadPath . htmlspecialchars($g['filename']) ?>"
                                alt=""
                            >
                        </div>
                    </td>

                    <td>
                        <strong><?= htmlspecialchars($g['caption'] ?: 'Untitled Image') ?></strong>
                        <div class="meta">
                            <code><?= htmlspecialchars($g['filename']) ?></code>
                        </div>
                    </td>

                    <td>
                        <span class="badge badge-neutral">
                            <?= htmlspecialchars($g['category'] ?: 'Uncategorized') ?>
                        </span>
                    </td>

                    <td class="text-right">
                        <div class="actions">
                            <a href="gallery_edit.php?id=<?= $g['id'] ?>" class="icon-btn edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form
                                action="gallery-handler.php"
                                method="post"
                                onsubmit="return confirm('Delete this image permanently?')"
                            >
                                <input type="hidden" name="id" value="<?= $g['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <button name="delete" class="icon-btn delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>

</div>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>
