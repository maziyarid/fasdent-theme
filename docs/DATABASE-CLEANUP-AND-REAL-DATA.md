# Fasdent Database – Cleanup, Security, Performance & Real Data Injection

**File:** `db-tools/01-cleanup-security-performance-real-data.sql`  
**Date:** 2026-07-23  
**Database:** `fasdenti_rd` (prefix `fd_`)  
**Source dump:** `attachments/db.sql` (MariaDB 10.6)

---

## Why this approach?

Instead of re-running the PHP demo importer (which is designed for fresh installs), we inject the real doctor data, contact information, floating-chat settings and clean the existing production-like database **directly**.

This is safer, faster and preserves all Rank Math SEO data, existing services, menus, bookings history, ACF fields, etc.

---

## What the SQL script does

### 1. Security hardening
- Forces `users_can_register = 0`
- Closes comments & pingbacks by default
- Permanently deletes the two fictional/trashed demo doctors (Sara & Reza) – only real Dr. Keyvan remains (ID 27)
- Removes the default “Hello World” comment
- Cleans orphaned postmeta
- Clears leftover mailserver credentials
- (Optional) can force session re-login

### 2. Performance cleanup
- Deletes completed / failed / canceled Action Scheduler rows older than 7 days
- Cleans Rank Math 404 logs
- Removes all expired transients (`_transient_*` and `_site_transient_*`)
- Removes old auto-drafts
- Runs `OPTIMIZE TABLE` + `ANALYZE TABLE` on all critical tables

### 3. Real data injection
**Doctor (post ID 27)**
- Title, slug, excerpt and full bio updated with accurate information
- License: **۱۹۱۷۴۰**
- Title: **دکتری حرفه‌ای (ایمپلنتولوژیست)**
- Experience: **۱۰+**
- Brands: **Bego, Megagen, Straumann, Sic, 3zahn**
- Contact links (phone, email, both Instagrams) embedded in content

**Theme Mods (`theme_mods_fasdent-theme`)**
- Clinic name, doctor name, phone, international phone
- Email: `Dr.keyvan.alipasandii@gmail.com`
- Hours: `از ساعت ۱۱ صبح الی ۱۹ شب`
- Instagram: `@Dr.keyvan_alipasandi`
- WhatsApp ready
- Stats adjusted to realistic numbers (5000+, ۱۰+, 2000+)
- Full floating-chat settings pre-filled (enabled, position, WhatsApp, phone, email)
- Booking URL set

**Core options**
- `blogname` and `blogdescription` updated
- Admin email set to the real Gmail
- Timezone & date formats confirmed

### 4. Final optimization
- `OPTIMIZE TABLE` + `ANALYZE TABLE` statements included
- (Commented) optional conversion of key tables to InnoDB for better concurrency

---

## How to apply

### Option A – phpMyAdmin / Adminer
1. Take a full database backup.
2. Select database `fasdenti_rd`.
3. Go to SQL tab.
4. Paste the entire content of `01-cleanup-security-performance-real-data.sql`.
5. Click Go / Execute.
6. Clear all caches (Rank Math, object cache, page cache, CDN).
7. Log out of WordPress and log back in.
8. Visit **Appearance → Customize** and verify the floating chat + contact fields.
9. Go to **Settings → Permalinks → Save** (flush rewrite rules).

### Option B – WP-CLI (recommended if available)
```bash
wp db query < db-tools/01-cleanup-security-performance-real-data.sql --path=/path/to/wordpress
wp cache flush
wp rewrite flush
```

### Option C – Fresh import of original dump + script
```bash
mysql -u USER -p fasdenti_rd < attachments/db.sql
mysql -u USER -p fasdenti_rd < db-tools/01-cleanup-security-performance-real-data.sql
```

---

## After running – verification checklist

- [ ] Homepage shows correct doctor name, hours, phone, stats
- [ ] `/doctors/dr-keyvan-alipasandi/` shows updated bio + license + brands
- [ ] Floating contact button appears (native widget) with WhatsApp / Phone / Email
- [ ] Customizer → دکمه تماس شناور shows correct values
- [ ] No “دکتر سارا” or “دکتر رضا” appear anywhere
- [ ] Comments are closed by default on new posts
- [ ] Action Scheduler table is much smaller
- [ ] Site still loads quickly (object cache / page cache still work)

---

## Security notes

- The admin password hash is **not** changed (you keep full control).
- Session tokens are left intact so you stay logged in; if you want to force everyone to re-login, uncomment the `DELETE ... session_tokens` line.
- Registration remains closed.
- Default comment/ping status is now closed.
- No credentials are stored in plain text.

---

## Performance notes

- Expired transients and old Action Scheduler rows are the biggest free wins.
- `OPTIMIZE TABLE` reclaims space and rebuilds indexes.
- For even better performance on high traffic:
  - Enable object caching (Redis / Memcached)
  - Keep Rank Math analytics limited
  - Use a good page-cache plugin + CDN
  - Convert remaining MyISAM tables to InnoDB if the host supports it

---

## Rollback

If anything goes wrong, simply restore the full backup you took before running the script.

---

**This script + the earlier theme v2.2.0 changes complete the transition from demo content to real clinic data, with a cleaner, more secure and faster database.**
