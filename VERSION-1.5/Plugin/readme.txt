=== Alipasandi Service Content ===
Contributors: alipasandi-clinic
Requires at least: 6.4
Requires PHP: 8.0
Stable tag: 1.2.1

Portable service-page content for Alipasandi Clinic. Owns registered post meta,
the accessible editor UI, explicit migration, revisions, validation, export,
logging and health checks. The theme owns presentation only.

== Installation ==
1. Back up the database.
2. Install and activate this plugin before theme 1.4.24.
3. Open Tools > Service Content.
4. Run Migration / Retry explicitly and require Health PASS.

No migration runs on front-end requests or merely because the theme updates.

== Changelog ==

= 1.2.1 =
* Configure default form notifications for clinic@fasdent.ir and Dr.keyvan.alipasandii@gmail.com.
* Configure noreply@fasdent.ir as the default production From address.

= 1.2.0 =
* Moved NAP settings UI, form processing, rate limiting and Rank Math integration out of the theme.
* Added dry-run migration, duplicate-key fail-closed behavior, Site Health and compatibility checks.
* Data remains intact on deactivation/deletion; no uninstall cleanup is registered.

= 1.0.1 =
* Prevent a duplicate-function fatal during activation when theme 1.4.21 has
  already loaded its service compatibility module.

= 1.0.0 =
* Initial portable service-content release.
