</div> <footer class="app-footer">
            <div class="footer-content">
                <div class="copyright">
                    &copy; <?= date('Y') ?> <strong>ReSEED</strong> Admin System
                </div>
                <div class="server-status">
                    <span class="dot"></span> System Operational
                </div>
            </div>
        </footer>

    </main> </div> <style>
    .app-footer {
        background: var(--bg-surface);
        border-top: 1px solid var(--border-color);
        padding: 20px 24px;
        margin-top: auto;
    }
    
    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--text-muted);
        font-size: 13px;
    }

    .server-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ecfdf5;
        color: #15803d;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 600;
    }

    .dot {
        width: 6px;
        height: 6px;
        background: #22c55e;
        border-radius: 50%;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
    }

    @media(max-width: 640px) {
        .footer-content { flex-direction: column; gap: 10px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('mobileOverlay');
        const body = document.body;
        const DESKTOP_BREAKPOINT = 992;
        const STORE_KEY = 'reseed_sidebar_state';

        // 1. Check LocalStorage on Load (Desktop Only)
        // This prevents the sidebar from flashing expanded then collapsing on refresh
        const savedState = localStorage.getItem(STORE_KEY);
        if (window.innerWidth >= DESKTOP_BREAKPOINT && savedState === 'collapsed') {
            body.classList.add('sidebar-collapsed');
        }

        // 2. Toggle Function
        function handleToggle() {
            const isDesktop = window.innerWidth >= DESKTOP_BREAKPOINT;

            if (isDesktop) {
                // Desktop: Toggle Class + Save State
                body.classList.toggle('sidebar-collapsed');
                const newState = body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded';
                localStorage.setItem(STORE_KEY, newState);
            } else {
                // Mobile: Toggle Overlay Class
                body.classList.toggle('mobile-open');
            }
        }

        // 3. Event Listeners
        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                handleToggle();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                body.classList.remove('mobile-open');
            });
        }

        // 4. Handle Resize Events to clean up classes
        window.addEventListener('resize', () => {
            if (window.innerWidth >= DESKTOP_BREAKPOINT) {
                // If we grew to desktop, remove mobile classes
                body.classList.remove('mobile-open');
            } else {
                // If we shrank to mobile, remove desktop collapse classes
                body.classList.remove('sidebar-collapsed');
            }
        });
    });
</script>

</body>
</html>