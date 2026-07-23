/**
 * Fasdent Mobile Navigation Drawer
 * Proper off-canvas with backdrop, body lock, focus trap, Escape
 * @package Fasdent
 */
(() => {
  'use strict';
  const ready = (fn) => {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn, { once: true });
    } else { fn(); }
  };
  ready(() => {
    const nav = document.getElementById('site-navigation') || document.querySelector('.site-nav');
    const toggle = document.querySelector('.mobile-toggle');
    const backdrop = document.querySelector('.nav-backdrop');
    const desktopMq = window.matchMedia('(min-width: 961px)');
    let previousFocus = null;
    if (!nav || !toggle) return;

    const focusables = () =>
      [...nav.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')]
        .filter((el) => !el.hidden && el.offsetParent !== null);

    const closeNav = (restore = true) => {
      nav.classList.remove('is-open');
      nav.setAttribute('aria-hidden', desktopMq.matches ? 'false' : 'true');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', toggle.dataset.openLabel || 'باز کردن منوی موبایل');
      document.body.classList.remove('nav-open');
      if (backdrop) {
        backdrop.classList.remove('is-visible');
        backdrop.hidden = true;
        backdrop.setAttribute('aria-hidden', 'true');
      }
      if (restore && previousFocus instanceof HTMLElement) previousFocus.focus();
    };

    const openNav = () => {
      if (desktopMq.matches) return;
      previousFocus = document.activeElement;
      nav.classList.add('is-open');
      nav.setAttribute('aria-hidden', 'false');
      toggle.setAttribute('aria-expanded', 'true');
      toggle.setAttribute('aria-label', toggle.dataset.closeLabel || 'بستن منوی موبایل');
      document.body.classList.add('nav-open');
      if (backdrop) {
        backdrop.hidden = false;
        backdrop.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(() => backdrop.classList.add('is-visible'));
      }
      const first = focusables()[0];
      if (first) setTimeout(() => first.focus(), 80);
    };

    toggle.addEventListener('click', () => {
      if (toggle.getAttribute('aria-expanded') === 'true') closeNav();
      else openNav();
    });
    backdrop?.addEventListener('click', () => closeNav());
    nav.addEventListener('click', (e) => {
      if (!desktopMq.matches && e.target.closest('a') && !e.target.closest('.menu-item-has-children > a[href="#"]')) {
        closeNav(false);
      }
    });
    document.addEventListener('keydown', (e) => {
      if (!nav.classList.contains('is-open')) return;
      if (e.key === 'Escape') { e.preventDefault(); closeNav(); return; }
      if (e.key !== 'Tab') return;
      const items = focusables();
      if (!items.length) return;
      const first = items[0];
      const last = items[items.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });
    const sync = () => {
      if (desktopMq.matches) { closeNav(false); nav.setAttribute('aria-hidden', 'false'); }
      else { nav.setAttribute('aria-hidden', nav.classList.contains('is-open') ? 'false' : 'true'); }
    };
    if (desktopMq.addEventListener) desktopMq.addEventListener('change', sync);
    else desktopMq.addListener(sync);
    sync();
  });
})();
