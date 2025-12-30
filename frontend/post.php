<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| FRONTEND — SINGLE STORY (CLOUDINARY ENABLED)
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

function getBlogPostUrl($path) {
    if (empty($path)) return null;
    return (strpos($path, 'http') === 0) ? $path : '/uploads/posts/' . $path;
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
    <main class="container mx-auto" style="min-height:70vh;padding:12rem 1.5rem;text-align:center;">
        <div class="mb-6 text-slate-200">
            <i class="fa-solid fa-ghost text-6xl"></i>
        </div>
        <h1 style="font-size:3rem;font-weight:900;letter-spacing:-0.04em;margin-bottom:1rem;color:#0f172a;">Story not found</h1>
        <p style="color:#64748b;margin-bottom:3rem;font-size:1.2rem;">The article you’re looking for has moved or been archived.</p>
        <a href="blog.php" style="background:#10b981;color:white;padding:12px 30px;border-radius:99px;font-weight:700;text-decoration:none;display:inline-block;">
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
$mediaUrl = getBlogPostUrl($post['cover_image']);
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

/* Reading Progress Bar */
#reading-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 5px;
    width: 0%;
    background: linear-gradient(to right, var(--primary), #34d399);
    z-index: 10000;
    transition: width .1s ease-out;
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
    display: inline-block;
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
    align-items: center;
    gap: 1rem;
    font-size: 1rem;
    color: var(--muted);
    font-weight: 500;
}

.author-badge {
    color: var(--ink);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
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

/* Body Content */
.article-body {
    max-width: 740px;
    margin: 0 auto;
    padding: 0 1.5rem;
    font-size: 1.25rem;
    line-height: 1.9;
    color: var(--ink-light);
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

/* Styled HTML content from Admin */
.article-body p { margin-bottom: 2rem; }
.article-body h2 { 
    font-size: 2.2rem; 
    font-weight: 800; 
    color: var(--ink); 
    margin: 4rem 0 1.5rem; 
    letter-spacing: -0.03em;
}
.article-body blockquote {
    border-left: 5px solid var(--primary);
    padding: 1rem 0 1rem 2rem;
    margin: 3rem 0;
    font-style: italic;
    font-size: 1.5rem;
    color: var(--ink);
    background: #f0fdf4;
    border-radius: 0 16px 16px 0;
}

/* Footer & Back */
.article-footer {
    max-width: 740px;
    margin: 6rem auto 0;
    padding: 3rem 1.5rem 0;
    border-top: 1px solid #f1f5f9;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    font-weight: 800;
    color: var(--muted);
    text-decoration: none;
    transition: all 0.3s;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.back-btn:hover {
    color: var(--primary-dark);
    transform: translateX(-5px);
}

@media (max-width: 768px) {
    .article-header { padding-top: 5rem; }
    .article-title { font-size: 2.5rem; }
    .article-body { font-size: 1.15rem; }
}
</style>

<div id="reading-progress"></div>

<main class="article-wrap">

    <header class="article-header">
        <span class="article-kicker">Field Journal</span>

        <h1 class="article-title">
            <?= htmlspecialchars($post['title']) ?>
        </h1>

        <div class="article-meta">
            <span class="author-badge">
                <i class="fa-solid fa-circle-user text-emerald-500"></i>
                <?= htmlspecialchars($post['author'] ?: 'Team ReSEED') ?>
            </span>
            <span class="text-slate-300">•</span>
            <span><?= date('F j, Y', strtotime($post['published_at'])) ?></span>
            <span class="text-slate-300">•</span>
            <span class="flex items-center gap-1">
                <i class="fa-regular fa-clock"></i> <?= $readTime ?> min read
            </span>
        </div>
    </header>

    <?php if ($mediaUrl): ?>
        <section class="article-media">
            <div class="media-frame">
                <?php if ($isVideo): ?>
                    <video controls playsinline poster="">
                        <source src="<?= htmlspecialchars($mediaUrl) ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php else: ?>
                    <img
                        src="<?= htmlspecialchars($mediaUrl) ?>"
                        alt="<?= htmlspecialchars($post['title']) ?>"
                    >
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <article class="article-body">
        <?= $post['content'] ?>
    </article>

    <footer class="article-footer">
        <div class="flex justify-between items-center">
            <a href="blog.php" class="back-btn">
                <i class="fa-solid fa-arrow-left-long"></i>
                Back to Journal
            </a>
            
            <div class="flex gap-4 items-center">
                <span class="text-xs font-bold uppercase text-slate-400">Share</span>
                <a href="https://twitter.com/share?url=<?= urlencode((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" target="_blank" class="text-slate-400 hover:text-sky-400 transition-colors">
                    <i class="fa-brands fa-twitter text-xl"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" target="_blank" class="text-slate-400 hover:text-blue-600 transition-colors">
                    <i class="fa-brands fa-facebook text-xl"></i>
                </a>
            </div>
        </div>
    </footer>

</main>

<script>
// Dynamic Reading Progress
window.addEventListener('scroll', () => {
    const doc = document.documentElement;
    const scrollPercent = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
    document.getElementById('reading-progress').style.width = scrollPercent + '%';
});

// Smooth fade in for body images (if any inside content)
document.querySelectorAll('.article-body img').forEach(img => {
    img.style.borderRadius = '16px';
    img.style.margin = '2rem 0';
    img.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>