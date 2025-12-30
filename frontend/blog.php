<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| BOOTSTRAP
|--------------------------------------------------------------------------
*/
require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function reading_time(string $html): int
{
    $words = str_word_count(strip_tags($html));
    return max(1, (int) ceil($words / 200));
}

/**
 * Cloudinary-native image helper
 */
function getBlogMediaUrl(?string $url): string
{
    return $url ?: 'https://via.placeholder.com/800x500?text=Field+Journal';
}

/*
|--------------------------------------------------------------------------
| FETCH PUBLISHED POSTS
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT
        title,
        slug,
        excerpt,
        content,
        cover_media,
        media_type,
        featured,
        author,
        published_at
    FROM posts
    WHERE published_at IS NOT NULL
    ORDER BY featured DESC, published_at DESC
");

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* =========================================================
    BLOG — REFINED EDITORIAL UI
========================================================= */
:root {
    --green: #10b981;
    --green-dark: #059669;
    --ink: #0f172a;
    --muted: #64748b;
    --radius: 24px;
    --shadow-sm: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}
.blog-hero { padding: 6rem 0; background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%); text-align: center; }
.blog-hero h1 { font-size: clamp(2.8rem, 7vw, 4.5rem); font-weight: 900; letter-spacing: -.05em; margin-bottom: 1.2rem; color: var(--ink); }
.blog-hero p { font-size: 1.25rem; color: var(--muted); max-width: 600px; margin: 0 auto; font-weight: 500; }
.blog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 4rem 2.5rem; padding: 4rem 0 8rem; }
.blog-card { text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%; transition: all .4s cubic-bezier(0.2,1,0.3,1); }
.blog-card:hover { transform: translateY(-12px); }
.blog-media { position: relative; aspect-ratio: 16 / 10; border-radius: var(--radius); overflow: hidden; background: #f1f5f9; margin-bottom: 1.8rem; box-shadow: var(--shadow-sm); }
.blog-media img, .blog-media video { width: 100%; height: 100%; object-fit: cover; transition: transform .8s ease; }
.blog-card:hover img { transform: scale(1.08); }
.badge-featured { position: absolute; top: 1.2rem; left: 1.2rem; background: rgba(255,255,255,.95); padding: .5rem 1rem; border-radius: 99px; font-size: .7rem; font-weight: 800; text-transform: uppercase; color: var(--green-dark); }
.blog-meta { font-size: .75rem; font-weight: 700; text-transform: uppercase; color: var(--green-dark); display: flex; gap: .75rem; margin-bottom: .8rem; }
.blog-meta span { color: var(--muted); font-weight: 600; }
.blog-title { font-size: 1.6rem; font-weight: 800; margin-bottom: 1rem; color: var(--ink); }
.blog-excerpt { font-size: 1.05rem; color: var(--muted); line-height: 1.6; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.blog-cta { margin-top: auto; font-weight: 800; font-size: .95rem; color: var(--green-dark); display: inline-flex; gap: .5rem; align-items: center; }
</style>

<main>

<section class="blog-hero">
    <div class="container mx-auto px-4">
        <h1>Field Journal</h1>
        <p>Stories, updates, and ecological insights from our restoration work.</p>
    </div>
</section>

<section class="container mx-auto px-4">

<?php if (!$posts): ?>

    <div class="py-32 text-center bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200">
        <h3 class="text-xl font-bold text-slate-900">The journal is empty.</h3>
        <p class="text-slate-500">Check back soon for new stories.</p>
    </div>

<?php else: ?>

<div class="blog-grid">

<?php foreach ($posts as $post):

    $mediaUrl = getBlogMediaUrl($post['cover_media']);
    $url      = '/post.php?slug=' . urlencode($post['slug']);
    $read     = reading_time($post['content']);
?>

<article>
<a href="<?= $url ?>" class="blog-card">

<div class="blog-media">
<?php if ($post['featured']): ?>
    <span class="badge-featured">Featured</span>
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
    <?= htmlspecialchars($post['author'] ?: 'Team ReSEED') ?>
    <span>•</span>
    <span><?= date('M j, Y', strtotime($post['published_at'])) ?></span>
    <span>•</span>
    <span><?= $read ?> min read</span>
</div>

<h2 class="blog-title"><?= htmlspecialchars($post['title']) ?></h2>

<p class="blog-excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>

<span class="blog-cta">Read Story →</span>

</a>
</article>

<?php endforeach; ?>

</div>

<?php endif; ?>

</section>

</main>

<?php require_once dirname(__DIR__) . '/backend/includes/footer.php'; ?>
