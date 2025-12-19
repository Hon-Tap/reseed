<?php
// Frontend page — standardized bootstrap

require_once __DIR__ . '/../backend/includes/config.php';
require_once __DIR__ . '/includes/header.php';



$uploadPath = 'uploads/projects/';

// Helper: Convert External Links to Embed URLs (Same as in project.php)
function getEmbedUrl($url) {
    $url = htmlspecialchars_decode($url);
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        $url = preg_replace('/watch\?v=([a-zA-Z0-9_-]+)/', 'embed/$1', $url);
        $url = preg_replace('/youtu.be\/([a-zA-Z0-9_-]+)/', 'www.youtube.com/embed/$1', $url);
        // Add params for clean grid background playback
        return htmlspecialchars($url . "?autoplay=1&mute=1&controls=0&loop=1&playlist=" . getIDFromUrl($url));
    } elseif (strpos($url, 'vimeo.com') !== false) {
        $videoId = (int) substr(parse_url($url, PHP_URL_PATH), 1);
        return "https://player.vimeo.com/video/" . $videoId . "?background=1&autoplay=1&loop=1&byline=0&title=0";
    }
    return htmlspecialchars($url);
}

// Helper: Extract ID for YouTube looping
function getIDFromUrl($url) {
    preg_match('/([a-zA-Z0-9_-]+)$/', $url, $matches);
    return $matches[0] ?? '';
}

// Fetch projects
try {
    $stmt = $pdo->query("
        SELECT id, title, slug, summary, cover_image, media_type, media_url, 
               status, location, featured, start_date 
        FROM projects 
        ORDER BY featured DESC, created_at DESC
    ");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Projects fetch error: ' . $e->getMessage());
    $projects = [];
}
?>

<style>
    /* =========================================================
   PROJECTS PAGE — EDITORIAL GRID
   Fully compatible with layout.css
========================================================= */

:root {
  --primary: #0b8a15;
  --primary-dark: #086b11;

  --surface: #ffffff;
  --surface-soft: #f4faf7;

  --text-main: #0f172a;
  --text-muted: #64748b;

  --radius-xl: 20px;
  --radius-lg: 16px;
  --radius-md: 12px;

  --shadow-soft: 0 8px 24px rgba(0, 0, 0, 0.05);
  --shadow-hover: 0 18px 48px rgba(0, 0, 0, 0.10);

  --ease: cubic-bezier(.22, 1, .36, 1);
}

/* ================= PAGE HEADER ================= */

.page-header {
  background: linear-gradient(
    180deg,
    var(--surface-soft) 0%,
    #ffffff 100%
  );
  padding-block: clamp(3.5rem, 7vw, 5.5rem);
  text-align: center;
}

.page-header h1 {
  font-size: clamp(2.2rem, 4vw, 3rem);
  font-weight: 800;
  color: var(--text-main);
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
}

.page-header p {
  font-size: 1.05rem;
  color: var(--text-muted);
  max-width: 680px;
  margin-inline: auto;
  line-height: 1.65;
}

/* ================= GRID ================= */

.project-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: clamp(1.5rem, 3vw, 2.25rem);
  padding-bottom: clamp(4rem, 6vw, 6rem);
}

/* ================= CARD ================= */

.project-card {
  position: relative;
  background: var(--surface);
  border-radius: var(--radius-xl);
  overflow: hidden;

  /* subtle gradient border */
  background:
    linear-gradient(#ffffff, #ffffff) padding-box,
    linear-gradient(
      145deg,
      rgba(11, 138, 21, 0.15),
      rgba(11, 138, 21, 0.03)
    ) border-box;

  border: 1px solid transparent;
  box-shadow: var(--shadow-soft);

  display: flex;
  flex-direction: column;

  transition:
    transform .4s var(--ease),
    box-shadow .4s var(--ease);
}

.project-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-hover);
}

/* ================= MEDIA ================= */

.card-media {
  position: relative;
  aspect-ratio: 16 / 9;
  background: #000;
  overflow: hidden;
}

.card-media img,
.card-media video,
.card-media iframe {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;

  transform: scale(1);
  transition: transform .6s ease;
}

/* Disable iframe interaction inside grid */
.card-media iframe {
  pointer-events: none;
}

/* Calm micro-zoom on media only */
.project-card:hover .card-media img,
.project-card:hover .card-media video {
  transform: scale(1.04);
}

/* Media overlay for depth */
.card-media::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(
    180deg,
    rgba(0,0,0,0) 40%,
    rgba(0,0,0,0.12) 100%
  );
  pointer-events: none;
}

/* ================= BADGES ================= */

