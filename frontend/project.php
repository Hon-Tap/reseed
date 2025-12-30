<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| PROJECT DETAIL PAGE
|--------------------------------------------------------------------------
*/
require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';

/*
|--------------------------------------------------------------------------
| INPUT
|--------------------------------------------------------------------------
*/

$slug = $_GET['slug'] ?? '';
if ($slug === '') {
    header('Location: projects.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| FETCH PROJECT
|--------------------------------------------------------------------------
*/

try {
    $stmt = $pdo->prepare("
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
        WHERE slug = :slug
        LIMIT 1
    ");
    $stmt->execute(['slug' => $slug]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Project detail fetch error: ' . $e->getMessage());
    $project = false;
}

if (!$project) {
    header('Location: projects.php?not_found=1');
    exit;
}

/*
|--------------------------------------------------------------------------
| FETCH RELATED PROJECTS
|--------------------------------------------------------------------------
*/

try {
    $stmt = $pdo->prepare("
        SELECT
            title,
            slug,
            cover_media,
            location,
            status
        FROM projects
        WHERE id != :id
        ORDER BY created_at DESC
        LIMIT 3
    ");
    $stmt->execute(['id' => $project['id']]);
    $relatedProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $relatedProjects = [];
}

/*
|--------------------------------------------------------------------------
| NORMALIZE DATA
|--------------------------------------------------------------------------
*/

$title       = htmlspecialchars($project['title']);
$status      = strtolower(preg_replace('/[^a-z0-9]/', '', $project['status'] ?? 'ongoing'));
$location    = htmlspecialchars($project['location'] ?? 'Global');
$summary     = htmlspecialchars($project['summary'] ?? '');
$description = nl2br(htmlspecialchars($project['description'] ?? ''));

$startDate = $project['start_date']
    ? date('M Y', strtotime($project['start_date']))
    : '';

$endDate = $project['end_date']
    ? date('M Y', strtotime($project['end_date']))
    : 'Ongoing';

$mediaType  = $project['media_type'] ?? 'image';
$coverMedia = $project['cover_media'];

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">

<header class="project-hero">
    <a href="projects.php"
       class="inline-flex items-center text-emerald-600 font-bold hover:text-emerald-700">
        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Projects
    </a>

    <h1><?= $title ?></h1>

    <span class="status-pill status-<?= $status ?>">
        <?= ucfirst($status) ?>
    </span>
</header>

<div class="media-stage">

<?php if ($mediaType === 'video' && $coverMedia): ?>

    <video autoplay muted loop playsinline controls>
        <source src="<?= htmlspecialchars($coverMedia) ?>" type="video/mp4">
    </video>

<?php elseif ($mediaType === 'image' && $coverMedia): ?>

    <img src="<?= htmlspecialchars($coverMedia) ?>" alt="<?= $title ?>">

<?php else: ?>

    <div class="flex flex-col items-center justify-center h-full text-slate-300">
        <i class="fa-regular fa-image text-6xl mb-4"></i>
        <span class="font-bold">No Media Available</span>
    </div>

<?php endif; ?>

</div>

<div class="content-grid">

<div class="main-column">
    <?php if ($summary): ?>
        <div class="lead-summary"><?= $summary ?></div>
    <?php endif; ?>

    <div class="rich-text"><?= $description ?></div>
</div>

<aside class="sidebar-column">
    <div class="sticky-sidebar">

        <div class="info-item">
            <span class="info-label">Location</span>
            <div class="info-value">
                <i class="fa-solid fa-location-dot text-emerald-500"></i>
                <?= $location ?>
            </div>
        </div>

        <div class="info-item">
            <span class="info-label">Timeline</span>
            <div class="info-value">
                <i class="fa-regular fa-calendar-check text-emerald-500"></i>
                <?= $startDate ?> — <?= $endDate ?>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100">
            <span class="info-label">Share this project</span>
            <div class="flex gap-4 text-2xl mt-2">
                <a href="#" class="text-blue-600"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="text-sky-400"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="text-blue-700"><i class="fa-brands fa-linkedin"></i></a>
            </div>
        </div>

    </div>
</aside>

</div>
</div>

<?php if (!empty($relatedProjects)): ?>

<section class="bg-slate-50 py-20 border-t border-slate-200">
    <div class="container mx-auto px-6">

        <h2 class="text-3xl font-black text-slate-900 mb-10">Related Projects</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <?php foreach ($relatedProjects as $rp): ?>

                <a href="project.php?slug=<?= urlencode($rp['slug']) ?>" class="group no-underline">

                    <div class="aspect-video rounded-2xl overflow-hidden shadow-sm mb-4 bg-white">

                        <?php if (!empty($rp['cover_media'])): ?>
                            <img
                                src="<?= htmlspecialchars($rp['cover_media']) ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            >
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full text-slate-300">
                                <i class="fa-regular fa-image text-4xl"></i>
                            </div>
                        <?php endif; ?>

                    </div>

                    <h4 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600">
                        <?= htmlspecialchars($rp['title']) ?>
                    </h4>

                    <p class="text-sm text-slate-500">
                        <?= htmlspecialchars($rp['location'] ?? '') ?>
                    </p>

                </a>

            <?php endforeach; ?>

        </div>

    </div>
</section>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
