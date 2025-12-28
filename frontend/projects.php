<?php
/**
 * Frontend Projects Page
 */

require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';

// Configuration
$uploadPath = 'uploads/projects/';

/**
 * Safely transforms YouTube/Vimeo links into embeddable URLs
 */
function getEmbedUrl($url) {
    if (empty($url)) return '';
    $url = htmlspecialchars_decode($url);

    // YouTube Logic
    if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $matches)) {
        $videoId = $matches[1];
        return "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&controls=0&loop=1&playlist={$videoId}";
    }

    // Vimeo Logic
    $path = parse_url($url, PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    $videoId = end($segments); 
    if (strpos($url, 'vimeo.com') !== false && is_numeric($videoId)) {
        return "https://player.vimeo.com/video/{$videoId}?background=1&autoplay=1&loop=1&byline=0&title=0";
    }

    return htmlspecialchars($url);
}

// Fetch projects
try {
    $stmt = $pdo->query(
        "SELECT id, title, slug, summary, cover_image, media_type, media_url, 
               status, location, featured, start_date 
        FROM projects 
        ORDER BY featured DESC, created_at DESC"
    );
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Projects fetch error: ' . $e->getMessage());
    $projects = [];
}
?>

<style>
:root {
  --primary: #0b8a15;
  --primary-dark: #086b11;
  --surface: #ffffff;
  --surface-soft: #f4faf7;
  --text-main: #0f172a;
  --text-muted: #64748b;
  --radius-xl: 20px;
  --shadow-soft: 0 8px 24px rgba(0, 0, 0, 0.05);
  --shadow-hover: 0 18px 48px rgba(0, 0, 0, 0.10);
  --ease: cubic-bezier(.22, 1, .36, 1);
}

.page-header {
    background: linear-gradient(180deg, var(--surface-soft) 0%, #ffffff 100%);
    padding-block: clamp(3.5rem, 7vw, 5.5rem);
    text-align: center;
}

.page-header h1 {
    font-size: clamp(2.2rem, 4vw, 3rem);
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 1rem;
}

.project-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    padding-bottom: 5rem;
}

.project-card {
    position: relative;
    background: var(--surface);
    border-radius: var(--radius-xl);
    overflow: hidden;
    border: 1px solid rgba(11, 138, 21, 0.1);
    box-shadow: var(--shadow-soft);
    display: flex;
    flex-direction: column;
    transition: transform .4s var(--ease), box-shadow .4s var(--ease);
}

.project-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.card-media {
    position: relative;
    aspect-ratio: 16 / 9;
    background: #000;
    overflow: hidden;
}

.card-media img, .card-media video, .card-media iframe {
    width: 100%; height: 100%; object-fit: cover; display: block;
}

.card-media iframe { pointer-events: none; }

.badge-featured {
    position: absolute; top: 12px; right: 12px;
    background: linear-gradient(135deg, #facc15, #fde047);
    color: #3f3f46; font-size: .65rem; font-weight: 800;
    padding: 5px 12px; border-radius: 999px; z-index: 2;
}

.badge-location {
    position: absolute; bottom: 12px; left: 12px;
    background: rgba(255, 255, 255, 0.95);
    padding: 6px 12px; border-radius: 999px; font-size: .7rem; font-weight: 700; z-index: 2;
}

.card-body { padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column; }

.status-dot {
    font-size: .65rem; font-weight: 800; text-transform: uppercase;
    padding: 4px 10px; border-radius: 999px; display: inline-block;
}

/* Status Colors (Standardized) */
.status-completed { background: #dcfce7; color: #14532d; }
.status-ongoing { background: #fef3c7; color: #92400e; }
.status-planned { background: #f1f5f9; color: #475569; }

.card-title { font-size: 1.25rem; margin: 0.5rem 0; color: var(--text-main); }
.card-summary { color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem; flex-grow: 1; }

.card-link { color: var(--primary); font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 5px; }
.card-link:hover { color: var(--primary-dark); }
</style>

<div class="page-header">
    <div class="container">
        <h1>Our Projects</h1>
        <p>Explore how our initiatives are transforming communities.</p>
    </div>
</div>

<div class="container">
    <?php if (empty($projects)): ?>
        <div style="text-align: center; padding: 100px 0;">
            <h3>No projects found.</h3>
        </div>
    <?php else: ?>
        <div class="project-grid">
            <?php foreach ($projects as $p): 
                $statusClass = 'status-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $p['status']));
            ?>
                <article class="project-card">
                    <div class="card-media">
                        <?php if ($p['featured']): ?>
                            <span class="badge-featured">FEATURED</span>
                        <?php endif; ?>

                        <?php if ($p['media_type'] === 'video' && !empty($p['cover_image'])): ?>
                            <video muted autoplay loop playsinline>
                                <source src="<?= $uploadPath . htmlspecialchars($p['cover_image']) ?>" type="video/mp4">
                            </video>
                        <?php elseif ($p['media_type'] === 'image' && !empty($p['cover_image'])): ?>
                            <img src="<?= $uploadPath . htmlspecialchars($p['cover_image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                        <?php elseif (!empty($p['media_url'])): ?>
                            <iframe src="<?= getEmbedUrl($p['media_url']) ?>" frameborder="0" allow="autoplay; encrypted-media"></iframe>
                        <?php else: ?>
                            <div style="height:100%; background:#eee; display:flex; align-items:center; justify-content:center;">No Media</div>
                        <?php endif; ?>

                        <div class="badge-location">
                            <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($p['location']) ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="card-meta">
                            <span class="status-dot <?= $statusClass ?>"><?= htmlspecialchars($p['status']) ?></span>
                            <span style="float:right; font-size:0.8rem;"><?= date('M Y', strtotime($p['start_date'])) ?></span>
                        </div>

                        <h3 class="card-title"><?= htmlspecialchars($p['title']) ?></h3>
                        <p class="card-summary"><?= htmlspecialchars(mb_strimwidth($p['summary'], 0, 100, '...')) ?></p>

                        <a href="project.php?slug=<?= urlencode($p['slug']) ?>" class="card-link">
                            Read Full Story &rarr;
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/backend/includes/footer.php'; ?>