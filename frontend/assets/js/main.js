/* ------------------ refactor.js ------------------ */
/* Modern, defensive, accessible UI controllers (ES6) */

class HeaderController {
  constructor(headerId = "site-header", backToTopId = "backToTop") {
    this.header = document.getElementById(headerId);
    this.backToTopBtn = document.getElementById(backToTopId);
    this.scrollThreshold = 50;
    this.backToTopThreshold = 400;
    if (this.header) {
      this.onScroll = this.handleScroll.bind(this);
      window.addEventListener("scroll", this.onScroll, { passive: true });
      this.handleScroll(); // initial
    }
    if (this.backToTopBtn) {
      this.backToTopBtn.addEventListener("click", (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: "smooth" });
      });
    }
  }

  handleScroll() {
    if (!this.header) return;
    this.header.classList.toggle("scrolled", window.scrollY > this.scrollThreshold);
    if (this.backToTopBtn) {
      this.backToTopBtn.classList.toggle("visible", window.scrollY > this.backToTopThreshold);
    }
  }
}

class NavController {
  constructor(hamburgerId = "hamburger", navId = "main-nav", offcanvasId = "nav-offcanvas") {
    this.hamburger = document.getElementById(hamburgerId);
    this.desktopNav = document.getElementById(navId);
    this.offcanvas = document.getElementById(offcanvasId) || null;

    this.handleDocumentKey = this.handleDocumentKey.bind(this);
    this.handleOutsideClick = this.handleOutsideClick.bind(this);

    if (this.hamburger) {
      this.hamburger.addEventListener("click", this.toggleNav.bind(this));
      this.hamburger.setAttribute("aria-expanded", "false");
      this.hamburger.setAttribute("aria-controls", offcanvasId);
    }

    // Close nav when a link inside offcanvas is clicked
    const offLinks = this.offcanvas ? this.offcanvas.querySelectorAll("a") : [];
    offLinks.forEach(a => a.addEventListener("click", () => this.closeNav()));

    // Desktop dropdown keyboard accessibility
    this.initDropdownAccessibility();
    // Ensure donate duplicates gone
    this.dedupeCTAs();
    // Move about/approach into Who We Are dropdown (if present)
    this.rehomeWhoWeAreLinks();
  }

  toggleNav() {
    if (!this.offcanvas || !this.hamburger) return;
    const opening = !this.offcanvas.classList.contains("open");
    this.offcanvas.classList.toggle("open", opening);
    this.hamburger.setAttribute("aria-expanded", String(opening));
    if (opening) {
      document.body.style.overflow = "hidden";
      document.addEventListener("keydown", this.handleDocumentKey);
      document.addEventListener("click", this.handleOutsideClick);
      // focus first focusable element in offcanvas
      setTimeout(() => {
        const focusable = this.offcanvas.querySelector('a, button, input, [tabindex]:not([tabindex="-1"])');
        if (focusable) focusable.focus();
      }, 80);
    } else {
      document.body.style.overflow = "";
      document.removeEventListener("keydown", this.handleDocumentKey);
      document.removeEventListener("click", this.handleOutsideClick);
      this.hamburger.focus();
    }

    // Swap icon if present <i class="fas ...">
    const icon = this.hamburger.querySelector("i");
    if (icon) {
      icon.classList.toggle("fa-bars", !opening);
      icon.classList.toggle("fa-times", opening);
    }
  }

  closeNav() {
    if (!this.offcanvas || !this.hamburger) return;
    if (this.offcanvas.classList.contains("open")) {
      this.offcanvas.classList.remove("open");
      this.hamburger.setAttribute("aria-expanded", "false");
      document.body.style.overflow = "";
      document.removeEventListener("keydown", this.handleDocumentKey);
      document.removeEventListener("click", this.handleOutsideClick);
    }
  }

