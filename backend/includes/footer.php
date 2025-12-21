<div id="contactModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-card">
        <button class="modal-close" aria-label="Close modal">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <div class="modal-header">
            <h3>Get in Touch</h3>
            <p>Have a question or want to partner with us? We'd love to hear from you.</p>
        </div>

        <form action="/api/contact.php" method="POST" class="contact-form" id="contactForm">
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

            <div id="contactFormStatus" class="contact-status"></div>

            <button type="submit" class="btn btn-primary btn-block">
                <span>Send Message</span>
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<footer class="site-footer">
    <div class="container">
        
        <div class="footer-top">
            
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="/assets/images/Re-logo.jpeg" alt="ReSEED Logo" loading="lazy">
                    <span>ReSEED</span>
                </div>
                <p>Restoring livelihoods, regenerating ecosystems, and rebuilding communities across South Sudan through sustainable intervention.</p>
                
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://x.com/yourhandle" target="_blank" rel="noopener" aria-label="Follow us on X (formerly Twitter)"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>

            <div class="footer-widget">
                <h4>Explore</h4>
                <ul class="footer-nav">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="projects.php">Our Projects</a></li>
                    <li><a href="gallery.php">Impact Gallery</a></li>
                    <li><a href="blog.php">Latest News</a></li>
                    <li><a href="https://api.reseed.org/donate" target="_blank">Donate Now</a></li>
                </ul>
            </div>

            <div class="footer-widget">
                <h4>Contact Us</h4>
                <ul class="footer-contact-list">
                    <li>
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Juba, South Sudan</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-envelope"></i>
                        <a href="mailto:info@reseed-ss.org">info@reseed-ss.org</a>
                    </li>
                    <li>
                        <i class="fa-solid fa-phone"></i>
                        <a href="tel:+211912345678">+211 912 345 678</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="copyright">
                <p>&copy; <span id="year"></span> ReSEED. All rights reserved.</p>
                <span class="separator">•</span>
                <a href="/privacy.php">Privacy</a>
                <span class="separator">•</span>
                <a href="/admin.php" class="admin-link"><i class="fa-solid fa-lock"></i> Staff</a>
            </div>

            <button id="backToTop" class="back-to-top" aria-label="Back to top">
                <i class="fa-solid fa-arrow-up"></i>
            </button>
        </div>
    </div>
</footer>

