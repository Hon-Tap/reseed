<?php
declare(strict_types=1);

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

/*
|--------------------------------------------------------------------------
| FETCH POST BY SLUG
|--------------------------------------------------------------------------
*/
$slug = trim($_GET['slug'] ?? '');
$stmt = $pdo->prepare("
    SELECT title, slug, content, cover_media, media_type, author, published_at
    FROM posts
    WHERE slug = :slug AND published_at IS NOT NULL
    LIMIT 1
");
$stmt->execute(['slug' => $slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| NOT FOUND STATE (Polished)
|--------------------------------------------------------------------------
*/
if (!$post): ?>
<style>
    .error-state { min-height: 80vh; display: flex; align-items: center; justify-content: center; background: #f8fafc; }
    .error-card { text-align: center; max-width: 500px; padding: 3rem; background: white; border-radius: 32px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); }
    .error-icon { font-size: 5rem; color: #e2e8f0; margin-bottom: 2rem; display: inline-block; }
</style>
<main class="error-state px-4">
    <div class="error-card">
        <i class="fa-solid fa-compass-drafting error-icon"></i>
        <h1 class="fw-black text-dark mb-3" style="font-size: 2.5rem; letter-spacing: -0.04em;">Story Missing</h1>
        <p class="text-muted mb-5 fs-5">This journal entry may have been moved, renamed, or is currently being edited by our team.</p>
        <a href="/blog.php" class="btn btn-dark px-5 py-3 rounded-pill fw-bold">
            <i class="fa-solid fa-arrow-left me-2"></i> Return to Journal
        </a>
    </div>
</main>
<?php 
require_once dirname(__DIR__) . '/backend/includes/footer.php'; 
exit; 
endif;

$mediaUrl = $post['cover_media'];
$isVideo  = ($post['media_type'] ?? 'image') === 'video';
$readTime = reading_time($post['content']);
?>

<style>
    :root {
        --emerald-brand: #059669;
        --navy-dark: #0f172a;
        --slate-muted: #64748b;
        --body-font-size: 1.2rem;
    }

    /* Reading Progress Bar */
    #reading-progress {
        position: fixed;
        top: 0; left: 0;
        height: 6px;
        width: 0%;
        background: linear-gradient(90deg, var(--emerald-brand), #34d399);
        z-index: 9999;
        transition: width 0.1s ease;
    }

    .article-container { background: #fff; }

    /* Header Section */
    .article-hero {
        padding: 8rem 0 4rem;
        max-width: 900px;
        margin: 0 auto;
        text-align: center;
    }

    .category-kicker {
        color: var(--emerald-brand);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
        display: block;
    }

    .article-main-title {
        font-size: clamp(2.5rem, 7vw, 4.2rem);
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: -0.05em;
        color: var(--navy-dark);
        margin-bottom: 2.5rem;
    }

    .meta-ribbon {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1.5rem;
        color: var(--slate-muted);
        font-weight: 600;
    }

    .meta-ribbon .sep { width: 5px; height: 5px; background: #e2e8f0; border-radius: 50%; }

    /* Visual Media */
    .article-feature-media {
        max-width: 1100px;
        margin: 0 auto 5rem;
        padding: 0 1.5rem;
    }

    .media-wrapper {
        border-radius: 32px;
        overflow: hidden;
        box-shadow: 0 40px 100px -20px rgba(15, 23, 42, 0.15);
        background: #f1f5f9;
    }

    .media-wrapper img, .media-wrapper video {
        width: 100%;
        display: block;
        max-height: 75vh;
        object-fit: cover;
    }

    /* Long-form Typography */
    .article-content {
        max-width: 780px;
        margin: 0 auto;
        padding: 0 1.5rem;
        font-size: var(--body-font-size);
        line-height: 1.85;
        color: #334155;
    }

    .article-content p { margin-bottom: 2rem; }
    .article-content h2, .article-content h3 { 
        color: var(--navy-dark); 
        font-weight: 800; 
        margin: 3.5rem 0 1.5rem;
        letter-spacing: -0.02em;
    }

    /* Signature Footer */
    .article-post-footer {
        max-width: 780px;
        margin: 6rem auto 0;
        padding: 4rem 1.5rem;
        border-top: 2px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .author-info { display: flex; align-items: center; gap: 1rem; }
    .author-avatar { width: 48px; height: 48px; background: #ecfdf5; color: var(--emerald-brand); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; }

    .back-to-journal {
        text-decoration: none;
        font-weight: 700;
        color: var(--navy-dark);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: transform 0.3s ease;
    }
    .back-to-journal:hover { color: var(--emerald-brand); transform: translateX(-5px); }
</style>

<div id="reading-progress"></div>

<main class="article-container">
    <header class="article-hero px-4">
        <span class="category-kicker">Field Journal Entry</span>
        <h1 class="article-main-title"><?= htmlspecialchars($post['title']) ?></h1>
        
        <div class="meta-ribbon">
            <div class="author-tag">By <?= htmlspecialchars($post['author'] ?: 'ReSEED Team') ?></div>
            <div class="sep"></div>
            <div><?= date('F j, Y', strtotime($post['published_at'])) ?></div>
            <div class="sep"></div>
            <div><i class="fa-regular fa-clock me-1"></i> <?= $readTime ?> min read</div>
        </div>
    </header>

    <?php if ($mediaUrl): ?>
    <section class="article-feature-media">
        <div class="media-wrapper">
            <?php if ($isVideo): ?>
                <video controls playsinline poster="">
                    <source src="<?= htmlspecialchars($mediaUrl) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php else: ?>
                <img src="<?= htmlspecialchars($mediaUrl) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <article class="article-content">
        <?= $post['content'] ?>
    </article>

    <footer class="article-post-footer">
        <div class="author-info">
            <div class="author-avatar">
                <?= strtoupper(substr($post['author'] ?: 'R', 0, 1)) ?>
            </div>
            <div>
                <small class="text-muted d-block">Published by</small>
                <span class="fw-bold"><?= htmlspecialchars($post['author'] ?: 'ReSEED Editorial Team') ?></span>
            </div>
        </div>

        <a href="/blog.php" class="back-to-journal">
            <i class="fa-solid fa-arrow-left-long"></i>
            <span>All Stories</span>
        </a>
    </footer>
</main>

<script>
    // Update Reading Progress Bar
    window.onscroll = function() {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        document.getElementById("reading-progress").style.width = scrolled + "%";
    };
</script>

<?php require_once dirname(__DIR__) . '/backend/includes/footer.php'; ?>