# Fasdent Demo Data

Sample content for **کلینیک دندانپزشکی فس‌دنت**.

## How to import

1. Activate the Fasdent theme.
2. In wp-admin go to **Appearance → بارگذاری نمونه داده**.
3. Click **شروع بارگذاری نمونه داده**.
4. Open **Settings → Permalinks** and click **Save** (flush rewrite rules).

## What gets created

| Step | Content |
|------|---------|
| Taxonomy | 10 `service_category` terms |
| Services | ~20 `service` posts (price, duration, icon, FAQs, steps, benefits) |
| Doctors | 3 `doctor` posts |
| Testimonials | 8 `testimonial` posts |
| FAQs | 12 `faq` posts |
| Pages | Home, Blog, Appointment, Contact, About, FAQ, Pricing, Gallery, legal pages, Sitemap, Knowledge Base |
| Posts | 5 SEO blog articles |
| Menus | `main-menu`, `footer-menu`, `legal-menu` |
| Options | Customizer mods + site title / tagline / timezone |

## Reset

Use **حذف نمونه داده** on the same admin page. Only content tracked in `fasdent_demo_imported_ids` is removed.

## File map

```
data/demo/
├── import.php          # Admin UI + runner
├── taxonomy-terms.php
├── services.php
├── doctors.php
├── testimonials.php
├── faqs.php
├── pages.php
├── posts.php
├── menus.php
├── options.php
└── inc/                # Legacy wrappers only (require parent files)
```

Do not put new content only under `inc/` — the importer loads files from this directory root.
