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
| Fetch gallery items
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        id,
        filename,
        caption,
        category,
        created_at
    FROM gallery
    ORDER BY created_at DESC
");

$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

$uploadUrl  = UPLOAD_URL . '/gallery/';
$uploadRoot = UPLOAD_ROOT . '/gallery/';

?>

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

        <!-- Empty state -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fa-solid fa-images"></i>
            </div>
            <h3>No images yet</h3>
            <p>Upload your first gallery image to get started.</p>
        </div>

    <?php else: ?>

        <!-- Table -->
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

                    <?php
                        $filePath = $uploadRoot . ($g['filename'] ?? '');
                        $hasImage = $g['filename'] && file_exists($filePath);
                    ?>

                    <tr>
                        <!-- Preview -->
                        <td>
                            <?php if ($hasImage): ?>
                                <img
                                    src="<?= $uploadUrl . e($g['filename']) ?>"
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
                            <strong><?= e($g['caption'] ?: 'Untitled Image') ?></strong>
                            <div class="meta">
                                <code><?= e($g['filename']) ?></code>
                            </div>
                        </td>

                        <!-- Category -->
                        <td>
                            <span class="badge badge-neutral">
                                <?= e($g['category'] ?: 'Uncategorized') ?>
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="text-right">
                            <div class="actions">
                                <a
                                    href="gallery_edit.php?id=<?= (int)$g['id'] ?>"
                                    class="icon-btn edit"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <form
                                    action="handlers/gallery-handler.php"
                                    method="post"
                                    onsubmit="return confirm('Delete this image permanently?')"
                                >
                                    <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
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
