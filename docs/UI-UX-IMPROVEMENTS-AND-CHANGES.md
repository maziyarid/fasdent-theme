# Fasdent Theme — UI/UX Improvements, Real Data & Chaty Integration
**Version:** 2.2.0  
**Date:** 2026-07-23  
**Author notes:** All changes prepared for the maziyarid/fasdent-theme repository. Apply by replacing the listed files and uploading the clinic images.

---

## 1. Summary of Changes

### A. Real Clinic Data Replacement
All demo/placeholder data replaced with the official information for **دکتر کیوان علی‌پسندی**:

| Field | New Real Value |
|-------|----------------|
| Full name | دکتر کیوان علی‌پسندی |
| Title / Specialty | دکتری حرفه‌ای (ایمپلنتولوژیست) |
| Medical license (شماره نظام) | ۱۹۱۷۴۰ |
| Experience | بیش از ۱۰ سال سابقه |
| Implant brands used | Bego, Megagen, Straumann, Sic, 3zahn |
| Phone | +98 920 144 1469 (09201441469) |
| Email | Dr.keyvan.alipasandii@gmail.com |
| Instagram (personal) | @Dr.keyvan_alipasandi |
| Instagram (clinic) | @Fasdent.clinic |
| Working hours | از ساعت ۱۱ صبح الی ۱۹ شب |

**Files updated:**
- `data/demo/options.php` — all theme_mods, social links, stats, floating-chat defaults, blogname/description
- `data/demo/doctors.php` — only the real doctor remains; accurate bio, meta fields (license, brands, years), content with contact links
- `style.css` — description and version bumped to 2.2.0
- `functions.php` — version 2.2.0, added floating-chat require, improved booking_button to respect customizer URL

### B. Chaty Floating Button + Native Floating Chat
- Added complete native floating contact widget (`inc/floating-chat.php`).
  - Customizer section: **Appearance → Customize → دکمه تماس شناور (Native + Chaty)**
  - Channels: WhatsApp, Phone, Telegram (optional), Email
  - Defaults pre-filled with real phone / WhatsApp / email
  - Accessible (keyboard, Escape, focus management, ARIA)
  - Position left/right, reducible motion friendly
- CSS compatibility for the third-party **Chaty** plugin (high z-index so it never sits behind the sticky header or mobile drawer).  
  Recommended: Install Chaty plugin if you prefer its multi-channel UI; the native widget works immediately without any plugin.
- The native widget renders on `wp_footer` priority 30.

### C. Clinic Images Integration
The 8 attached real clinic photos have been prepared with semantic filenames:

```
assets/images/clinic/
├── reception-area.jpg          (پذیرش)
├── doctor-office.jpg           (اتاق پزشک / گواهینامه‌ها)
├── waiting-lounge.jpg          (فضای انتظار با گیاه و مبل)
├── waiting-sofa.jpg            (مبل خاکستری + دیوار چوبی سبز)
├── treatment-room-1.jpg        (یونیت درمان + دیوار چوبی + گیاه دایره‌ای)
├── treatment-room-2.jpg        (یونیت از زاویه دیگر)
├── treatment-room-3.jpg        (یونیت کامل با نور)
└── treatment-room-window.jpg   (یونیت رو به پنجره + ابزار)
```

**How to use them:**
1. Copy the 8 renamed JPG files into `wp-content/themes/fasdent-theme/assets/images/clinic/` (or upload them via Media Library).
2. In the Gallery page template or Elementor gallery widget, select these images.
3. On the single-doctor page and front-page “about doctor / clinic tour” sections, you can hard-reference them with:
   ```php
   <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/clinic/treatment-room-1.jpg' ); ?>" alt="اتاق درمان کلینیک فس‌دنت" loading="lazy" width="1200" height="800">
   ```
4. For better performance convert to WebP after upload (theme already has automatic WebP conversion on upload).

### D. UI/UX & Responsiveness Improvements
- Version bump and cleaner functions.php (booking URL now respects Customizer).
- Floating chat is fully responsive (mobile-friendly launcher, panel, safe areas).
- Existing main.css already contains strong RTL logical properties, mobile drawer (≤960px), touch targets, reduced-motion, and high-contrast support.
- Additional recommended CSS (add to a child-theme `style.css` or append to `assets/css/main.css`):

