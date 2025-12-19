<?php
// index.php — ReSEED Landing Page (PRO UI MASTER EDITION)

// Bootstrap / config
require_once __DIR__ . '/../backend/includes/header.php';

// --------------------------------------------------
// Fetch Latest Posts (SAFE: works with or without DB)
// --------------------------------------------------

$latestPosts = [];

if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                title,
                slug,
                excerpt,
                cover_image,
                media_type,
                published_at
            FROM posts
            WHERE published_at IS NOT NULL
            ORDER BY published_at DESC
            LIMIT 3
        ");
        $stmt->execute();
        $latestPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log silently in production
        error_log('Homepage post query failed: ' . $e->getMessage());
        $latestPosts = [];
    }
}
?>


<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Inter:wght@400;500;700&display=swap');

:root {
    --primary: #099227;
    --primary-dark: #022c10;
    --primary-light: #078f1e;
    --accent: #53b810;

    --text-main: #1e293b;
    --text-muted: #64748b;

    --bg-light: #f8fafc;
    --white: #ffffff;

    --radius-xl: 40px;
    --radius-lg: 24px;
    --radius-md: 16px;

    --ease: cubic-bezier(0.23, 1, 0.32, 1);
}

/* ---------------- GLOBAL SAFETY ---------------- */

* {
    box-sizing: border-box;
}

html, body {
    width: 100%;
    max-width: 100%;
    overflow-x: clip;
}

body {
    font-family: 'Inter', sans-serif;
    color: var(--text-main);
    line-height: 1.6;
    background: var(--white);
}

/* Consistent width */
.container {
    max-width: 1240px;
    margin-inline: auto;
    padding-inline: clamp(1.25rem, 4vw, 2rem);
    width: 100%;
}

/* Media safety */
img, video {
    max-width: 100%;
    height: auto;
    display: block;
}

/* Sections never bleed */
section {
    position: relative;
    width: 100%;
    overflow: hidden;
}

/* ---------------- HERO ---------------- */

.hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: #4a773c;
    color: white;
    overflow: hidden;
}

.hero-bg {
    position: absolute;
    inset: 0;
    object-fit: cover;
    opacity: 0.6;
    z-index: 1;
    animation: slowZoom 25s infinite alternate;
}

@keyframes slowZoom {
    from { transform: scale(1); }
    to   { transform: scale(1.1); }
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to right,
        rgba(8,49,26,0.95),
        rgba(7,48,24,0.6)
    );
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    max-width: 820px;
}

.hero h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(2rem, 4vw, 3.5rem);
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 1.5rem;
}

.hero .lead {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 3rem;
}

/* ---------------- BUTTONS ---------------- */

.btn-hero-primary {
    background: linear-gradient(135deg, #16b931, #065f19);
    color: white;
    border: none;
    padding: 16px 34px;
    border-radius: 999px;
    font-weight: 700;
    transition: all 0.3s var(--ease);
}

.btn-hero-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 40px rgba(6,95,25,0.4);
}

.btn-hero-outline {
    background: rgba(255,255,255,0.08);
    border: 2px solid rgba(255,255,255,0.4);
    color: white;
    padding: 16px 34px;
    border-radius: 999px;
    transition: all 0.3s var(--ease);
}

.btn-hero-outline:hover {
    background: white;
    color: var(--primary-dark);
}

/* ---------------- GRIDS ---------------- */

.about-grid,
.h-grid,
.journey-grid {
    width: 100%;
    max-width: 100%;
}

.about-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
}

@media (max-width: 991px) {
    .about-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}

</style>

<section class="hero">
    <img src="/reseed/assets/images/Re-logo.png" class="hero-bg" alt="Hero">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content" data-aos="fade-up" data-aos-duration="1200">
            <span class="hero-badge">South Sudan Community Movement</span>
            <h1>Restoring Nature.<br><span style="color: var(--primary-light)">Reseeding Prosperity.</span></h1>
            <p class="lead">A community-led movement transforming fragile landscapes into resilient ecosystems and sustainable livelihoods.</p>
            <div class="d-flex gap-3 flex-wrap mt-2">
                <a href="#about" class="btn btn-hero-primary btn-lg px-5 rounded-pill py-3 fw-bold shadow-lg">Our Mission</a>
                <a href="#get-involved" class="btn btn-hero-outline btn-lg px-5 rounded-pill py-3 fw-bold">Join Us</a>
            </div>
        </div>
    </div>
</section>

