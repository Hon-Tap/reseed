        </div> <!-- /page-content -->

        <footer class="app-footer">
            <div class="footer-inner">
                <div class="footer-left">
                    &copy; <?= date('Y') ?> <strong>ReSEED</strong>
                    <span class="footer-separator">•</span>
                    Admin System
                </div>

                <div class="footer-right" title="System status">
                    <span class="status-dot" aria-hidden="true"></span>
                    <span class="status-text">Operational</span>
                </div>
            </div>
        </footer>

    </main>
</div>
<style>
/* ===============================
   Footer
================================ */
.app-footer {
    margin-top: auto;
    padding: 18px 28px;
    background: var(--bg-surface);
    border-top: 1px solid var(--border);
}

.footer-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    font-size: 13px;
    color: var(--text-muted);
}

.footer-left strong {
    color: var(--text-main);
}

.footer-separator {
    margin: 0 6px;
    opacity: .4;
}

.footer-right {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 999px;
    background: #f1f5f9;
    font-size: 11px;
    font-weight: 600;
    color: var(--primary-dark);
    white-space: nowrap;
}

/* Status indicator */
.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--accent);
    box-shadow: 0 0 0 rgba(34,197,94,.6);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%   { transform: scale(.9); box-shadow: 0 0 0 0 rgba(34,197,94,.6); }
    70%  { transform: scale(1);  box-shadow: 0 0 0 6px rgba(34,197,94,0); }
    100% { transform: scale(.9); box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}

@media (max-width: 640px) {
    .footer-inner {
        flex-direction: column;
        text-align: center;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const body        = document.body;
    const toggleBtn   = document.getElementById('sidebarToggle');
    const overlay     = document.getElementById('overlay');

    const DESKTOP_BP  = 992;
    const STORAGE_KEY = 'sidebar-state';

    /* Restore sidebar state (desktop only) */
    if (
        window.innerWidth >= DESKTOP_BP &&
        localStorage.getItem(STORAGE_KEY) === 'collapsed'
    ) {
        body.classList.add('sidebar-collapsed');
    }

    const toggleSidebar = () => {
        if (window.innerWidth >= DESKTOP_BP) {
            body.classList.toggle('sidebar-collapsed');
            localStorage.setItem(
                STORAGE_KEY,
                body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded'
            );
        } else {
            body.classList.toggle('mobile-open');
        }
    };

    toggleBtn?.addEventListener('click', e => {
        e.stopPropagation();
        toggleSidebar();
    });

    overlay?.addEventListener('click', () => {
        body.classList.remove('mobile-open');
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= DESKTOP_BP) {
            body.classList.remove('mobile-open');
        }
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            body.classList.remove('mobile-open');
        }
    });
});
</script>

</body>
</html>
