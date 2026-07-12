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

  /* ── ToC Toggle ──────────────────────────────────── */
  var tocToggle = document.querySelector('.toc-toggle');
  if (tocToggle) {
    var tocList = document.getElementById('toc-list');
    tocToggle.addEventListener('click', function () {
      var expanded = tocToggle.getAttribute('aria-expanded') === 'true';
      tocToggle.setAttribute('aria-expanded', String(!expanded));
      if (tocList) tocList.hidden = expanded;
      var chevron = tocToggle.querySelector('.toc-chevron');
      if (chevron) chevron.style.transform = expanded ? 'rotate(-90deg)' : '';
    });
  }

  /* ── Live Search ─────────────────────────────────── */
  document.querySelectorAll('.live-search-wrapper input').forEach(function (input) {
    var resultsBox = input.parentElement.querySelector('.live-search-results');
    if (!resultsBox || typeof fasdentData === 'undefined') return;
    var debounceTimer;
    input.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      var term = input.value.trim();
      if (term.length < 2) { resultsBox.innerHTML = ''; return; }
      debounceTimer = setTimeout(function () {
        var fd = new FormData();
        fd.append('action', 'fasdent_live_search');
        fd.append('term',   term);
        fd.append('nonce',  fasdentData.nonce);
        fetch(fasdentData.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            resultsBox.innerHTML = '';
            if (!data.success || !data.data.results.length) {
              resultsBox.innerHTML = '<div class="search-no-results">نتیجه‌ای یافت نشد.</div>';
              return;
            }
            data.data.results.forEach(function (item) {
              var a = document.createElement('a');
              a.href = item.url;
              a.className = 'search-result-item';
              a.innerHTML = (item.thumbnail ? '<img src="' + item.thumbnail + '" alt="" loading="lazy">' : '')
                + '<div class="search-result-item__body">'
                + '<div class="search-result-item__title">' + item.title + '</div>'
                + '<div class="search-result-item__type">' + item.type + '</div></div>';
              resultsBox.appendChild(a);
            });
          });
      }, 300);
    });
    document.addEventListener('click', function (e) {
      if (!input.parentElement.contains(e.target)) resultsBox.innerHTML = '';
    });
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { resultsBox.innerHTML = ''; input.blur(); }
    });
  });

  /* ── Multi-step Booking Form ─────────────────────── */
  var bookingForm = document.getElementById('fasdent-booking-form');
  if (bookingForm) {
    var bSteps   = Array.from(bookingForm.querySelectorAll('.booking-step'));
    var bDots    = Array.from(document.querySelectorAll('.step-dot'));
    var bLines   = Array.from(document.querySelectorAll('.step-line'));
    var bCurrent = 0;

    var gaField = document.getElementById('ga_session_field');
    if (gaField) {
      try { gaField.value = (document.cookie.match(/_ga=([^;]+)/) || [])[1] || ''; } catch(e) {}
    }

    function bShowStep(idx) {
      bSteps.forEach(function (s, i) { s.hidden = (i !== idx); });
      bDots.forEach(function (d, i) { d.classList.toggle('active', i === idx); d.classList.toggle('done', i < idx); });
      bLines.forEach(function (l, i) { l.classList.toggle('done', i < idx); });
      bCurrent = idx;
      window.scrollTo({ top: bookingForm.getBoundingClientRect().top + window.scrollY - 90, behavior: 'smooth' });
    }

    function bValidate(idx) {
      var ok = true;
      bSteps[idx].querySelectorAll('[required]').forEach(function (f) {
        f.setCustomValidity('');
        if (!f.checkValidity()) { f.reportValidity(); ok = false; }
      });
      return ok;
    }

    function bBuildSummary() {
      var fd = new FormData(bookingForm);
      var pairs = { name:'نام', phone:'تلفن', email:'ایمیل', symptoms:'شرح مشکل', preferred_date:'تاریخ', time_range:'بازه' };
      var html = '<dl class="booking-summary">';
      Object.keys(pairs).forEach(function (k) {
        var v = fd.get(k); if (v && v.trim()) html += '<dt>' + pairs[k] + '</dt><dd>' + v + '</dd>';
      });
      var svcSel = bookingForm.querySelector('[name="service_id"]');
      if (svcSel && svcSel.value) {
        var opt = svcSel.querySelector('option[value="' + svcSel.value + '"]');
        if (opt) html += '<dt>خدمت</dt><dd>' + opt.textContent.trim() + '</dd>';
      }
      if (fd.get('is_emergency')) html += '<dt></dt><dd style="color:#dc2626;">⚠️ اورژانسی</dd>';
      html += '</dl>';
      var summary = document.getElementById('booking-summary');
      if (summary) summary.innerHTML = html;
    }

    bookingForm.querySelectorAll('.booking-next').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (!bValidate(bCurrent)) return;
        if (bCurrent === bSteps.length - 2) bBuildSummary();
        bShowStep(bCurrent + 1);
        if (typeof gtag === 'function') gtag('event', 'booking_step_' + (bCurrent + 1));
      });
    });
    bookingForm.querySelectorAll('.booking-prev').forEach(function (btn) {
      btn.addEventListener('click', function () { bShowStep(Math.max(0, bCurrent - 1)); });
    });

    bookingForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!bValidate(bCurrent)) return;
      var submitBtn = bookingForm.querySelector('.booking-submit');
      var msgEl     = bookingForm.querySelector('.form-message');
      if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'در حال ثبت...'; }
      var fd = new FormData(bookingForm);
      fd.set('action', 'fasdent_submit_booking');
      fetch(fasdentData.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success) {
            bookingForm.hidden = true;
            var sEl = document.getElementById('booking-success');
            var mEl = document.getElementById('booking-success-msg');
            if (sEl) { sEl.hidden = false; }
            if (mEl && data.data && data.data.message) mEl.textContent = data.data.message;
            if (typeof gtag === 'function') gtag('event', 'booking_submitted', { value: 1 });
            try { if (typeof clarity === 'function') clarity('event', 'booking_submitted'); } catch(er) {}
          } else {
            if (msgEl) { msgEl.textContent = (data.data && data.data.message) || 'خطایی رخ داد.'; msgEl.className = 'form-message form-message--error'; }
          }
        })
        .catch(function () {
          if (msgEl) { msgEl.textContent = 'خطا در ارتباط با سرور.'; msgEl.className = 'form-message form-message--error'; }
        })
        .finally(function () {
          if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'ثبت نوبت'; }
        });
    });

    bShowStep(0);
  }

});
