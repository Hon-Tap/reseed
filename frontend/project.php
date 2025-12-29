<?php
/**
 * Project Detail Page - Cloudinary Enabled
 */

require_once __DIR__ . '/../backend/includes/config.php';

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

// 2. Fetch related projects
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

/**
 * Helper to ensure we use the Cloudinary URL or a placeholder
 */
function getImageUrl($path) {
    if (empty($path)) return 'https://via.placeholder.com/1200x800?text=No+Image+Available';
    return (strpos($path, 'http') === 0) ? $path : $path; // In your new setup, it's always the full URL
}

/**
 * Helper to convert YouTube/Vimeo links to Embed URL automatically
 */
function getEmbedUrl($url) {
    $url = htmlspecialchars_decode($url);
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        $url = preg_replace('/watch\?v=([a-zA-Z0-9_-]+)/', 'embed/$1', $url);
        $url = preg_replace('/youtu.be\/([a-zA-Z0-9_-]+)/', 'www.youtube.com/embed/$1', $url);
    } elseif (strpos($url, 'vimeo.com') !== false) {
        $segments = explode('/', rtrim(parse_url($url, PHP_URL_PATH), '/'));
        $videoId = end($segments);
        $url = "https://player.vimeo.com/video/" . $videoId;
    }
    return htmlspecialchars($url);
}
?>

<style>
    :root {
        --primary: #10b981;
        --primary-dark: #059669;
        --surface-soft: #f8fafc;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --radius-xl: 32px;
        --radius-lg: 16px;
        --shadow-float: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    }

    .project-hero {
        padding: 80px 0 40px;
        text-align: center;
    }

    .project-hero h1 {
        font-size: clamp(2.5rem, 6vw, 4rem);
        font-weight: 900;
        color: var(--text-main);
        letter-spacing: -0.04em;
        line-height: 1.1;
        margin: 20px 0;
    }

    .media-stage {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto 80px;
        background: #f1f5f9;
        border-radius: var(--radius-xl);
        overflow: hidden;
        box-shadow: var(--shadow-float);
        aspect-ratio: 16 / 9;
        position: relative;
    }

    .media-stage img, .media-stage video, .media-stage iframe {
        width: 100%; height: 100%; object-fit: cover; border: none;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 80px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px 100px;
    }

    .lead-summary {
        font-size: 1.5rem;
        line-height: 1.5;
        color: var(--text-main);
        font-weight: 600;
        margin-bottom: 40px;
        color: #1e293b;
    }

    .rich-text {
        font-size: 1.15rem;
        line-height: 1.8;
        color: #475569;
    }

    .sticky-sidebar {
        position: sticky;
        top: 40px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        padding: 40px;
        border-radius: var(--radius-xl);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
    }

    .status-pill {
        padding: 6px 16px;
        border-radius: 99px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-completed { background: #dcfce7; color: #065f46; }
    .status-ongoing { background: #eff6ff; color: #1e40af; }
    .status-planned { background: #fffbeb; color: #92400e; }

    .info-item { margin-bottom: 30px; }
    .info-label { display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; }
    .info-value { font-size: 1.1rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px; }

    @media (max-width: 992px) {
        .content-grid { grid-template-columns: 1fr; gap: 40px; }
        .sticky-sidebar { position: static; }
    }
</style>

<div class="container">
    <header class="project-hero">
        <a href="projects.php" class="inline-flex items-center text-emerald-600 font-bold no-underline hover:text-emerald-700">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back to Projects
        </a>
        <h1><?= htmlspecialchars($project['title']) ?></h1>
        <span class="status-pill status-<?= strtolower(preg_replace('/[^a-z]/', '', $project['status'])) ?>">
            <?= htmlspecialchars($project['status']) ?>
        </span>
    </header>

    <div class="media-stage">
        <?php 
        $mainImage = getImageUrl($project['cover_image']);
        if ($project['cover_image']): 
        ?>
            <?php if ($project['media_type'] == 'video'): ?>
                <video controls playsinline autoplay muted class="w-full h-full">
                    <source src="<?= htmlspecialchars($mainImage) ?>" type="video/mp4">
                </video>
            <?php else: ?>
                <img src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($project['title']) ?>">
            <?php endif; ?>
        <?php elseif ($project['media_url']): ?>
            <iframe src="<?= getEmbedUrl($project['media_url']) ?>" allowfullscreen></iframe>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center h-full text-slate-300">
                <i class="fa-regular fa-image text-6xl mb-4"></i>
                <span class="font-bold">No Media Available</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="content-grid">
        <div class="main-column">
            <div class="lead-summary">
                <?= htmlspecialchars($project['summary']) ?>
            </div>
            <div class="rich-text">
                <?= nl2br(htmlspecialchars($project['description'])) ?>
            </div>
        </div>

        <aside class="sidebar-column">
            <div class="sticky-sidebar">
                <div class="info-item">
                    <span class="info-label">Location</span>
                    <div class="info-value">
                        <i class="fa-solid fa-location-dot text-emerald-500"></i>
                        <?= htmlspecialchars($project['location']) ?>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">Timeline</span>
                    <div class="info-value">
                        <i class="fa-regular fa-calendar-check text-emerald-500"></i>
                        <?= date('M Y', strtotime($project['start_date'])) ?> 
                        <?= $project['end_date'] ? ' — ' . date('M Y', strtotime($project['end_date'])) : ' (Ongoing)' ?>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <span class="info-label">Share this impact</span>
                    <div class="flex gap-4 text-2xl mt-2">
                        <a href="#" class="text-blue-600"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="text-sky-400"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="text-blue-700"><i class="fa-brands fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<?php if (!empty($related_projects)): ?>
<section class="bg-slate-50 py-20 border-t border-slate-200">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-black text-slate-900 mb-10">Related Projects</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($related_projects as $rp): 
                $rpImg = getImageUrl($rp['cover_image']);
            ?>
                <a href="project.php?slug=<?= htmlspecialchars($rp['slug']) ?>" class="group no-underline">
                    <div class="aspect-video rounded-2xl overflow-hidden shadow-sm mb-4 bg-white">
                        <img src="<?= htmlspecialchars($rpImg) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">
                        <?= htmlspecialchars($rp['title']) ?>
                    </h4>
                    <p class="text-sm text-slate-500"><?= htmlspecialchars($rp['location']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>