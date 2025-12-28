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

$uploadsUrl   = '/uploads/posts/';
$defaultImage = '/assets/images/blog-default.jpg';
?>

<style>
/* =========================================================
   BLOG — EDITORIAL UI
========================================================= */

:root {
    --green: #0b8a15;
    --green-dark: #086b11;
    --ink: #0f172a;
    --muted: #64748b;
    --radius: 22px;
}

/* ---------- HERO ---------- */
.blog-hero {
    padding: 5rem 0;
    background:
        radial-gradient(circle at 80% 20%, rgba(11,138,21,.08), transparent 50%),
        radial-gradient(circle at 20% 80%, rgba(241,245,249,.9), transparent 50%);
    text-align: center;
    border-bottom: 1px solid rgba(0,0,0,.04);
}

.blog-hero h1 {
    font-size: clamp(2.6rem, 6vw, 4rem);
    font-weight: 900;
    letter-spacing: -.04em;
    margin-bottom: 1rem;
    color: var(--ink);
}

.blog-hero p {
    font-size: 1.15rem;
    color: var(--muted);
}

/* ---------- GRID ---------- */
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 3.5rem 2.5rem;
    padding: 5rem 0;
}

/* ---------- CARD ---------- */
.blog-card {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: transform .35s ease;
}

.blog-card:hover {
    transform: translateY(-10px);
}

/* ---------- MEDIA ---------- */
.blog-media {
    position: relative;
    aspect-ratio: 16 / 10;
    border-radius: var(--radius);
    overflow: hidden;
    background: #f1f5f9;
    margin-bottom: 1.5rem;
}

.blog-media img,
.blog-media video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .8s ease;
}

.blog-card:hover img {
    transform: scale(1.06);
}

.badge-featured {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: #fff;
    padding: .4rem .9rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: .05em;
    color: var(--green-dark);
    box-shadow: 0 6px 16px rgba(0,0,0,.12);
}

/* ---------- TEXT ---------- */
.blog-meta {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--green);
    display: flex;
    gap: .6rem;
    flex-wrap: wrap;
    margin-bottom: .6rem;
}

.blog-meta span {
    color: var(--muted);
    font-weight: 600;
}

.blog-title {
    font-size: 1.4rem;
    font-weight: 800;
    line-height: 1.25;
    margin-bottom: .8rem;
    color: var(--ink);
}

.blog-excerpt {
    font-size: .98rem;
    color: var(--muted);
    line-height: 1.6;
    margin-bottom: 1.3rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-cta {
    margin-top: auto;
    font-weight: 700;
    font-size: .9rem;
    color: var(--green);
    display: inline-flex;
    gap: .4rem;
    align-items: center;
}

.blog-cta svg {
    transition: transform .3s ease;
}

.blog-card:hover .blog-cta svg {
    transform: translateX(6px);
}
</style>

<main>

    <!-- HERO -->
    <section class="blog-hero">
        <div class="container">
            <h1>Field Journal</h1>
            <p>Stories and insights from the ground as we restore ecosystems.</p>
        </div>
    </section>

    <!-- POSTS -->
    <section class="container">

        <?php if (!$posts): ?>
            <div style="padding:6rem 0;text-align:center;">
                <p style="font-size:1.1rem;color:var(--muted)">
                    No stories published yet. Check back soon.
                </p>
            </div>
        <?php else: ?>

            <div class="blog-grid">

                <?php foreach ($posts as $post):

                    $image = $post['cover_image']
                        ? $uploadsUrl . $post['cover_image']
                        : $defaultImage;

                    $url  = '/post.php?slug=' . urlencode($post['slug']);
                    $read = reading_time($post['content']);
                ?>

                <article>
                    <a href="<?= $url ?>" class="blog-card">

                        <div class="blog-media">
                            <?php if ($post['featured']): ?>
                                <span class="badge-featured">Featured</span>
                            <?php endif; ?>

                            <?php if (($post['media_type'] ?? 'image') === 'video'): ?>
                                <video muted autoplay loop playsinline>
                                    <source src="<?= $image ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <img src="<?= $image ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                            <?php endif; ?>
                        </div>

                        <div class="blog-meta">
                            <?= htmlspecialchars($post['author']) ?>
                            <span>• <?= date('M j, Y', strtotime($post['published_at'])) ?></span>
                            <span>• <?= $read ?> min read</span>
                        </div>

                        <h2 class="blog-title"><?= htmlspecialchars($post['title']) ?></h2>

                        <p class="blog-excerpt">
                            <?= htmlspecialchars($post['excerpt']) ?>
                        </p>

                        <span class="blog-cta">
                            Read Story
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
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
