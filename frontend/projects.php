<?php
/**
 * Frontend Projects Page - Cloudinary Enabled
 */

require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';

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
  --primary: #10b981; /* Matches your Emerald-600 admin theme */
  --primary-dark: #059669;
  --surface: #ffffff;
  --surface-soft: #f8fafc;
  --text-main: #0f172a;
  --text-muted: #64748b;
  --radius-xl: 24px;
  --shadow-soft: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
  --shadow-hover: 0 20px 40px -15px rgba(16, 185, 129, 0.15);
  --ease: cubic-bezier(.22, 1, .36, 1);
}

.page-header {
    background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
    padding-block: 5rem;
    text-align: center;
}

.page-header h1 {
    font-size: clamp(2.5rem, 5vw, 3.5rem);
    font-weight: 900;
    color: var(--text-main);
    letter-spacing: -0.025em;
    margin-bottom: 1rem;
}

.project-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 2.5rem;
    padding-bottom: 6rem;
}

.project-card {
    background: var(--surface);
    border-radius: var(--radius-xl);
    overflow: hidden;
    border: 1px solid #f1f5f9;
    box-shadow: var(--shadow-soft);
    display: flex;
    flex-direction: column;
    transition: all .4s var(--ease);
}

.project-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
    border-color: #d1fae5;
}

.card-media {
    position: relative;
    aspect-ratio: 16 / 10;
    background: #f1f5f9;
    overflow: hidden;
}

.card-media img, .card-media video, .card-media iframe {
    width: 100%; height: 100%; object-fit: cover; display: block;
}

.badge-featured {
    position: absolute; top: 1rem; right: 1rem;
    background: #fbbf24;
    color: #78350f; font-size: 0.7rem; font-weight: 800;
    padding: 6px 14px; border-radius: 99px; z-index: 2;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
}

.badge-location {
    position: absolute; bottom: 1rem; left: 1rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(8px);
    padding: 6px 14px; border-radius: 99px; font-size: 0.75rem; 
    font-weight: 700; color: #1e293b; z-index: 2;
}

.card-body { padding: 2rem; flex-grow: 1; display: flex; flex-direction: column; }

.status-badge {
    font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
    padding: 5px 12px; border-radius: 99px; display: inline-block;
    letter-spacing: 0.05em;
}

.status-completed { background: #ecfdf5; color: #065f46; }
.status-ongoing { background: #eff6ff; color: #1e40af; }
.status-planned { background: #fffbeb; color: #92400e; }

.card-title { 
    font-size: 1.5rem; font-weight: 800; margin: 0.75rem 0; 
    color: var(--text-main); line-height: 1.2;
}
.card-summary { color: var(--text-muted); font-size: 1rem; line-height: 1.6; margin-bottom: 2rem; flex-grow: 1; }

.card-link { 
    color: var(--primary); font-weight: 800; text-decoration: none; 
    display: inline-flex; align-items: center; gap: 8px; 
    transition: gap 0.2s;
}
.card-link:hover { color: var(--primary-dark); gap: 12px; }
</style>

<div class="page-header">
    <div class="container">
        <h1>Our Initiatives</h1>
        <p class="text-slate-500 max-w-xl mx-auto font-medium">Discover how we are driving sustainable growth and community development through our active projects.</p>
    </div>
</div>

<div class="container mx-auto px-4">
    <?php if (empty($projects)): ?>
        <div class="text-center py-32 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
            <i class="fa-solid fa-folder-open text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-xl font-bold text-slate-900">No projects currently listed.</h3>
            <p class="text-slate-500">Check back soon for updates.</p>
        </div>
    <?php else: ?>
        <div class="project-grid">
            <?php foreach ($projects as $p): 
                $statusClass = 'status-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $p['status'] ?? 'planned'));
                
                // Cloudinary URL Logic: If it starts with http, use it directly.
                $imageSrc = (strpos($p['cover_image'] ?? '', 'http') === 0) 
                            ? $p['cover_image'] 
                            : 'https://via.placeholder.com/800x600?text=No+Image';
            ?>
                <article class="project-card">
                    <div class="card-media">
                        <?php if ($p['featured']): ?>
                            <span class="badge-featured">FEATURED</span>
                        <?php endif; ?>

                        <?php if ($p['media_type'] === 'video' && !empty($p['cover_image'])): ?>
                            <video muted autoplay loop playsinline>
                                <source src="<?= htmlspecialchars($imageSrc) ?>" type="video/mp4">
                            </video>
                        <?php elseif ($p['media_type'] === 'image' && !empty($p['cover_image'])): ?>
                            <img src="<?= htmlspecialchars($imageSrc) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
                        <?php elseif (!empty($p['media_url'])): ?>
                            <iframe src="<?= getEmbedUrl($p['media_url']) ?>" frameborder="0" allow="autoplay; encrypted-media"></iframe>
                        <?php else: ?>
                            <div class="flex h-full items-center justify-center bg-slate-100 text-slate-400">
                                <i class="fa-solid fa-image text-4xl"></i>
                            </div>
                        <?php endif; ?>

                        <div class="badge-location">
                            <i class="fa-solid fa-location-dot mr-1"></i> <?= htmlspecialchars($p['location'] ?? 'Global') ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($p['status'] ?? 'Planned') ?></span>
                            <span class="text-xs font-bold text-slate-400"><?= date('M Y', strtotime($p['start_date'])) ?></span>
                        </div>

                        <h3 class="card-title"><?= htmlspecialchars($p['title']) ?></h3>
                        <p class="card-summary"><?= htmlspecialchars(mb_strimwidth($p['summary'], 0, 110, '...')) ?></p>

                        <a href="project.php?slug=<?= urlencode($p['slug']) ?>" class="card-link">
                            <span>View details</span>
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/backend/includes/footer.php'; ?>