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
    --radius-pill: 50px;
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

h1, h2, h3, h4, .font-heading {
    font-family: 'Plus Jakarta Sans', sans-serif;
    letter-spacing: -0.03em;
    color: var(--primary-dark);
}

img, video { max-width: 100%; height: auto; display: block; }
a { text-decoration: none; transition: 0.3s; }

.container {
    max-width: 1280px;
    margin-inline: auto;
    padding-inline: clamp(1.5rem, 5vw, 3rem);
    position: relative;
    z-index: 2;
}

.section { padding: clamp(60px, 10vw, 120px) 0; position: relative; }

/* ================= BUTTONS & SHINE EFFECT ================= */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1rem 2.5rem;
    border-radius: var(--radius-pill);
    font-weight: 700;
    transition: all 0.4s var(--ease-elastic);
    cursor: pointer;
    border: none;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.95rem;
}

/* The Shine Animation */
.btn::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -60%;
    width: 20%;
    height: 200%;
    background: rgba(255, 255, 255, 0.4);
    transform: rotate(30deg);
    transition: none;
    pointer-events: none;
}

.btn:hover::after {
    left: 120%;
    transition: all 0.6s ease-in-out;
}

/* Primary Hero Button */
.btn-hero-primary {
    background: var(--primary);
    color: white !important;
    box-shadow: 0 10px 20px rgba(9, 146, 39, 0.3);
}

.btn-hero-primary:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(9, 146, 39, 0.4);
    background: var(--primary-light);
}

