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
    if ($url === '') return '';
    $url = htmlspecialchars_decode($url);

    // YouTube
    if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $m)) {
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
            id, title, slug, summary, cover_image,
            media_type, media_url, status, location,
            featured, start_date
        FROM projects
        ORDER BY featured DESC, created_at DESC
    ");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Projects fetch error: ' . $e->getMessage());
    $projects = [];
}
?>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
/* ================= PREMIUM PROJECT UI ================= */
:root {
    --primary-green: #099227;
    --dark-green: #022c10;
    --slate: #64748b;
    --ink: #0f172a;
}

.page-header {
    padding: 100px 0 60px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    text-align: center;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.page-header h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: 800;
    color: var(--dark-green);
    margin-bottom: 15px;
}

/* ---------- PROJECT GRID ---------- */
.project-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 40px;
    padding: 80px 0;
}

.project-card {
    background: white;
    border-radius: 30px;
    overflow: hidden;
    border: 1px solid #f1f5f9;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.project-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.15);
    border-color: var(--primary-green);
}

/* ---------- MEDIA ---------- */
.card-media {
    position: relative;
    height: 260px;
    overflow: hidden;
    background: #e2e8f0;
}

.card-media img, .card-media video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-media iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.badge-featured {
    position: absolute;
    top: 20px; left: 20px;
    background: var(--primary-green);
    color: white;
    padding: 6px 14px;
    border-radius: 100px;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    z-index: 10;
}

.badge-location {
    position: absolute;
    bottom: 20px; left: 20px;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(8px);
    color: white;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* ---------- BODY ---------- */
.card-body {
    padding: 30px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 15px;
}

.status-active { background: #dcfce7; color: #166534; }
.status-planned { background: #fef9c3; color: #854d0e; }
.status-completed { background: #dbeafe; color: #1e40af; }

.card-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--ink);
    margin-bottom: 12px;
    line-height: 1.2;
}

.card-summary {
    color: var(--slate);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 25px;
}

.card-link {
    margin-top: auto;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 800;
    color: var(--primary-green);
    text-decoration: none;
    transition: gap 0.3s;
}

.card-link:hover { gap: 15px; }

@media (max-width: 768px) {
    .project-grid { grid-template-columns: 1fr; }
}
</style>

<div class="page-header">
    <div class="container">
        <span class="text-success fw-bold text-uppercase small tracking-widest d-block mb-2">Our Work</span>
        <h1>Building Resilience</h1>
        <p class="text-muted mx-auto" style="max-width: 600px;">
            Discover how we are driving sustainable growth and ecological restoration through our community-led initiatives.
        </p>
    </div>
</div>

<div class="container">
    <?php if (!$projects): ?>
        <div class="text-center py-5">
            <div class="p-5 bg-light rounded-5 border border-dashed">
                <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>
                <h3 class="fw-bold">Initiatives Loading...</h3>
                <p class="text-muted">We are currently updating our project list. Check back soon.</p>
            </div>
        </div>
    <?php else: ?>

    <div class="project-grid">
        <?php foreach ($projects as $p):
            $title    = htmlspecialchars($p['title'] ?? 'Untitled Initiative');
            $slug     = urlencode($p['slug'] ?? '');
            $summary  = htmlspecialchars(mb_strimwidth($p['summary'] ?? '', 0, 110, '…'));
            $location = htmlspecialchars($p['location'] ?? 'South Sudan');
            $status   = strtolower($p['status'] ?? 'planned');
            $statusCss = 'status-' . $status;
            
            // Image resolution
            $coverImage = (!empty($p['cover_image']) && str_starts_with($p['cover_image'], 'http')) 
                          ? $p['cover_image'] 
                          : 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&q=80&w=1000';
        ?>

        <article class="project-card" data-aos="fade-up">
            <div class="card-media">
                <?php if (!empty($p['featured'])): ?>
                    <span class="badge-featured">FEATURED</span>
                <?php endif; ?>

                <?php if ($p['media_type'] === 'video'): ?>
                    <video muted autoplay loop playsinline>
                        <source src="<?= htmlspecialchars($coverImage) ?>" type="video/mp4">
                    </video>
                <?php elseif (!empty($p['media_url'])): ?>
                    <iframe src="<?= getEmbedUrl($p['media_url']) ?>" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                <?php else: ?>
                    <img src="<?= htmlspecialchars($coverImage) ?>" alt="<?= $title ?>" loading="lazy">
                <?php endif; ?>

                <div class="badge-location">
                    <i class="fa-solid fa-location-dot mr-1"></i> <?= $location ?>
                </div>
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="status-badge <?= $statusCss ?>"><?= ucfirst($status) ?></span>
                    <span class="text-muted small fw-bold">
                        <?= !empty($p['start_date']) ? date('M Y', strtotime($p['start_date'])) : '' ?>
                    </span>
                </div>

                <h3 class="card-title"><?= $title ?></h3>
                <p class="card-summary"><?= $summary ?></p>

                <a href="project.php?slug=<?= $slug ?>" class="card-link">
                    <span>Explore Initiative</span>
                    <i class="fa-solid fa-arrow-right-long"></i>
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
    AOS.init({ duration: 1000, once: true, easing: 'ease-out-quad' });
</script>

<?php require_once dirname(__DIR__) . '/backend/includes/footer.php'; ?>