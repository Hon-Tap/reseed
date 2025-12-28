<?php
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
    /* Palette: Deep Forest & Vibrant Life */
     --primary: #099227ff;

    --primary-dark: #022c10ff;

    --primary-light: #078f1eff;

    --accent: #53b810ff;
    
    /* Neutrals */
    --text-main: #0f172a;
    --text-muted: #64748b;
    --bg-body: #ffffff;
    --bg-surface: #f8fafc;
    
    /* Glassmorphism Logic */
    --glass-bg: rgba(255, 255, 255, 0.7);
    --glass-border: rgba(255, 255, 255, 0.5);
    --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    --blur-strength: 12px;

    /* Spacing & Motion */
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
    overflow-x: hidden;
}

h1, h2, h3, h4, .font-heading {
    font-family: 'Plus Jakarta Sans', sans-serif;
    letter-spacing: -0.03em;
    color: var(--primary-dark);
}

img, video { max-width: 100%; height: auto; display: block; }
a { text-decoration: none; }

.container {
    max-width: 1280px;
    margin-inline: auto;
    padding-inline: clamp(1.5rem, 5vw, 3rem);
    position: relative;
    z-index: 2;
}

.section { padding: 120px 0; position: relative; }
.section-title {
    font-size: clamp(2.5rem, 5vw, 3.5rem);
    font-weight: 800;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ================= 1. SEXY HERO SECTION ================= */
.hero {
    min-height: 100vh;
    position: relative;
    display: flex;
    align-items: center;
    background: #022c15; /* Fallback */
    overflow: hidden;
    color: white;
}

/* Dynamic Background Layers */
.hero-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.4;
    filter: saturate(1.2) contrast(1.1);
    transform: scale(1.05); /* Slight zoom for depth */
}

/* Gradient Mesh Overlay */
.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, #022c10 0%, rgba(2, 44, 16, 0.8) 60%, rgba(2, 44, 16, 0.2) 100%);
    z-index: 2;
}

/* Floating Orbs (The "Sexy" Factor) */
.hero::before, .hero::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    z-index: 3;
    animation: floatOrb 10s ease-in-out infinite alternate;
}

.hero::before {
    top: -10%;
    right: -5%;
    width: 500px;
    height: 500px;
    background: rgba(69, 185, 16, 0.2);
}

.hero::after {
    bottom: -10%;
    left: -10%;
    width: 400px;
    height: 400px;
    background: rgba(136, 245, 11, 0.15); /* Subtle gold hint */
    animation-delay: -5s;
}

@keyframes floatOrb {
    0% { transform: translate(0, 0); }
    100% { transform: translate(30px, 50px); }
}

.hero-content {
    position: relative;
    z-index: 10;
    max-width: 850px;
}

/* Glass Badge */
.hero-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 10px 24px;
    border-radius: 100px;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 2px;
    color: var(--accent);
    text-transform: uppercase;
    margin-bottom: 2.5rem;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.hero h1 {
    font-size: clamp(3rem, 7vw, 5.5rem);
    line-height: 1;
    font-weight: 800;
    margin-bottom: 2rem;
    color: #fff;
    -webkit-text-fill-color: #fff; /* Override global gradient */
}

.hero h1 span {
    display: block;
    background: linear-gradient(90deg, #4ed334ff, #6ee782ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero .lead {
    font-size: clamp(1.1rem, 2vw, 1.35rem);
    color: rgba(255, 255, 255, 0.85);
    margin-bottom: 3.5rem;
    font-weight: 300;
    max-width: 600px;
}

/* Modern Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.4s var(--ease-elastic);
}

.btn-hero-primary {
    background: var(--primary);
    color: #022c15 !important;
    border: none;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.btn-hero-primary::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, transparent 100%);
    z-index: -1;
    transition: opacity 0.3s;
}

.btn-hero-primary:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 20px 40px -10px rgba(16, 185, 129, 0.5);
}

.btn-hero-outline {
    color: #fff !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(4px);
}

.btn-hero-outline:hover {
    background: rgba(255, 255, 255, 1);
    color: var(--primary-dark) !important;
    transform: translateY(-4px);
}

/* ================= 2. ABOUT SECTION (Asymmetrical) ================= */
.about-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 100px;
    align-items: center;
}

.about-img-wrapper {
    position: relative;
}

/* The "Off-Axis" Border Effect */
.about-img-wrapper::before {
    content: '';
    position: absolute;
    top: 20px;
    left: -20px;
    width: 100%;
    height: 100%;
    border: 2px solid var(--primary);
    border-radius: var(--radius-xl);
    z-index: 0;
    transition: transform 0.5s var(--ease-elastic);
}

