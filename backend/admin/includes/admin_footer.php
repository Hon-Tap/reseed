</div> <footer class="app-footer">
            <div class="footer-content">
                <div class="copyright">
                    &copy; <?= date('Y') ?> <strong>ReSEED</strong>. All rights reserved.
                </div>
                <div class="version-tag">
                    <span class="pulse-dot"></span> System Operational
                </div>
            </div>
        </footer>
    </main> 
</div> <style>
    .app-footer { 
        margin-top: auto; 
        padding: 24px 32px; 
        background: var(--bg-surface); 
        border-top: 1px solid var(--border);
    }
    .footer-content { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        color: var(--text-muted); 
        font-size: 13px; 
    }
    .version-tag { 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        background: #f1f5f9; 
        padding: 6px 12px; 
        border-radius: 20px; 
        font-size: 11px; 
        font-weight: 600; 
        color: var(--primary-dark);
    }
    .pulse-dot { 
        width: 8px; 
        height: 8px; 
        background-color: var(--accent); 
        border-radius: 50%; 
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        animation: pulse-green 2s infinite; 
    }
    
    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    @media (max-width: 600px) {
        .footer-content { flex-direction: column; gap: 12px; text-align: center; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay   = document.getElementById('overlay');
    const body      = document.body;

    // Load initial state
    if (localStorage.getItem('sidebar-state') === 'collapsed' && window.innerWidth >= 992) {
        body.classList.add('sidebar-collapsed');
    }

    const toggleSidebar = () => {
        if (window.innerWidth >= 992) {
            // Desktop toggle
            body.classList.toggle('sidebar-collapsed');
            const state = body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded';
            localStorage.setItem('sidebar-state', state);
        } else {
            // Mobile toggle
            body.classList.toggle('mobile-open');
        }
    };

    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            body.classList.remove('mobile-open');
        });
    }

    // Auto-close mobile menu on resize if moving to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992 && body.classList.contains('mobile-open')) {
            body.classList.remove('mobile-open');
        }
    });
});
</script>
</body>
</html>