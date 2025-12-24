<?php
// backend/admin/includes/admin_footer.php
?>
        </section> <footer class="app-footer">
            <div class="footer-content">
                <span>© <?= date('Y') ?> <strong>ReSEED</strong> Admin Panel</span>
                <span class="footer-tag">Refined Control • v2.0</span>
            </div>
        </footer>
    </main> </div> <script>
(() => {
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobile-toggle');
    const body = document.body;

    // 1. Mobile Sidebar Toggle
    if (mobileToggle && sidebar) {
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });
    }

    // 2. Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 1024 && 
            sidebar.classList.contains('open') && 
            !sidebar.contains(e.target) && 
            e.target !== mobileToggle) {
            sidebar.classList.remove('open');
        }
    });

    // 3. Smooth fade-in for page content
    const container = document.querySelector('.container-fluid');
    if (container) {
        container.style.opacity = '0';
        container.style.transition = 'opacity 0.4s ease-in-out';
        setTimeout(() => container.style.opacity = '1', 50);
    }

    // 4. Contact Badge Auto-Refresh (Optional)
    async function refreshContactBadge() {
        try {
            const res = await fetch('ajax/contact_count.php');
            if (res.ok) {
                const data = await res.json();
                const badge = document.querySelector('.badge');
                if (badge) {
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? 'block' : 'none';
                }
            }
        } catch (err) { /* silent fail */ }
    }
    // setInterval(refreshContactBadge, 60000); 
})();
</script>

<style>
    .app-footer {
        margin-top: auto;
        padding: 24px 32px;
        border-top: 1px solid #e2e8f0;
        background: white;
    }
    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #64748b;
        font-size: 13px;
    }
    .footer-tag {
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 11px;
        opacity: 0.7;
    }

    /* Mobile specific footer tweaks */
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }
    }

    /* Custom Scrollbar for modern look */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { 
        background: #cbd5e1; 
        border-radius: 10px; 
    }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

</body>
</html>