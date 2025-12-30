<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| FRONTEND PROJECTS PAGE
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';

/*
|--------------------------------------------------------------------------
| FETCH PROJECTS (MATCHES DB STRUCTURE)
|--------------------------------------------------------------------------
*/

try {
    $stmt = $pdo->query("
        SELECT
            id,
            title,
            slug,
            summary,
            description,
            location,
            start_date,
            end_date,
            media_type,
            cover_media,
            status,
            featured,
            created_at
        FROM projects
        WHERE status IS NULL OR status != 'archived'
        ORDER BY featured DESC, created_at DESC
    ");

    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Projects fetch error: ' . $e->getMessage());
    $projects = [];
}
?>

<div class="page-header">
    <div class="container text-center">
        <h1>Our Initiatives</h1>
        <p class="text-slate-500 max-w-xl mx-auto font-medium">
            Discover how we are driving sustainable growth and community development through our active projects.
        </p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">

<?php if (empty($projects)): ?>

    <div class="text-center py-32 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
        <i class="fa-solid fa-folder-open text-5xl text-slate-300 mb-4"></i>
        <h3 class="text-xl font-bold text-slate-900">No projects available</h3>
        <p class="text-slate-500">Projects will appear here once published.</p>
    </div>

<?php else: ?>

<div class="project-grid">

<?php foreach ($projects as $p): ?>

<?php
    $title     = htmlspecialchars($p['title']);
    $slug      = urlencode($p['slug']);
    $summary   = htmlspecialchars(mb_strimwidth($p['summary'] ?? '', 0, 120, '…'));
    $location  = htmlspecialchars($p['location'] ?? '—');
    $status    = htmlspecialchars($p['status'] ?? 'ongoing');
    $featured  = (bool) $p['featured'];

    $dateLabel = $p['start_date']
        ? date('M Y', strtotime($p['start_date']))
        : '';

    $mediaType  = $p['media_type'] ?? 'image';
    $coverMedia = $p['cover_media'];
?>

<article class="project-card">

    <div class="card-media">

        <?php if ($featured): ?>
            <span class="badge-featured">FEATURED</span>
        <?php endif; ?>

        <?php if ($mediaType === 'image' && $coverMedia): ?>

            <img
                src="<?= htmlspecialchars($coverMedia) ?>"
                alt="<?= $title ?>"
                loading="lazy"
            >

        <?php elseif ($mediaType === 'video' && $coverMedia): ?>

            <video autoplay muted loop playsinline>
                <source src="<?= htmlspecialchars($coverMedia) ?>" type="video/mp4">
            </video>

        <?php else: ?>

            <div class="flex h-full items-center justify-center bg-slate-100 text-slate-400">
                <i class="fa-solid fa-image text-4xl"></i>
            </div>

        <?php endif; ?>

        <?php if ($location): ?>
            <span class="badge-location">
                <i class="fa-solid fa-location-dot"></i> <?= $location ?>
            </span>
        <?php endif; ?>

    </div>

    <div class="card-body">

        <div class="flex justify-between items-center mb-2">
            <span class="status-badge status-<?= strtolower($status) ?>">
                <?= ucfirst($status) ?>
            </span>

            <?php if ($dateLabel): ?>
                <span class="text-xs text-slate-400 font-semibold">
                    <?= $dateLabel ?>
                </span>
            <?php endif; ?>
        </div>

        <h3 class="card-title"><?= $title ?></h3>
        <p class="card-summary"><?= $summary ?></p>

        <a href="project.php?slug=<?= $slug ?>" class="card-link">
            <span>View details</span>
            <i class="fa-solid fa-arrow-right-long"></i>
        </a>

    </div>

</article>

<?php endforeach; ?>

</div>
<?php endif; ?>

</div>

<?php require_once dirname(__DIR__) . '/backend/includes/footer.php'; ?>
