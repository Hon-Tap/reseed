<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/includes/config.php';
require_once __DIR__ . '/backend/includes/header.php';

$limit  = 15; // Increased slightly for better masonry flow
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

try {
    $totalImages = (int) $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
    $totalPages  = max(1, (int) ceil($totalImages / $limit));

    $stmt = $pdo->prepare("
        SELECT filename, caption, category, created_at
        FROM gallery
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $images = [];
}
?>

<style>
:root {
    --primary: #099227;
    --dark: #0f172a;
    --radius: 2rem;
}

.gallery-hero {
    padding: 10rem 0 5rem;
    background: radial-gradient(circle at 10% 10%, #f0fdf4 0%, transparent 40%);
    text-align: center;
}

.gallery-grid {
    column-count: 1;
    column-gap: 1.5rem;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

@media (min-width: 640px) { .gallery-grid { column-count: 2; } }
@media (min-width: 1024px) { .gallery-grid { column-count: 3; } }

.gallery-card {
    break-inside: avoid;
    margin-bottom: 1.5rem;
    position: relative;
    border-radius: var(--radius);
    overflow: hidden;
    background: #f8fafc;
    transition: all 0.5s cubic-bezier(0.2, 1, 0.3, 1);
}

.gallery-card img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.7s ease;
}

.gallery-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.gallery-card:hover img {
    transform: scale(1.05);
}

.card-info {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 2rem 1.5rem 1.5rem;
    background: linear-gradient(to top, rgba(15,23,42,0.9), transparent);
    color: white;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-card:hover .card-info {
    opacity: 1;
}

.cat-tag {
    font-size: 0.65rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--primary);
    background: white;
    padding: 0.25rem 0.75rem;
    border-radius: 99px;
    margin-bottom: 0.5rem;
    display: inline-block;
}

.pagination-btn {
    padding: 1rem 2rem;
    border-radius: 1.5rem;
    font-weight: 800;
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
}

.pagination-btn:hover:not(:disabled) {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
    transform: scale(1.05);
}
</style>

<header class="gallery-hero">
    <div class="container mx-auto px-6">
        <h1 class="text-6xl font-black text-slate-900 tracking-tighter mb-4">The Archive</h1>
        <p class="text-slate-500 text-lg font-medium max-w-xl mx-auto italic">Capturing the evolution of ReSEED through the lens of time.</p>
    </div>
</header>

<main class="pb-20">
    <?php if (!$images): ?>
        <div class="text-center py-20">
            <p class="text-slate-400 font-bold">The vault is currently empty.</p>
        </div>
    <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($images as $img): 
                $url = (strpos($img['filename'], 'http') === 0) ? $img['filename'] : UPLOADS_URL . '/gallery/' . $img['filename'];
                $caption = $img['caption'] ?: 'ReSEED Archive';
            ?>
                <div class="gallery-card group">
                    <img src="<?= htmlspecialchars($url) ?>" alt="<?= htmlspecialchars($caption) ?>" loading="lazy">
                    <div class="card-info">
                        <span class="cat-tag"><?= htmlspecialchars($img['category'] ?: 'General') ?></span>
                        <h3 class="font-bold text-lg leading-tight"><?= htmlspecialchars($caption) ?></h3>
                        <a href="<?= htmlspecialchars($url) ?>" download class="mt-4 inline-flex text-xs font-black uppercase tracking-widest hover:text-emerald-400 transition">
                            Download Asset
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex flex-col items-center mt-16 gap-6">
            <div class="flex gap-4">
                <a href="?page=<?= $page - 1 ?>" 
                   class="pagination-btn <?= $page <= 1 ? 'pointer-events-none opacity-30' : '' ?>">
                   Prev
                </a>
                <a href="?page=<?= $page + 1 ?>" 
                   class="pagination-btn <?= $page >= $totalPages ? 'pointer-events-none opacity-30' : '' ?>">
                   Next
                </a>
            </div>
            <p class="text-slate-400 text-xs font-black uppercase tracking-widest">
                Page <?= $page ?> <span class="mx-2 text-slate-200">/</span> <?= $totalPages ?>
            </p>
        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/backend/includes/footer.php'; ?>