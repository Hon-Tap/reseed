<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| FRONTEND — SINGLE STORY (CLOUDINARY NATIVE)
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

function getPostMediaUrl(?string $url): ?string
{
    return $url ?: null;
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
        cover_media,
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
<main class="container mx-auto" style="min-height:70vh;padding:12rem 1.5rem;text-align:center;">
    <div class="mb-6 text-slate-200">
        <i class="fa-solid fa-ghost text-6xl"></i>
    </div>
    <h1 style="font-size:3rem;font-weight:900;margin-bottom:1rem;color:#0f172a;">
        Story not found
    </h1>
    <p style="color:#64748b;margin-bottom:3rem;font-size:1.2rem;">
        The article you’re looking for has moved or been archived.
    </p>
    <a href="/blog.php"
       style="background:#10b981;color:white;padding:12px 30px;border-radius:99px;font-weight:700;text-decoration:none;">
        Return to Journal
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

$mediaUrl = getPostMediaUrl($post['cover_media']);
$isVideo  = ($post['media_type'] ?? 'image') === 'video';
$readTime = reading_time($post['content']);
?>

<style>
/* =========================================================
   SINGLE ARTICLE — PREMIUM EDITORIAL LAYOUT
========================================================= */

:root {
    --primary: #10b981;
    --primary-dark: #059669;
    --ink: #0f172a;
    --ink-light: #1e293b;
    --muted: #64748b;
    --surface: #ffffff;
    --radius-xl: 32px;
}

/* Reading Progress */
#reading-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 5px;
    width: 0%;
    background: linear-gradient(to right, var(--primary), #34d399);
    z-index: 10000;
}

.article-wrap {
    background: var(--surface);
    padding-bottom: 8rem;
}

/* Header */
.article-header {
    max-width: 850px;
    margin: 0 auto;
    padding: 8rem 1.5rem 4rem;
    text-align: center;
}

.article-kicker {
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--primary-dark);
    margin-bottom: 1.5rem;
}

.article-title {
    font-size: clamp(2.8rem, 6vw, 4.5rem);
    font-weight: 900;
    line-height: 1.05;
    color: var(--ink);
    letter-spacing: -0.05em;
    margin-bottom: 2.5rem;
}

.article-meta {
    display: flex;
    justify-content: center;
    gap: 1rem;
    font-size: 1rem;
    color: var(--muted);
    font-weight: 500;
}

/* Media */
.article-media {
    max-width: 1200px;
    margin: 0 auto 6rem;
    padding: 0 1.5rem;
}

.media-frame {
    aspect-ratio: 16 / 9;
    border-radius: var(--radius-xl);
    overflow: hidden;
    background: #f1f5f9;
    box-shadow: 0 40px 80px -20px rgba(0,0,0,0.15);
}

.media-frame img,
.media-frame video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Body */
.article-body {
    max-width: 740px;
    margin: 0 auto;
    padding: 0 1.5rem;
    font-size: 1.25rem;
    line-height: 1.9;
    color: var(--ink-light);
}

/* Footer */
.article-footer {
    max-width: 740px;
    margin: 6rem auto 0;
    padding: 3rem 1.5rem 0;
    border-top: 1px solid #f1f5f9;
}

.back-btn {
    font-weight: 800;
    color: var(--muted);
    text-decoration: none;
}
</style>

<div id="reading-progress"></div>

<main class="article-wrap">

<header class="article-header">
    <span class="article-kicker">Field Journal</span>
    <h1 class="article-title"><?= htmlspecialchars($post['title']) ?></h1>

    <div class="article-meta">
        <span><?= htmlspecialchars($post['author'] ?: 'Team ReSEED') ?></span>
        <span>•</span>
        <span><?= date('F j, Y', strtotime($post['published_at'])) ?></span>
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
            <img src="<?= htmlspecialchars($mediaUrl) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<article class="article-body">
    <?= $post['content'] ?>
</article>

<footer class="article-footer">
    <a href="/blog.php" class="back-btn">
        ← Back to Journal
    </a>
</footer>

</main>

<script>
window.addEventListener('scroll', () => {
    const d = document.documentElement;
    const p = (d.scrollTop / (d.scrollHeight - d.clientHeight)) * 100;
    document.getElementById('reading-progress').style.width = p + '%';
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
