# Fasdent Theme v2.6.0 — Final delivery

## New modules

| File | Purpose |
|------|---------|
| `inc/chat-channels-admin.php` | Admin UI: add/remove chat channels + icon picker |
| `inc/landing-blocks.php` | Editable landing blocks (ACF flexible + JSON fallback) |
| `inc/booking-rest.php` | REST API `fasdent/v1/bookings` |
| `inc/theme-featured-images.php` | Theme `assets/images/{slug}.webp` as featured images |
| `inc/admin-bookings.php` | Table + search + CSV export + API key |
| `db-tools/02-v26-seed-kb-ba-images-meta.sql` | Seed data |

## Deploy checklist

1. Deploy theme files from `main`
2. Place generated images in `wp-content/themes/fasdent-theme/assets/images/` as `{slug}.webp`
3. Import SQL: `db-tools/02-v26-seed-kb-ba-images-meta.sql` into `fasdenti_data`
4. **Settings → Permalinks → Save**
5. Appearance → **کانال‌های چت** — verify channels
6. **نوبت‌ها** — set REST API key if needed
7. Flush caches (Rank Math / object cache)

## REST Bookings API

```
GET  /wp-json/fasdent/v1/bookings?status=pending&per_page=50
GET  /wp-json/fasdent/v1/bookings/{id}
POST /wp-json/fasdent/v1/bookings/{id}/status  { "status": "confirmed" }
```

Auth: `X-Fasdent-Key: <key>` or WP Application Password (admin).

## Image naming (from sitemap)

Last URL segment + `.webp`. Homepage: `home.webp`.

Examples: `about.webp`, `dental-laminate.webp`, `what-is-dental-implant.webp`
