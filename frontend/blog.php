<?php
// Frontend page — standardized bootstrap
require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';

/* ---------------- HELPERS ---------------- */
function readingTime(string $content): int {
    $words = str_word_count(strip_tags($content));
    return max(1, ceil($words / 200));
}

/* ---------------- FETCH POSTS ---------------- */
$stmt = $pdo->query("
    SELECT 
        title, slug, excerpt, content, 
        cover_image, media_type, 
        featured, author, published_at 
    FROM posts 
    WHERE published_at IS NOT NULL 
    ORDER BY featured DESC, published_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------------- CONFIG ---------------- */
$uploadPath   = 'uploads/posts/';
$defaultImage = 'assets/images/blog-default.jpg';
?>

<style>
/* =========================================================
   REFINED BLOG SYSTEM
   Focus: Vertical Rhythm, Anti-Stretch, Editorial Polish
========================================================= */

:root {
  --primary: #0b8a15;
  --primary-dark: #086b11;
  --text-main: #0f172a;
  --text-muted: #64748b;
  --radius-blog: 20px;
  --blog-card-max: 420px; 
}

/* --- HERO SECTION --- */
.blog-hero {
  padding-block: clamp(4rem, 12vw, 4rem);
  background: 
    radial-gradient(circle at top right, rgba(11,138,21,0.06), transparent 50%),
    radial-gradient(circle at bottom left, rgba(241,245,249,0.8), transparent 50%);
  text-align: center;
  border-bottom: 1px solid rgba(0,0,0,0.03);
}

.blog-hero h1 {
  font-size: clamp(2.5rem, 6vw, 4rem);
  font-weight: 900;
  letter-spacing: -0.04em;
  color: var(--text-main);
  margin-bottom: 1.5rem;
}

.blog-hero p {
  /* max-width: 50ch; */
  margin-inline: auto;
  font-size: 1.2rem;
  line-height: 1.6;
  color: var(--text-muted);
}

/* --- GRID DISCIPLINE --- */
.blog-grid {
  display: grid;
  /* Prevents cards from growing too wide on large screens */
  grid-template-columns: repeat(auto-fill, minmax(min(100%, 340px), 1fr));
  gap: 4rem 2.5rem; 
  justify-content: center;
  padding-bottom: 5rem;
  margin-left: 7%
}

/* --- CARD ARCHITECTURE --- */
.blog-card {
  display: flex;
  flex-direction: column;
  text-decoration: none;
  color: inherit;
  height: 100%;
  max-width: var(--blog-card-max);
  margin-inline: auto;
  transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  
  /* Reset layout.css global card impacts */
  background: transparent !important;
  border: none !important;
  padding: 0 !important;
  box-shadow: none !important;
}

.blog-card:hover {
  transform: translateY(-10px);
}

/* --- MEDIA --- */
.blog-media-wrapper {
  position: relative;
  border-radius: var(--radius-blog);
  overflow: hidden;
  aspect-ratio: 16 / 10;
  margin-bottom: 1.5rem;
  background: #f8fafc;
}

.blog-media-wrapper img, 
.blog-media-wrapper video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.8s ease;
}

.blog-card:hover .blog-media-wrapper img {
  transform: scale(1.06);
}

.featured-badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: white;
  color: var(--primary-dark);
  padding: 0.5rem 1rem;
  border-radius: 50px;
  font-size: 0.7rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  z-index: 2;
}

/* --- TYPOGRAPHY --- */
.blog-meta {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.6rem;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.02em;
  color: var(--primary);
  margin-bottom: 0.75rem;
}

.blog-meta .dot {
  width: 3px;
  height: 3px;
  background: #cbd5e1;
  border-radius: 50%;
}

.blog-meta .author-name {
    color: var(--text-main);
}

.blog-meta .date-read {
  color: var(--text-muted);
  font-weight: 500;
}

.blog-title {
  font-size: 1.5rem;
  font-weight: 800;
  line-height: 1.25;
  color: var(--text-main);
  margin-bottom: 1rem;
  transition: color 0.3s ease;
}

.blog-card:hover .blog-title {
  color: var(--primary);
}

.blog-excerpt {
  font-size: 1rem;
  line-height: 1.6;
  color: var(--text-muted);
  margin-bottom: 1.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* --- CTA BUTTON --- */
.blog-cta {
  margin-top: auto;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 700;
  font-size: 0.9rem;
  color: var(--primary);
}

.blog-cta svg {
  transition: transform 0.3s var(--ease);
}

.blog-card:hover .blog-cta svg {
  transform: translateX(6px);
}

@media (max-width: 768px) {
    .blog-hero { padding-block: 4rem; }
    .blog-grid { gap: 3rem 1.5rem; }
}
</style>

<main>
    <section class="blog-hero">
        <div class="container">
            <h1>Journal.</h1>
            <p>Documenting the evolution of sustainable ecosystems through the lens of ReSEED.</p>
        </div>
    </section>

    <section class="container blog-section">
        <?php if (!$posts): ?>
            <div style="text-align:center; padding: 100px 0;">
                <p style="font-size: 1.2rem; color: var(--text-muted)">Our story is just beginning. Check back soon.</p>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($posts as $post): 
                    $img = $post['cover_image'] ? $uploadPath.$post['cover_image'] : $defaultImage;
                    $url = 'post.php?slug='.urlencode($post['slug']);
                    $read = readingTime($post['content']);
                ?>
                <article>
                    <a href="<?= $url ?>" class="blog-card">
                        <div class="blog-media-wrapper">
                            <?php if ($post['featured']): ?>
                                <span class="featured-badge">Editor's Pick</span>
                            <?php endif; ?>
                            
                            <?php if ($post['media_type'] === 'video'): ?>
                                <video muted autoplay loop playsinline>
                                    <source src="<?= $img ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <img src="<?= $img ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                            <?php endif; ?>
                        </div>

                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="author-name"><?= htmlspecialchars($post['author']) ?></span>
                                <span class="dot"></span>
                                <span class="date-read"><?= date('M j', strtotime($post['published_at'])) ?></span>
                                <span class="dot"></span>
                                <span class="date-read"><?= $read ?> min read</span>
                            </div>

                            <h2 class="blog-title"><?= htmlspecialchars($post['title']) ?></h2>
                            
                            <p class="blog-excerpt">
                                <?= htmlspecialchars($post['excerpt']) ?>
                            </p>

                            <div class="blog-cta">
                                <span>Read Full Article</span>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </div>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include dirname(__DIR__) . '/backend/includes/footer.php'; ?>
