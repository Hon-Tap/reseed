<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| BLOG — RECHARTED UI
|--------------------------------------------------------------------------
*/
require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
function reading_time(string $html): int {
    $words = str_word_count(strip_tags($html));
    return max(1, (int) ceil($words / 200));
}

function getBlogMediaUrl(?string $url): string {
    return $url ?: 'https://via.placeholder.com/800x500?text=Field+Journal';
}

/*
|--------------------------------------------------------------------------
| FETCH POSTS
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT title, slug, excerpt, content, cover_media, media_type, featured, author, published_at
    FROM posts
    WHERE published_at IS NOT NULL
    ORDER BY featured DESC, published_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    :root {
        --emerald-primary: #059669;
        --navy-ink: #0f172a;
        --slate-text: #64748b;
        --bg-soft: #f8fafc;
        --radius-lg: 24px;
        --transition-main: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .blog-wrapper {
        background-color: var(--bg-soft);
        min-height: 100vh;
    }

    /* Hero Section */
    .blog-hero {
        padding: 8rem 0 5rem;
        background: radial-gradient(circle at top left, #ffffff 0%, #f1f5f9 100%);
        border-bottom: 1px solid rgba(0,0,0,0.05);
        text-align: center;
    }

    .blog-hero .badge-top {
        background: #dcfce7;
        color: var(--emerald-primary);
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 1.5rem;
        display: inline-block;
    }

    .blog-hero h1 {
        font-size: clamp(2.5rem, 6vw, 4rem);
        font-weight: 900;
        letter-spacing: -0.04em;
        color: var(--navy-ink);
        margin-bottom: 1rem;
    }

    /* Grid & Cards */
    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 3.5rem 2.5rem;
        padding-bottom: 8rem;
    }

    .blog-card {
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: var(--transition-main);
    }

    .blog-media-wrap {
        position: relative;
        aspect-ratio: 16 / 10;
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin-bottom: 1.8rem;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08);
        background: #e2e8f0;
    }

    .blog-media-wrap img, 
    .blog-media-wrap video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1.2s ease;
    }

    .blog-card:hover {
        transform: translateY(-12px);
    }

    .blog-card:hover .blog-media-wrap img {
        transform: scale(1.08);
    }

    /* Glass Badge Overlay */
    .badge-featured-glass {
        position: absolute;
        top: 1.25rem;
        left: 1.25rem;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--navy-ink);
        z-index: 2;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* Content Styling */
    .blog-meta {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--emerald-primary);
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 0.75rem;
    }

    .blog-meta span.separator {
        width: 4px;
        height: 4px;
        background: #cbd5e1;
        border-radius: 50%;
    }

    .blog-meta .meta-text {
        color: var(--slate-text);
        font-weight: 600;
    }

    .blog-title {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.3;
        color: var(--navy-ink);
        margin-bottom: 1rem;
        transition: color 0.3s ease;
    }

    .blog-card:hover .blog-title {
        color: var(--emerald-primary);
    }

    .blog-excerpt {
        font-size: 1rem;
        color: var(--slate-text);
        line-height: 1.6;
        margin-bottom: 1.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .blog-cta {
        margin-top: auto;
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--navy-ink);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: gap 0.3s ease;
    }

    .blog-cta i {
        color: var(--emerald-primary);
        font-size: 1rem;
    }

    .blog-card:hover .blog-cta {
        gap: 0.8rem;
    }

    /* Empty State */
    .empty-journal {
        padding: 6rem 2rem;
        background: white;
        border-radius: 32px;
        border: 2px dashed #e2e8f0;
        text-align: center;
    }

    @media (max-width: 768px) {
        .blog-grid { grid-template-columns: 1fr; gap: 3rem; }
    }
</style>

<div class="blog-wrapper">
    <header class="blog-hero">
        <div class="container px-4">
            <span class="badge-top">Field Journal</span>
            <h1>Stories & Insights</h1>
            <p class="mx-auto text-secondary">Ecological updates and community stories from the heart of our restoration work.</p>
        </div>
    </header>

    <div class="container px-4 mt-5">
        <?php if (!$posts): ?>
            <div class="empty-journal">
                <i class="fa-solid fa-feather-pointed mb-4 opacity-20" style="font-size: 4rem;"></i>
                <h3 class="fw-bold text-dark">The journal is currently quiet.</h3>
                <p class="text-muted">Check back soon for new stories from the field.</p>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($posts as $post): 
                    $mediaUrl = getBlogMediaUrl($post['cover_media']);
                    $url = '/post.php?slug=' . urlencode($post['slug']);
                    $read = reading_time($post['content']);
                ?>
                    <article>
                        <a href="<?= $url ?>" class="blog-card">
                            <div class="blog-media-wrap">
                                <?php if ($post['featured']): ?>
                                    <span class="badge-featured-glass">
                                        <i class="fa-solid fa-crown me-1"></i> Featured
                                    </span>
                                <?php endif; ?>

                                <?php if (($post['media_type'] ?? 'image') === 'video'): ?>
                                    <video muted autoplay loop playsinline>
                                        <source src="<?= htmlspecialchars($mediaUrl) ?>" type="video/mp4">
                                    </video>
                                <?php else: ?>
                                    <img src="<?= htmlspecialchars($mediaUrl) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                                <?php endif; ?>
                            </div>

                            <div class="blog-meta">
                                <span><?= htmlspecialchars($post['author'] ?: 'ReSEED Team') ?></span>
                                <span class="separator"></span>
                                <span class="meta-text"><?= date('M j, Y', strtotime($post['published_at'])) ?></span>
                                <span class="separator"></span>
                                <span class="meta-text"><?= $read ?> min read</span>
                            </div>

                            <h2 class="blog-title"><?= htmlspecialchars($post['title']) ?></h2>

                            <p class="blog-excerpt">
                                <?= htmlspecialchars($post['excerpt']) ?>
                            </p>

                            <span class="blog-cta">
                                Read Full Story <i class="fa-solid fa-arrow-right-long"></i>
                            </span>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/backend/includes/footer.php'; ?>