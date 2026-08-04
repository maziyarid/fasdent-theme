(() => {
  'use strict';

  const ready = (callback) => {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback, { once: true });
    } else {
      callback();
    }
  };

  ready(() => {
    const header = document.querySelector('.site-header');
    const nav = document.getElementById('primary-navigation') || document.querySelector('.site-nav');
    const toggle = document.getElementById('primary-menu-toggle') || document.querySelector('.menu-toggle, .mobile-toggle');
    const backdrop = document.querySelector('.nav-backdrop');
    const desktopQuery = window.matchMedia('(min-width: 961px)');
    let previousFocus = null;

    const updateMobileOffset = () => {
      if (!header) return;
      document.documentElement.style.setProperty('--mobile-nav-top', `${Math.max(0, header.getBoundingClientRect().bottom)}px`);
    };

    const focusableElements = () => nav ? [...nav.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')].filter((element) => !element.hidden && element.offsetParent !== null) : [];

    const closeNavigation = (restoreFocus = true) => {
      if (!nav || !toggle) return;
      nav.classList.remove('is-open');
      nav.setAttribute('aria-hidden', desktopQuery.matches ? 'false' : 'true');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', toggle.dataset.openLabel || 'باز کردن فهرست اصلی');
      document.body.classList.remove('nav-open');
      if (backdrop) {
        backdrop.classList.remove('is-visible');
        backdrop.hidden = true;
      }
      if (restoreFocus && previousFocus instanceof HTMLElement) previousFocus.focus();
    };

    const openNavigation = () => {
      if (!nav || !toggle || desktopQuery.matches) return;
      previousFocus = document.activeElement;
      updateMobileOffset();
      nav.classList.add('is-open');
      nav.setAttribute('aria-hidden', 'false');
      toggle.setAttribute('aria-expanded', 'true');
      toggle.setAttribute('aria-label', toggle.dataset.closeLabel || 'بستن فهرست اصلی');
      document.body.classList.add('nav-open');
      if (backdrop) {
        backdrop.hidden = false;
        requestAnimationFrame(() => backdrop.classList.add('is-visible'));
      }
      const first = focusableElements()[0];
      if (first) window.setTimeout(() => first.focus(), 100);
    };

    if (nav && toggle) {
      toggle.dataset.openLabel ||= toggle.getAttribute('aria-label') || 'باز کردن فهرست اصلی';
      toggle.dataset.closeLabel ||= 'بستن فهرست اصلی';

      nav.querySelectorAll('.menu-item-has-children, .page_item_has_children').forEach((item, index) => {
        const submenu = [...item.children].find((child) => child.matches('ul, .sub-menu'));
        if (!submenu) return;
        if (!submenu.id) submenu.id = `fasdent-submenu-${index + 1}`;

        let submenuToggle = [...item.children].find((child) => child.classList?.contains('submenu-toggle'));
        if (!submenuToggle) {
          submenuToggle = document.createElement('button');
          submenuToggle.type = 'button';
          submenuToggle.className = 'submenu-toggle';
          submenuToggle.innerHTML = '<i class="fa-solid fa-chevron-down" aria-hidden="true"></i><span class="screen-reader-text">باز کردن زیرمنو</span>';
          item.insertBefore(submenuToggle, submenu);
        }
        submenuToggle.setAttribute('aria-controls', submenu.id);
        submenuToggle.setAttribute('aria-expanded', 'false');
        submenuToggle.addEventListener('click', () => {
          const expanded = submenuToggle.getAttribute('aria-expanded') === 'true';
          submenuToggle.setAttribute('aria-expanded', String(!expanded));
          item.classList.toggle('is-submenu-open', !expanded);
        });
      });

      toggle.addEventListener('click', () => {
        if (toggle.getAttribute('aria-expanded') === 'true') closeNavigation();
        else openNavigation();
      });
      backdrop?.addEventListener('click', () => closeNavigation());

      nav.addEventListener('click', (event) => {
        if (!desktopQuery.matches && event.target.closest('a') && !event.target.closest('.menu-item-has-children > a[href="#"]')) closeNavigation(false);
      });

      document.addEventListener('keydown', (event) => {
        if (!nav.classList.contains('is-open')) return;
        if (event.key === 'Escape') {
          event.preventDefault();
          closeNavigation();
          return;
        }
        if (event.key !== 'Tab') return;
        const focusables = focusableElements();
        if (!focusables.length) return;
        const first = focusables[0];
        const last = focusables[focusables.length - 1];
        if (event.shiftKey && document.activeElement === first) {
          event.preventDefault();
          last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
          event.preventDefault();
          first.focus();
        }
      });

      const syncBreakpoint = () => {
        updateMobileOffset();
        if (desktopQuery.matches) {
          closeNavigation(false);
          nav.setAttribute('aria-hidden', 'false');
        } else {
          nav.setAttribute('aria-hidden', nav.classList.contains('is-open') ? 'false' : 'true');
        }
      };
      desktopQuery.addEventListener?.('change', syncBreakpoint);
      window.addEventListener('resize', updateMobileOffset, { passive: true });
      window.addEventListener('scroll', updateMobileOffset, { passive: true });
      syncBreakpoint();
    }

    document.querySelectorAll('[data-copy-url]').forEach((button) => {
      button.addEventListener('click', async () => {
        const status = button.closest('.social-share')?.querySelector('.social-share__status');
        try {
          await navigator.clipboard.writeText(button.dataset.copyUrl || window.location.href);
          if (status) status.textContent = 'پیوند کپی شد.';
          button.classList.add('is-copied');
        } catch {
          if (status) status.textContent = 'کپی خودکار ممکن نبود؛ نشانی مرورگر را کپی کنید.';
        }
      });
    });

    document.querySelectorAll('[data-fasdent-chat]').forEach((chat) => {
      const launcher = chat.querySelector('[data-chat-toggle]');
      const closeButton = chat.querySelector('[data-chat-close]');
      const panel = chat.querySelector('.fasdent-chat__panel');
      if (!launcher || !panel) return;

      const setChatOpen = (open) => {
        chat.classList.toggle('is-open', open);
        launcher.setAttribute('aria-expanded', String(open));
        panel.hidden = !open;
        if (open) window.setTimeout(() => panel.querySelector('a, button')?.focus(), 80);
      };

      launcher.addEventListener('click', () => setChatOpen(!chat.classList.contains('is-open')));
      closeButton?.addEventListener('click', () => {
        setChatOpen(false);
        launcher.focus();
      });
      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && chat.classList.contains('is-open')) {
          setChatOpen(false);
          launcher.focus();
        }
      });
      document.addEventListener('click', (event) => {
        if (chat.classList.contains('is-open') && !chat.contains(event.target)) setChatOpen(false);
      });
    });
  });
})();
