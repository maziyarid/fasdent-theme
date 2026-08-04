/**
 * Before/After comparison slider + gallery filter
 */
(() => {
  'use strict';
  const ready = (fn) => {
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn, { once: true });
    else fn();
  };

  ready(() => {
    document.querySelectorAll('[data-ba-slider]').forEach((slider) => {
      const viewport = slider.querySelector('.ba-slider__viewport');
      const afterWrap = slider.querySelector('.ba-slider__after-wrap');
      const handle = slider.querySelector('.ba-slider__handle');
      if (!viewport || !afterWrap || !handle) return;

      let dragging = false;

      const setPos = (clientX) => {
        const rect = viewport.getBoundingClientRect();
        let ratio = (clientX - rect.left) / rect.width;
        ratio = Math.min(1, Math.max(0, ratio));
        const pct = ratio * 100;
        afterWrap.style.clipPath = 'inset(0 ' + (100 - pct) + '% 0 0)';
        handle.style.left = pct + '%';
        handle.setAttribute('aria-valuenow', String(Math.round(pct)));
      };

      const start = (e) => {
        dragging = true;
        viewport.classList.add('is-dragging');
        if (e.type === 'touchstart') setPos(e.touches[0].clientX);
        else setPos(e.clientX);
      };
      const move = (e) => {
        if (!dragging) return;
        if (e.type === 'touchmove') setPos(e.touches[0].clientX);
        else setPos(e.clientX);
      };
      const end = () => {
        dragging = false;
        viewport.classList.remove('is-dragging');
      };

      handle.addEventListener('mousedown', start);
      viewport.addEventListener('mousedown', start);
      handle.addEventListener('touchstart', start, { passive: true });
      viewport.addEventListener('touchstart', start, { passive: true });
      window.addEventListener('mousemove', move);
      window.addEventListener('touchmove', move, { passive: true });
      window.addEventListener('mouseup', end);
      window.addEventListener('touchend', end);

      handle.addEventListener('keydown', (e) => {
        const cur = parseFloat(handle.getAttribute('aria-valuenow') || '50');
        let next = cur;
        if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') next = Math.max(0, cur - 5);
        if (e.key === 'ArrowRight' || e.key === 'ArrowUp') next = Math.min(100, cur + 5);
        if (next !== cur) {
          e.preventDefault();
          const rect = viewport.getBoundingClientRect();
          setPos(rect.left + (next / 100) * rect.width);
        }
      });
    });

    const filterBtns = document.querySelectorAll('[data-ba-filter]');
    const items = document.querySelectorAll('[data-ba-cats]');
    if (filterBtns.length && items.length) {
      filterBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
          filterBtns.forEach((b) => b.classList.remove('is-active'));
          btn.classList.add('is-active');
          const cat = btn.dataset.baFilter;
          items.forEach((item) => {
            if (cat === 'all' || item.dataset.baCats.split(/\s+/).includes(cat)) {
              item.hidden = false;
            } else {
              item.hidden = true;
            }
          });
        });
      });
    }
  });
})();
