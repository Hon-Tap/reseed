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
 */
function getBlogImageUrl($path) {
    if (empty($path)) return 'https://via.placeholder.com/800x500?text=Field+Journal';
    // If it's a full URL (Cloudinary), return it. Otherwise, assume it's a legacy local path.
    return (strpos($path, 'http') === 0) ? $path : '/uploads/posts/' . $path;
}

/*
|--------------------------------------------------------------------------
| FETCH PUBLISHED POSTS
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT
        title,
        slug,
        excerpt,
        content,
        cover_image,
        media_type,
        featured,
        author,
        published_at
    FROM posts
    WHERE published_at IS NOT NULL
    ORDER BY featured DESC, published_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* =========================================================
    BLOG — REFINED EDITORIAL UI
========================================================= */

:root {
    --green: #10b981; /* Emerald 500 */
    --green-dark: #059669; /* Emerald 600 */
    --ink: #0f172a;
    --muted: #64748b;
    --radius: 24px;
    --shadow-sm: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

/* ---------- HERO ---------- */
.blog-hero {
    padding: 6rem 0;
    background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
    text-align: center;
}

.blog-hero h1 {
    font-size: clamp(2.8rem, 7vw, 4.5rem);
    font-weight: 900;
    letter-spacing: -.05em;
    margin-bottom: 1.2rem;
    color: var(--ink);
    line-height: 1;
}

.blog-hero p {
    font-size: 1.25rem;
    color: var(--muted);
    max-width: 600px;
    margin: 0 auto;
    font-weight: 500;
}

/* ---------- GRID ---------- */
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 4rem 2.5rem;
    padding: 4rem 0 8rem;
}

/* ---------- CARD ---------- */
.blog-card {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: all .4s cubic-bezier(0.2, 1, 0.3, 1);
}

.blog-card:hover {
    transform: translateY(-12px);
}

/* ---------- MEDIA ---------- */
.blog-media {
    position: relative;
    aspect-ratio: 16 / 10;
    border-radius: var(--radius);
    overflow: hidden;
    background: #f1f5f9;
    margin-bottom: 1.8rem;
    box-shadow: var(--shadow-sm);
}

.blog-media img,
.blog-media video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .8s ease;
}

.blog-card:hover img {
    transform: scale(1.08);
}

.badge-featured {
    position: absolute;
    top: 1.2rem;
    left: 1.2rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    padding: .5rem 1rem;
    border-radius: 99px;
    font-size: .7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--green-dark);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 2;
}

/* ---------- TEXT ---------- */
.blog-meta {
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--green-dark);
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-bottom: .8rem;
}

.blog-meta .dot {
    width: 4px;
    height: 4px;
    background: #cbd5e1;
    border-radius: 50%;
}

.blog-meta span {
    color: var(--muted);
    font-weight: 600;
}

.blog-title {
    font-size: 1.6rem;
    font-weight: 800;
    line-height: 1.3;
    margin-bottom: 1rem;
    color: var(--ink);
    transition: color 0.3s;
}

.blog-card:hover .blog-title {
    color: var(--green-dark);
}

.blog-excerpt {
    font-size: 1.05rem;
    color: var(--muted);
    line-height: 1.6;
    margin-bottom: 1.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-cta {
    margin-top: auto;
    font-weight: 800;
    font-size: .95rem;
    color: var(--green-dark);
    display: inline-flex;
    gap: .5rem;
    align-items: center;
}

.blog-cta svg {
    transition: transform .3s var(--ease);
}

.blog-card:hover .blog-cta svg {
    transform: translateX(8px);
}
</style>

<main>

    <section class="blog-hero">
        <div class="container mx-auto px-4">
            <h1>Field Journal</h1>
            <p>Stories, updates, and ecological insights from our global restoration efforts.</p>
        </div>
    </section>

    <section class="container mx-auto px-4">

        <?php if (!$posts): ?>
            <div class="py-32 text-center bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200">
                <i class="fa-solid fa-feather-pointed text-5xl text-slate-300 mb-4"></i>
                <h3 class="text-xl font-bold text-slate-900">The journal is empty.</h3>
                <p class="text-slate-500">Check back soon for new stories from the field.</p>
            </div>
        <?php else: ?>

            <div class="blog-grid">

                <?php foreach ($posts as $post):
                    $image = getBlogImageUrl($post['cover_image']);
                    $url   = '/post.php?slug=' . urlencode($post['slug']);
                    $read  = reading_time($post['content']);
                ?>

                <article>
                    <a href="<?= $url ?>" class="blog-card">

                        <div class="blog-media">
                            <?php if ($post['featured']): ?>
                                <span class="badge-featured">Featured</span>
                            <?php endif; ?>

                            <?php if (($post['media_type'] ?? 'image') === 'video'): ?>
                                <video muted autoplay loop playsinline>
                                    <source src="<?= htmlspecialchars($image) ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                            <?php endif; ?>
                        </div>

                        <div class="blog-meta">
                            <?= htmlspecialchars($post['author'] ?: 'Team Reseed') ?>
                            <div class="dot"></div>
                            <span><?= date('M j, Y', strtotime($post['published_at'])) ?></span>
                            <div class="dot"></div>
                            <span><?= $read ?> min read</span>
                        </div>

                        <h2 class="blog-title"><?= htmlspecialchars($post['title']) ?></h2>

                        <p class="blog-excerpt">
                            <?= htmlspecialchars($post['excerpt']) ?>
                        </p>

                        <span class="blog-cta">
                            Read Story
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </span>

                    </a>
                </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

<?php require_once dirname(__DIR__) . '/backend/includes/footer.php'; ?>