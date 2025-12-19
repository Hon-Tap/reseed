<?php
// includes/admin_footer.php
?>

        </section>

        <!-- ===================== FOOTER ===================== -->
        <footer class="app-footer">
            © <?= date('Y') ?> ReSEED Admin · Built for clarity & control
        </footer>

    </main>
</div>

<script>
/* =========================================================
   RESEED ADMIN UI ENGINE
   (Footer Scoped — Safe & Predictable)
   ========================================================= */
(() => {
  const sidebar   = document.getElementById('sidebar');
  const hamburger = document.getElementById('hamburger');
  const overlay   = document.getElementById('overlay');

  if (!sidebar || !hamburger) return;

  const MOBILE = 900;
  const STORE  = 'reseed_sidebar_collapsed';

  /* =====================
     INITIAL STATE
  ===================== */
  if (window.innerWidth > MOBILE) {
    sidebar.classList.toggle(
      'collapsed',
      localStorage.getItem(STORE) === 'true'
    );
  }

  /* =====================
     SIDEBAR ACTIONS
  ===================== */
  function openMobile() {
    sidebar.classList.add('open');
    overlay?.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeMobile() {
    sidebar.classList.remove('open');
    overlay?.classList.remove('show');
    document.body.style.overflow = '';
  }

  function toggleDesktop() {
    const collapsed = sidebar.classList.toggle('collapsed');
    localStorage.setItem(STORE, String(collapsed));
  }

  hamburger.addEventListener('click', () => {
    window.innerWidth <= MOBILE ? openMobile() : toggleDesktop();
  });

  overlay?.addEventListener('click', closeMobile);

  /* =====================
     KEYBOARD SUPPORT
  ===================== */
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeMobile();
  });

  /* =====================
     TOUCH GESTURES
  ===================== */
  let startX = 0;

  document.addEventListener('touchstart', e => {
    if (e.touches.length === 1) {
      startX = e.touches[0].clientX;
    }
  }, { passive: true });

  document.addEventListener('touchend', e => {
    if (!startX) return;

    const endX = e.changedTouches[0].clientX;
    const diff = endX - startX;

    if (Math.abs(diff) < 80) return;

    if (diff > 0 && startX < 40) openMobile();
    if (diff < 0) closeMobile();

    startX = 0;
  }, { passive: true });

  /* =====================
     LIVE CONTACT BADGE
  ===================== */
  async function refreshContactBadge() {
    try {
      const res = await fetch('ajax/contact_count.php', { cache: 'no-store' });
      if (!res.ok) return;

      const { count } = await res.json();
      const badge = document.querySelector('.nav .badge');
      if (!badge) return;

      badge.textContent = count;
      badge.style.display = count > 0 ? 'inline-flex' : 'none';
    } catch (_) {}
  }

  setInterval(refreshContactBadge, 30000);
})();
</script>

<style>
/* ===================== FOOTER ===================== */
.app-footer{
  padding:16px 24px;
  font-size:13px;
  color:#64748b;
  text-align:center;
  background:transparent;
}
</style>

</body>
</html>