.about-img-wrapper:hover::before {
    transform: translate(10px, -10px);
}

.about-img {
    position: relative;
    z-index: 1;
    border-radius: var(--radius-xl);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Modern Accordion */
.accordion-box { margin-top: 3rem; }
.acc-item { border-bottom: 1px solid rgba(0,0,0,0.08); }

.acc-trigger {
    width: 100%;
    padding: 24px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.25rem;
    font-weight: 700;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--primary-dark);
    transition: color 0.3s;
}

.acc-trigger:hover { color: var(--primary); }

.acc-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease-out;
    color: var(--text-muted);
}

/* ================= 3. PILLARS (Glassmorphism Cards) ================= */
.bg-light {
    background-color: var(--bg-surface);
    background-image: radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0px, transparent 50%),
                      radial-gradient(at 100% 100%, rgba(245, 158, 11, 0.05) 0px, transparent 50%);
}

.h-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 40px;
}

.card-elegant {
    background: var(--glass-bg);
    backdrop-filter: blur(var(--blur-strength));
    border: 1px solid var(--glass-border);
    padding: 50px 40px;
    border-radius: var(--radius-lg);
    transition: all 0.4s var(--ease-smooth);
    position: relative;
    overflow: hidden;
}

/* Card Hover Glow */
.card-elegant::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.4s;
}

.card-elegant:hover {
    transform: translateY(-15px);
    box-shadow: 0 30px 60px -15px rgba(16, 185, 129, 0.2);
    border-color: var(--primary);
}

.card-elegant:hover::after { opacity: 1; }

.icon-box {
    width: 70px;
    height: 70px;
    background: white;
    color: var(--primary);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin-bottom: 30px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    position: relative;
    z-index: 2;
}

/* ================= 4. JOURNEY (Dark Theme) ================= */
/* We invert the colors here for high impact */
.section-dark {
    background: #0f172a;
    color: white;
}

.section-dark .section-title {
    background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.journey-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 30px;
    margin-top: 60px;
}

.journey-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 40px 30px;
    border-radius: var(--radius-lg);
    text-align: left;
    transition: all 0.4s ease;
    position: relative;
}

.journey-step {
    font-size: 3.5rem;
    font-weight: 800;
    color: transparent;
    -webkit-text-stroke: 1px rgba(255,255,255,0.2);
    line-height: 1;
    margin-bottom: 20px;
    transition: all 0.4s;
}

.journey-card:hover {
    background: var(--primary);
    border-color: var(--primary);
}

.journey-card:hover .journey-step {
    -webkit-text-stroke: 1px rgba(255,255,255,0.6);
    transform: scale(1.1) translateX(10px);
}

.journey-card h5 { color: white; margin-bottom: 10px; font-size: 1.25rem; }
.journey-card p { color: rgba(255,255,255,0.6); font-size: 0.95rem; }
.journey-card:hover p { color: rgba(255,255,255,0.9); }

/* ================= 5. FIELD STORIES ================= */
.news-card {
    background: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    transition: all 0.4s var(--ease-smooth);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.news-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.1);
}

.news-media-container {
    height: 280px;
    width: 100%;
    overflow: hidden;
    position: relative;
}

.news-media-container img, 
.news-media-container video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.8s var(--ease-smooth);
}

.news-card:hover .news-media-container img,
.news-card:hover .news-media-container video {
    transform: scale(1.1);
}

.news-body { padding: 35px; flex-grow: 1; display: flex; flex-direction: column; }

.news-body small {
    color: var(--primary);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.75rem;
}

.news-body h4 {
    font-size: 1.35rem;
    font-weight: 800;
    margin: 10px 0 15px;
    line-height: 1.3;
}

.news-body .btn { margin-top: auto; align-self: flex-start; }

/* ================= 6. PARTNERSHIP (Parallax Effect) ================= */
.partnership-section {
    position: relative;
    padding: 160px 0;
    color: white;
    overflow: hidden;
}

.partnership-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
}

/* Advanced Dark Gradient */
.partnership-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, #022c10 0%, rgba(2, 44, 16, 0.8) 60%, rgba(2, 44, 16, 0.2) 100%);
    z-index: 2;
}

.partnership-content {
    position: relative;
    z-index: 3;
    max-width: 650px;
}

.impact-label {
    color: var(--accent);
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 1.5rem;
}

.impact-label::before {
    content: '';
    width: 40px; height: 2px;
    background: var(--accent);
}

.partnership-content h2 {
    font-size: clamp(2.5rem, 5vw, 4rem);
    color: white;
    margin-bottom: 1.5rem;
}

