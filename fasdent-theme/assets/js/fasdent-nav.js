/**
 * Fasdent Mobile Nav + Floating Chat
 */
(() => {
  'use strict';
  const ready = (fn) => {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn, { once: true });
    } else { fn(); }
  };
  ready(() => {
    const nav = document.getElementById('primary-navigation') || document.querySelector('.site-nav');
    const toggle = document.getElementById('primary-menu-toggle') || document.querySelector('.mobile-toggle');
    const backdrop = document.getElementById('nav-backdrop') || document.querySelector('.nav-backdrop');
    const desktopMq = window.matchMedia('(min-width: 961px)');
    let prevFocus = null;

    if (nav && toggle) {
      const closeNav = (restore = true) => {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', toggle.dataset.openLabel || 'باز کردن منوی اصلی');
        document.body.classList.remove('nav-open');
        if (backdrop) {
          backdrop.classList.remove('is-visible');
          setTimeout(() => {
            if (!nav.classList.contains('is-open')) {
              backdrop.hidden = true;
              backdrop.setAttribute('aria-hidden', 'true');
            }
          }, 280);
        }
        if (restore && prevFocus instanceof HTMLElement) {
          try { prevFocus.focus(); } catch (e) {}
        }
      };

      const openNav = () => {
        if (desktopMq.matches) return;
        prevFocus = document.activeElement;
        nav.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', toggle.dataset.closeLabel || 'بستن منوی اصلی');
        document.body.classList.add('nav-open');
        if (backdrop) {
          backdrop.hidden = false;
          backdrop.setAttribute('aria-hidden', 'false');
          requestAnimationFrame(() => backdrop.classList.add('is-visible'));
        }
        const first = nav.querySelector('a[href]');
        if (first) setTimeout(() => first.focus(), 80);
      };

      toggle.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (toggle.getAttribute('aria-expanded') === 'true') closeNav();
        else openNav();
      });

      if (backdrop) {
        backdrop.addEventListener('click', (e) => {
          e.preventDefault();
          closeNav();
        });
      }
      document.addEventListener('click', (e) => {
        if (!nav.classList.contains('is-open')) return;
        if (nav.contains(e.target) || toggle.contains(e.target)) return;
        closeNav();
      });

      nav.addEventListener('click', (e) => {
        if (!desktopMq.matches && e.target.closest('a')) closeNav(false);
      });

      document.addEventListener('keydown', (e) => {
        if (!nav.classList.contains('is-open')) return;
        if (e.key === 'Escape') { e.preventDefault(); closeNav(); }
      });

      const sync = () => { if (desktopMq.matches) closeNav(false); };
      if (desktopMq.addEventListener) desktopMq.addEventListener('change', sync);
      else desktopMq.addListener(sync);
    }

    document.querySelectorAll('[data-fasdent-chat]').forEach((chat) => {
      const launcher = chat.querySelector('[data-chat-toggle]');
      const closeBtn = chat.querySelector('[data-chat-close]');
      const panel = chat.querySelector('.fasdent-chat__panel');
      if (!launcher || !panel) return;

      const setOpen = (open) => {
        chat.classList.toggle('is-open', open);
        launcher.setAttribute('aria-expanded', String(open));
        panel.hidden = !open;
        if (open) {
          const first = panel.querySelector('a, button');
          if (first) setTimeout(() => first.focus(), 60);
        }
      };

      launcher.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        setOpen(!chat.classList.contains('is-open'));
      });
      if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
          e.preventDefault();
          setOpen(false);
          launcher.focus();
        });
      }
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && chat.classList.contains('is-open')) {
          setOpen(false);
          launcher.focus();
        }
      });
      document.addEventListener('click', (e) => {
        if (chat.classList.contains('is-open') && !chat.contains(e.target)) setOpen(false);
      });
    });
  });
})();
