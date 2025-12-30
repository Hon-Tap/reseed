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
body { font-family: 'Inter', sans-serif; color: var(--text-main); line-height: 1.7; background-color: var(--bg-body); overflow-x: hidden; }
h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.03em; color: var(--primary-dark); }
.container { max-width: 1280px; margin-inline: auto; padding-inline: clamp(1.5rem, 5vw, 3rem); position: relative; z-index: 2; }
.section { padding: clamp(80px, 10vh, 120px) 0; position: relative; }
.section-title { font-size: clamp(2.5rem, 5vw, 3.5rem); font-weight: 800; margin-bottom: 1rem; background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

/* ================= 1. REFINED HERO SECTION ================= */
.hero { min-height: 100vh; position: relative; display: flex; align-items: center; background: #022c15; overflow: hidden; color: white; padding-top: 80px; }
.hero-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.4; filter: saturate(1.2) contrast(1.1); transform: scale(1.05); }
.hero-overlay { position: absolute; inset: 0; background: linear-gradient(90deg, #022c10 0%, rgba(2, 44, 16, 0.8) 60%, rgba(2, 44, 16, 0) 100%); z-index: 2; }

.hero-content { position: relative; z-index: 10; max-width: 850px; }
.hero-badge { display: inline-block; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); padding: 10px 24px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; letter-spacing: 2px; color: var(--accent); text-transform: uppercase; margin-bottom: 2rem; }
.hero h1 { font-size: clamp(2.8rem, 8vw, 5.5rem); line-height: 1.1; font-weight: 800; margin-bottom: 2rem; color: #fff; }
.hero h1 span { display: block; background: linear-gradient(90deg, #4ed334, #6ee782); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

/* Fixed Clickable Buttons for Responsive */
.hero-cta-group { display: flex; flex-wrap: wrap; gap: 15px; position: relative; z-index: 20; }
.btn { text-decoration: none; display: inline-flex; align-items: center; justify-content: center; padding: 18px 42px; border-radius: 100px; font-weight: 700; transition: all 0.4s var(--ease-elastic); cursor: pointer; border: none; }
.btn-primary { background: var(--primary); color: #022c15 !important; }
.btn-primary:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4); }
.btn-outline { color: #fff !important; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(5px); }
.btn-outline:hover { background: #fff; color: var(--primary-dark) !important; transform: translateY(-5px); }

/* ================= 2. ABOUT SECTION ================= */
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: clamp(40px, 8vw, 100px); align-items: center; }
.about-img { border-radius: var(--radius-xl); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); position: relative; z-index: 1; }
.acc-item { border-bottom: 1px solid rgba(0,0,0,0.08); }
.acc-trigger { width: 100%; padding: 20px 0; display: flex; justify-content: space-between; align-items: center; font-size: 1.2rem; font-weight: 700; background: none; border: none; cursor: pointer; color: var(--primary-dark); }
.acc-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; color: var(--text-muted); }

/* ================= 3. PATH TO PROSPERITY (Visual Upgrade) ================= */
.journey-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 4rem; }
.journey-card { 
    background: rgba(255, 255, 255, 0.03); 
    border: 1px solid rgba(255, 255, 255, 0.1); 
    padding: 3rem 2rem; 
    border-radius: var(--radius-lg); 
    position: relative;
    transition: var(--ease-smooth) 0.4s;
}
.journey-card:hover { background: rgba(255, 255, 255, 0.07); transform: translateY(-10px); border-color: var(--primary); }
.journey-step { 
    font-size: 4rem; font-weight: 900; line-height: 1; margin-bottom: 1.5rem;
    background: linear-gradient(180deg, var(--primary) 0%, transparent 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    opacity: 0.4;
}
.journey-card h5 { font-size: 1.4rem; color: #fff; margin-bottom: 0.75rem; font-weight: 800; }
.journey-card p { color: #94a3b8; font-size: 0.95rem; }

/* ================= 4. REFINED NEWS SECTION ================= */
.news-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2.5rem; margin-top: 3rem; }
.news-card { 
    background: #fff; border-radius: var(--radius-lg); overflow: hidden; 
    box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: 0.4s var(--ease-smooth);
    display: flex; flex-direction: column; height: 100%; text-decoration: none;
}
.news-card:hover { transform: translateY(-12px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
.news-thumb { aspect-ratio: 16/10; overflow: hidden; position: relative; }
.news-thumb img, .news-thumb video { width: 100%; height: 100%; object-fit: cover; transition: 0.8s; }
.news-card:hover .news-thumb img { transform: scale(1.1); }
.news-content { padding: 2rem; flex: 1; display: flex; flex-direction: column; }
.news-meta { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--primary); margin-bottom: 0.8rem; letter-spacing: 1px; }
.news-card h4 { font-size: 1.35rem; font-weight: 800; line-height: 1.3; margin-bottom: 1rem; color: var(--primary-dark); }
.news-card p { font-size: 0.95rem; color: var(--text-muted); margin-bottom: 1.5rem; }
.news-link { margin-top: auto; font-weight: 800; color: var(--primary-dark); display: flex; align-items: center; gap: 8px; font-size: 0.9rem; }

/* Responsive Fixes */
@media (max-width: 991px) {
    .about-grid { grid-template-columns: 1fr; }
    .hero { text-align: center; }
    .hero-cta-group { justify-content: center; }
    .hero-overlay { background: radial-gradient(circle at center, rgba(2, 44, 16, 0.85) 0%, #022c10 100%); }
}
</style>

<section class="hero">
    <img src="/assets/images/Re-logo.jpeg" class="hero-bg" alt="ReSEED">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <span class="hero-badge">South Sudan Community Movement</span>
            <h1>Restoring Nature.<br><span>Reseeding Prosperity.</span></h1>
            <p class="lead" style="font-size: 1.25rem; opacity: 0.9; margin-bottom: 3rem; font-weight: 400;">A community-led movement transforming fragile landscapes into resilient ecosystems and sustainable livelihoods.</p>
            
            <div class="hero-cta-group">
                <a href="#about" class="btn btn-primary">Our Mission</a>
                <a href="#get-involved" class="btn btn-outline">Join the Movement</a>
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
                <span style="color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.8rem; display: block; margin-bottom: 1rem;">The ReSEED Story</span>
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

<section class="section" style="background: #0f172a; color: white;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title" style="background: linear-gradient(90deg, #fff, #94a3b8); -webkit-background-clip: text;">The Path to Prosperity</h2>
            <p style="color: #94a3b8; max-width: 600px; margin: 0 auto;">Our strategy moves communities from immediate survival to generational wealth.</p>
        </div>
        <div class="journey-row">
            <div class="journey-card" data-aos="fade-up" data-aos-delay="100">
                <div class="journey-step">01</div>
                <h5>Stabilize</h5>
                <p>Relief and emergency stabilization for zones affected by climate shocks.</p>
            </div>
            <div class="journey-card" data-aos="fade-up" data-aos-delay="200">
                <div class="journey-step">02</div>
                <h5>Restore</h5>
                <p>Providing regenerative tools and training to fix degraded soil health.</p>
            </div>
            <div class="journey-card" data-aos="fade-up" data-aos-delay="300">
                <div class="journey-step">03</div>
                <h5>Sustain</h5>
                <p>Scaling climate-smart production and building local market connections.</p>
            </div>
            <div class="journey-card" data-aos="fade-up" data-aos-delay="400">
                <div class="journey-step">04</div>
                <h5>Thrive</h5>
                <p>Achieving total food sovereignty and community-owned financial wealth.</p>
            </div>
        </div>
    </div>
</section>

<?php
$stmt = $pdo->prepare("SELECT title, slug, excerpt, cover_media, media_type, published_at FROM posts WHERE published_at IS NOT NULL ORDER BY published_at DESC LIMIT 3");
$stmt->execute();
$latestPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="section bg-light">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem;">
            <div>
                <h2 class="section-title">Field Stories</h2>
                <p class="text-muted">Direct updates from our restoration sites.</p>
            </div>
            <a href="/blog.php" style="color: var(--primary); font-weight: 800; text-decoration: none;">View All <i class="fa-solid fa-arrow-right-long ms-2"></i></a>
        </div>

        <div class="news-grid">
            <?php if (!empty($latestPosts)): foreach ($latestPosts as $post): 
                $mediaUrl = $post['cover_media'] ?: 'https://via.placeholder.com/800x500';
                $url = '/post.php?slug=' . urlencode($post['slug']);
            ?>
                <a href="<?= $url ?>" class="news-card" data-aos="fade-up">
                    <div class="news-thumb">
                        <?php if (($post['media_type'] ?? 'image') === 'video'): ?>
                            <video muted autoplay loop playsinline><source src="<?= htmlspecialchars($mediaUrl) ?>" type="video/mp4"></video>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($mediaUrl) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="news-content">
                        <span class="news-meta"><?= date('M j, Y', strtotime($post['published_at'])) ?></span>
                        <h4><?= htmlspecialchars($post['title']) ?></h4>
                        <p><?= htmlspecialchars(mb_strimwidth($post['excerpt'], 0, 100, '...')) ?></p>
                        <span class="news-link">Read Story <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            <?php endforeach; else: ?>
                <p class="text-muted">No stories found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="get-involved" class="section" style="background: #022c10; color: white; overflow: hidden;">
    <img src="/assets/images/Re-logo.jpeg" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.15;" alt="CTA">
    <div class="container" style="text-align: center; max-width: 800px;">
        <span style="color: var(--accent); font-weight: 800; text-transform: uppercase; letter-spacing: 3px; font-size: 0.8rem;">Scale Our Impact</span>
        <h2 style="color: white; font-size: clamp(2.5rem, 5vw, 4rem); margin: 1.5rem 0;">Let's Restore the Land Together</h2>
        <p class="lead mb-5">Join our network of partners transforming South Sudan’s landscapes for a resilient future.</p>
        <button class="btn btn-primary open-contact-modal" style="padding: 20px 60px; font-size: 1.1rem;">Partner With Us</button>
    </div>
</section>

<script>
    AOS.init({ once: true, duration: 1000, easing: 'ease-out-cubic' });

    // Accordion Logic
    document.querySelectorAll('.acc-trigger').forEach(btn => {
        btn.addEventListener('click', () => {
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('i');
            const isOpen = content.style.maxHeight;

            document.querySelectorAll('.acc-content').forEach(c => c.style.maxHeight = null);
            document.querySelectorAll('.acc-trigger i').forEach(i => i.className = 'fa-solid fa-plus small');

            if (!isOpen) {
                content.style.maxHeight = content.scrollHeight + "px";
                icon.className = 'fa-solid fa-minus small';
            }
        });
    });
</script>

<?php include dirname(__DIR__) . '/backend/includes/footer.php'; ?>