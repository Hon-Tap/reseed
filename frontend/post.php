<?php
// Frontend page — standardized bootstrap

require_once __DIR__ . '/../backend/includes/config.php';



function readingTime(string $content): int {
  return max(1, ceil(str_word_count(strip_tags($content)) / 200));
}

$stmt = $pdo->prepare("
  SELECT * FROM posts 
  WHERE slug = ? AND published_at IS NOT NULL 
  LIMIT 1
");
$stmt->execute([$_GET['slug'] ?? '']);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post): ?>
  <section class="container" style="padding:160px 20px; text-align:center; min-height: 60vh;">
    <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Story not found</h2>
    <a href="blog.php" style="color: #10b981; font-weight: 700; text-decoration: none;">← Return to Journal</a>
  </section>
<?php else:
  $img = $post['cover_image'] ? 'uploads/posts/'.$post['cover_image'] : null;
  $read = readingTime($post['content']);
?>

<style>
/* Reading Progress Bar */
#progress-bar {
    position: fixed; top: 0; left: 0; width: 0%; height: 4px;
    background: #10b981; z-index: 9999; transition: width 0.1s ease;
}

.article-container { background: #fff; padding-bottom: 120px; }

/* Header */
.article-header {
    max-width: 900px; margin: 0 auto;
    padding: 120px 24px 60px; text-align: center;
}

.article-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800; line-height: 1.1;
    color: #0f172a; margin: 16px 0 32px;
}

/* Equalized Media Layout */
.article-media-box {
    max-width: 1100px;
    margin: 0 auto 80px;
    padding: 0 24px;
}

.media-aspect-ratio {
    position: relative;
    width: 100%;
    /* This creates a consistent cinematic 16:9 shape for both images and videos */
    aspect-ratio: 16 / 9; 
    border-radius: 28px;
    overflow: hidden;
    background: #f1f5f9;
    box-shadow: 0 30px 60px -12px rgba(0,0,0,0.15);
}

.media-aspect-ratio img, 
.media-aspect-ratio video {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures content fills the box without distortion */
    display: block;
    border: none;
}

/* Content */
.article-content {
    max-width: 740px; margin: 0 auto; padding: 0 24px;
    font-size: 1.25rem; line-height: 1.8; color: #334155;
}

.article-content h2 { color: #0f172a; margin-top: 48px; }

.article-footer {
    max-width: 740px; margin: 80px auto 0;
    padding: 40px 24px; border-top: 1px solid #e2e8f0;
    display: flex; justify-content: space-between; align-items: center;
}

.back-btn {
    text-decoration: none; color: #0f172a; font-weight: 700;
    display: flex; align-items: center; gap: 8px;
}

@media (max-width: 768px) {
    .media-aspect-ratio { aspect-ratio: 4 / 3; } /* Slightly taller on mobile */
    .article-header { padding: 80px 20px 40px; }
}
</style>

<div id="progress-bar"></div>

<article class="article-container">
  <header class="article-header">
    <div style="color: #10b981; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; font-size: 0.8rem;">
        ReSEED Editorial
    </div>
    <h1 class="article-title"><?= htmlspecialchars($post['title']) ?></h1>
    
    <div style="display: flex; justify-content: center; align-items: center; gap: 12px; color: #64748b;">
      <strong><?= htmlspecialchars($post['author']) ?></strong>
      <span>•</span>
      <span><?= date('M j, Y', strtotime($post['published_at'])) ?></span>
      <span>•</span>
      <span><?= $read ?> min read</span>
    </div>
  </header>

  <?php if ($img): ?>
    <div class="article-media-box">
      <div class="media-aspect-ratio">
        <?php if ($post['media_type'] === 'video'): ?>
            <video controls playsinline poster="">
                <source src="<?= $img ?>" type="video/mp4">
            </video>
        <?php else: ?>
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($post['title']) ?>">
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="article-content">
    <?= $post['content'] ?>
  </div>

  <footer class="article-footer">
    <a href="blog.php" class="back-btn">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      Back to Journal
    </a>
  </footer>
</article>

<script>
window.onscroll = function() {
    let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    let scrolled = (winScroll / height) * 100;
    document.getElementById("progress-bar").style.width = scrolled + "%";
};
</script>

<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>