/* Outline Hero Button */
.btn-hero-outline {
    background: rgba(255, 255, 255, 0.05);
    color: white !important;
    border: 1.5px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.btn-hero-outline:hover {
    background: white;
    color: var(--primary-dark) !important;
    border-color: white;
    transform: translateY(-5px);
}

/* Luxury Partnership Button */
.btn-luxury {
    background: white;
    color: var(--primary-dark);
    box-shadow: 0 0 0 4px rgba(255,255,255,0.2);
}

.btn-luxury:hover {
    background: var(--accent);
    color: white;
    box-shadow: 0 0 0 8px rgba(83, 184, 16, 0.3);
}

/* ================= CTA GROUPS & STATUS ================= */
.cta-luxury-group {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-top: 2rem;
}

.cta-status { display: flex; align-items: center; gap: 0.75rem; }

.status-text {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
}

.pulse-dot {
    width: 8px; height: 8px;
    background-color: var(--accent);
    border-radius: 50%;
    position: relative;
}

.pulse-dot::after {
    content: ""; position: absolute; inset: 0;
    background-color: inherit; border-radius: 50%;
    animation: luxury-pulse 2s infinite;
}

@keyframes luxury-pulse {
    0% { transform: scale(1); opacity: 0.8; }
    100% { transform: scale(3); opacity: 0; }
}

/* ================= HERO SECTION ================= */
.hero {
    min-height: 100vh;
    position: relative;
    display: flex;
    align-items: center;
    background: #022c15;
    overflow: hidden;
    color: white;
    padding: 100px 0;
}

.hero-bg {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover; opacity: 0.4;
    filter: saturate(1.2) contrast(1.1);
    transform: scale(1.05);
}

.hero-overlay {
    position: absolute; inset: 0;
    background: radial-gradient(circle at 10% 20%, rgba(6, 78, 59, 0.8) 0%, rgba(2, 44, 21, 0.95) 100%);
    z-index: 2;
}

.hero-content { position: relative; z-index: 10; max-width: 850px; }

.hero h1 {
    font-size: clamp(2.5rem, 8vw, 5rem);
    line-height: 1.1;
    font-weight: 800;
    margin-bottom: 2rem;
}

.hero h1 span {
    display: block;
    background: linear-gradient(90deg, #34d399, #6ee7b7);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ================= ABOUT SECTION ================= */
.about-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(40px, 8vw, 100px);
    align-items: center;
}

.about-img-wrapper { position: relative; }
.about-img-wrapper::before {
    content: ''; position: absolute; top: 20px; left: -20px;
    width: 100%; height: 100%; border: 2px solid var(--primary);
    border-radius: var(--radius-xl); z-index: 0;
    transition: transform 0.5s var(--ease-elastic);
}

.about-img {
    position: relative; z-index: 1;
    border-radius: var(--radius-xl);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* ================= CARDS (Pillars & News) ================= */
.h-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.card-elegant {
    background: var(--glass-bg);
    backdrop-filter: blur(var(--blur-strength));
    border: 1px solid var(--glass-border);
    padding: 40px;
    border-radius: var(--radius-lg);
    transition: all 0.4s var(--ease-smooth);
}

.card-elegant:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border-color: var(--primary);
}

/* ================= PARTNERSHIP ================= */
.partnership-section {
    position: relative; padding: 120px 0;
    background: #022c10; color: white; overflow: hidden;
}

.partnership-bg {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover; z-index: 1; opacity: 0.5;
}

.partnership-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(90deg, #022c10 30%, rgba(2, 44, 16, 0.4) 100%);
    z-index: 2;
}

/* ================= MOBILE RESPONSIVENESS ================= */
@media (max-width: 991px) {
    .about-grid { grid-template-columns: 1fr; text-align: center; }
    .about-img-wrapper { max-width: 500px; margin: 0 auto; }
}

@media (max-width: 768px) {
    /* Hero Buttons stack on mobile */
    .hero .d-flex {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn { width: 100%; }

    .cta-luxury-group {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
}

@media (max-width: 576px) {
    .hero h1 { font-size: 2.5rem; }
    .section { padding: 60px 0; }
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
            
            <div class="d-flex gap-3 flex-wrap align-items-center">
                <a href="#about" class="btn btn-hero-primary btn-lg px-5 rounded-pill py-3 fw-bold">
                    Our Mission
                </a>
                <a href="#get-involved" class="btn btn-hero-outline btn-lg px-5 rounded-pill py-3 fw-bold">
                    Join The Movement
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
                        $coverImage = $post['cover_image'] ?? 'default.jpg';
                        $mediaType  = $post['media_type'] ?? 'image';
                        $mediaPath  = '/backend/uploads/posts/' . $coverImage;
                    ?>
                    <article class="news-card" data-aos="fade-up">
                        <div class="news-media-container">
                            <?php if ($mediaType === 'video'): ?>
                                <video muted autoplay loop playsinline>
                                    <source src="<?= htmlspecialchars($mediaPath) ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($mediaPath) ?>" alt="<?= htmlspecialchars($post['title'] ?? 'Story image') ?>" loading="lazy">
                            <?php endif; ?>
                        </div>

                        <div class="news-body">
                            <small>
                                <?= !empty($post['published_at']) ? date('M d, Y', strtotime($post['published_at'])) : 'Unpublished' ?>
                            </small>
                            <h4 class="font-heading">
                                <?= htmlspecialchars($post['title'] ?? 'Untitled Story') ?>
                            </h4>
                            <p class="text-muted small mb-4">
                                <?= htmlspecialchars(mb_strimwidth($post['excerpt'] ?? '', 0, 110, '...')) ?>
                            </p>
                            <?php if (!empty($post['slug'])): ?>
                                <a href="/frontend/post.php?slug=<?= urlencode($post['slug']) ?>" class="btn btn-sm btn-outline-success rounded-pill px-4">Read Story</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5 w-100">
                    <p class="text-muted mb-3">No field stories published yet.</p>
                    <a href="/blog.php" class="btn btn-outline-success rounded-pill">Visit News Archive</a>
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
            
            <div class="cta-luxury-group">
                <button class="btn-luxury open-contact-modal">
                    <span>Partner With Us</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
                <div class="cta-status">
                    <span class="pulse-dot"></span>
                    <span class="status-text">Donations Opening Soon</span>
                </div>
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