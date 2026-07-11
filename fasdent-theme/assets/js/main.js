/**
 * Fasdent Main JavaScript
 * کلینیک دندانپزشکی فس‌دنت — دکتر کیوان علی‌پسندی
 */
document.addEventListener('DOMContentLoaded', function () {

  /* ── FAQ Accordion ───────────────────────────────────── */
  document.querySelectorAll('.faq-item').forEach(function (item) {
    var button = item.querySelector('button');
    if (!button) return;
    button.addEventListener('click', function () {
      var isOpen    = item.classList.contains('is-open');
      var container = item.parentElement;
      if (container) {
        container.querySelectorAll('.faq-item.is-open').forEach(function (el) {
          if (el !== item) {
            el.classList.remove('is-open');
            var btn = el.querySelector('button');
            if (btn) btn.setAttribute('aria-expanded', 'false');
          }
        });
      }
      item.classList.toggle('is-open', !isOpen);
      button.setAttribute('aria-expanded', String(!isOpen));
    });
  });

  /* ── Mobile Navigation Toggle ────────────────────────── */
  var toggle = document.querySelector('.mobile-toggle');
  var nav    = document.querySelector('.site-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var expanded = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(expanded));
    });
    document.addEventListener('click', function (e) {
      if (!nav.contains(e.target) && !toggle.contains(e.target)) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* ── AJAX Forms (BUG-001 FIX) ───────────────────────── */
  // Uses fasdentData.ajaxUrl (localized in enqueue.php) — NOT window.location.href.
  document.querySelectorAll('form[data-ajax-form]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (typeof fasdentData === 'undefined') return;

      var submitBtn   = form.querySelector('button[type="submit"], input[type="submit"]');
      var originalTxt = submitBtn ? (submitBtn.textContent || submitBtn.value) : '';
      var msgEl       = form.querySelector('.form-message');

      if (submitBtn) {
        submitBtn.textContent = 'در حال ارسال...';
        submitBtn.disabled    = true;
      }
      if (msgEl) {
        msgEl.textContent = '';
        msgEl.className   = 'form-message';
      }

      var formData = new FormData(form);
      if (!formData.get('action')) {
        formData.append('action', 'fasdent_handle_form');
      }

      fetch(fasdentData.ajaxUrl, {
        method:      'POST',
        body:        formData,
        credentials: 'same-origin',
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data.success) {
            if (msgEl) {
              msgEl.textContent = (data.data && data.data.message) || 'پیام شما با موفقیت ارسال شد.';
              msgEl.className   = 'form-message form-message--success';
            }
            form.reset();
            if (typeof gtag === 'function') {
              gtag('event', 'form_submit', { form_type: formData.get('form_type') || 'contact' });
            }
          } else {
            if (msgEl) {
              msgEl.textContent = (data.data && data.data.message) || 'خطایی رخ داد. لطفاً دوباره امتحان کنید.';
              msgEl.className   = 'form-message form-message--error';
            }
          }
        })
        .catch(function () {
          if (msgEl) {
            msgEl.textContent = 'خطا در ارتباط با سرور. لطفاً مجدداً تلاش کنید.';
            msgEl.className   = 'form-message form-message--error';
          }
        })
        .finally(function () {
          if (submitBtn) {
            submitBtn.textContent = originalTxt;
            submitBtn.disabled    = false;
          }
        });
    });
  });

  /* ── Back-to-top button ──────────────────────────────── */
  var backTop = document.querySelector('.back-to-top');
  if (backTop) {
    window.addEventListener('scroll', function () {
      backTop.classList.toggle('is-visible', window.scrollY > 400);
    }, { passive: true });
    backTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ── Reading Progress Bar ───────────────────────────── */
  var progressBar = document.querySelector('.reading-progress');
  if (progressBar) {
    window.addEventListener('scroll', function () {
      var scrollTop = window.scrollY;
      var docHeight = document.documentElement.scrollHeight - window.innerHeight;
      var progress  = docHeight > 0 ? Math.min(100, (scrollTop / docHeight) * 100) : 0;
      progressBar.style.setProperty('--progress', progress + '%');
      progressBar.setAttribute('aria-valuenow', Math.round(progress));
    }, { passive: true });
  }

  /* ── Cookie Consent Banner ──────────────────────────── */
  var cookieBanner = document.getElementById('fasdent-cookie-banner');
  if (cookieBanner && !localStorage.getItem('fasdent_cookies_accepted')) {
    cookieBanner.removeAttribute('hidden');
    var acceptBtn = cookieBanner.querySelector('.cookie-accept');
    var rejectBtn = cookieBanner.querySelector('.cookie-reject');
    if (acceptBtn) {
      acceptBtn.addEventListener('click', function () {
        localStorage.setItem('fasdent_cookies_accepted', '1');
        cookieBanner.setAttribute('hidden', '');
        if (typeof gtag === 'function') {
          gtag('consent', 'update', { analytics_storage: 'granted', ad_storage: 'denied' });
        }
      });
    }
    if (rejectBtn) {
      rejectBtn.addEventListener('click', function () {
        localStorage.setItem('fasdent_cookies_accepted', '0');
        cookieBanner.setAttribute('hidden', '');
      });
    }
  }

  /* ── Lightbox for gallery images ────────────────────── */
  document.querySelectorAll('[data-lightbox]').forEach(function (img) {
    img.style.cursor = 'zoom-in';
    img.addEventListener('click', function () {
      var overlay = document.createElement('div');
      overlay.className = 'fasdent-lightbox';
      overlay.setAttribute('role', 'dialog');
      overlay.setAttribute('aria-modal', 'true');
      overlay.innerHTML = '<button class="fasdent-lightbox__close" aria-label="بستن">&times;</button>'
        + '<img src="' + img.src + '" alt="' + (img.alt || '') + '">';
      document.body.appendChild(overlay);
      document.body.style.overflow = 'hidden';
      overlay.querySelector('.fasdent-lightbox__close').focus();
      overlay.addEventListener('click', function (e) {
        if (e.target === overlay || e.target.classList.contains('fasdent-lightbox__close')) {
          overlay.remove();
          document.body.style.overflow = '';
        }
      });
      overlay.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          overlay.remove();
          document.body.style.overflow = '';
        }
      });
    });
  });

  /* ── Before/After Comparison Slider ─────────────────── */
  document.querySelectorAll('.before-after-slider').forEach(function (slider) {
    var handle   = slider.querySelector('.ba-handle');
    var afterDiv = slider.querySelector('.ba-after');
    if (!handle || !afterDiv) return;
    var dragging = false;
    var setPos   = function (x) {
      var rect  = slider.getBoundingClientRect();
      var ratio = Math.min(1, Math.max(0, (x - rect.left) / rect.width));
      afterDiv.style.clipPath = 'inset(0 ' + ((1 - ratio) * 100) + '% 0 0)';
      handle.style.left       = (ratio * 100) + '%';
    };
    handle.addEventListener('mousedown',  function () { dragging = true; });
    handle.addEventListener('touchstart', function () { dragging = true; }, { passive: true });
    window.addEventListener('mousemove',  function (e) { if (dragging) setPos(e.clientX); });
    window.addEventListener('touchmove',  function (e) { if (dragging) setPos(e.touches[0].clientX); }, { passive: true });
    window.addEventListener('mouseup',    function () { dragging = false; });
    window.addEventListener('touchend',   function () { dragging = false; });
  });

  /* ── Table of Contents — Active Section Highlight ───── */
  var tocLinks = document.querySelectorAll('.toc-nav a[href^="#"]');
  if (tocLinks.length) {
    var headings = Array.from(tocLinks).map(function (link) {
      return document.getElementById(decodeURIComponent(link.getAttribute('href').slice(1)));
    }).filter(Boolean);

    window.addEventListener('scroll', function () {
      var scrollY   = window.scrollY + 120;
      var activeIdx = 0;
      headings.forEach(function (h, i) {
        if (h.offsetTop <= scrollY) activeIdx = i;
      });
      tocLinks.forEach(function (link, i) {
        link.classList.toggle('is-active', i === activeIdx);
      });
    }, { passive: true });
  }

  /* ── Reaction Buttons ───────────────────────────────── */
  document.querySelectorAll('.reaction-btn[data-reaction]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (btn.classList.contains('is-active') || typeof fasdentData === 'undefined') return;
      var postEl  = btn.closest('[data-post-id]');
      var postId  = postEl ? postEl.dataset.postId : '0';
      var fd = new FormData();
      fd.append('action',   'fasdent_post_reaction');
      fd.append('post_id',  postId);
      fd.append('reaction', btn.dataset.reaction);
      fd.append('nonce',    fasdentData.nonce);
      fetch(fasdentData.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success) {
            btn.classList.add('is-active');
            var countEl = btn.querySelector('.reaction-count');
            if (countEl && data.data && data.data.count) countEl.textContent = data.data.count;
          }
        });
    });
  });

  /* ── Poll Voting ────────────────────────────────────── */
  document.querySelectorAll('.poll-option-btn[data-option]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (typeof fasdentData === 'undefined') return;
      var pollEl = btn.closest('.poll-widget[data-poll-id]');
      if (!pollEl || pollEl.classList.contains('voted')) return;
      var fd = new FormData();
      fd.append('action',    'fasdent_poll_vote');
      fd.append('poll_id',   pollEl.dataset.pollId);
      fd.append('option_id', btn.dataset.option);
      fd.append('nonce',     fasdentData.nonce);
      fetch(fasdentData.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success) {
            pollEl.classList.add('voted');
            if (data.data && data.data.html) pollEl.innerHTML = data.data.html;
          }
        });
    });
  });

});
