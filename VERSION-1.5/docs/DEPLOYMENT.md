# Deployment Guide — Version 1.5.0

## 1. Prepare

1. Take a full backup of the current production files and database.
2. Note the currently active theme and plugin versions.
3. Confirm you have FTP / SFTP / file-manager access and wp-admin access.

## 2. Upload Only the Canonical Folders

- Upload the contents of `Theme/` into  
  `wp-content/themes/<active-theme-slug>/`  
  (overwrite existing files; do not leave old Version-* folders inside the theme directory).

- Upload the contents of `Plugin/` into  
  `wp-content/plugins/alipasandi-service-content/`  
  (or the exact slug used previously).

**Do not upload** any of these folders to the live server:

- 1.4.29, Updated 1.4.29, New Fasdent, Manually updated, React, Version 1.4.x, Chat History, historical reports.

## 3. Activate

1. In wp-admin → Plugins: activate the companion plugin if not already active.
2. In Appearance → Themes: activate the theme if needed.
3. Confirm the version numbers shown match 1.5.0 / 1.2.0.

## 4. Cache & Settings

1. Purge every cache layer:
   - WordPress cache plugin
   - Object cache / Redis if present
   - Server / LiteSpeed / Nginx cache
   - CDN (Cloudflare etc.)
   - Browser cache (or test in private window)
2. Open Customizer and verify:
   - Site identity / logo settings (note: this theme uses a code-based brand mark, not necessarily the Custom Logo)
   - Phone, address, opening hours
   - Floating chat / contact channels
3. Confirm Settings → Reading uses the correct front page.

## 5. Verify

Follow the checklist in `ACCEPTANCE-CRITERIA.md`.  
Collect the evidence screenshots and network/console results.

## 6. Rollback

If the site is worse after deploy:

1. Restore the previous theme/plugin files from the backup taken in step 1.
2. Purge caches again.
3. Report the exact difference observed so the register can be updated.

## Asset Paths

All theme assets use `get_template_directory_uri()`.  
After moving the theme folder the paths update automatically.  
If you see 404s for CSS/JS/images, the wrong folder was activated or cache is still serving old URLs.
