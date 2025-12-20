<?php
// index.php — ReSEED Landing Page (PRO UI MASTER EDITION)
require_once dirname(__DIR__) . '/backend/includes/config.php';
require_once dirname(__DIR__) . '/backend/includes/header.php';



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
    --primary: #099227ff;
    --primary-dark: #022c10ff;
    --primary-light: #078f1eff;
    --accent: #53b810ff;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --bg-light: #f8fafc;
    --white: #ffffff;
    --radius-xl: 40px;
    --radius-lg: 24px;
    --radius-md: 16px;
    --ease: cubic-bezier(0.23, 1, 0.32, 1);
}
/* ================= CORE LAYOUT FIX ================= */

/* Consistent content width (matches header) */
.container {
    max-width: 1240px;
    margin-inline: auto;
    padding-inline: clamp(1.25rem, 4vw, 2rem);
    width: 100%;
}

/* Prevent accidental horizontal stretching */
html, body {
    width: 100%;
    max-width: 100%;
    overflow-x: clip; /* safer than hidden */
}

/* Header-aware hero height */
.hero {
    min-height: calc(100vh - var(--header-h, 80px));
}

/* Ensure sections never bleed outside viewport */
section {
    position: relative;
    width: 100%;
    overflow: hidden;
}

/* Defensive fix for grids */
.about-grid,
.h-grid,
.journey-grid {
    width: 100%;
    max-width: 100%;
}

/* Media elements safety */
img, video {
    max-width: 100%;
    height: auto;
    display: block;
}


body {
    font-family: 'Inter', sans-serif;
    color: var(--text-main);
    line-height: 1.6;
    background-color: var(--white);
}

h1, h2, h3, h4, .font-heading {
    font-family: 'Plus Jakarta Sans', sans-serif;
    letter-spacing: -0.02em;
}

.section { padding: 100px 0; }
.bg-light { background: var(--bg-light); }
.section-title { font-size: clamp(2.2rem, 4vw, 3.3rem); font-weight: 800; margin-bottom: 1.5rem; }

/* ================= HERO SECTION (UPGRADED) ================= */
.hero {
    min-height: 100vh;
    position: relative;
    display: flex;
    align-items: center;
    color: var(--white);
    overflow: hidden;
    background: #4a773cff;
}

.hero-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
    opacity: 0.6;
    animation: slowZoom 25s infinite alternate;
}

@keyframes slowZoom {
    from { transform: scale(1); }
    to { transform: scale(1.1); }
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, 
        rgba(8, 49, 26, 0.95) 0%, 
        rgba(7, 48, 24, 0.7) 50%, 
        rgba(5, 43, 21, 0.4) 100%);
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    max-width: 800px;
}

.hero-badge {
    display: inline-block;
    background: rgba(17, 83, 28, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(43, 252, 78, 0.4);
    padding: 10px 24px;
    border-radius: 100px;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 2px;
    color: gold;
    margin-bottom: 2rem;
    text-transform: uppercase;
}

.hero h1 {
    font-size: clamp(1.5rem, 4vw, 3rem);
    line-height: 1.1;
    font-weight: 800;
    margin-bottom: 1.5rem;
    color: #e2e8f0;
}

.hero .lead {
    font-size: 1.4rem;
    opacity: 0.9;
    margin-bottom: 3rem;
}
/* Custom Hero Primary Button */
.btn-hero-primary {
    background: linear-gradient(135deg, #16b931 0%, #065f19 100%);
    color: #ffffff !important;
    border: none;
    transition: all 0.3s var(--ease);
    position: relative;
    overflow: hidden;
}

.btn-hero-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(6, 95, 25, 0.4) !important;
    filter: brightness(1.1);
}

/* Custom Hero Outline Button */
.btn-hero-outline {
    color: #ffffff !important;
    border: 2px solid rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(5px);
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.3s var(--ease);
}

.btn-hero-outline:hover {
    background: #ffffff !important;
    color: var(--primary-dark) !important;
    border-color: #ffffff;
    transform: translateY(-3px);
}

/* Mobile adjustments */
@media (max-width: 576px) {
    .btn-hero-primary, .btn-hero-outline {
        width: 100%; /* Stack buttons nicely on mobile */
        text-align: center;
    }
}

/* ================= ABOUT SECTION ================= */
.about-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

.about-img {
    width: 100%;
    height: 600px;
    object-fit: cover;
    border-radius: var(--radius-xl);
    box-shadow: 0 30px 60px rgba(0,0,0,0.1);
}

.acc-item { border-bottom: 1px solid #e5e7eb; }
.acc-trigger {
    width: 100%;
    padding: 24px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.2rem;
    font-weight: 700;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--primary-dark);
}

.acc-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s var(--ease);
    color: var(--text-muted);
}

