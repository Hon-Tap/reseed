<?php
// projects.php — ReSEED Projects (PRO UI EDITION)

// Always load config FIRST (one level up from /frontend)
require_once __DIR__ . '/../includes/config.php';



$slug = $_GET['slug'] ?? null;
if (!$slug) { header("Location: projects.php"); exit; }

// 1. Fetch the main project
try {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Project Detail Error: " . $e->getMessage());
    $project = false;
}

if (!$project) { header("Location: projects.php?not_found=1"); exit; }

// 2. Fetch related projects (Exclude current, limit 3)
try {
    $related_stmt = $pdo->prepare("
        SELECT title, slug, cover_image, location, status, media_type, media_url 
        FROM projects 
        WHERE id != ? 
        ORDER BY created_at DESC LIMIT 3
    ");
    $related_stmt->execute([$project['id']]);
    $related_projects = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $related_projects = [];
}

include __DIR__ . '/includes/header.php'; 
$uploadPath = 'uploads/projects/'; 

// Helper to convert YouTube/Vimeo links to Embed URL automatically
function getEmbedUrl($url) {
    $url = htmlspecialchars_decode($url); // Decode first
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        $url = preg_replace('/watch\?v=([a-zA-Z0-9_-]+)/', 'embed/$1', $url);
        $url = preg_replace('/youtu.be\/([a-zA-Z0-9_-]+)/', 'www.youtube.com/embed/$1', $url);
    } elseif (strpos($url, 'vimeo.com') !== false) {
        $videoId = (int) substr(parse_url($url, PHP_URL_PATH), 1);
        $url = "https://player.vimeo.com/video/" . $videoId;
    }
    return htmlspecialchars($url);
}
?>