```css
/* === Extra UI/UX polish & Chaty compatibility (v2.2.0) === */
:root {
  --brand-cyan: #09d4d6;
  --brand-blue: #0e55b1;
  --brand-navy: #071f3f;
}

/* Chaty plugin never sits under sticky header / mobile nav */
#chaty-widget,
.chaty-channels-list,
.chaty-float-button {
  z-index: 99999 !important;
}
@media (max-width: 640px) {
  #chaty-widget {
    inset-block-end: 5.5rem !important; /* avoid collision with native launcher if both active */
  }
}

/* Native floating chat mobile safety */
.fasdent-chat__launcher {
  min-inline-size: 3.25rem;
  min-block-size: 3.25rem;
  touch-action: manipulation;
}
.fasdent-chat__panel {
  max-inline-size: min(22rem, calc(100vw - 1.5rem));
}

/* Clinic image cards – consistent aspect & hover */
.clinic-gallery img,
.gallery-item img {
  aspect-ratio: 4/3;
  object-fit: cover;
  border-radius: var(--radius, 1rem);
  transition: transform 0.35s ease, box-shadow 0.35s ease;
}
.clinic-gallery img:hover,
.gallery-item img:hover {
  transform: scale(1.03);
  box-shadow: 0 12px 32px rgba(7, 31, 63, 0.15);
}

/* Better mobile CTA stacking */
@media (max-width: 640px) {
  .hero-actions,
  .cta-banner .btn {
    width: 100%;
    justify-content: center;
  }
}
```

### E. Responsiveness Checklist (already strong, reinforced)
- Desktop > 1100 px – full header, multi-column
- 961–1100 px – compact desktop
- ≤ 960 px – hamburger + off-canvas drawer, body scroll lock, focus trap
- ≤ 768 px – stacked cards, single-column content
- ≤ 640 px – full-width buttons, safe padding, floating chat offset
- ≤ 380 px – extra-small phone safeguards
- Logical properties + `direction: rtl` throughout
- Touch targets ≥ 44 px, reduced-motion media query respected

---

## 2. Files to Replace / Add in the Repository

| Path | Action |
|------|--------|
| `fasdent-theme/style.css` | Replace (version + description) |
| `fasdent-theme/functions.php` | Replace (v2.2.0 + floating-chat require) |
| `fasdent-theme/inc/floating-chat.php` | **Add new** |
| `fasdent-theme/data/demo/options.php` | Replace (real data) |
| `fasdent-theme/data/demo/doctors.php` | Replace (single real doctor) |
| `fasdent-theme/assets/images/clinic/*.jpg` | **Add the 8 renamed images** (user must copy from attachments) |

Optional but recommended:
- Append the extra CSS block above to `assets/css/main.css` or create `assets/css/site-overrides.css` and enqueue it after main.css.

---

## 3. How to Apply After Pulling the Changes

1. Upload the new/replaced PHP & CSS files to the live theme.
2. Place the 8 clinic images into `assets/images/clinic/`.
3. Go to **Appearance → Customize**:
   - Confirm contact info, hours, social links, stats.
   - Open **دکمه تماس شناور** and verify WhatsApp / phone / email are filled.
4. (Optional) Install & activate the **Chaty** plugin, configure the same phone/WhatsApp/email, and the theme CSS will keep it above everything.
5. Clear all caches (WordPress, CDN, browser, optimization plugins).
6. Re-save Permalinks.
7. Test on real mobile devices (iOS Safari + Chrome Android) for the floating button, drawer, and image gallery.

---

## 4. Next Recommended Steps for Even Better UX

- Add exact clinic address + Google Maps embed (Customizer already has `fasdent_map_embed`).
- Create a proper “Clinic Tour / Gallery” page that uses the 8 new images in a masonry or lightbox grid.
- Add before/after cases (real patient photos with consent) to the gallery CPT.
- Write short Persian service descriptions that mention the implant brands.
- Enable the booking form and test the full appointment flow with the real phone number.
- Run PageSpeed Insights & Lighthouse after the images are optimized to WebP.

---

## 5. Rollback

All previous demo doctors and options are overwritten only when the demo importer is run again.  
If you need the old multi-doctor demo, restore the previous `data/demo/doctors.php` and `options.php` from git history.

---

**Document prepared so you can review, apply, or extend every change later.**  
Any further UI polish (specific Elementor sections, extra micro-interactions, etc.) can be added on top of this foundation.
