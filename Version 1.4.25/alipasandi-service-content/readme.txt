=== Alipasandi Service Content ===
Contributors: alipasandi-clinic
Requires at least: 6.4
Requires PHP: 8.0
Stable tag: 1.3.0

Business-logic owner for Service content, NAP, appointment/contact processing, validation, migration, revisions, export and operational health.

== Production Contract ==
1. Back up database and uploads.
2. Install Plugin 1.3.0, then Theme 1.4.25.
3. Configure Settings > اطلاعات کلینیک explicitly (phone/E.164/address/notify email/domain From/booking times and official data).
4. WordPress timezone must be Asia/Tehran.
5. Run Tools > Service Content migration/health explicitly.
6. Deploy exact hashes to Staging and complete environmental evidence before Production.

No service/NAP data is deleted on deactivate/uninstall. No migration runs merely because the theme updates.

== Changelog ==
= 1.3.0 =
* Registry metadata SSOT and appointment options derived from registry plus explicit extras.
* Semantic E.164/country/geo/booking-slot/opening-hours validation.
* Canonical OpeningHoursSpecification integration for Rank Math.
* Calendar-valid WP-timezone appointment requests with +365-day horizon and schedule-aware validation when official hours exist.
* Explicit notify/From/From-name configuration; no Gmail fallback; forms fail closed on invalid mail config.
* Safer client-IP handling and per-action privacy-minimized rate limit.
* Expanded Site Health for NAP/mail/timezone/version/Rank Math/legacy fallback.
* Preserved service meta/schema compatibility and activation collision safety.

= 1.2.0 =
* Previous 1.4.24 companion plugin; superseded by 1.3.0.
