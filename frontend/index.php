<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';
?>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ================= PREMIUM THEME VARIABLES ================= */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Inter:wght@300;400;500;600&display=swap');

:root {
    --primary: #099227;
    --primary-dark: #022c10;
    --primary-light: #078f1e;
    --accent: #53b810;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --bg-body: #ffffff;
    --bg-surface: #f8fafc;
    --radius-xl: 32px;
    --radius-lg: 20px;
    --ease-elastic: cubic-bezier(0.34, 1.56, 0.64, 1);
    --ease-smooth: cubic-bezier(0.4, 0, 0.2, 1);
}

/* ================= RESET & CORE ================= */
* { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; overflow-x: hidden; }
body {
    font-family: 'Inter', sans-serif;
    color: var(--text-main);
    line-height: 1.7;
    background-color: var(--bg-body);
}

h1, h2, h3, h4 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    letter-spacing: -0.03em;
    color: var(--primary-dark);
}

.container {
    max-width: 1280px;
    margin-inline: auto;
    padding-inline: clamp(1.5rem, 5vw, 3rem);
}

.section { padding: 100px 0; position: relative; }
.section-title {
    font-size: clamp(2.5rem, 5vw, 3.5rem);
    font-weight: 800;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ================= 1. HERO SECTION ================= */
.hero {
    min-height: 90vh;
    position: relative;
    display: flex;
    align-items: center;
    background: #022c15;
    overflow: hidden;
    color: white;
    border-bottom-left-radius: 60px;
    border-bottom-right-radius: 60px;
}

.hero-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.35;
    transform: scale(1.1);
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, #022c10 20%, rgba(2, 44, 16, 0.3) 100%);
    z-index: 2;
}

.hero-content { position: relative; z-index: 10; max-width: 800px; }

.hero-badge {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 8px 20px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--accent);
    text-transform: uppercase;
    margin-bottom: 2rem;
}

.hero h1 {
    font-size: clamp(3rem, 7vw, 5.5rem);
    line-height: 1.1;
    font-weight: 800;
    color: #fff;
    margin-bottom: 1.5rem;
}

