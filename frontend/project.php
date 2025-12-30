<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| PROJECT DETAIL PAGE (REFINED UI)
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../backend/includes/config.php';

/*
|--------------------------------------------------------------------------
| INPUT & FETCH
|--------------------------------------------------------------------------
*/

$slug = $_GET['slug'] ?? '';
if ($slug === '') {
    header('Location: projects.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE slug = :slug LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Project detail error: ' . $e->getMessage());
    $project = false;
}

if (!$project) {
    header('Location: projects.php?not_found=1');
    exit;
}

/*
|--------------------------------------------------------------------------
| FETCH RELATED
|--------------------------------------------------------------------------
*/
try {
    $stmt = $pdo->prepare("
        SELECT title, slug, cover_image, location, status 
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
| HELPERS
|--------------------------------------------------------------------------
*/
function resolveMediaUrl(?string $url): string {
    if (!$url) return 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=1200';
    return (str_starts_with($url, 'http')) ? $url : "/uploads/projects/{$url}";
}

function embedUrl(string $url): string {
    $url = htmlspecialchars_decode($url);
    if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $m)) {
        return "https://www.youtube.com/embed/{$m[1]}?autoplay=1&mute=1&loop=1&controls=0&playlist={$m[1]}";
    }
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| DATA NORMALIZATION
|--------------------------------------------------------------------------
*/
$title       = htmlspecialchars($project['title'] ?? 'Untitled');
$status      = strtolower(preg_replace('/[^a-z]/', '', $project['status'] ?? 'planned'));
$location    = htmlspecialchars($project['location'] ?? 'South Sudan');
$summary     = htmlspecialchars($project['summary'] ?? '');
$description = nl2br(htmlspecialchars($project['description'] ?? ''));
$startDate   = !empty($project['start_date']) ? date('M Y', strtotime($project['start_date'])) : 'Launch';
$endDate     = !empty($project['end_date']) ? date('M Y', strtotime($project['end_date'])) : 'Ongoing';

$coverMedia  = resolveMediaUrl($project['cover_image'] ?? null);
$mediaType   = $project['media_type'] ?? 'image';
$embedMedia  = $project['media_url'] ?? '';

require_once __DIR__ . '/includes/header.php';
?>

<style>
/* ================= PROJECT DETAIL REFINED UI ================= */
:root {
    --primary-green: #099227;
    --ink: #0f172a;
    --slate: #64748b;
}

.project-header {
    padding: 80px 0 40px;
    background: #fff;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.1em;
    color: var(--primary-green);
    margin-bottom: 24px;
    text-decoration: none;
}

.project-title {
    font-size: clamp(2.5rem, 6vw, 4.5rem);
    font-weight: 800;
    color: var(--ink);
    line-height: 1.1;
    letter-spacing: -0.03em;
    margin-bottom: 20px;
}

/* ---------- MEDIA STAGE ---------- */
.media-stage {
    width: 100%;
    aspect-ratio: 21 / 9;
    border-radius: 40px;
    overflow: hidden;
    background: #f1f5f9;
    margin-bottom: 60px;
    box-shadow: 0 40px 100px -20px rgba(15, 23, 42, 0.15);
}

.media-stage img, .media-stage video, .media-stage iframe {
    width: 100%; height: 100%; object-fit: cover; border: none;
}

/* ---------- CONTENT GRID ---------- */
.content-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 80px;
    margin-bottom: 100px;
}

.lead-summary {
    font-size: 1.5rem;
    line-height: 1.5;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 40px;
    padding-left: 24px;
    border-left: 4px solid var(--primary-green);
}

.rich-content {
    font-size: 1.15rem;
    line-height: 1.8;
    color: #334155;
}

/* ---------- SIDEBAR INFO ---------- */
.sidebar-info {
    position: sticky;
    top: 40px;
    background: #f8fafc;
    border-radius: 32px;
    padding: 40px;
    border: 1px solid #e2e8f0;
}

.info-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    color: var(--slate);
    letter-spacing: 0.1em;
    margin-bottom: 8px;
}

.info-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-pill {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 800;
    background: #dcfce7;
    color: #166534;
}

@media (max-width: 1024px) {
    .content-layout { grid-template-columns: 1fr; gap: 40px; }
    .media-stage { aspect-ratio: 16 / 9; }
}
</style>

<main class="container">
    <header class="project-header" data-aos="fade-down">
        <a href="projects.php" class="back-link">
            <i class="fas fa-arrow-left"></i> All Initiatives
        </a>
        <h1 class="project-title"><?= $title ?></h1>
        <div class="d-flex align-items-center gap-3">
            <span class="status-pill">Project <?= ucfirst($status) ?></span>
            <span class="text-muted fw-bold small"><i class="fas fa-map-marker-alt text-success me-1"></i> <?= $location ?></span>
        </div>
    </header>

    <section class="media-stage" data-aos="zoom-in">
        <?php if ($mediaType === 'video'): ?>
            <video autoplay muted loop playsinline controls>
                <source src="<?= htmlspecialchars($coverMedia) ?>" type="video/mp4">
            </video>
        <?php elseif ($mediaType === 'image'): ?>
            <img src="<?= htmlspecialchars($coverMedia) ?>" alt="<?= $title ?>">
        <?php elseif ($embedMedia): ?>
            <iframe src="<?= embedUrl($embedMedia) ?>" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        <?php endif; ?>
    </section>

    <div class="content-layout">
        <div class="main-content" data-aos="fade-up">
            <div class="lead-summary"><?= $summary ?></div>
            <div class="rich-content">
                <?= $description ?>
            </div>
        </div>

        <aside class="sidebar-column" data-aos="fade-left">
            <div class="sidebar-info">
                <span class="info-label">Location</span>
                <div class="info-value"><?= $location ?></div>

                <span class="info-label">Timeline</span>
                <div class="info-value">
                    <i class="far fa-calendar-alt text-success"></i>
                    <?= $startDate ?> — <?= $endDate ?>
                </div>

                <span class="info-label">Contact</span>
                <div class="info-value">office@reseed-ss.org</div>

                <div class="pt-4 mt-2 border-top">
                    <span class="info-label">Share Impact</span>
                    <div class="d-flex gap-3 fs-4 mt-2">
                        <a href="#" class="text-dark"><i class="fab fa-x-twitter"></i></a>
                        <a href="#" class="text-primary"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-primary"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</main>

<?php if ($relatedProjects): ?>
<section class="py-5 bg-light border-top">
    <div class="container py-5">
        <h2 class="fw-black mb-5 h1">More Initiatives</h2>
        <div class="row g-4">
            <?php foreach ($relatedProjects as $rp): ?>
                <div class="col-md-4">
                    <a href="project.php?slug=<?= urlencode($rp['slug']) ?>" class="text-decoration-none">
                        <div class="rounded-5 overflow-hidden shadow-sm mb-3 bg-white" style="height: 200px;">
                            <img src="<?= htmlspecialchars(resolveMediaUrl($rp['cover_image'] ?? null)) ?>" 
                                 class="w-100 h-100 object-cover hover-scale" alt="Related">
                        </div>
                        <h4 class="fw-bold text-dark"><?= htmlspecialchars($rp['title']) ?></h4>
                        <p class="text-muted small"><?= htmlspecialchars($rp['location']) ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
    AOS.init({ duration: 800, once: true });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>