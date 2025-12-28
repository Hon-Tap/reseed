<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Frontend — Gallery (Final, Fixed, Polished)
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$limit  = 12;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

try {
    $totalImages = (int) $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
    $totalPages  = max(1, (int) ceil($totalImages / $limit));

    $stmt = $pdo->prepare("
        SELECT filename, caption, created_at
        FROM gallery
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    $images = [];
}
?>

<style>
/* ===== Design Tokens ===== */
:root{
  --primary:#10b981;
  --text-main:#0f172a;
  --text-muted:#64748b;
  --radius-xl:24px;
  --ease:cubic-bezier(.22,1,.36,1);
  --gallery-gap:clamp(1.25rem,2.5vw,2rem);
  --gallery-max:1200px;
}

/* ===== Hero ===== */
.gallery-hero{
  padding:clamp(7rem,12vw,9rem) 0 4rem;
  text-align:center;
  background:radial-gradient(700px 280px at 80% -10%,#dcfce7,transparent),#fff;
}
.gallery-hero h1{
  font-size:clamp(2.75rem,5vw,3.75rem);
  font-weight:900;
  color:var(--text-main);
  letter-spacing:-.04em;
}

/* ===== Layout ===== */
.gallery-wrap{
  max-width:var(--gallery-max);
  margin-inline:auto;
  padding-inline:var(--container-pad);
}

/* ===== Masonry ===== */
.gallery-masonry{
  columns:3;
  column-gap:var(--gallery-gap);
}
@media(min-width:1400px){
  .gallery-masonry{columns:3 360px;}
}
@media(max-width:1024px){
  .gallery-masonry{columns:2;}
}
@media(max-width:640px){
  .gallery-masonry{columns:1;}
}

/* ===== Card ===== */
.gallery-item{
  break-inside:avoid;
  margin-bottom:var(--gallery-gap);
  position:relative;
  overflow:hidden;
  border-radius:var(--radius-xl);
  background:#fff;
  transition:transform .5s var(--ease), box-shadow .5s var(--ease);
}
.gallery-item img{
  width:100%;
  display:block;
  transition:transform .8s var(--ease);
}
@media(hover:hover){
  .gallery-item:hover{
    transform:translateY(-6px);
    box-shadow:0 28px 60px rgba(0,0,0,.14);
  }
  .gallery-item:hover img{transform:scale(1.06);}
}

/* ===== Overlay ===== */
.item-overlay{
  position:absolute;
  inset:0;
  background:linear-gradient(to top,rgba(15,23,42,.88),rgba(15,23,42,.15) 55%,transparent);
  display:flex;
  flex-direction:column;
  justify-content:flex-end;
  padding:clamp(1.25rem,3vw,1.75rem);
  opacity:0;
  transition:opacity .35s ease;
}
.gallery-item:hover .item-overlay{opacity:1;}

/* ===== Download ===== */
.download-btn{
  margin-top:.75rem;
  display:inline-flex;
  align-items:center;
  gap:.5rem;
  padding:.6rem 1.2rem;
  border-radius:999px;
  background:rgba(255,255,255,.18);
  backdrop-filter:blur(10px);
  color:#fff;
  text-decoration:none;
  font-weight:700;
  font-size:.75rem;
  border:1px solid rgba(255,255,255,.3);
  transition:all .25s ease;
}
.download-btn:hover{
  background:#fff;
  color:var(--text-main);
}

/* ===== Pagination ===== */
.pagination-deck{
  display:flex;
  justify-content:center;
  align-items:center;
  gap:1.25rem;
  margin:4rem 0 7rem;
}
.nav-btn{
  display:inline-flex;
  align-items:center;
  gap:.6rem;
  padding:.85rem 1.6rem;
  border-radius:999px;
  background:#fff;
  border:1px solid #e2e8f0;
  color:var(--text-main);
  font-weight:700;
  text-decoration:none;
  transition:all .25s var(--ease);
  box-shadow:0 4px 12px rgba(0,0,0,.06);
}
.nav-btn:hover:not(.disabled){
  background:var(--primary);
  color:#fff;
  border-color:var(--primary);
  transform:translateY(-2px);
  box-shadow:0 12px 26px rgba(16,185,129,.25);
}
.nav-btn.disabled{
  opacity:.45;
  pointer-events:none;
}
.page-indicator{
  font-weight:600;
  font-size:.85rem;
  color:var(--text-muted);
}
</style>

<section class="gallery-hero">
  <div class="container">
    <h1>The Archive</h1>
    <p>Explore our visual history, one frame at a time.</p>
  </div>
</section>

<main class="gallery-wrap">

<?php if (!$images): ?>

  <div style="text-align:center; padding:100px 0;">
    <p>No images found in this collection.</p>
    <a href="gallery.php">Return to start</a>
  </div>

<?php else: ?>

  <div class="gallery-masonry">
    <?php foreach ($images as $img): 
        $url = UPLOADS_URL . '/gallery/' . $img['filename'];
        $cap = trim((string)($img['caption'] ?? '')) ?: 'ReSEED Visual Archive';
    ?>
      <figure class="gallery-item">
        <img src="<?= htmlspecialchars($url) ?>" alt="<?= htmlspecialchars($cap) ?>" loading="lazy">
        <figcaption class="item-overlay">
          <div style="color:#fff; font-weight:700;">
            <?= htmlspecialchars($cap) ?>
          </div>
          <a href="<?= htmlspecialchars($url) ?>" download class="download-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7 10 12 15 17 10"/>
              <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download
          </a>
        </figcaption>
      </figure>
    <?php endforeach; ?>
  </div>

  <div class="pagination-deck">
    <a href="?page=<?= $page - 1 ?>" class="nav-btn <?= $page <= 1 ? 'disabled' : '' ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.5">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
      Previous
    </a>

    <span class="page-indicator">Page <?= $page ?> of <?= $totalPages ?></span>

    <a href="?page=<?= $page + 1 ?>" class="nav-btn <?= $page >= $totalPages ? 'disabled' : '' ?>">
      Next
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.5">
        <polyline points="9 18 15 12 9 6"/>
      </svg>
    </a>
  </div>

<?php endif; ?>

</main>

<?php include dirname(__DIR__) . '/backend/includes/footer.php'; ?>
