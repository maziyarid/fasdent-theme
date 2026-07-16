# Fasdent Demo Data

Sample content for **کلینیک دندانپزشکی فس‌دنت**.

## How to import

1. Activate the Fasdent theme.
2. In wp-admin go to **Appearance → بارگذاری نمونه داده**.
3. Click **شروع بارگذاری نمونه داده**.
4. Open **Settings → Permalinks** and click **Save**.

## What gets created

| Step | Content |
|------|---------|
| Taxonomy | 10 `service_category` terms |
| Services | ~20 `service` posts |
| Doctors | 3 `doctor` posts |
| Testimonials | 8 `testimonial` posts |
| FAQs | 12 `faq` posts |
| Pages | Home, Blog, Appointment, Contact, About, FAQ, Pricing, Gallery, legal pages, Sitemap, Knowledge Base |
| Posts | **29** SEO blog articles (content plan) with trust meta + internal links |
| Menus | `main-menu`, `footer-menu`, `legal-menu` |
| Options | Customizer mods + site title / tagline / timezone |

## Blog posts

Posts load from `posts-data/batch-*.php` and include:

- H2 structure aligned with the content spreadsheet
- `quick_answer`, clinical reviewer, disclaimers, citations, FAQs
- Internal links between related posts and to `/service/*` + `/appointment/`
- Categories: implant, insurance, tooth-replacement, cosmetic, dentistry, tehran, restoration, cost

## Reset

Use **حذف نمونه داده** on the same admin page.

## File map

```
data/demo/
├── import.php
├── taxonomy-terms.php
├── services.php
├── doctors.php
├── testimonials.php
├── faqs.php
├── pages.php
├── posts.php
├── posts-data/
│   ├── batch-1.php
│   ├── batch-2.php
│   └── batch-3.php
├── menus.php
├── options.php
└── inc/   # legacy wrappers
```
