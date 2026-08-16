# Fasdent Version 1.5 — Deployment Procedure

## Package Layout

The WordPress ZIP must contain the theme root directly. Do not upload a ZIP that creates `VERSION-1.5/Theme/` inside `wp-content/themes/`.

Correct theme ZIP structure:

```text
fasdent-theme-1.5.0/
├── style.css
├── functions.php
├── front-page.php
├── header.php
├── footer.php
├── assets/
├── inc/
└── template-parts/
```

Correct plugin ZIP structure:

```text
alipasandi-service-content-1.2.0/
├── alipasandi-service-content.php
├── includes/
└── readme.txt
```

## Pre-Deployment Stop Conditions

Stop if the active production versions are unknown, the backup is missing, the package hash is not recorded, or the package has nested/historical/prototype files.

## Deployment Sequence

1. Record active theme/plugin versions and WordPress/PHP versions.
2. Create a full database and `wp-content/uploads` backup.
3. Verify that the backup can be accessed and restoration instructions exist.
4. Put the site in a maintenance window if necessary.
5. Install the exact Version 1.5.0 theme package.
6. Install and activate the exact Version 1.2.0 plugin package.
7. Confirm WordPress Address and Site Address.
8. Confirm the static homepage and menus.
9. Confirm logo, site icon, phone, address, hours, booking URL, and floating-chat settings.
10. Purge WordPress, object, server, CDN, and browser caches.
11. Test the canonical HTTPS host and all redirect variants.
12. Run the P0/P1 acceptance checklist.
13. Record evidence and rollback reference.

## Rollback

If a P0 check fails:

1. Stop public delivery approval.
2. Preserve the failure evidence.
3. Restore the previous approved theme/plugin package or backup.
4. Purge caches again.
5. Re-run the smoke test.
6. Record the rollback and new corrective action.

Never delete the only backup or modify production database content while troubleshooting without a fresh backup.