<style>
    /* --- FOOTER VARIABLES --- */
    :root {
        --footer-bg: #052e16; /* Very Dark Green */
        --footer-text: #cbd5e1; /* Slate 300 */
        --footer-heading: #ffffff;
        --input-bg: #f8fafc;
        --input-border: #e2e8f0;
    }

    /* --- MODAL STYLES --- */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(15, 42, 15, 0.6); /* Slate 900 with opacity */
        backdrop-filter: blur(8px);
        z-index: 2000;
        display: flex;
        align-items: center; justify-content: center;
        opacity: 0; pointer-events: none;
        transition: opacity 0.3s ease;
        padding: 1rem;
    }
    .modal-overlay.open { opacity: 1; pointer-events: all; }

    .modal-card {
        background: #fff;
        width: 100%; max-width: 500px;
        padding: 2.5rem;
        border-radius: var(--radius-lg);
        position: relative;
        transform: translateY(20px);
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: var(--shadow-lg);
    }
    .modal-overlay.open .modal-card { transform: translateY(0); }

    .modal-close {
        position: absolute; top: 1.5rem; right: 1.5rem;
        background: var(--bg-body); border: none;
        width: 36px; height: 36px; border-radius: 50%;
        color: var(--text-muted); cursor: pointer; font-size: 1.1rem;
        transition: 0.2s;
    }
    .modal-close:hover { background: #fee2e2; color: #ef4444; }

    .modal-header { text-align: center; margin-bottom: 2rem; }
    .modal-header h3 { font-size: 1.75rem; color: var(--color-heading); margin-bottom: 0.5rem; }
    .modal-header p { color: var(--text-muted); font-size: 0.95rem; }

    /* Floating Label Forms */
    .form-group { position: relative; margin-bottom: 1.5rem; }
    
    .form-group input, .form-group textarea {
        width: 100%; padding: 1rem 1rem;
        border: 2px solid var(--input-border);
        border-radius: var(--radius-sm);
        background: var(--input-bg);
        font-family: inherit; font-size: 1rem; outline: none;
        transition: 0.3s;
    }
    
    .form-group input:focus, .form-group textarea:focus {
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 4px var(--color-primary-light);
    }

    .form-group label {
        position: absolute; left: 1rem; top: 1rem;
        color: var(--text-muted); pointer-events: none;
        transition: 0.2s; font-size: 0.95rem;
    }

    /* Floating Effect Logic */
    .form-group input:focus ~ label,
    .form-group input:not(:placeholder-shown) ~ label,
    .form-group textarea:focus ~ label,
    .form-group textarea:not(:placeholder-shown) ~ label {
        top: -0.6rem; left: 0.8rem;
        background: #fff; padding: 0 0.4rem;
        font-size: 0.75rem; color: var(--primary); font-weight: 700;
        border-radius: 4px;
    }

    .btn-block { width: 100%; padding: 1rem; font-size: 1rem; background-color: green; color: white; border: none; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: background-color 0.3s ease; }

    /* Status Messages */
    .contact-status {
        margin-bottom: 1rem; padding: 0.75rem; border-radius: var(--radius-sm);
        font-size: 0.9rem; text-align: center; display: none;
    }
    .contact-status.show { display: block; }
    .contact-status.success { background: #dcfce7; color: #166534; }
    .contact-status.error { background: #fee2e2; color: #991b1b; }
    .contact-status.loading { background: #f1f5f9; color: #475569; }

    /* --- FOOTER CSS --- */
    .site-footer {
        background-color: var(--footer-bg);
        color: var(--footer-text);
        padding-top: 5rem;
        margin-top: auto; /* Pushes footer to bottom if content is short */
    }

    .footer-top {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr; /* Brand is wider */
        gap: 4rem;
        padding-bottom: 4rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .footer-logo {
        display: flex; align-items: center; gap: 10px;
        font-family: 'Merriweather', serif; font-weight: 900;
        font-size: 1.5rem; color: var(--footer-heading);
        margin-bottom: 1rem;
    }
    .footer-logo img { width: 40px; height: 40px; border-radius: 50%; }

    .footer-brand p { line-height: 1.8; opacity: 0.8; max-width: 40ch; margin-bottom: 1.5rem; }

    .footer-widget h4 {
        color: var(--footer-heading); margin-bottom: 1.5rem;
        font-size: 1.1rem; letter-spacing: 0.5px; text-transform: uppercase;
    }

    /* Footer Links */
    .footer-nav li, .footer-contact-list li { margin-bottom: 0.8rem; }
    
    .footer-nav a {
        color: var(--footer-text); transition: 0.3s;
        display: inline-block;
    }
    .footer-nav a:hover {
        color: var(--accent);
        transform: translateX(5px);
    }

    .footer-contact-list li { display: flex; align-items: flex-start; gap: 1rem; }
    .footer-contact-list i { color: var(--primary); margin-top: 5px; }

    /* Social Icons */
    .social-links { display: flex; gap: 0.8rem; }
    .social-links a {
        width: 40px; height: 40px;
        background: rgba(255,255,255,0.05);
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; color: #fff; transition: 0.3s;
    }
    .social-links a:hover { background: var(--primary); transform: translateY(-3px); }

    /* Footer Bottom */
    .footer-bottom {
        padding: 2rem 0;
        display: flex; justify-content: space-between; align-items: center;
        font-size: 0.9rem;
    }

    .copyright { display: flex; align-items: center; gap: 0.5rem; opacity: 0.7; }
    .copyright a:hover { color: var(--accent); }
    .separator { opacity: 0.5; }

    /* Back to Top */
    .back-to-top {
        width: 45px; height: 45px;
        border-radius: 50%; border: none;
        background: var(--primary); color: white;
        cursor: pointer; opacity: 0; pointer-events: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        transform: translateY(10px);
    }
    .back-to-top.visible { opacity: 1; pointer-events: all; transform: translateY(0); }
    .back-to-top:hover { background: var(--accent); transform: translateY(-3px); }

    /* Mobile */
    @media (max-width: 992px) {
        .footer-top { grid-template-columns: 1fr 1fr; gap: 2rem; }
        .footer-brand { grid-column: span 2; }
    }
    @media (max-width: 600px) {
        .footer-top { grid-template-columns: 1fr; }
        .footer-brand { grid-column: span 1; }
        .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
        .copyright { flex-direction: column; gap: 0.2rem; }
        .separator { display: none; }
    }
</style>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    // --- Initialize AOS (Animations) ---
    AOS.init({
        once: true,
        duration: 800,
        offset: 50
    });

    // --- Dynamic Year ---
    document.getElementById('year').textContent = new Date().getFullYear();

    // --- Back to Top Logic ---
    const backToTopBtn = document.getElementById('backToTop');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 400) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });

    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // --- Modal Logic ---
    const modal = document.getElementById('contactModal');
    // Select all triggers (links in nav, footer, etc.)
    const openBtns = document.querySelectorAll('.open-contact-modal');
    const closeBtn = modal.querySelector('.modal-close');

    function toggleModal(show) {
        if (show) {
            modal.classList.add('open');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        } else {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    openBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            toggleModal(true);
        });
    });

    closeBtn.addEventListener('click', () => toggleModal(false));
    
    // Close on click outside card
    modal.addEventListener('click', (e) => {
        if (e.target === modal) toggleModal(false);
    });

    // --- AJAX Form Submission ---
    const form = document.getElementById('contactForm');
    const statusEl = document.getElementById('contactFormStatus');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // 1. Loading State
        submitBtn.disabled = true;
        submitBtn.querySelector('span').textContent = 'Sending...';
        statusEl.className = 'contact-status loading show';
        statusEl.textContent = 'Processing your message...';

        try {
            // 2. Send Data
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            // 3. Handle Response
            if (data.success) {
                statusEl.className = 'contact-status success show';
                statusEl.textContent = 'Message sent! We will get back to you soon.';
                form.reset();
                setTimeout(() => toggleModal(false), 2500); // Close after success
            } else {
                throw new Error(data.message || 'Something went wrong.');
            }

        } catch (error) {
            statusEl.className = 'contact-status error show';
            statusEl.textContent = error.message;
        } finally {
            // 4. Reset Button
            submitBtn.disabled = false;
            submitBtn.querySelector('span').textContent = 'Send Message';
        }
    });
</script>

</body>
</html>