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
| FETCH PROJECTS
|--------------------------------------------------------------------------
*/

try {
    $stmt = $pdo->query("
        SELECT id, title, slug, summary, description, location, start_date, 
               end_date, media_type, cover_media, status, featured, created_at
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

<style>
    :root {
        --primary-emerald: #0a9605ff;
        --dark-navy: #0f172a;
        --soft-bg: #f8fafc;
        --card-radius: 20px;
        --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .projects-container {
        background-color: var(--soft-bg);
        min-height: 100vh;
        padding-bottom: 5rem;
    }

    /* Page Header Styling */
    .page-header {
        padding: 6rem 0 4rem;
        background: radial-gradient(circle at top right, #ffffff, #f1f5f9);
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .page-header h1 {
        font-weight: 900;
        letter-spacing: -0.04em;
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        color: var(--dark-navy);
        margin-bottom: 1rem;
    }

    /* Card Styling */
    .project-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
    }

    .project-card {
        background: #fff;
        border-radius: var(--card-radius);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .project-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    }

    .card-media {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .card-media img, 
    .card-media video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s ease;
    }

    .project-card:hover .card-media img {
        transform: scale(1.08);
    }

    /* Glass Badges */
    .badge-featured {
        position: absolute;
        top: 1.25rem;
        left: 1.25rem;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--dark-navy);
        z-index: 2;
    }

    .badge-location {
        position: absolute;
        bottom: 1.25rem;
        left: 1.25rem;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(8px);
        color: #fff;
        padding: 0.3rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 500;
        z-index: 2;
    }

    .card-body {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .status-badge {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
    }

    .status-ongoing { background: #dcfce7; color: #166534; }
    .status-completed { background: #dbeafe; color: #1e40af; }

    .card-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--dark-navy);
        margin: 1rem 0;
        line-height: 1.2;
    }

    .card-summary {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .card-link {
        margin-top: auto;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--dark-navy);
        font-weight: 700;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .card-link i {
        color: var(--primary-emerald);
        transition: transform 0.3s ease;
    }

    .card-link:hover {
        color: var(--primary-emerald);
    }

    .card-link:hover i {
        transform: translateX(5px);
    }

    /* Empty State */
    .empty-state {
        background: white;
        border: 2px dashed #e2e8f0;
        border-radius: 32px;
        padding: 5rem 2rem;
    }

    @media (max-width: 768px) {
        .project-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="projects-container">
    <header class="page-header">
        <div class="container text-center">
            <h1>Our Initiatives</h1>
            <p class="text-secondary opacity-75 max-w-xl mx-auto fs-5">
                Driving sustainable growth and community development through innovative, field-first solutions.
            </p>
        </div>
    </header>

    <div class="container mt-5">
        <?php if (empty($projects)): ?>
            <div class="empty-state text-center">
                <i class="fa-solid fa-folder-open text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                <h3 class="fw-bold">No active initiatives</h3>
                <p class="text-muted">Check back soon for updates on our latest projects.</p>
            </div>
        <?php else: ?>
            <div class="project-grid">
                <?php foreach ($projects as $p): ?>
                    <?php
                        $title     = htmlspecialchars($p['title']);
                        $slug      = urlencode($p['slug']);
                        $summary   = htmlspecialchars(mb_strimwidth($p['summary'] ?? '', 0, 110, '…'));
                        $location  = htmlspecialchars($p['location'] ?? 'Field');
                        $status    = htmlspecialchars($p['status'] ?? 'ongoing');
                        $featured  = (bool) $p['featured'];
                        $dateLabel = $p['start_date'] ? date('M Y', strtotime($p['start_date'])) : '';
                        $mediaType = $p['media_type'] ?? 'image';
                        $coverMedia = $p['cover_media'];
                    ?>

                    <article class="project-card">
                        <div class="card-media">
                            <?php if ($featured): ?>
                                <span class="badge-featured">
                                    <i class="fa-solid fa-star text-warning me-1"></i> Featured
                                </span>
                            <?php endif; ?>

                            <?php if ($mediaType === 'image' && $coverMedia): ?>
                                <img src="<?= htmlspecialchars($coverMedia) ?>" alt="<?= $title ?>" loading="lazy">
                            <?php elseif ($mediaType === 'video' && $coverMedia): ?>
                                <video autoplay muted loop playsinline>
                                    <source src="<?= htmlspecialchars($coverMedia) ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <div class="h-100 d-flex align-items-center justify-center bg-light text-muted">
                                    <i class="fa-solid fa-image fa-3x opacity-25"></i>
                                </div>
                            <?php endif; ?>

                            <?php if ($location): ?>
                                <span class="badge-location">
                                    <i class="fa-solid fa-location-dot me-1"></i> <?= $location ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="status-badge status-<?= strtolower($status) ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                                <?php if ($dateLabel): ?>
                                    <small class="text-muted fw-bold"><?= $dateLabel ?></small>
                                <?php endif; ?>
                            </div>

                            <h3 class="card-title"><?= $title ?></h3>
                            <p class="card-summary"><?= $summary ?></p>

                            <a href="/project.php?slug=<?= $slug ?>" class="card-link">
                                <span>View Details</span>
                                <i class="fa-solid fa-arrow-right-long"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/backend/includes/footer.php'; ?>