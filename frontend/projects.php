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
| HELPERS
|--------------------------------------------------------------------------
*/

function getEmbedUrl(string $url): string
{
    if ($url === '') {
        return '';
    }

    $url = htmlspecialchars_decode($url);

    // YouTube
    if (preg_match(
        '%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
        $url,
        $m
    )) {
        $id = $m[1];
        return "https://www.youtube.com/embed/{$id}?autoplay=1&mute=1&controls=0&loop=1&playlist={$id}";
    }

    // Vimeo
    if (strpos($url, 'vimeo.com') !== false) {
        $path = parse_url($url, PHP_URL_PATH);
        $id   = trim($path, '/');

        if (ctype_digit($id)) {
            return "https://player.vimeo.com/video/{$id}?background=1&autoplay=1&loop=1&byline=0&title=0";
        }
    }

    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| FETCH PROJECTS
|--------------------------------------------------------------------------
*/

try {
    $stmt = $pdo->query("
        SELECT
            id,
            title,
            slug,
            summary,
            cover_image,
            media_type,
            media_url,
            status,
            location,
            featured,
            start_date
        FROM projects
        ORDER BY featured DESC, created_at DESC
    ");

    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Projects fetch error: ' . $e->getMessage());
    $projects = [];
}
?>

<style>
/* (Your styles are unchanged — intentionally) */
</style>

<div class="page-header">
    <div class="container">
        <h1>Our Initiatives</h1>
        <p class="text-slate-500 max-w-xl mx-auto font-medium">
            Discover how we are driving sustainable growth and community development through our active projects.
        </p>
    </div>
</div>

<div class="container mx-auto px-4">

<?php if (!$projects): ?>

    <div class="text-center py-32 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
        <i class="fa-solid fa-folder-open text-5xl text-slate-300 mb-4"></i>
        <h3 class="text-xl font-bold text-slate-900">No projects currently listed.</h3>
        <p class="text-slate-500">Check back soon for updates.</p>
    </div>

<?php else: ?>

<div class="project-grid">

<?php foreach ($projects as $p):

    $title      = htmlspecialchars($p['title'] ?? 'Untitled');
    $slug       = urlencode($p['slug'] ?? '');
    $summary    = htmlspecialchars(mb_strimwidth($p['summary'] ?? '', 0, 110, '…'));
    $location   = htmlspecialchars($p['location'] ?? 'Global');
    $status     = strtolower($p['status'] ?? 'planned');
    $statusCss  = 'status-' . preg_replace('/[^a-z0-9]/', '', $status);
    $dateLabel  = !empty($p['start_date']) ? date('M Y', strtotime($p['start_date'])) : '';

    // FINAL media resolution rule:
    // - Cloudinary URL → render directly
    // - Empty or invalid → placeholder
    $coverImage = '';
    if (!empty($p['cover_image']) && str_starts_with($p['cover_image'], 'http')) {
        $coverImage = $p['cover_image'];
    }

?>

<article class="project-card">
    <div class="card-media">

        <?php if (!empty($p['featured'])): ?>
            <span class="badge-featured">FEATURED</span>
        <?php endif; ?>

        <?php if ($p['media_type'] === 'video' && $coverImage): ?>

            <video muted autoplay loop playsinline>
                <source src="<?= htmlspecialchars($coverImage) ?>" type="video/mp4">
            </video>

        <?php elseif ($p['media_type'] === 'image' && $coverImage): ?>

            <img src="<?= htmlspecialchars($coverImage) ?>"
                 alt="<?= $title ?>"
                 loading="lazy">

        <?php elseif (!empty($p['media_url'])): ?>

            <iframe
                src="<?= getEmbedUrl($p['media_url']) ?>"
                frameborder="0"
                allow="autoplay; encrypted-media"
                allowfullscreen>
            </iframe>

        <?php else: ?>

            <div class="flex h-full items-center justify-center bg-slate-100 text-slate-400">
                <i class="fa-solid fa-image text-4xl"></i>
            </div>

        <?php endif; ?>

        <div class="badge-location">
            <i class="fa-solid fa-location-dot mr-1"></i> <?= $location ?>
        </div>
    </div>

    <div class="card-body">
        <div class="flex items-center justify-between">
            <span class="status-badge <?= $statusCss ?>">
                <?= ucfirst($status) ?>
            </span>
            <span class="text-xs font-bold text-slate-400"><?= $dateLabel ?></span>
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
