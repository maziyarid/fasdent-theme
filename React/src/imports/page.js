/**
 * Fasdent — page.js
 * ---------------------------------------------------------------------------
 * Dedicated behaviors for the "Fasdent Sample Page" template (page.php).
 * Enqueue AFTER assets/js/main.js (if any). No jQuery dependency.
 *
 * Features:
 *   - Auto-generated Table of Contents from H2/H3 inside .post-content
 *   - Scroll-spy: highlights active TOC item
 *   - Sticky TOC collapse/expand (aria-expanded)
 *   - Reading progress bar
 *   - Reveal-on-scroll (data-reveal="fade-up|fade-left|fade-right")
 *   - Back-to-top button
 *   - Copy-link social button
 *   - Honeypot-aware newsletter submission (HIPAA-friendly)
 *   - Cookie consent (opt-in; gates GA/analytics)
 *   - Smooth in-page anchor scrolling with header offset
 *
 * Respects prefers-reduced-motion.
 */
(function () {
	'use strict';

	var doc = document;
	var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function ready(fn) {
		if (doc.readyState !== 'loading') fn();
		else doc.addEventListener('DOMContentLoaded', fn);
	}

	function slugify(str) {
		return String(str || '')
			.trim()
			.toLowerCase()
			.replace(/[\s\u200c]+/g, '-')
			.replace(/[^\w\u0600-\u06FF\-]/g, '')
			.replace(/\-+/g, '-')
			.replace(/^\-|\-$/g, '');
	}

	/* ---------------------------------------------------------------------
	 * TOC generation
	 * ------------------------------------------------------------------ */
	function buildTOC() {
		var tocList = doc.getElementById('toc-list');
		var content = doc.querySelector('.post-content');
		if (!tocList || !content) return;

		var headings = content.querySelectorAll('h2, h3');
		if (!headings.length) {
			var tocNav = doc.getElementById('toc');
			if (tocNav) tocNav.style.display = 'none';
			return;
		}

		var frag = doc.createDocumentFragment();
		var used = {};
		headings.forEach(function (h) {
			var id = h.id;
			if (!id) {
				id = slugify(h.textContent) || 'section';
				if (used[id]) { id = id + '-' + (++used[id]); }
				else { used[id] = 1; }
				h.id = id;
			}
			var li = doc.createElement('li');
			li.className = 'toc-item toc-item--' + h.tagName.toLowerCase();
			var a = doc.createElement('a');
			a.href = '#' + id;
			a.textContent = h.textContent.trim();
			a.setAttribute('data-toc-link', id);
			li.appendChild(a);
			frag.appendChild(li);
		});
		tocList.appendChild(frag);
	}

	/* ---------------------------------------------------------------------
	 * TOC collapse/expand toggle
	 * ------------------------------------------------------------------ */
	function initTOCToggle() {
		var btn = doc.querySelector('.toc-toggle');
		var list = doc.getElementById('toc-list');
		if (!btn || !list) return;
		btn.setAttribute('aria-expanded', 'true');
		btn.addEventListener('click', function () {
			var open = btn.getAttribute('aria-expanded') === 'true';
			btn.setAttribute('aria-expanded', open ? 'false' : 'true');
			if (open) list.setAttribute('hidden', '');
			else list.removeAttribute('hidden');
		});
	}

	/* ---------------------------------------------------------------------
	 * Scroll-spy
	 * ------------------------------------------------------------------ */
	function initScrollSpy() {
		var links = doc.querySelectorAll('[data-toc-link]');
		if (!links.length || !('IntersectionObserver' in window)) return;

		var linkById = {};
		var targets = [];
		links.forEach(function (a) {
			var id = a.getAttribute('data-toc-link');
			var el = doc.getElementById(id);
			if (el) {
				linkById[id] = a;
				targets.push(el);
			}
		});

		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				var a = linkById[entry.target.id];
				if (!a) return;
				if (entry.isIntersecting) {
					links.forEach(function (l) { l.classList.remove('is-active'); });
					a.classList.add('is-active');
				}
			});
		}, {
			rootMargin: '-25% 0px -65% 0px',
			threshold: 0
		});

		targets.forEach(function (t) { observer.observe(t); });
	}

	/* ---------------------------------------------------------------------
	 * Reading progress bar
	 * ------------------------------------------------------------------ */
	function initReadingProgress() {
		var bar = doc.querySelector('.reading-progress__bar');
		if (!bar) return;
		function update() {
			var scrollTop = window.pageYOffset || doc.documentElement.scrollTop;
			var docHeight = doc.documentElement.scrollHeight - window.innerHeight;
			var pct = docHeight > 0 ? Math.max(0, Math.min(100, (scrollTop / docHeight) * 100)) : 0;
			bar.style.width = pct + '%';
		}
		update();
		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update, { passive: true });
	}

	/* ---------------------------------------------------------------------
	 * Reveal on scroll
	 * ------------------------------------------------------------------ */
	function initReveal() {
		var els = doc.querySelectorAll('[data-reveal]');
		if (!els.length) return;
		if (prefersReducedMotion || !('IntersectionObserver' in window)) {
			els.forEach(function (el) { el.classList.add('is-visible'); });
			return;
		}
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					io.unobserve(entry.target);
				}
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
		els.forEach(function (el) { io.observe(el); });
	}

	/* ---------------------------------------------------------------------
	 * Back-to-top
	 * ------------------------------------------------------------------ */
	function initBackToTop() {
		var btn = doc.querySelector('.back-to-top');
		if (!btn) return;
		function toggle() {
			if ((window.pageYOffset || doc.documentElement.scrollTop) > 600) {
				btn.classList.add('is-visible');
			} else {
				btn.classList.remove('is-visible');
			}
		}
		toggle();
		window.addEventListener('scroll', toggle, { passive: true });
		btn.addEventListener('click', function () {
			window.scrollTo({
				top: 0,
				behavior: prefersReducedMotion ? 'auto' : 'smooth'
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Copy-link button
	 * ------------------------------------------------------------------ */
	function initCopyLink() {
		var btn = doc.querySelector('.social-btn--copy');
		if (!btn) return;
		btn.addEventListener('click', function () {
			var url = btn.getAttribute('data-copy-url') || window.location.href;
			var done = function () {
				btn.classList.add('is-copied');
				var original = btn.innerHTML;
				btn.innerHTML = '<i class="fa-solid fa-check" aria-hidden="true"></i>';
				setTimeout(function () {
					btn.classList.remove('is-copied');
					btn.innerHTML = original;
				}, 1500);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(done).catch(function () {
					legacyCopy(url); done();
				});
			} else {
				legacyCopy(url); done();
			}
		});

		function legacyCopy(text) {
			var ta = doc.createElement('textarea');
			ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
			doc.body.appendChild(ta); ta.focus(); ta.select();
			try { doc.execCommand('copy'); } catch (e) {}
			doc.body.removeChild(ta);
		}
	}

	/* ---------------------------------------------------------------------
	 * Smooth in-page anchors (with sticky header offset)
	 * ------------------------------------------------------------------ */
	function initSmoothAnchors() {
		doc.addEventListener('click', function (e) {
			var a = e.target.closest && e.target.closest('a[href^="#"]');
			if (!a) return;
			var hash = a.getAttribute('href');
			if (!hash || hash === '#' || hash.length < 2) return;
			var target = doc.getElementById(hash.slice(1));
			if (!target) return;
			e.preventDefault();
			var top = target.getBoundingClientRect().top + window.pageYOffset - 80;
			window.scrollTo({
				top: top,
				behavior: prefersReducedMotion ? 'auto' : 'smooth'
			});
			target.setAttribute('tabindex', '-1');
			target.focus({ preventScroll: true });
			history.replaceState(null, '', hash);
		});
	}

	/* ---------------------------------------------------------------------
	 * Newsletter form (HIPAA-friendly)
	 *   - Honeypot check
	 *   - Blocks obvious PHI-looking input (email is the only field, but we
	 *     defensively reject long strings containing digits + words that
	 *     look like health information).
	 * ------------------------------------------------------------------ */
	function initNewsletter() {
		var form = doc.querySelector('.newsletter-form');
		if (!form) return;
		form.addEventListener('submit', function (e) {
			var hp = form.querySelector('.hp-field');
			if (hp && hp.value) { e.preventDefault(); return; }
			var email = form.querySelector('input[type="email"]');
			if (!email) return;
			var v = String(email.value || '').trim();
			if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
				e.preventDefault();
				showFormMessage(form, 'lang-invalid-email', 'error');
				return;
			}
			// Basic PHI-shape guard (no digits/health words in an email addr).
			if (/(hiv|cancer|diabet|hepat|psychiat|mri|dna|ssn|nationalid|codemelli)/i.test(v)) {
				e.preventDefault();
				showFormMessage(form, 'lang-no-phi', 'error');
				return;
			}
			showFormMessage(form, 'lang-submitting', 'success');
		});
	}

	function showFormMessage(form, key, kind) {
		var messages = {
			'lang-invalid-email': 'لطفاً یک ایمیل معتبر وارد کنید.',
			'lang-no-phi':        'برای حریم خصوصی، لطفاً از وارد کردن اطلاعات پزشکی حساس در این فرم خودداری کنید.',
			'lang-submitting':    'در حال ارسال… ممنون از عضویت شما.'
		};
		var existing = form.querySelector('.form-message');
		if (existing) existing.remove();
		var el = doc.createElement('p');
		el.className = 'form-message form-message--' + (kind || 'success');
		el.textContent = messages[key] || key;
		form.appendChild(el);
	}

	/* ---------------------------------------------------------------------
	 * Cookie consent (opt-in, GA gated)
	 * ------------------------------------------------------------------ */
	function initCookieConsent() {
		try {
			var stored = localStorage.getItem('fasdent_cookie_consent');
			if (stored === 'accepted' || stored === 'rejected') return;
		} catch (e) {}

		var banner = doc.createElement('aside');
		banner.className = 'cookie-banner';
		banner.setAttribute('role', 'region');
		banner.setAttribute('aria-label', 'اعلان کوکی');
		banner.innerHTML =
			'<div class="cookie-banner__inner">' +
				'<p class="cookie-banner__text">' +
					'<i class="fa-solid fa-cookie-bite" aria-hidden="true" style="color:#f59e0b;margin-inline-end:.35rem;"></i>' +
					'این سایت برای بهبود تجربه‌ی شما از کوکی‌های ضروری و تحلیلی استفاده می‌کند. اطلاعات پزشکی شما جمع‌آوری نمی‌شود.' +
				'</p>' +
				'<div class="cookie-banner__actions">' +
					'<button type="button" class="btn btn--primary cookie-accept">پذیرش</button>' +
					'<button type="button" class="btn cookie-reject">رد</button>' +
				'</div>' +
			'</div>';

		// Inline minimal styles for the banner (self-contained).
		var style = doc.createElement('style');
		style.textContent =
			'.cookie-banner{position:fixed;inset-block-end:0;inset-inline:0;background:#0f172a;color:#e2e8f0;padding:1rem;z-index:9999;box-shadow:0 -8px 30px rgba(0,0,0,.25);}' +
			'.cookie-banner__inner{display:flex;flex-wrap:wrap;align-items:center;gap:1rem;justify-content:space-between;max-width:1160px;margin:0 auto;}' +
			'.cookie-banner__text{margin:0;font-size:.9rem;line-height:1.7;}' +
			'.cookie-banner__actions{display:flex;gap:.5rem;flex-wrap:wrap;}' +
			'.cookie-banner .btn{padding:.55rem 1rem;font-size:.85rem;}' +
			'.cookie-reject{background:transparent;color:#e2e8f0;border:1px solid #64748b;}' +
			'.cookie-reject:hover{background:#1e293b;color:#fff;}';
		doc.head.appendChild(style);

		doc.body.appendChild(banner);

		banner.querySelector('.cookie-accept').addEventListener('click', function () {
			try { localStorage.setItem('fasdent_cookie_consent', 'accepted'); } catch (e) {}
			banner.remove();
			// Gate analytics here — page can dispatch this event and load GA/Matomo on it.
			doc.dispatchEvent(new CustomEvent('fasdent:consent', { detail: { accepted: true } }));
		});
		banner.querySelector('.cookie-reject').addEventListener('click', function () {
			try { localStorage.setItem('fasdent_cookie_consent', 'rejected'); } catch (e) {}
			banner.remove();
			doc.dispatchEvent(new CustomEvent('fasdent:consent', { detail: { accepted: false } }));
		});
	}

	/* ---------------------------------------------------------------------
	 * Init
	 * ------------------------------------------------------------------ */
	ready(function () {
		buildTOC();
		initTOCToggle();
		initScrollSpy();
		initReadingProgress();
		initReveal();
		initBackToTop();
		initCopyLink();
		initSmoothAnchors();
		initNewsletter();
		initCookieConsent();
	});
})();