.badge-featured {
  position: absolute;
  top: 12px;
  right: 12px;

  background: linear-gradient(135deg, #facc15, #fde047);
  color: #3f3f46;

  font-size: .65rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .06em;

  padding: 5px 12px;
  border-radius: 999px;
  box-shadow: 0 4px 14px rgba(0,0,0,.18);
  z-index: 2;
}

.badge-location {
  position: absolute;
  bottom: 12px;
  left: 12px;

  background: rgba(255, 255, 255, 0.95);
  color: var(--text-main);

  font-size: .7rem;
  font-weight: 700;

  padding: 6px 12px;
  border-radius: 999px;

  display: inline-flex;
  align-items: center;
  gap: 6px;

  box-shadow: 0 4px 12px rgba(0,0,0,.15);
  z-index: 2;
}

/* ================= CONTENT ================= */

.card-body {
  padding: 1.4rem 1.5rem 1.6rem;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.card-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: .6rem;
}

.status-dot {
  font-size: .6rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .06em;
  padding: 4px 12px;
  border-radius: 999px;
}

.status-completed { background: #dcfce7; color: #14532d; }
.status-ongoing   { background: #fef3c7; color: #92400e; }
.status-planned   { background: #f1f5f9; color: #475569; }

.card-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-main);
  line-height: 1.3;
  margin-bottom: .6rem;
}

.card-summary {
  font-size: .95rem;
  color: var(--text-muted);
  line-height: 1.6;
  margin-bottom: 1.2rem;

  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;

  flex-grow: 1;
}

/* ================= LINK ================= */

.card-link {
  font-size: .85rem;
  font-weight: 700;
  color: var(--primary);

  display: inline-flex;
  align-items: center;
  gap: .4rem;

  transition: color .25s ease, gap .25s ease;
}

.card-link::after {
  content: '→';
  transition: transform .25s ease;
}

.project-card:hover .card-link {
  color: var(--primary-dark);
  gap: .7rem;
}

/* ================= RESPONSIVE ================= */

@media (max-width: 768px) {
  .page-header h1 {
    font-size: 2.1rem;
  }
}

</style>

<div class="page-header">
    <div class="container">
        <h1>Our Projects</h1>
        <p>From emergency response to long-term sustainability, explore how our initiatives are transforming communities across the region.</p>
    </div>
</div>

<div class="container">
    
    <?php if (empty($projects)): ?>
        <div style="text-align: center; padding: 100px 20px; color: var(--text-muted);">
            <i class="fa-regular fa-folder-open" style="font-size: 4rem; opacity: 0.3; margin-bottom: 20px;"></i>
            <h3 style="font-size: 1.5rem; margin: 0;">No projects found yet.</h3>
            <p>Check back soon for updates.</p>
        </div>
    <?php else: ?>

        <div class="project-grid">
            <?php foreach ($projects as $p): ?>
                
                <article class="project-card">
                    
                    <div class="card-media">
                        <?php if ($p['featured']): ?>
                            <span class="badge-featured"><i class="fa-solid fa-star"></i> Featured</span>
                        <?php endif; ?>

                        <?php if ($p['cover_image'] && $p['media_type'] === 'video'): ?>
                            <video muted autoplay loop playsinline>
                                <source src="<?= $uploadPath . htmlspecialchars($p['cover_image']) ?>" type="video/mp4">
                            </video>

                        <?php elseif ($p['cover_image'] && $p['media_type'] === 'image'): ?>
                            <img src="<?= $uploadPath . htmlspecialchars($p['cover_image']) ?>" 
                                 alt="<?= htmlspecialchars($p['title']) ?>">

                        <?php elseif (!empty($p['media_url'])): ?>
                            <iframe src="<?= getEmbedUrl($p['media_url']) ?>" 
                                    frameborder="0" 
                                    allow="autoplay; encrypted-media"></iframe>

                        <?php else: ?>
                            <div style="height:100%; display:flex; align-items:center; justify-content:center; background:#f1f5f9;">
                                <i class="fa-regular fa-image" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                            </div>
                        <?php endif; ?>

                        <div class="badge-location">
                            <i class="fa-solid fa-location-dot" style="color: var(--primary);"></i>
                            <?= htmlspecialchars($p['location']) ?>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="card-meta">
                            <span class="status-dot status-<?= strtolower(str_replace(' ', '', $p['status'])) ?>">
                                <?= htmlspecialchars($p['status']) ?>
                            </span>
                            <span style="font-size:0.8rem; color:var(--text-muted);">
                                <?= date('M Y', strtotime($p['start_date'])) ?>
                            </span>
                        </div>

                        <h3 class="card-title"><?= htmlspecialchars($p['title']) ?></h3>
                        
                        <div class="card-summary">
                            <?= htmlspecialchars(mb_strimwidth($p['summary'], 0, 120, '...')) ?>
                        </div>

                        <a href="project.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="card-link">
                            Read Full Story <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                </article>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>