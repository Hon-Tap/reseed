<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| FRONTEND — GALLERY (ARCHIVE REWRITE)
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/backend/includes/config.php';
require_once __DIR__ . '/backend/includes/header.php';

/*
|--------------------------------------------------------------------------
| PAGINATION LOGIC
|--------------------------------------------------------------------------
*/
$limit  = 12; 
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

try {
    $totalImages = (int) $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
    $totalPages  = max(1, (int) ceil($totalImages / $limit));

    $stmt = $pdo->prepare("
        SELECT filename, caption, category, created_at
        FROM gallery
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Gallery Fetch Error: " . $e->getMessage());
    $images = [];
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<style>
/* ================= GALLERY REFINED UI ================= */
:root {
    --primary-green: #099227;
    --ink: #0f172a;
    --slate: #64748b;
}

.gallery-hero {
    padding: 120px 0 80px;
    background: radial-gradient(circle at top right, #f0fdf4 0%, #ffffff 60%);
    text-align: center;
}

.gallery-hero h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(3rem, 8vw, 5.5rem);
    font-weight: 800;
    letter-spacing: -0.05em;
    color: var(--ink);
    margin-bottom: 20px;
}

/* ---------- MASONRY GRID ---------- */
.masonry-container {
    columns: 3 350px; /* Modern CSS Masonry */
    column-gap: 30px;
    padding: 0 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.gallery-item {
    break-inside: avoid;
    margin-bottom: 30px;
    position: relative;
    border-radius: 32px;
    overflow: hidden;
    background: #f1f5f9;
    transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
    cursor: zoom-in;
}

.gallery-item img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.8s ease;
}

.gallery-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.2);
}

.gallery-item:hover img {
    transform: scale(1.08);
}

/* ---------- HOVER OVERLAY ---------- */
.gallery-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(15, 23, 42, 0.8) 0%, transparent 60%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 30px;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.category-pill {
    align-self: flex-start;
    background: var(--primary-green);
    color: white;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 5px 12px;
    border-radius: 100px;
    margin-bottom: 12px;
}

.gallery-caption {
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    margin: 0;
}

/* ---------- PAGINATION ---------- */
.pagination-wrap {
    margin-top: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.page-link-custom {
    padding: 14px 32px;
    border-radius: 100px;
    border: 2px solid #e2e8f0;
    color: var(--ink);
    font-weight: 800;
    text-decoration: none;
    transition: all 0.3s ease;
}

.page-link-custom:hover:not(.disabled) {
    border-color: var(--primary-green);
    background: var(--primary-green);
    color: white;
    transform: translateY(-3px);
}

.page-link-custom.disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .masonry-container { columns: 1; }
    .gallery-hero { padding-top: 80px; }
}
</style>

<main class="bg-white">
    <header class="gallery-hero" data-aos="fade-down">
        <div class="container">
            <span class="text-success fw-bold text-uppercase tracking-widest small d-block mb-3">Visual History</span>
            <h1>The Archive</h1>
            <p class="lead text-muted mx-auto" style="max-width: 600px;">
                A collection of moments capturing our journey toward a greener, more resilient South Sudan.
            </p>
        </div>
    </header>

    <div class="masonry-container">
        <?php if (empty($images)): ?>
            <div class="py-5 text-center w-100">
                <div class="p-5 bg-light rounded-5 border border-dashed">
                    <i class="fas fa-camera-retro fa-3x text-muted mb-3"></i>
                    <h3 class="fw-bold">No assets found</h3>
                    <p class="text-muted">The archive is currently being updated.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($images as $img): 
                $url = (strpos($img['filename'], 'http') === 0) ? $img['filename'] : UPLOADS_URL . '/gallery/' . $img['filename'];
                $caption = $img['caption'] ?: 'ReSEED Visual Asset';
            ?>
                <a href="<?= htmlspecialchars($url) ?>" class="glightbox gallery-item" data-gallery="reseed-gallery" data-title="<?= htmlspecialchars($caption) ?>" data-aos="fade-up">
                    <img src="<?= htmlspecialchars($url) ?>" alt="<?= htmlspecialchars($caption) ?>" loading="lazy">
                    <div class="gallery-overlay">
                        <span class="category-pill"><?= htmlspecialchars($img['category'] ?: 'Impact') ?></span>
                        <p class="gallery-caption"><?= htmlspecialchars($caption) ?></p>
                        <div class="mt-3 text-white-50 small fw-bold uppercase tracking-tighter">
                            <i class="fas fa-expand-alt me-2"></i> View Fullscreen
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination-wrap pb-5">
        <div class="d-flex gap-3">
            <a href="?page=<?= $page - 1 ?>" class="page-link-custom <?= $page <= 1 ? 'disabled' : '' ?>">
                <i class="fas fa-chevron-left me-2"></i> Previous
            </a>
            <a href="?page=<?= $page + 1 ?>" class="page-link-custom <?= $page >= $totalPages ? 'disabled' : '' ?>">
                Next <i class="fas fa-chevron-right ms-2"></i>
            </a>
        </div>
        <p class="text-muted small fw-bold text-uppercase tracking-widest">
            Page <?= $page ?> <span class="mx-2 opacity-25">/</span> <?= $totalPages ?>
        </p>
    </div>
    <?php endif; ?>
</main>

<script>
    // Initialize Lightbox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });

    // Initialize Scroll Animations
    AOS.init({
        duration: 1000,
        once: true,
        offset: 50
    });
</script>

<?php include __DIR__ . '/backend/includes/footer.php'; ?>