.partnership-content .lead {
    font-size: 1.25rem;
    color: rgba(255,255,255,0.8);
    margin-bottom: 3rem;
    font-weight: 300;
}

.btn-luxury {
    background: white;
    color: var(--primary-dark);
    padding: 20px 50px;
    border-radius: 100px;
    font-weight: 700;
    font-size: 1.1rem;
    border: none;
    cursor: pointer;
    box-shadow: 0 0 0 4px rgba(255,255,255,0.2);
    transition: all 0.3s;
}

.btn-luxury:hover {
    background: var(--accent);
    color: white;
    box-shadow: 0 0 0 8px rgba(245, 158, 11, 0.3);
    transform: translateY(-2px);
}

/* Mobile Tweaks */
@media (max-width: 991px) {
    .about-grid { grid-template-columns: 1fr; gap: 50px; }
    .hero::before, .hero::after { opacity: 0.5; }
    .h-grid { gap: 20px; }
}
</style>

<section class="hero">
    <img src="/assets/images/Re-logo.jpeg" class="hero-bg" alt="ReSEED Landscape">
    <div class="hero-overlay"></div>
    
    <div class="container">
        <div class="hero-content" data-aos="fade-up" data-aos-duration="1200">
            <span class="hero-badge">South Sudan Community Movement</span>
            <h1>Restoring Nature.<br><span>Reseeding Prosperity.</span></h1>
            <p class="lead">A community-led movement transforming fragile landscapes into resilient ecosystems and sustainable livelihoods.</p>
            
            <div style="display:flex;flex-wrap:wrap;gap:20px;">
                <!-- Primary -->
               <a href="#about"
                class="btn btn-hero-primary"
                style="padding:18px 48px;border-radius:100px;font-weight:700;">
                    Our Mission
                </a>

                <a href="#get-involved"
                class="btn btn-hero-outline"
                style="padding:18px 48px;border-radius:100px;font-weight:700;">
                    Join the Movement
                </a>

            </div>

        </div>
    </div>
</section>

<section id="about" class="section">
    <div class="container">
        <div class="about-grid">
            <div class="about-img-wrapper" data-aos="fade-right">
                <img src="/assets/images/Re-team.jpg" class="about-img" alt="ReSEED Team">
            </div>
            
            <div data-aos="fade-left">
                <span class="text-success fw-bold text-uppercase tracking-widest mb-2 d-block" style="letter-spacing: 2px; font-size: 0.85rem;">The ReSEED Story</span>
                <h2 class="section-title">Rooted in Resilience.</h2>
                <p class="text-muted mb-4 fs-5" style="font-weight: 300;">We bridge the gap between humanitarian aid and long-term climate adaptation through community-owned solutions.</p>

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
            <p class="text-muted mx-auto" style="max-width: 600px;">Our holistic approach ensures that every seed planted grows into a sustainable future.</p>
        </div>
        
        <div class="h-grid">
            <div class="card-elegant" data-aos="fade-up" data-aos-delay="100">
                <div class="icon-box"><i class="fa-solid fa-seedling"></i></div>
                <h3 class="fw-bold mb-3">Regenerate</h3>
                <p class="text-muted">Revitalizing degraded soils using indigenous knowledge and modern agroecology to bring the land back to life.</p>
            </div>
            
            <div class="card-elegant" data-aos="fade-up" data-aos-delay="200">
                <div class="icon-box"><i class="fa-solid fa-people-group"></i></div>
                <h3 class="fw-bold mb-3">Empower</h3>
                <p class="text-muted">Investing in local leaders and youth to take ownership of their climate future through education and tools.</p>
            </div>
            
            <div class="card-elegant" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-box"><i class="fa-solid fa-shield-heart"></i></div>
                <h3 class="fw-bold mb-3">Resilience</h3>
                <p class="text-muted">Building shock-resistant food systems that withstand floods and droughts, ensuring stability year-round.</p>
            </div>
        </div>
    </div>
</section>

<section class="section section-dark">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">The Path to Prosperity</h2>
        </div>
        <div class="journey-grid">
            <div class="journey-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="journey-step">01</div>
                <h5 class="fw-bold">Stabilize</h5>
                <p>Relief and emergency stabilization for fragile zones.</p>
            </div>
            <div class="journey-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="journey-step">02</div>
                <h5 class="fw-bold">Restore</h5>
                <p>Providing tools and regenerative farming techniques.</p>
            </div>
            <div class="journey-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="journey-step">03</div>
                <h5 class="fw-bold">Sustain</h5>
                <p>Scaling climate-smart systems and market links.</p>
            </div>
            <div class="journey-card" data-aos="zoom-in" data-aos-delay="400">
                <div class="journey-step">04</div>
                <h5 class="fw-bold">Thrive</h5>
                <p>Achieving full self-reliance and community wealth.</p>
            </div>
        </div>
    </div>