  handleDocumentKey(e) {
    if (e.key === "Escape") this.closeNav();
    if (e.key === "Tab" && this.offcanvas && this.offcanvas.classList.contains("open")) {
      // basic focus trap
      const focusable = Array.from(this.offcanvas.querySelectorAll('a, button, input, [tabindex]:not([tabindex="-1"])')).filter(n => !n.disabled);
      if (focusable.length === 0) return;
      const first = focusable[0], last = focusable[focusable.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  }

  handleOutsideClick(e) {
    if (!this.offcanvas) return;
    if (!this.offcanvas.contains(e.target) && !this.hamburger.contains(e.target)) {
      this.closeNav();
    }
  }

  initDropdownAccessibility() {
    // Attach accessible toggles to dropdowns
    const dropdowns = document.querySelectorAll(".dropdown");
    dropdowns.forEach(dd => {
      const toggle = dd.querySelector(".dropdown-toggle");
      const menu = dd.querySelector(".dropdown-menu");
      if (!toggle || !menu) return;
      toggle.setAttribute("role", "button");
      toggle.setAttribute("aria-expanded", "false");
      toggle.addEventListener("click", (e) => {
        const show = !menu.classList.contains("show");
        menu.classList.toggle("show", show);
        toggle.setAttribute("aria-expanded", String(show));
      });
      // keyboard support
      toggle.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          toggle.click();
        } else if (e.key === "Escape") {
          menu.classList.remove("show");
          toggle.setAttribute("aria-expanded", "false");
          toggle.focus();
        }
      });
      // close dropdown when clicking outside
      document.addEventListener("click", (e) => {
        if (!dd.contains(e.target)) {
          menu.classList.remove("show");
          toggle.setAttribute("aria-expanded", "false");
        }
      });
    });
  }

  dedupeCTAs() {
    // Remove duplicate donate buttons: prefer one with .donate-role or data-role="donate"
    const donateSelectors = '[data-role="donate"], .donate-role, .btn-donate';
    const donateNodes = Array.from(document.querySelectorAll(donateSelectors));
    if (donateNodes.length > 1) {
      // keep the first visible one, remove others
      let kept = null;
      for (const el of donateNodes) {
        if (!kept && el.offsetParent !== null) {
          kept = el;
          el.classList.add("donate-role");
        } else {
          el.remove();
        }
      }
    }
    // Ensure contact btn unique too
    const contactSelectors = '[data-role="contact"], .btn-contact';
    const contactNodes = Array.from(document.querySelectorAll(contactSelectors));
    if (contactNodes.length > 1) {
      // keep first
      let kept = null;
      for (const el of contactNodes) {
        if (!kept && el.offsetParent !== null) {
          kept = el;
        } else {
          el.remove();
        }
      }
    }
  }

  rehomeWhoWeAreLinks() {
    // Attempts to move #about-link and #approach-link into the "Who We Are" dropdown.
    // If HTML structure already contains dropdown, use it.
    try {
      const dropdown = document.querySelector('.dropdown[data-name="who-we-are"]') || document.querySelector('.dropdown#who-we-are') || document.querySelector('.nav-item.who-we-are .dropdown');
      if (!dropdown) return;
      const menu = dropdown.querySelector('.dropdown-menu');
      if (!menu) {
        // create menu if absent
        const newMenu = document.createElement('div');
        newMenu.className = 'dropdown-menu';
        dropdown.appendChild(newMenu);
      }
      const about = document.getElementById('about-link') || document.querySelector('[data-link="about"]');
      const approach = document.getElementById('approach-link') || document.querySelector('[data-link="approach"]');
      [about, approach].forEach(link => {
        if (link && link instanceof HTMLElement) {
          // move, remove duplicates
          const cloned = link.cloneNode(true);
          cloned.id = cloned.id ? cloned.id + "-in-dd" : null;
          // avoid adding separators if already present
          menu.appendChild(cloned);
          link.remove(); // remove original to avoid duplicates in nav
        }
      });
    } catch (err) {
      console.warn("rehomeWhoWeAreLinks: ", err);
    }
  }
}

class ThemeManager {
  constructor(toggleId = "theme-toggle") {
    this.toggle = document.getElementById(toggleId);
    this.icon = this.toggle ? this.toggle.querySelector("i") : null;
    if (this.toggle) {
      this.loadTheme();
      this.toggle.addEventListener("click", this.toggleTheme.bind(this));
    }
  }

  loadTheme() {
    const stored = localStorage.getItem("theme");
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = stored === "dark" || (stored === null && prefersDark);
    document.body.classList.toggle("dark", isDark);
    this.updateIcon(isDark);
  }

