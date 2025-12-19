<div id="contactModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-card">
        <button class="modal-close" aria-label="Close modal">&times;</button>
        <div class="modal-header">
            <h3>Get in Touch</h3>
            <p>Have a question or want to partner with us? We'd love to hear from you.</p>
        </div>
        <form action="/reseed/api/contact-handler.php" method="POST" class="contact-form">
            <div class="form-group">
                <input type="text" name="name" id="name" required placeholder=" ">
                <label for="name">Your Name</label>
            </div>
            <div class="form-group">
                <input type="email" name="email" id="email" required placeholder=" ">
                <label for="email">Your Email</label>
            </div>
            <div class="form-group">
                <textarea name="message" id="message" rows="4" required placeholder=" "></textarea>
                <label for="message">How can we help?</label>
            </div>
            <button type="submit" class="nav-donate" style="width: 100%; border: none; cursor: pointer; padding: 1rem;">
                Send Message
            </button>
        </form>
    </div>
</div>

<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="/reseed/assets/images/Re-logo.png" alt="ReSEED Logo">
                    <span>ReSEED</span>
                </div>
                <p>Restoring livelihoods, regenerating ecosystems, and rebuilding communities across South Sudan through sustainable intervention.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter-x"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>

            <div class="footer-links">
                <h4>Explore</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="projects.php">Our Projects</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="blog.php">Latest News</a></li>
                    <li><a href="#donate">Donate</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>Contact Us</h4>
                <ul>
                    <li><i class="fa-solid fa-location-dot"></i> Juba, South Sudan</li>
                    <li><i class="fa-solid fa-envelope"></i> info@reseed-ss.org</li>
                    <li><i class="fa-solid fa-phone"></i> +211 912 345 678</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="bottom-left">
                <p>&copy; <span id="year"></span> ReSEED. All rights reserved.</p>
                <a href="/reseed/admin/login.php" class="admin-link">
                    <i class="fa-solid fa-lock"></i> Admin Access
                </a>
            </div>
            <button id="backToTop" class="back-to-top" aria-label="Back to top">
                <i class="fa-solid fa-arrow-up"></i>
            </button>
        </div>
    </div>
</footer>

<style>
/* --- MODAL CSS --- */
.modal-overlay {
    position: fixed; 
    inset: 0; 
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(8px); 
    z-index: 9999; /* Must be higher than header */
    display: none; 
    align-items: center; 
    justify-content: center;
    padding: 1rem;
    opacity: 0; 
    transition: opacity 0.3s ease;
}
.modal-overlay.open { display: flex; opacity: 1; }

