# Fasdent — Sample Page System

Beautiful, RTL, HIPAA-aware, WCAG 2.1 AA, FTC-safe WordPress page template
for the Fasdent dental clinic (http://fasdent.ir/).

## Contents

```
fasdent/
├── page.php                              # WordPress page template
├── assets/
│   ├── css/page.css                      # Dedicated stylesheet (load after main.css)
│   └── js/page.js                        # Dedicated JS (no jQuery)
├── inc/
│   └── prompts/page-generator.md         # Master AI generation prompt
├── sample-pages/
│   ├── about-the-clinic.md               # Sample 1 — About page (paste-ready)
│   └── implants-service.md               # Sample 2 — Implants service page
└── README.md
```

## Install

1. Copy `page.php` to the root of your active theme.
2. Copy `assets/css/page.css` and `assets/js/page.js` into your theme's assets.
3. In `functions.php`, enqueue **after** `main.css` / `main.js`:

    ```php
    add_action('wp_enqueue_scripts', function () {
        // Only load on pages that use the Fasdent Sample Page template.
        if (!is_page_template('page.php')) return;
        $ver = filemtime(get_stylesheet_directory() . '/assets/css/page.css');
        wp_enqueue_style(
            'fasdent-page',
            get_stylesheet_directory_uri() . '/assets/css/page.css',
            ['fasdent-main'],
            $ver
        );
        wp_enqueue_script(
            'fasdent-page',
            get_stylesheet_directory_uri() . '/assets/js/page.js',
            [],
            filemtime(get_stylesheet_directory() . '/assets/js/page.js'),
            true
        );
    });
    ```

4. Make sure Font Awesome 6 is loaded (the template uses `fa-solid`, `fa-regular`, `fa-brands`).

## Create a page

1. WP Admin → **Pages → Add New**.
2. Title + content (use blocks/HTML — see `inc/prompts/page-generator.md` for the
   exact classes the CSS styles).
3. In **Page Attributes → Template**, select **Fasdent Sample Page**.
4. Fill Custom Fields (ACF-compatible):

| Meta key | Type | Example |
|---|---|---|
| `fasdent_kicker` | text | خدمات ما |
| `fasdent_subtitle` | text | یک جمله زیرعنوان |
| `fasdent_quick_answer` | textarea | پاسخ ۴۰–۷۰ کلمه‌ای |
| `fasdent_reviewer_name` | text | دکتر مریم رضایی |
| `fasdent_reviewer_credentials` | text | دندانپزشک عمومی |
| `fasdent_reviewer_license` | text | نظام پزشکی: ۱۲۳۴۵۶ |
| `fasdent_review_date` | date (YYYY-MM-DD) | 2026-06-20 |
| `fasdent_reading_time` | number | 6 |
| `fasdent_hero_badges` | textarea (`icon\|label` per line) | fa-solid fa-user-doctor\|بازبینی شده |
| `fasdent_primary_cta_label` | text | دریافت نوبت |
| `fasdent_primary_cta_url` | url | /reserve/ |
| `fasdent_show_toc` | text ("1" or "") | 1 |

Global options (from `wp_options`):

| Option key | Purpose |
|---|---|
| `fasdent_emergency_phone` | Emergency phone number |
| `fasdent_booking_url` | Booking URL (defaults to `/reserve/`) |

## Sample pages

Two ready-to-paste sample pages live in `sample-pages/`:
- `about-the-clinic.md` — About the Clinic
- `implants-service.md` — Dental Implants (service page)

Each file has a `=== META ===` block for the Custom Fields and a
`=== CONTENT ===` block with the HTML to paste into the WordPress editor.

## Compliance notes

- **HIPAA-aware**: no PHI fields on-page. Newsletter form is honeypotted and
  submitted to a REST endpoint you control (make sure it's HTTPS + BAA).
- **WCAG 2.1 AA**: skip link, semantic headings, focus visible, reduced-motion
  respected, RTL logical properties.
- **FTC**: no guarantees, "results may vary" repeated where outcomes are
  discussed.

## License

Provided for the Fasdent theme. Adapt freely inside the theme.