  toggleTheme() {
    const isDark = !document.body.classList.contains("dark");
    document.body.classList.toggle("dark", isDark);
    localStorage.setItem("theme", isDark ? "dark" : "light");
    this.updateIcon(isDark);
  }

  updateIcon(isDark) {
    if (!this.icon) return;
    // Prefer explicit toggle to replace classes reliably
    this.icon.classList.remove("fa-moon", "fa-sun");
    this.icon.classList.add(isDark ? "fa-sun" : "fa-moon");
  }
}

class ModalController {
  constructor(modalId, openSelector = ".open-modal", closeSelector = ".close-modal") {
    this.modal = document.getElementById(modalId);
    this.openBtns = Array.from(document.querySelectorAll(openSelector));
    this.closeBtn = this.modal ? this.modal.querySelector(closeSelector) : null;
    this.onKey = this.onKey.bind(this);
    if (this.modal) {
      this.openBtns.forEach(b => b.addEventListener("click", (e) => this.open(e)));
      if (this.closeBtn) this.closeBtn.addEventListener("click", () => this.close());
      this.modal.addEventListener("click", (e) => { if (e.target === this.modal) this.close(); });
    }
  }

  open(e) {
    if (e && e.preventDefault) e.preventDefault();
    if (!this.modal) return;
    this.modal.classList.add("open");
    document.body.style.overflow = "hidden";
    document.addEventListener("keydown", this.onKey);
    // focus first focusable in modal
    const focusable = this.modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable) focusable.focus();
  }

  close() {
    if (!this.modal) return;
    this.modal.classList.remove("open");
    document.body.style.overflow = "";
    document.removeEventListener("keydown", this.onKey);
  }

  onKey(e) {
    if (e.key === "Escape") this.close();
  }
}

class ContactFormHandler {
  constructor(formId = "contactForm", actionUrl = "/reseed/api/process-form.php") {
    this.form = document.getElementById(formId);
    this.actionUrl = actionUrl;
    if (this.form) {
      this.responseDiv = document.getElementById("form-response") || null;
      this.submitBtn = this.form.querySelector('button[type="submit"]') || null;
      this.form.addEventListener("submit", (e) => this.handleSubmit(e));
    }
  }

  async handleSubmit(e) {
    e.preventDefault();
    if (!this.form) return;
    try {
      const formData = new FormData(this.form);
      if (this.responseDiv) { this.responseDiv.style.display = 'none'; this.responseDiv.className = ''; }
      if (this.submitBtn) {
        this.submitBtn.disabled = true;
        this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
      }
      const res = await fetch(this.actionUrl, { method: "POST", body: formData, headers: { 'Accept': 'application/json' } });
      if (!res.ok) {
        const text = await res.text().catch(() => `HTTP ${res.status}`);
        throw new Error(text || `Server error ${res.status}`);
      }
      const json = await res.json().catch(() => ({ success: true, message: 'Sent (no JSON response)' }));
      if (json.success) {
        this.showResponse(json.message || 'Message sent', 'success');
        this.form.reset();
        // close modal if inside one
        const modal = this.form.closest('.modal');
        if (modal) setTimeout(() => modal.classList.remove('open'), 1400);
      } else {
        this.showResponse(json.message || 'Submission failed', 'error');
      }
    } catch (err) {
      console.error("ContactForm error:", err);
      this.showResponse(`Error: ${err.message}`, 'error');
    } finally {
      if (this.submitBtn) {
        this.submitBtn.disabled = false;
        this.submitBtn.textContent = 'Send Message';
      }
    }
  }

  showResponse(text, type = 'info') {
    if (!this.responseDiv) {
      alert(text);
      return;
    }
    this.responseDiv.textContent = text;
    this.responseDiv.className = `alert-${type}`;
    this.responseDiv.style.display = 'block';
  }
}