.modal-card {
    background: white; 
    width: 100%; 
    max-width: 480px; 
    padding: clamp(1.5rem, 5vw, 2.5rem);
    border-radius: 24px; 
    position: relative;
    transform: translateY(30px); 
    transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
.modal-overlay.open .modal-card { transform: translateY(0); }

.modal-close {
    position: absolute; top: 1.25rem; right: 1.25rem;
    background: #f1f5f9; border: none; width: 36px; height: 36px; 
    border-radius: 50%; font-size: 1.5rem; cursor: pointer; color: #64748b;
    display: flex; align-items: center; justify-content: center; transition: 0.2s;
}
.modal-close:hover { background: #e2e8f0; color: #0f172a; }

.modal-header { text-align: center; margin-bottom: 2rem; }
.modal-header h3 { font-family: 'Merriweather', serif; font-size: 1.75rem; color: #0f172a; margin-bottom: 0.5rem; }
.modal-header p { color: #64748b; font-size: 0.95rem; }

/* --- FORM STYLING --- */
.form-group { position: relative; margin-bottom: 1.25rem; }
.form-group input, .form-group textarea {
    width: 100%; padding: 1.1rem 1rem; border: 1.5px solid #e2e8f0; border-radius: 12px;
    font-family: inherit; outline: none; transition: 0.3s; background: #f8fafc; font-size: 1rem;
}
.form-group input:focus, .form-group textarea:focus { border-color: var(--green); background: white; box-shadow: 0 0 0 4px rgba(15, 140, 4, 0.1); }
.form-group label {
    position: absolute; left: 1rem; top: 1.1rem; color: #94a3b8; pointer-events: none;
    transition: 0.2s; font-size: 0.95rem;
}
.form-group input:focus ~ label, .form-group input:not(:placeholder-shown) ~ label,
.form-group textarea:focus ~ label, .form-group textarea:not(:placeholder-shown) ~ label {
    top: -0.6rem; left: 0.8rem; background: white; padding: 0 0.5rem; font-size: 0.75rem; color: var(--green); font-weight: 700;
}

/* --- FOOTER CSS --- */
.site-footer {
    background-color: #0e3116;
    color: #cbd5e1;
    padding-top: 5rem;
    border-top: 1px solid rgba(255,255,255,0.05);
}
.footer-top {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr;
    gap: 4rem;
    padding-bottom: 4rem;
}
.footer-logo { display: flex; align-items: center; gap: 0.8rem; color: #fff; font-family: 'Merriweather', serif; font-weight: 900; font-size: 1.5rem; margin-bottom: 1.5rem; }
.footer-logo img { width: 50px; height: 50px; border-radius: 50%; }
.footer-brand p { line-height: 1.7; opacity: 0.8; max-width: 35ch; margin-bottom: 2rem; }

.footer-links h4, .footer-contact h4 { color: #fff; margin-bottom: 1.5rem; font-size: 1.1rem; letter-spacing: 0.05em; text-transform: uppercase; }
.footer-links ul, .footer-contact ul { list-style: none; }
.footer-links li { margin-bottom: 0.8rem; }
.footer-links a { color: #cbd5e1; transition: 0.3s; }
.footer-links a:hover { color: #fff; padding-left: 5px; }
.footer-contact li { display: flex; gap: 1rem; margin-bottom: 1rem; align-items: flex-start; }
.footer-contact i { color: var(--green); margin-top: 4px; }

.social-links { display: flex; gap: 1rem; }
.social-links a {
    width: 42px; height: 42px; background: rgba(255,255,255,0.05);
    display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #fff; transition: 0.3s;
}
.social-links a:hover { background: var(--green); transform: translateY(-3px); }

.footer-bottom {
    padding: 2rem 0;
    border-top: 1px solid rgba(255,255,255,0.05);
    display: flex; justify-content: space-between; align-items: center;
}
.bottom-left { display: flex; align-items: center; gap: 2rem; font-size: 0.9rem; }
.admin-link { color: #64748b; transition: 0.3s; padding-left: 2rem; border-left: 1px solid rgba(255,255,255,0.1); }
.admin-link:hover { color: #ef4444; }

.back-to-top {
    width: 45px; height: 45px; border-radius: 50%; border: none;
    background: var(--green); color: white; cursor: pointer; transition: 0.3s;
}
.back-to-top:hover { background: var(--green-dark); transform: translateY(-5px); }

@media (max-width: 992px) {
    .footer-top { grid-template-columns: 1fr 1fr; }
    .footer-brand { grid-column: span 2; }
}
@media (max-width: 600px) {
    .footer-top { grid-template-columns: 1fr; }
    .footer-brand { grid-column: span 1; }
    .footer-bottom { flex-direction: column; gap: 1.5rem; text-align: center; }
    .bottom-left { flex-direction: column; gap: 1rem; }
    .admin-link { border: none; padding: 0; }
}
</style>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
(() => {
    'use strict';

    /* ======================================================
       DOM READY
    ====================================================== */
    document.addEventListener('DOMContentLoaded', () => {
        initAOS();
        initNavigation();
        initHeaderScroll();
        initModal();
        initUtilities();
        initContactForm();
    });

    /* ======================================================
       1. AOS
    ====================================================== */
    function initAOS() {
        if (window.AOS) {
            AOS.init({
                once: true,
                duration: 800,
                easing: 'ease-out-cubic'
            });
        }
    }

    /* ======================================================
       2. NAVIGATION & MOBILE MENU
    ====================================================== */
    function initNavigation() {
        const header      = document.querySelector('.site-header');
        const navToggle   = document.querySelector('.nav-toggle');
        const mainNav     = document.querySelector('.main-nav');
        const backdrop    = document.querySelector('.nav-backdrop');
        const navLinks    = document.querySelectorAll('.nav-link');

        if (!navToggle || !mainNav) return;

        const icon = navToggle.querySelector('i');

        const openMenu = () => {
            mainNav.classList.add('active');
            backdrop?.classList.add('active');
            icon?.classList.replace('fa-bars', 'fa-xmark');
            document.body.style.overflow = 'hidden';
        };

        const closeMenu = () => {
            mainNav.classList.remove('active');
            backdrop?.classList.remove('active');
            icon?.classList.replace('fa-xmark', 'fa-bars');
            document.body.style.overflow = '';
        };

        const toggleMenu = (e) => {
            e?.preventDefault();
            mainNav.classList.contains('active') ? closeMenu() : openMenu();
        };

        navToggle.addEventListener('click', toggleMenu);
        backdrop?.addEventListener('click', closeMenu);

        navLinks.forEach(link =>
            link.addEventListener('click', () => {
                if (mainNav.classList.contains('active')) closeMenu();
            })
        );
    }

    /* ======================================================
       3. HEADER SCROLL EFFECT
    ====================================================== */
    function initHeaderScroll() {
        const header = document.querySelector('.site-header');
        if (!header) return;

        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 50);
        });
    }

    /* ======================================================
       4. MODAL (CONTACT)
    ====================================================== */
    function initModal() {
        const modal     = document.getElementById('contactModal');
        const openBtns  = document.querySelectorAll('.open-contact-modal');
        const closeBtn  = modal?.querySelector('.modal-close');

        if (!modal) return;

        const openModal = (e) => {
            e.preventDefault();
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(() => modal.classList.add('open'));
        };

        const closeModal = () => {
            modal.classList.remove('open');
            document.body.style.overflow = '';
            setTimeout(() => modal.style.display = 'none', 300);
        };

        openBtns.forEach(btn => btn.addEventListener('click', openModal));
        closeBtn?.addEventListener('click', closeModal);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    }

    /* ======================================================
       5. UTILITIES
    ====================================================== */
    function initUtilities() {
        // Dynamic Year
        const yearEl = document.getElementById('year');
        if (yearEl) yearEl.textContent = new Date().getFullYear();

        // Back to Top
        const btt = document.getElementById('backToTop');
        if (!btt) return;

        window.addEventListener('scroll', () => {
            const visible = window.scrollY > 400;
            btt.style.opacity = visible ? '1' : '0';
            btt.style.pointerEvents = visible ? 'auto' : 'none';
        });

        btt.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ======================================================
       6. CONTACT FORM (AJAX)
    ====================================================== */
    function initContactForm() {
        const form     = document.getElementById('contactForm');
        const statusEl = document.getElementById('contactFormStatus');

        if (!form || !statusEl) return;

        const COLORS = {
            info: '#64748b',
            success: '#16a34a',
            error: '#dc2626'
        };

        const setStatus = (msg, type = 'info') => {
            statusEl.textContent = msg;
            statusEl.style.color = COLORS[type] || COLORS.info;
        };

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            setStatus('Sending message...', 'info');

            try {
                const res = await fetch('/reseed/backend/api/contact-handler.php', {
                    method: 'POST',
                    body: new FormData(form)
                });

                if (!res.ok) throw new Error('Request failed');

                const data = await res.json();

                if (data.success) {
                    setStatus(data.message || 'Message sent successfully.', 'success');
                    form.reset();
                } else {
                    setStatus(data.message || 'Something went wrong.', 'error');
                }
            } catch (err) {
                console.error('Contact form error:', err);
                setStatus('Network error. Please try again later.', 'error');
            }
        });
    }

})();
</script>
