/**
 * Fasdent Floating Contact Widget
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
          if (first) window.setTimeout(() => first.focus(), 80);
        }
      };
      launcher.addEventListener('click', () => setOpen(!chat.classList.contains('is-open')));
      closeBtn?.addEventListener('click', () => { setOpen(false); launcher.focus(); });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && chat.classList.contains('is-open')) {
          setOpen(false); launcher.focus();
        }
      });
      document.addEventListener('click', (e) => {
        if (chat.classList.contains('is-open') && !chat.contains(e.target)) setOpen(false);
      });
    });
  });
})();