.hero h1 span {
    background: linear-gradient(90deg, #4ed334, #a7f3d0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ================= 2. ABOUT & PILLARS ================= */
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
.about-img { border-radius: var(--radius-xl); box-shadow: 0 30px 60px -15px rgba(0,0,0,0.3); }

.card-elegant {
    background: white;
    padding: 40px;
    border-radius: var(--radius-lg);
    border: 1px solid #f1f5f9;
    transition: all 0.4s var(--ease-smooth);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.card-elegant:hover {
    transform: translateY(-10px);
    border-color: var(--primary);
    box-shadow: 0 20px 40px -10px rgba(9, 146, 39, 0.15);
}

.icon-box {
    width: 60px; height: 60px;
    background: #ecfdf5;
    color: var(--primary);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin-bottom: 25px;
}

/* ================= 3. FIELD STORIES (The Fix Area) ================= */
.news-card {
    background: white;
    border-radius: 24px;
    overflow: hidden;
    transition: all 0.4s var(--ease-smooth);
    border: 1px solid #f1f5f9;
    height: 100%;
}

.news-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }

.news-media { height: 240px; overflow: hidden; position: relative; }
.news-media img, .news-media video { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
.news-card:hover .news-media img { transform: scale(1.05); }

/* ================= 4. PARTNERSHIP ================= */
.partnership-section {
    padding: 120px 0;
    background: var(--primary-dark);
    color: white;
    border-radius: 60px;
    margin: 40px 20px;
    overflow: hidden;
    position: relative;
}

.partnership-content { position: relative; z-index: 5; max-width: 600px; }

@media (max-width: 991px) {
    .about-grid { grid-template-columns: 1fr; gap: 40px; }
    .hero { border-radius: 0; }
}
</style>

<section class="hero">
    <img src="/assets/images/Re-logo.jpeg" class="hero-bg" alt="Landscape">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <span class="hero-badge"><i class="fas fa-seedling me-2"></i> Community-Led Restoration</span>
            <h1>Restoring Nature.<br><span>Reseeding Prosperity.</span></h1>
            <p class="lead mb-5 fs-5 opacity-75">Transforming fragile landscapes into resilient ecosystems and sustainable livelihoods in South Sudan.</p>
            <div class="d-flex gap-3 flex-wrap">
                <a href="#about" class="btn btn-success px-5 py-3 rounded-pill fw-bold">Our Mission</a>
                <a href="#get-involved" class="btn btn-outline-light px-5 py-3 rounded-pill fw-bold">Partner With Us</a>
            </div>
        </div>
    </div>
</section>

<section id="about" class="section">
    <div class="container">
        <div class="about-grid">
            <div class="position-relative" data-aos="fade-right">
                <img src="/assets/images/Re-team.jpg" class="about-img img-fluid" alt="Team">
                <div class="position-absolute bottom-0 end-0 p-4 bg-white rounded-4 shadow-lg m-4 d-none d-md-block">
                    <h5 class="fw-800 mb-0 text-success">100% Community Owned</h5>
                </div>
            </div>
            <div data-aos="fade-left">
                <h2 class="section-title">Rooted in Resilience.</h2>
                <p class="text-muted fs-5 mb-5">We bridge the gap between humanitarian aid and long-term climate adaptation through community-owned solutions.</p>
                
                <div class="accordion-box">
                    <div class="acc-item mb-3">
                        <button class="acc-trigger w-100 text-start py-3 border-0 bg-transparent fw-bold fs-5 d-flex justify-content-between">
                            Our Mission <i class="fas fa-plus text-success"></i>
                        </button>
                        <div class="acc-content px-1"><p class="text-muted pb-3">To restore hope through sustainable land regeneration and community empowerment—transforming vulnerability into resilience.</p></div>
                    </div>
                    <div class="acc-item mb-3">
                        <button class="acc-trigger w-100 text-start py-3 border-0 bg-transparent fw-bold fs-5 d-flex justify-content-between">
                            Our Vision <i class="fas fa-plus text-success"></i>
                        </button>
                        <div class="acc-content px-1"><p class="text-muted pb-3">A resilient South Sudan where communities thrive in harmony with nature and achieve total food sovereignty.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-surface">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">The Three Pillars</h2>
            <p class="text-muted">Our holistic approach for a sustainable future.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-elegant">
                    <div class="icon-box"><i class="fas fa-leaf"></i></div>
                    <h3>Regenerate</h3>
                    <p class="text-muted mb-0">Revitalizing degraded soils using indigenous knowledge and modern agroecology.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-elegant">
                    <div class="icon-box"><i class="fas fa-users"></i></div>
                    <h3>Empower</h3>
                    <p class="text-muted mb-0">Investing in local leaders and youth to take ownership of their climate future.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card-elegant">
                    <div class="icon-box"><i class="fas fa-shield-alt"></i></div>
                    <h3>Resilience</h3>
                    <p class="text-muted mb-0">Building food systems that withstand floods and droughts year-round.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
/**
 * REWRITTEN FETCH LOGIC
 * Fixed: cover_image -> cover_media
 */
try {
    $stmt = $pdo->prepare("
        SELECT title, slug, excerpt, cover_media, media_type, published_at 
        FROM posts 
        WHERE published_at IS NOT NULL 
        ORDER BY published_at DESC 
        LIMIT 3
    ");
    $stmt->execute();
    $latestPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $latestPosts = [];
}
?>

<section class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up">
            <div>
                <h2 class="section-title mb-0">Field Stories</h2>
                <p class="text-muted">Latest updates from the ground.</p>
            </div>
            <a href="/blog.php" class="btn btn-link text-success fw-bold text-decoration-none">See All <i class="fas fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php if ($latestPosts): ?>
                <?php foreach ($latestPosts as $post): 
                    $mediaUrl = (strpos($post['cover_media'] ?? '', 'http') === 0) 
                                ? $post['cover_media'] 
                                : '/uploads/posts/' . ($post['cover_media'] ?? 'default.jpg');
                ?>
                <div class="col-md-4" data-aos="zoom-in">
                    <article class="news-card">
                        <div class="news-media">
                            <?php if ($post['media_type'] === 'video'): ?>
                                <video muted autoplay loop playsinline><source src="<?= htmlspecialchars($mediaUrl) ?>" type="video/mp4"></video>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($mediaUrl) ?>" alt="Story Image" loading="lazy">
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <small class="text-success fw-bold text-uppercase d-block mb-2" style="font-size: 0.7rem;">
                                <?= date('M d, Y', strtotime($post['published_at'])) ?>
                            </small>
                            <h4 class="fw-bold mb-3"><?= htmlspecialchars($post['title']) ?></h4>
                            <p class="text-muted small mb-4"><?= htmlspecialchars(mb_strimwidth($post['excerpt'], 0, 90, '…')) ?></p>
                            <a href="/post.php?slug=<?= urlencode($post['slug']) ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">Read Story</a>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center w-100 py-5">
                    <p class="text-muted">Check back soon for field updates!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="get-involved" class="partnership-section" data-aos="fade-up">
    <div class="container">
        <div class="partnership-content">
            <span class="text-success fw-bold text-uppercase mb-3 d-block">Scale Our Impact</span>
            <h2 class="text-white mb-4">Let's Restore the Land Together</h2>
            <p class="lead opacity-75 mb-5">We are building a network of partners to transform South Sudan’s landscapes. Join us in creating a resilient future.</p>
            <div class="d-flex align-items-center gap-4">
                <button class="btn btn-light px-5 py-3 rounded-pill fw-bold open-contact-modal">Partner With Us</button>
                <span class="text-muted small text-uppercase fw-bold opacity-50">Donations opening soon</span>
            </div>
        </div>
    </div>
</section>

<script>
    AOS.init({ once: true, duration: 800 });

    // Accordion Logic
    document.querySelectorAll('.acc-trigger').forEach(btn => {
        btn.addEventListener('click', () => {
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('i');
            const isOpen = content.style.maxHeight;

            // Close all
            document.querySelectorAll('.acc-content').forEach(c => c.style.maxHeight = null);
            document.querySelectorAll('.acc-trigger i').forEach(i => i.className = 'fas fa-plus text-success');

            // Open targeted
            if (!isOpen) {
                content.style.maxHeight = content.scrollHeight + "px";
                icon.className = 'fas fa-minus text-success';
            }
        });
    });
</script>

<?php include dirname(__DIR__) . '/backend/includes/footer.php'; ?>