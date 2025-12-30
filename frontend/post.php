<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| FRONTEND — SINGLE STORY (EDITORIAL REWRITE)
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
    // Handle Cloudinary/Full URLs vs local uploads
    return (strpos($path, 'http') === 0) ? $path : '/uploads/posts/' . $path;
}

/*
|--------------------------------------------------------------------------
| FETCH POST BY SLUG (FIXED SCHEMA)
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
| NOT FOUND STATE
|--------------------------------------------------------------------------
*/
if (!$post): ?>
    <main class="container py-32 text-center">
        <div class="mb-5 text-slate-200">
            <i class="fa-solid fa- ghost fa-4x"></i>
        </div>
        <h1 class="display-4 fw-black mb-3">Story not found</h1>
        <p class="text-muted mb-5">The article you’re looking for has moved or been archived.</p>
        <a href="blog.php" class="btn btn-success px-5 py-3 rounded-pill fw-bold">
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
$mediaUrl = getBlogPostUrl($post['cover_media']);
$isVideo  = ($post['media_type'] ?? 'image') === 'video';
$readTime = reading_time($post['content']);
$currentUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<style>
/* ================= SINGLE STORY REFINED UI ================= */
:root {
    --primary-green: #099227;
    --ink: #0f172a;
    --slate: #64748b;
}

/* Reading Progress Indicator */
#reading-progress {
    position: fixed;
    top: 0; left: 0;
    height: 4px;
    width: 0%;
    background: var(--primary-green);
    z-index: 9999;
    transition: width 0.1s ease-out;
}

.article-header {
    max-width: 900px;
    margin: 0 auto;
    padding: 100px 20px 60px;
    text-align: center;
}

.article-title {
    font-size: clamp(2.5rem, 7vw, 4.5rem);
    font-weight: 800;
    line-height: 1.05;
    letter-spacing: -0.04em;
    color: var(--ink);
    margin-bottom: 30px;
}

.article-meta {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    font-weight: 700;
    color: var(--slate);
    font-size: 0.95rem;
}

.author-link {
    color: var(--primary-green);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-decoration: none;
}

/* Feature Media */
.article-hero-media {
    max-width: 1200px;
    margin: 0 auto 80px;
    padding: 0 20px;
}

.media-aspect {
    aspect-ratio: 16 / 9;
    border-radius: 40px;
    overflow: hidden;
    box-shadow: 0 40px 100px -20px rgba(15, 23, 42, 0.15);
    background: #f1f5f9;
}

.media-aspect img, .media-aspect video {
    width: 100%; height: 100%; object-fit: cover;
}

/* Body Content Styling */
.article-content {
    max-width: 760px;
    margin: 0 auto;
    padding: 0 25px;
    font-size: 1.25rem;
    line-height: 1.8;
    color: #334155;
}

.article-content h2, .article-content h3 {
    color: var(--ink);
    font-weight: 800;
    margin-top: 50px;
    margin-bottom: 20px;
}

.article-content p {
    margin-bottom: 25px;
}

.article-content blockquote {
    font-size: 1.6rem;
    font-style: italic;
    font-weight: 600;
    color: var(--ink);
    padding: 30px 40px;
    margin: 60px 0;
    background: #f0fdf4;
    border-left: 6px solid var(--primary-green);
    border-radius: 0 24px 24px 0;
}

.article-footer {
    max-width: 760px;
    margin: 80px auto;
    padding: 40px 25px 0;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

@media (max-width: 768px) {
    .article-header { padding-top: 60px; }
    .article-title { font-size: 2.5rem; }
}
</style>

<div id="reading-progress"></div>

<main class="bg-white">
    <header class="article-header" data-aos="fade-up">
        <nav class="mb-4">
            <a href="blog.php" class="text-success text-decoration-none fw-bold small text-uppercase tracking-widest">
                <i class="fas fa-arrow-left me-2"></i> Field Journal
            </a>
        </nav>
        
        <h1 class="article-title"><?= htmlspecialchars($post['title']) ?></h1>
        
        <div class="article-meta">
            <span class="author-link"><?= htmlspecialchars($post['author'] ?: 'Team ReSEED') ?></span>
            <span class="opacity-25">|</span>
            <span><?= date('F j, Y', strtotime($post['published_at'])) ?></span>
            <span class="opacity-25">|</span>
            <span><i class="far fa-clock me-1"></i> <?= $readTime ?> min read</span>
        </div>
    </header>

    <?php if ($mediaUrl): ?>
    <section class="article-hero-media" data-aos="zoom-in">
        <div class="media-aspect">
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

    <article class="article-content" data-aos="fade-up">
        <?= $post['content'] ?>
    </article>

    <footer class="article-footer">
        <a href="blog.php" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
            <i class="fas fa-chevron-left me-2"></i> All Stories
        </a>
        
        <div class="d-flex align-items-center gap-3">
            <span class="small fw-bold text-muted text-uppercase">Share</span>
            <a href="https://twitter.com/share?url=<?= urlencode($currentUrl) ?>" target="_blank" class="text-dark fs-5"><i class="fab fa-x-twitter"></i></a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" class="text-primary fs-5"><i class="fab fa-facebook"></i></a>
        </div>
    </footer>
</main>

<script>
// Logic for reading progress bar
window.addEventListener('scroll', () => {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    document.getElementById("reading-progress").style.width = scrolled + "%";
});

// Auto-style images found inside the database content
document.querySelectorAll('.article-content img').forEach(img => {
    img.classList.add('img-fluid', 'rounded-4', 'my-5', 'shadow-sm');
});

AOS.init({ duration: 1000, once: true });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>