/* ================= PILLARS & JOURNEY ================= */
.h-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.card-elegant {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 45px;
    border: 1px solid #f1f5f9;
    transition: all 0.4s var(--ease);
}

.card-elegant:hover {
    transform: translateY(-12px);
    box-shadow: 0 40px 80px rgba(15, 23, 42, 0.08);
}

.icon-box {
    width: 70px;
    height: 70px;
    background: #f0fdf4;
    color: var(--primary);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin-bottom: 30px;
}

.journey-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.journey-card {
    background: #e2e8f0;
    padding: 40px 30px;
    border-radius: var(--radius-md);
    text-align: center;
    transition: all 0.5s ease-in-out;
    border: 1px solid #f1f5f9;
}

.journey-step {
    font-size: 4rem;
    font-weight: 800;
    color: #cbd5e1;
    line-height: 1;
}

.journey-card:hover {
    background: rgba(36, 126, 18, 1);
    color: white;
}

.journey-card:hover .journey-step { color: rgba(255,255,255,0.2); }
.journey-card:hover .text-muted { color: rgba(255,255,255,0.8) !important; }

/* ================= NEWS (UPDATED FOR VIDEO) ================= */
.news-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid #f1f5f9;
    transition: all 0.4s var(--ease);
    display: flex;
    flex-direction: column;
}

.news-media-container {
    height: 250px;
    width: 100%;
    overflow: hidden;
    position: relative;
    background: #000;
}

.news-media-container img, 
.news-media-container video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.news-body { padding: 35px; flex-grow: 1; }
.news-body a{
    color: var(--primary-dark);
    font-weight: 400;
    font-size: 1rem;
    transition: color 0.3s var(--ease);
    border-bottom: 1px solid black;
}
.news-body a:hover { color: var(--primary); }

/* ================= PARTNERSHIP SECTION ================= */
.partnership-section {
    padding: 120px 0;
    position: relative;
    /* border-radius: var(--radius-xl);
    margin: 0 30px; */
    overflow: hidden;
    width: 100%;
}

.partnership-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.3;
    z-index: 1;
}

.partnership-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(115deg, rgba(10, 44, 4, 1) 40%, transparent 100%);
    z-index: 2;
}

.partnership-content {
    position: relative;
    z-index: 3;
    max-width: 700px;
    color: var(--white);
}

.impact-label {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    color: var(--primary-light);
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    font-size: 0.85rem;
    margin-bottom: 2rem;
}

.impact-label::before {
    content: '';
    width: 10px;
    height: 10px;
    background: #ff4d4d;
    border-radius: 40%;
    box-shadow: 0 0 15px #ff4d4d;
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.partnership-content h2 {
    font-size: clamp(2.5rem, 5vw, 4rem);
    line-height: 1.1;
    font-weight: 800;
    margin-bottom: 2rem;
    color: #e2e8f0;
}

.partnership-content .lead {
    border-left: 4px solid var(--primary-light);
    padding-left: 30px;
    font-size: 1.3rem;
    opacity: 0.9;
    margin-bottom: 3.5rem;
}

.btn-luxury {
    background: var(--white);
    color: var(--primary-dark);
    padding: 20px 45px;
    border-radius: 100px;
    font-weight: 700;
    font-size: 1.1rem;
    border: none;
    transition: all 0.3s;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

.btn-luxury:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    background: var(--primary-light);
    color: var(--white);
}

@media (max-width: 991px) {
    .about-grid { grid-template-columns: 1fr; gap: 40px; }
    .partnership-section { margin:10px 0; border-radius: 0; padding: 80px 40px; }
    .partnership-overlay { background: rgba(10, 48, 2, 1); }
}
</style>

<section class="hero">
    <img src="/assets/images/Re-logo.png">
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
                <img src="/assets/images/Re-team.jpg" class="about-img" alt="ReSEED Team">
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
    <img src="/assets/images/Re-logo.png" class="partnership-bg" alt="Landscape">
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

<?php include dirname(__DIR__) . '/backend/includes/footer.php'; ?>