<section id="about" class="section">
    <div class="container">
        <div class="about-grid">
            <div data-aos="fade-right">
                <img src="/reseed/assets/images/Re-team.jpg" class="about-img" alt="ReSEED Team">
            </div>
            <div data-aos="fade-left">
                <span class="text-success fw-bold text-uppercase tracking-widest mb-2 d-block">The ReSEED Story</span>
                <h2 class="section-title">Rooted in Resilience.</h2>
                <p class="text-muted mb-4 fs-5">We bridge the gap between humanitarian aid and long-term climate adaptation through community-owned solutions.</p>

                <div class="accordion-box">
                    <div class="acc-item">
                        <button class="acc-trigger">Our Mission <i class="fa-solid fa-plus small"></i></button>
                        <div class="acc-content"><p class="py-3">To restore hope through sustainable land regeneration and community empowerment—transforming vulnerability into resilience.</p></div>
                    </div>
                    <div class="acc-item">
                        <button class="acc-trigger">Our Vision <i class="fa-solid fa-plus small"></i></button>
                        <div class="acc-content"><p class="py-3">A resilient South Sudan where communities thrive in harmony with nature and achieve total food sovereignty.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">The Three Pillars</h2>
        </div>
        <div class="h-grid">
            <div class="card-elegant" data-aos="fade-up" data-aos-delay="100">
                <div class="icon-box"><i class="fa-solid fa-seedling"></i></div>
                <h3 class="fw-bold">Regenerate</h3>
                <p class="text-muted">Revitalizing degraded soils using indigenous knowledge and modern agroecology.</p>
            </div>
            <div class="card-elegant" data-aos="fade-up" data-aos-delay="200">
                <div class="icon-box"><i class="fa-solid fa-people-group"></i></div>
                <h3 class="fw-bold">Empower</h3>
                <p class="text-muted">Investing in local leaders and youth to take ownership of their climate future.</p>
            </div>
            <div class="card-elegant" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-box"><i class="fa-solid fa-shield-heart"></i></div>
                <h3 class="fw-bold">Resilience</h3>
                <p class="text-muted">Building shock-resistant food systems that withstand floods and droughts.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">The Path to Prosperity</h2>
        </div>
        <div class="journey-grid">
            <div class="journey-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="journey-step">01</div>
                <h5 class="fw-bold mt-4">Stabilize</h5>
                <p class="text-muted small">Relief and emergency stabilization for fragile zones.</p>
            </div>
            <div class="journey-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="journey-step">02</div>
                <h5 class="fw-bold mt-4">Restore</h5>
                <p class="text-muted small">Providing tools and regenerative farming techniques.</p>
            </div>
            <div class="journey-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="journey-step">03</div>
                <h5 class="fw-bold mt-4">Sustain</h5>
                <p class="text-muted small">Scaling climate-smart systems and market links.</p>
            </div>
            <div class="journey-card" data-aos="zoom-in" data-aos-delay="400">
                <div class="journey-step">04</div>
                <h5 class="fw-bold mt-4">Thrive</h5>
                <p class="text-muted small">Achieving full self-reliance and community wealth.</p>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <h2 class="section-title mb-0">Field Stories</h2>
            <a href="blog.php" class="btn btn-link text-success fw-bold text-decoration-none">See All News →</a>
        </div>
        <div class="h-grid">
            <?php foreach ($latestPosts as $post): 
                $media_path = 'uploads/posts/' . ($post['cover_image'] ?: 'default.jpg');
            ?>
            <div class="news-card" data-aos="fade-up">
                <div class="news-media-container">
                    <?php if ($post['media_type'] === 'video'): ?>
                        <video muted autoplay loop playsinline>
                            <source src="<?= $media_path ?>" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <img src="<?= $media_path ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                    <?php endif; ?>
                </div>
                <div class="news-body">
                    <small class="text-success fw-bold d-block mb-2"><?= date('M d, Y', strtotime($post['published_at'])) ?></small>
                    <h4 class="fw-bold mb-3"><?= htmlspecialchars($post['title']) ?></h4>
                    <p class="text-muted small"><?= htmlspecialchars(mb_strimwidth($post['excerpt'], 0, 100, '...')) ?></p>
                    <a href="post.php?slug=<?= urlencode($post['slug']) ?>" class="btn btn-sm btn-outline-success mt-3 rounded-pill">Read Story</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="get-involved" class="partnership-section">
    <img src="/reseed/assets/images/Re-logo.png" class="partnership-bg" alt="Landscape">
    <div class="partnership-overlay"></div>
    <div class="container">
        <div class="partnership-content" data-aos="fade-right">
            <span class="impact-label">Scale Our Impact</span>
            <h2>Let's Restore the Land Together</h2>
            <p class="lead">We are building a network of partners to transform South Sudan’s landscapes. Join us in creating a resilient future for the next generation.</p>
            
            <div class="d-flex align-items-center flex-wrap gap-4">
                <button class="btn btn-luxury open-contact-modal">Partner With Us</button>
                <span class="text-white-50 fw-bold small text-uppercase tracking-widest">Donations Opening Soon</span>
            </div>
        </div>
    </div>
</section>

<script>
AOS.init({ once:true, duration:1000, easing:'ease-out-cubic' });

document.querySelectorAll('.acc-trigger').forEach(btn=>{
    btn.addEventListener('click',()=>{
        const content=btn.nextElementSibling;
        const icon=btn.querySelector('i');

        document.querySelectorAll('.acc-content').forEach(c=>{
            if(c!==content){
                c.style.maxHeight=null;
                c.previousElementSibling.querySelector('i').className='fa-solid fa-plus small';
            }
        });

        if(content.style.maxHeight){
            content.style.maxHeight=null;
            icon.className='fa-solid fa-plus small';
        }else{
            content.style.maxHeight=content.scrollHeight+'px';
            icon.className='fa-solid fa-minus small';
        }
    });
});
</script>

<?php require_once __DIR__ . '/../backend/includes/footer.php'; ?>
