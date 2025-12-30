<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| BOOTSTRAP
|--------------------------------------------------------------------------
*/
require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
function reading_time(string $html): int
{
    $words = str_word_count(strip_tags($html));
    return max(1, (int) ceil($words / 200));
}

/**
 * Helper to ensure we use the Cloudinary URL or a placeholder
 * Updated to use the correct schema column
 */
function getBlogImageUrl($path) {
    if (empty($path)) return 'https://images.unsplash.com/photo-1542601906990-b4d3fb773b09?auto=format&fit=crop&q=80&w=1000';
    // If it's a full URL (Cloudinary), return it. Otherwise, assume it's a legacy local path.
    return (strpos($path, 'http') === 0) ? $path : '/uploads/posts/' . $path;
}

/*
|--------------------------------------------------------------------------
| FETCH PUBLISHED POSTS (FIXED SCHEMA)
|--------------------------------------------------------------------------
*/
try {
    $stmt = $pdo->query("
        SELECT
            title,
            slug,
            excerpt,
            content,
            cover_media,
            media_type,
            featured,
            author,
            published_at
        FROM posts
        WHERE published_at IS NOT NULL
        ORDER BY featured DESC, published_at DESC
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback if DB is empty or still syncing
    $posts = [];
}
?>

<style>
/* =========================================================
    BLOG — PREMIUM EDITORIAL UI
========================================================= */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@800&display=swap');

:root {
    --primary-green: #099227;
    --ink: #0f172a;
    --slate: #64748b;
    --glass-border: rgba(15, 23, 42, 0.08);
}

.blog-hero {
    padding: 100px 0 60px;
    background: radial-gradient(circle at top right, #f0fdf4 0%, #ffffff 70%);
    position: relative;
    overflow: hidden;
}

.blog-hero::after {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 200px; height: 200px;
    background: var(--primary-green);
    filter: blur(120px);
    opacity: 0.1;
}

.blog-hero h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(3rem, 8vw, 5rem);
    font-weight: 800;
    letter-spacing: -0.04em;
    color: var(--ink);
    margin-bottom: 20px;
}

/* ---------- BLOG GRID ---------- */
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 60px 40px;
    padding: 60px 0 120px;
}

/* ---------- POST CARD ---------- */
.post-card {
    text-decoration: none !important;
    display: block;
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.post-card:hover {
    transform: translateY(-10px);
}

.post-media-box {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 10;
    border-radius: 32px;
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.1);
}

.post-media-box img, 
.post-media-box video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.7s ease;
}

.post-card:hover .post-media-box img {
    transform: scale(1.06);
}

.featured-pill {
    position: absolute;
    top: 20px; left: 20px;
    background: white;
    padding: 6px 14px;
    border-radius: 100px;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--primary-green);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    z-index: 5;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--slate);
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 12px;
}

.post-meta span.author {
    color: var(--primary-green);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.post-title {
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1.3;
    color: var(--ink);
    margin-bottom: 12px;
    transition: color 0.3s;
}

.post-card:hover .post-title {
    color: var(--primary-green);
}

.post-excerpt {
    color: var(--slate);
    font-size: 1rem;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.read-btn {
    margin-top: 20px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 800;
    font-size: 0.9rem;
    color: var(--ink);
}

.read-btn i {
    font-size: 0.7rem;
    transition: transform 0.3s ease;
}

.post-card:hover .read-btn i {
    transform: translateX(5px);
}

@media (max-width: 768px) {
    .blog-grid { grid-template-columns: 1fr; gap: 40px; }
}
</style>

<main>
    <section class="blog-hero">
        <div class="container text-center">
            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 mb-3 fw-bold uppercase">Field Notes</span>
            <h1>The Field Journal</h1>
            <p class="lead text-secondary mx-auto" style="max-width: 600px;">Chronicles of restoration, community resilience, and ecological insights from South Sudan.</p>
        </div>
    </section>

    <div class="container">
        <?php if (empty($posts)): ?>
            <div class="py-5 text-center">
                <div class="p-5 bg-light rounded-5 border border-dashed">
                    <i class="fas fa-feather-pointed fa-3x text-muted mb-3"></i>
                    <h3 class="fw-bold">No stories found.</h3>
                    <p class="text-muted">We are currently drafting new updates from the ground.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($posts as $post): 
                    $mediaPath = getBlogImageUrl($post['cover_media']);
                    $slugUrl = '/post.php?slug=' . urlencode($post['slug']);
                    $minutes = reading_time($post['content']);
                ?>
                <article>
                    <a href="<?= $slugUrl ?>" class="post-card">
                        <div class="post-media-box">
                            <?php if ($post['featured']): ?>
                                <span class="featured-pill">Top Story</span>
                            <?php endif; ?>

                            <?php if (($post['media_type'] ?? 'image') === 'video'): ?>
                                <video muted autoplay loop playsinline>
                                    <source src="<?= htmlspecialchars($mediaPath) ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($mediaPath) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                            <?php endif; ?>
                        </div>

                        <div class="post-meta">
                            <span class="author"><?= htmlspecialchars($post['author'] ?: 'ReSEED Team') ?></span>
                            <span>•</span>
                            <span><?= date('M j, Y', strtotime($post['published_at'])) ?></span>
                            <span>•</span>
                            <span><?= $minutes ?> min read</span>
                        </div>

                        <h2 class="post-title"><?= htmlspecialchars($post['title']) ?></h2>
                        <p class="post-excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>

                        <div class="read-btn">
                            Read Full Story <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/backend/includes/footer.php'; ?>