</section>

<section class="section bg-surface">

    <div class="container">

        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h2 class="section-title mb-2">Field Stories</h2>
                <p class="text-muted">Updates from the ground.</p>
            </div>

            <a href="/blog.php" class="btn btn-link text-success fw-bold text-decoration-none">
                See All News <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="h-grid">

            <?php if (!empty($latestPosts) && is_array($latestPosts)): ?>
                <?php foreach ($latestPosts as $post): ?>

                    <?php
                        $title       = $post['title'] ?? 'Untitled Story';
                        $slug        = $post['slug'] ?? null;
                        $excerpt     = $post['excerpt'] ?? '';
                        $mediaType   = $post['media_type'] ?? 'image';
                        $coverImage  = $post['cover_image'] ?? null;

                        // Public uploads URL (no backend leakage)
                        $mediaUrl = $coverImage
                            ? UPLOADS_URL . '/posts/' . $coverImage
                            : null;
                    ?>

                    <article class="news-card" data-aos="fade-up">

                        <div class="news-media-container">

                            <?php if ($mediaType === 'video' && $mediaUrl): ?>

                                <video muted autoplay loop playsinline>
                                    <source src="<?= htmlspecialchars($mediaUrl) ?>" type="video/mp4">
                                </video>

                            <?php elseif ($mediaUrl): ?>

                                <img
                                    src="<?= htmlspecialchars($mediaUrl) ?>"
                                    alt="<?= htmlspecialchars($title) ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <div class="news-placeholder">
                                    <i class="fa-regular fa-image"></i>
                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="news-body">

                            <small class="text-muted">
                                <?= !empty($post['published_at'])
                                    ? date('M d, Y', strtotime($post['published_at']))
                                    : 'Unpublished'
                                ?>
                            </small>

                            <h4 class="font-heading">
                                <?= htmlspecialchars($title) ?>
                            </h4>

                            <p class="text-muted small mb-4">
                                <?= htmlspecialchars(mb_strimwidth($excerpt, 0, 110, '...')) ?>
                            </p>

                            <?php if ($slug): ?>
                                <a
                                    href="/frontend/post.php?slug=<?= urlencode($slug) ?>"
                                    class="btn btn-sm btn-outline-success rounded-pill px-4"
                                >
                                    Read Story
                                </a>
                            <?php endif; ?>

                        </div>

                    </article>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="text-center py-5 w-100">
                    <p class="text-muted mb-3">No field stories published yet.</p>
                    <a href="/blog.php" class="btn btn-outline-success rounded-pill">
                        Visit News Archive
                    </a>
                </div>

            <?php endif; ?>

        </div>

    </div>

</section>
<section id="get-involved" class="partnership-section">
    <img src="/assets/images/Re-logo.jpeg" class="partnership-bg" alt="Landscape">
    <div class="partnership-overlay"></div>
    <div class="container">
        <div class="partnership-content" data-aos="fade-right">
            <span class="impact-label">Scale Our Impact</span>
            <h2>Let's Restore the Land Together</h2>
            <p class="lead">We are building a network of partners to transform South Sudan’s landscapes. Join us in creating a resilient future.</p>
            
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:24px;">

                <button class="btn-luxury open-contact-modal">
                    Partner With Us
                </button>

                <span style="
                    font-size:0.75rem;
                    font-weight:700;
                    text-transform:uppercase;
                    letter-spacing:0.25em;
                    color:#9ca3af;
                ">
                    Donations opening soon
                </span>

            </div>

        </div>
    </div>
</section>

<script>
    AOS.init({ once:true, duration:1000, easing:'ease-out-cubic' });

    // Accordion Logic
    document.querySelectorAll('.acc-trigger').forEach(btn=>{
        btn.addEventListener('click',()=>{
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('i');

            // Close others
            document.querySelectorAll('.acc-content').forEach(c=>{
                if(c !== content){
                    c.style.maxHeight = null;
                    c.previousElementSibling.querySelector('i').className='fa-solid fa-plus small';
                    c.previousElementSibling.style.color = 'var(--primary-dark)';
                }
            });

            // Toggle current
            if(content.style.maxHeight){
                content.style.maxHeight = null;
                icon.className = 'fa-solid fa-plus small';
                btn.style.color = 'var(--primary-dark)';
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
                icon.className = 'fa-solid fa-minus small';
                btn.style.color = 'var(--primary)';
            }
        });
    });
</script>

<?php include dirname(__DIR__) . '/backend/includes/footer.php'; ?>