class AccordionManager {
  constructor(headerSelector = ".collapsible-header") {
    this.headers = Array.from(document.querySelectorAll(headerSelector));
    this.headers.forEach(h => {
      const targetId = h.getAttribute('aria-controls');
      const body = targetId ? document.getElementById(targetId) : null;
      if (body && h.getAttribute('aria-expanded') !== 'true') body.style.maxHeight = '0';
      h.addEventListener("click", () => this.toggle(h));
      h.addEventListener("keydown", (e) => { if (e.key === "Enter" || e.key === " ") { e.preventDefault(); this.toggle(h); }});
    });
  }
  toggle(header) {
    const id = header.getAttribute('aria-controls');
    const body = id ? document.getElementById(id) : null;
    if (!body) return;
    const expanded = header.getAttribute('aria-expanded') === 'true';
    if (expanded) {
      body.style.maxHeight = null;
      header.setAttribute('aria-expanded', 'false');
    } else {
      // close others
      this.headers.forEach(h => {
        if (h !== header) {
          const id2 = h.getAttribute('aria-controls');
          const b2 = id2 ? document.getElementById(id2) : null;
          if (b2) b2.style.maxHeight = null;
          h.setAttribute('aria-expanded', 'false');
        }
      });
      body.style.maxHeight = body.scrollHeight + 'px';
      header.setAttribute('aria-expanded', 'true');
    }
  }
}

class PostFetcher {
  constructor(containerId = "latest-posts-container", apiUrl = "/reseed/api/get-posts.php?limit=3") {
    this.container = document.getElementById(containerId);
    this.apiUrl = apiUrl;
    if (this.container) this.fetchLatest();
  }
  async fetchLatest() {
    if (!this.container) return;
    this.container.innerHTML = '<p class="loader"><i class="fas fa-sync fa-spin"></i> Loading latest posts...</p>';
    try {
      const res = await fetch(this.apiUrl);
      if (!res.ok) throw new Error(res.statusText || `HTTP ${res.status}`);
      const posts = await res.json();
      if (!Array.isArray(posts) || posts.length === 0) {
        this.container.innerHTML = '<p class="alert-info">No posts found at this time.</p>';
        return;
      }
      this.render(posts);
    } catch (err) {
      console.error("PostFetcher:", err);
      this.container.innerHTML = `<p class="alert-error">Could not load posts. (${err.message})</p>`;
    }
  }
  render(posts) {
    this.container.innerHTML = '';
    posts.forEach(post => {
      const date = post.created_at ? new Date(post.created_at).toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' }) : '';
      const img = post.cover_image ? `/reseed/uploads/images/${encodeURIComponent(post.cover_image)}` : '/reseed/assets/images/Re2.jpg';
      const summary = post.summary || (post.content ? post.content.replace(/<[^>]*>?/gm,'').slice(0,120) + '...' : '');
      const el = document.createElement('article');
      el.className = 'post-card reveal-on-scroll';
      el.innerHTML = `
        <a href="/reseed/post.php?slug=${encodeURIComponent(post.slug || '')}" class="card-image"><img src="${img}" alt="${(post.title||'')}" loading="lazy"></a>
        <div class="card-content">
          <span class="card-date">${date}</span>
          <h3><a href="/reseed/post.php?slug=${encodeURIComponent(post.slug||'')}">${post.title||''}</a></h3>
          <p>${summary}</p>
          <a href="/reseed/post.php?slug=${encodeURIComponent(post.slug||'')}" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
        </div>
      `;
      this.container.appendChild(el);
    });
    if (typeof AOS !== 'undefined') AOS.refresh();
  }
}

/* Initialize on DOMContentLoaded */
document.addEventListener("DOMContentLoaded", () => {
  "use strict";
  document.body.classList.remove("preload");

  if (typeof AOS !== "undefined") {
    AOS.init({ duration:800, easing:'ease-out-quad', once:true, offset:150, disable:'mobile' });
  }

  // instantiate controllers
  new HeaderController("site-header", "backToTop");
  new NavController("hamburger", "main-nav", "nav-offcanvas");
  new ThemeManager("theme-toggle");
  new ModalController("contactModal", ".open-contact-modal", ".close-modal");
  new ContactFormHandler("contactForm", "/reseed/api/process-form.php");
  new AccordionManager(".collapsible-header");
  new PostFetcher("latest-posts-container", '/reseed/api/get-posts.php?limit=3');
});

// Mobile dropdown toggle
document.querySelector('.dropdown-toggle').addEventListener('click', function (e) {
    if (window.innerWidth <= 768) {
        e.preventDefault();
        this.parentElement.classList.toggle('open');
    }
});
