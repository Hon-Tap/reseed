<?php
// backend/admin/includes/admin_footer.php
?>
        </div> <footer class="app-footer">
            <div class="footer-content">
                <div class="copyright">
                    <span>&copy; <?= date('Y') ?> <strong>ReSEED</strong> Admin Panel</span>
                </div>
                <div class="version-tag">
                    <span class="pulse-dot"></span> System v2.0
                </div>
            </div>
        </footer>
    </main> </div> <script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay   = document.getElementById('overlay');
    const body      = document.body;

    // 1. Desktop Persistent State
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
    if (isCollapsed && window.innerWidth >= 992) {
        body.classList.add('sidebar-collapsed');
    }

    // 2. The Click Event
    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.innerWidth >= 992) {
                body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', body.classList.contains('sidebar-collapsed'));
            } else {
                body.classList.toggle('mobile-open');
            }
        });
    }

    // 3. Overlay Close
    if (overlay) {
        overlay.addEventListener('click', () => {
            body.classList.remove('mobile-open');
        });
    }
});
</script>

<style>
    .app-footer { margin-top: auto; padding: 20px 32px; border-top: 1px solid #e2e8f0; background: #fff; }
    .footer-content { display: flex; justify-content: space-between; align-items: center; color: #64748b; font-size: 13px; }
    .version-tag { display: flex; align-items: center; gap: 8px; background: #f1f5f9; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .pulse-dot { width: 6px; height: 6px; background-color: #22c55e; border-radius: 50%; animation: pulse 2s infinite; }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
        70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
</style>
</body>
</html>