<style>
    /* =========================================================
       PROJECT DETAIL CSS — Immersive & Editorial
    ========================================================= */

    :root {
        --primary: #0b8a15;
        --primary-dark: #086b11;
        --surface: #ffffff;
        --surface-soft: #f4faf7;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --radius-xl: 24px;
        --radius-lg: 16px;
        --shadow-soft: 0 8px 24px rgba(0, 0, 0, 0.05);
        --shadow-float: 0 20px 40px rgba(0, 0, 0, 0.08);
        --ease: cubic-bezier(.22, 1, .36, 1);
    }

    /* --- Layout Structure --- */
    .detail-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .project-hero {
        padding: 60px 0 40px;
        text-align: center;
    }

    .project-hero h1 {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        line-height: 1.1;
        margin-bottom: 20px;
    }

    /* --- Media Player (The Cinema Box) --- */
    .media-stage {
        width: 100%;
        background: #000;
        border-radius: var(--radius-xl);
        overflow: hidden;
        box-shadow: var(--shadow-float);
        margin-bottom: 60px;
        position: relative;
        aspect-ratio: 16 / 9; 
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .media-stage img, 
    .media-stage video {
        width: 100%;
        height: 100%;
        object-fit: cover; 
    }

    .media-stage iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* --- Split Content Layout --- */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 60px;
        align-items: start;
        padding-bottom: 80px;
    }

    /* Left Column: Text */
    .main-text .lead-summary {
        font-size: 1.25rem;
        line-height: 1.6;
        color: var(--text-main);
        font-weight: 500;
        margin-bottom: 40px;
        padding-left: 20px;
        border-left: 4px solid var(--primary);
    }

    .rich-text {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #334155;
    }
    
    .rich-text p { margin-bottom: 24px; }
    .rich-text h2 { font-size: 1.8rem; margin: 40px 0 20px; color: var(--text-main); }
    .rich-text ul { margin-bottom: 24px; padding-left: 20px; }

    /* Right Column: Sticky Sidebar */
    .sticky-sidebar {
        position: sticky;
        top: 30px; 
        background: var(--surface);
        border: 1px solid #e2e8f0;
        padding: 30px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
    }

    .info-group { margin-bottom: 25px; }
    
    .info-label {
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 8px;
        font-weight: 700;
    }

    .info-value {
        font-size: 1.1rem;
        color: var(--text-main);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Status Pills */
    .status-pill {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-pill.completed { background: #dcfce7; color: #14532d; }
    .status-pill.ongoing { background: #fef3c7; color: #92400e; }
    .status-pill.planned { background: #f1f5f9; color: #475569; }

    /* --- Related Section (Matching Projects.php Cards) --- */
    .related-section {
        background: var(--surface-soft);
        padding: 80px 0;
        border-top: 1px solid #e2e8f0;
    }
    
    .related-card {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s var(--ease), box-shadow 0.3s var(--ease);
        display: flex;
        flex-direction: column;
        height: 100%;
        text-decoration: none;
    }

    .related-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .related-media {
        height: 200px;
        background: #eee;
        position: relative;
        overflow: hidden;
    }

    .related-media img, 
    .related-media video,
    .related-media iframe {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .related-card:hover .related-media img {
        transform: scale(1.05);
    }

    /* Location Badge on Card */
    .location-badge {
        position: absolute;
        bottom: 12px;
        left: 12px;
        background: rgba(255, 255, 255, 0.95);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        color: #333;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        z-index: 2;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .content-grid { grid-template-columns: 1fr; gap: 40px; }
        .sticky-sidebar { position: static; }
        .media-stage { border-radius: 12px; margin-bottom: 40px; }
        .project-hero h1 { font-size: 2rem; }
    }
</style>

<div class="detail-container">
    
    <header class="project-hero">
        <div style="margin-bottom: 20px;">
            <a href="projects.php" style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-decoration: none;">
                <i class="fa-solid fa-arrow-left"></i> &nbsp;Back to Projects
            </a>
        </div>
        
        <h1><?= htmlspecialchars($project['title']) ?></h1>
        
        <div style="display: flex; justify-content: center; gap: 15px; margin-top: 15px;">
            <span class="status-pill <?= strtolower(str_replace(' ', '', $project['status'])) ?>">
                <?= htmlspecialchars($project['status']) ?>
            </span>
        </div>
    </header>

    <div class="media-stage">
        <?php if ($project['cover_image']): ?>
            <?php if ($project['media_type'] == 'video'): ?>
                <video controls controlsList="nodownload" playsinline autoplay muted>
                    <source src="<?= $uploadPath . htmlspecialchars($project['cover_image']) ?>" type="video/mp4">
                    Your browser does not support HTML5 video.
                </video>
            <?php else: ?>
                <img src="<?= $uploadPath . htmlspecialchars($project['cover_image']) ?>" 
                     alt="<?= htmlspecialchars($project['title']) ?>">
            <?php endif; ?>

        <?php elseif ($project['media_url']): ?>
            <iframe 
                src="<?= getEmbedUrl($project['media_url']) ?>" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
            </iframe>
        
        <?php else: ?>
            <div style="color: #666; display: flex; flex-direction: column; align-items: center;">
                <i class="fa-regular fa-image" style="font-size: 3rem; margin-bottom: 10px;"></i>
                <span>No Media Available</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="content-grid">
        
        <article class="main-text">
            <div class="lead-summary">
                <?= htmlspecialchars($project['summary']) ?>
            </div>

            <div class="rich-text">
                <?= nl2br(htmlspecialchars($project['description'])) ?>
            </div>
        </article>

        <aside class="sticky-sidebar">
            <h3 style="margin-top:0; margin-bottom:20px; font-size:1.2rem; color:var(--text-main);">Project Details</h3>
            
            <div class="info-group">
                <span class="info-label">Location</span>
                <div class="info-value">
                    <i class="fa-solid fa-map-pin" style="color:var(--primary);"></i>
                    <?= htmlspecialchars($project['location']) ?>
                </div>
            </div>

            <div class="info-group">
                <span class="info-label">Start Date</span>
                <div class="info-value">
                    <i class="fa-regular fa-calendar" style="color:var(--primary);"></i>
                    <?= date('F j, Y', strtotime($project['start_date'])) ?>
                </div>
            </div>

            <?php if ($project['end_date']): ?>
            <div class="info-group">
                <span class="info-label">Completion</span>
                <div class="info-value">
                    <i class="fa-solid fa-flag-checkered" style="color:var(--text-muted);"></i>
                    <?= date('F j, Y', strtotime($project['end_date'])) ?>
                </div>
            </div>
            <?php endif; ?>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed #cbd5e1;">
                <span class="info-label">Share Project</span>
                <div style="display: flex; gap: 10px; font-size: 1.2rem;">
                    <a href="#" style="color: #1877f2;"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" style="color: #1da1f2;"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" style="color: #0a66c2;"><i class="fa-brands fa-linkedin"></i></a>
                </div>
            </div>
        </aside>

    </div>
</div>

<?php if (!empty($related_projects)): ?>
<div class="related-section">
    <div class="detail-container">
        <div class="related-header">
            <h2 style="font-size: 2rem; color: var(--text-main); font-weight: 800;">More Projects</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <?php foreach ($related_projects as $rp): ?>
                <a href="project.php?slug=<?= htmlspecialchars($rp['slug']) ?>" class="related-card">
                    
                    <div class="related-media">
                        
                        <?php if ($rp['cover_image'] && $rp['media_type'] === 'video'): ?>
                            <video muted autoplay loop playsinline style="width:100%; height:100%; object-fit:cover;">
                                <source src="<?= $uploadPath . htmlspecialchars($rp['cover_image']) ?>" type="video/mp4">
                            </video>

                        <?php elseif ($rp['cover_image'] && $rp['media_type'] === 'image'): ?>
                            <img src="<?= $uploadPath . htmlspecialchars($rp['cover_image']) ?>" 
                                 alt="<?= htmlspecialchars($rp['title']) ?>">

                        <?php elseif (!empty($rp['media_url'])): ?>
                             <iframe src="<?= getEmbedUrl($rp['media_url']) ?>?background=1&autoplay=1&loop=1&byline=0&title=0&muted=1" 
                                     style="pointer-events:none;" 
                                     frameborder="0"></iframe>
                        
                        <?php else: ?>
                            <div style="height:100%; display:flex; align-items:center; justify-content:center;">
                                <i class="fa-regular fa-image" style="font-size: 2rem; color: #ccc;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="location-badge">
                            <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($rp['location']) ?>
                        </div>
                    </div>

                    <div style="padding: 20px; display:flex; flex-direction:column; flex-grow:1;">
                        <h4 style="margin: 0 0 10px; color: var(--text-main); font-size: 1.15rem; font-weight:700;">
                            <?= htmlspecialchars($rp['title']) ?>
                        </h4>
                        <div style="margin-top:auto;">
                            <span style="font-size: 0.9rem; color: var(--primary); font-weight: 700;">Read More &rarr;</span>
                        </div>
                    </div>

                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>