<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Frontend — Single Post (Final, Clean, Polished)
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../backend/includes/config.php';

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function readingTime(string $content): int {
    return max(1, (int) ceil(str_word_count(strip_tags($content)) / 200));
}

/*
|--------------------------------------------------------------------------
| Fetch Post
|--------------------------------------------------------------------------
*/

$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare("
    SELECT *
    FROM posts
    WHERE slug = :slug
      AND published_at IS NOT NULL
    LIMIT 1
");

$stmt->execute(['slug' => $slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post):
?>

<section class="container" style="padding:160px 20px; text-align:center; min-height:60vh;">
    <h2 style="font-size:2.5rem; margin-bottom:20px;">Story not found</h2>
    <a href="blog.php" style="color:#10b981; font-weight:700; text-decoration:none;">
        ← Return to Journal
    </a>
</section>

<?php
else:

$mediaUrl = !empty($post['cover_image'])
    ? UPLOADS_URL . '/posts/' . $post['cover_image']
    : null;

$isVideo = ($post['media_type'] ?? 'image') === 'video';
$read    = readingTime($post['content']);
?>

<style>
/* Reading Progress */
#progress-bar {
    position: fixed;
    top: 0; left: 0;
    width: 0%; height: 4px;
    background: #10b981;
    z-index: 9999;
    transition: width .1s ease;
}

/* Layout */
.article-container { background:#fff; padding-bottom:120px; }

/* Header */
.article-header {
    max-width: 900px;
    margin: 0 auto;
    padding: 120px 24px 60px;
    text-align: center;
}

.article-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
    margin: 16px 0 32px;
}

/* Media */
.article-media-box {
    max-width: 1100px;
    margin: 0 auto 80px;
    padding: 0 24px;
}

.media-frame {
    aspect-ratio: 16 / 9;
    border-radius: 28px;
    overflow: hidden;
    background: #f1f5f9;
    box-shadow: 0 30px 60px -12px rgba(0,0,0,.15);
}

.media-frame img,
.media-frame video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    border: none;
}

/* Content */
.article-content {
    max-width: 740px;
    margin: 0 auto;
    padding: 0 24px;
    font-size: 1.25rem;
    line-height: 1.8;
    color: #334155;
}

.article-content h2 {
    margin-top: 48px;
    color: #0f172a;
}

/* Footer */
.article-footer {
    max-width: 740px;
    margin: 80px auto 0;
    padding: 40px 24px;
    border-top: 1px solid #e2e8f0;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    color: #0f172a;
    text-decoration: none;
}

@media (max-width: 768px) {
    .article-header { padding: 80px 20px 40px; }
    .media-frame { aspect-ratio: 4 / 3; }
}
</style>

<div id="progress-bar"></div>

<article class="article-container">

    <header class="article-header">
        <div style="color:#10b981; font-weight:800; letter-spacing:.1em; text-transform:uppercase; font-size:.8rem;">
            ReSEED Editorial
        </div>

        <h1 class="article-title">
            <?= htmlspecialchars($post['title']) ?>
        </h1>

        <div style="display:flex; justify-content:center; gap:12px; color:#64748b;">
            <strong><?= htmlspecialchars($post['author'] ?? 'Editorial') ?></strong>
            <span>•</span>
            <span><?= date('M j, Y', strtotime($post['published_at'])) ?></span>
            <span>•</span>
            <span><?= $read ?> min read</span>
        </div>
    </header>

    <?php if ($mediaUrl): ?>
        <div class="article-media-box">
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
        </div>
    <?php endif; ?>

    <div class="article-content">
        <?= $post['content'] ?>
    </div>

    <footer class="article-footer">
        <a href="blog.php" class="back-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Journal
        </a>
    </footer>

</article>

<script>
window.addEventListener('scroll', () => {
    const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const progress = (scrollTop / height) * 100;
    document.getElementById('progress-bar').style.width = progress + '%';
});
</script>

<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
