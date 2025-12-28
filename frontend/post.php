<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| FRONTEND — SINGLE STORY (PRODUCTION VERSION)
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../backend/includes/config.php';
require_once __DIR__ . '/includes/header.php';

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
| FETCH POST BY SLUG
|--------------------------------------------------------------------------
*/
$slug = trim($_GET['slug'] ?? '');

$stmt = $pdo->prepare("
    SELECT
        title,
        slug,
        content,
        cover_image,
        media_type,
        author,
        published_at
    FROM posts
    WHERE slug = :slug
      AND published_at IS NOT NULL
    LIMIT 1
");
$stmt->execute(['slug' => $slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| NOT FOUND
|--------------------------------------------------------------------------
*/
if (!$post): ?>
    <main class="container" style="min-height:70vh;padding:8rem 1.5rem;text-align:center;">
        <h1 style="font-size:2.5rem;font-weight:800;margin-bottom:1rem;">Story not found</h1>
        <p style="color:#64748b;margin-bottom:2rem;">The article you’re looking for doesn’t exist or was unpublished.</p>
        <a href="/blog.php" style="font-weight:700;color:#0b8a15;text-decoration:none;">
            ← Back to Journal
        </a>
    </main>
<?php
require_once __DIR__ . '/includes/footer.php';
exit;
endif;

/*
|--------------------------------------------------------------------------
| DERIVED DATA
|--------------------------------------------------------------------------
*/
$mediaUrl = $post['cover_image']
    ? UPLOADS_URL . '/posts/' . $post['cover_image']
    : null;

$isVideo  = ($post['media_type'] ?? 'image') === 'video';
$readTime = reading_time($post['content']);
?>

<style>
/* =========================================================
   SINGLE ARTICLE — EDITORIAL LAYOUT
========================================================= */

:root {
    --green: #0b8a15;
    --ink: #0f172a;
    --muted: #64748b;
    --surface: #ffffff;
    --radius: 28px;
}

/* Progress bar */
#reading-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 4px;
    width: 0%;
    background: var(--green);
    z-index: 9999;
    transition: width .1s ease;
}

/* Wrapper */
.article-wrap {
    background: var(--surface);
    padding-bottom: 7rem;
}

/* Header */
.article-header {
    max-width: 920px;
    margin: 0 auto;
    padding: 7rem 1.5rem 4rem;
    text-align: center;
}

.article-kicker {
    font-size: .75rem;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--green);
    margin-bottom: 1rem;
}

.article-title {
    font-size: clamp(2.6rem, 5vw, 4rem);
    font-weight: 900;
    line-height: 1.1;
    color: var(--ink);
    margin-bottom: 2rem;
}

.article-meta {
    display: flex;
    justify-content: center;
    gap: .75rem;
    flex-wrap: wrap;
    font-size: .9rem;
    color: var(--muted);
}

.article-meta strong {
    color: var(--ink);
}

/* Media */
.article-media {
    max-width: 1100px;
    margin: 0 auto 5rem;
    padding: 0 1.5rem;
}

.media-frame {
    aspect-ratio: 16 / 9;
    border-radius: var(--radius);
    overflow: hidden;
    background: #f1f5f9;
    box-shadow: 0 30px 70px -20px rgba(0,0,0,.18);
}

.media-frame img,
.media-frame video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Content */
.article-body {
    max-width: 760px;
    margin: 0 auto;
    padding: 0 1.5rem;
    font-size: 1.2rem;
    line-height: 1.85;
    color: #334155;
}

.article-body h2,
.article-body h3 {
    color: var(--ink);
    margin-top: 3rem;
}

/* Footer */
.article-footer {
    max-width: 760px;
    margin: 5rem auto 0;
    padding: 2.5rem 1.5rem 0;
    border-top: 1px solid #e5e7eb;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: .6rem;
    font-weight: 700;
    color: var(--ink);
    text-decoration: none;
}

.back-link:hover {
    color: var(--green);
}

@media (max-width: 768px) {
    .article-header { padding-top: 5rem; }
    .media-frame { aspect-ratio: 4 / 3; }
}
</style>

<div id="reading-progress"></div>

<main class="article-wrap">

    <header class="article-header">
        <div class="article-kicker">ReSEED Journal</div>

        <h1 class="article-title">
            <?= htmlspecialchars($post['title']) ?>
        </h1>

        <div class="article-meta">
            <strong><?= htmlspecialchars($post['author'] ?: 'Editorial') ?></strong>
            <span>•</span>
            <span><?= date('M j, Y', strtotime($post['published_at'])) ?></span>
            <span>•</span>
            <span><?= $readTime ?> min read</span>
        </div>
    </header>

    <?php if ($mediaUrl): ?>
        <section class="article-media">
            <div class="media-frame">
                <?php if ($isVideo): ?>
                    <video controls playsinline>
                        <source src="<?= htmlspecialchars($mediaUrl) ?>" type="video/mp4">
                    </video>
                <?php else: ?>
                    <img
                        src="<?= htmlspecialchars($mediaUrl) ?>"
                        alt="<?= htmlspecialchars($post['title']) ?>"
                        loading="lazy"
                    >
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <article class="article-body">
        <?= $post['content'] ?>
    </article>

    <footer class="article-footer">
        <a href="/blog.php" class="back-link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Journal
        </a>
    </footer>

</main>

<script>
window.addEventListener('scroll', () => {
    const doc = document.documentElement;
    const progress = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
    document.getElementById('reading-progress').style.width = progress + '%';
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
