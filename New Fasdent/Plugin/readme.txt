=== Alipasandi Service Content ===
Contributors: alipasandi-clinic
Requires at least: 6.4
Requires PHP: 8.2
Stable tag: 1.3.3

Business-logic owner for Service content, NAP, appointment/contact processing, validation, migration, revisions, export and operational health.

== Production Contract ==
1. Back up database and uploads.
2. Install Plugin 1.3.3, then Theme 1.4.28.
3. Configure Settings > اطلاعات کلینیک explicitly (phone/E.164/address/notify email/domain From/booking times and official data).
4. WordPress timezone must be Asia/Tehran.
5. Run Tools > Service Content migration/health explicitly.
6. Deploy exact hashes to Staging and complete environmental evidence before Production.

No service/NAP data is deleted on deactivate/uninstall. No migration runs merely because the theme updates.

== Changelog ==

= 1.3.3 =
* Fixed WP-CLI schema-repair registration token typo that could Fatal when WP_CLI bootstrap was active.
* Schema repair apply is now CLI-only and hard-blocked on Production; dry-run remains read-only.
* Rank Math local-entity observation is version-tagged and freshness-checked; static front/blog-page configuration is included in Health.
* Production debug-display state is surfaced by operational Health.
* Permanent CI gate rejects suspicious escaped-whitespace class tokens and a local WP-CLI registration/dry-run bootstrap smoke is shipped.

= 1.3.2 =
* Stable-key appointment identity, same-day datetime validation and versioned lead-time SSOT.
* Date-aware schedule policy for Theme UI with authoritative server validation.
* Registry duplicate-label warnings and managed-page identity Health checks.
* Explicit non-destructive schema-marker repair dry-run/apply for legacy migrated v1 records.
* Mail-failure attempts do not consume accepted-submission quota; patient phone validation requires real digits.
* Rank Math local entity target observation added to Health.

= 1.3.1 =
* Consolidated every bookable appointment choice into the Service Registry metadata SSOT; removed the secondary hard-coded extras list.
* Raised minimum supported PHP to 8.2 rather than claim unexecuted PHP 8.0 compatibility.
* Added strict six-field Registry schema validation; malformed/unknown-field entries fail closed and surface as Site Health Critical.
* Added Plugin-owned booking-horizon SSOT consumed by both server validation and Theme HTML max.
* Added Registry validation details and horizon to operational Health output.

= 1.3.0 =
* Registry metadata SSOT for page-backed services plus explicit appointment extras.
* Semantic E.164/country/geo/booking-slot/opening-hours validation.
* Canonical OpeningHoursSpecification integration for Rank Math.
* Calendar-valid WP-timezone appointment requests with +365-day horizon and schedule-aware validation when official hours exist.
* Explicit notify/From/From-name configuration; no Gmail fallback; forms fail closed on invalid mail config.
* Safer client-IP handling and per-action privacy-minimized rate limit.
* Expanded Site Health for NAP/mail/timezone/version/Rank Math/legacy fallback.
* Preserved service meta/schema compatibility and activation collision safety.

= 1.2.0 =
* Previous 1.4.24 companion plugin; superseded by later releases.

== WP-CLI / Schema Repair Safety ==
* Commands: `wp alipasandi service migrate`, `wp alipasandi service health`, `wp alipasandi service schema-repair`.
* `schema-repair` without `--apply` is the mandatory read-only dry-run.
* `schema-repair --apply` is blocked when `wp_get_environment_type()` is `production` and also requires `--confirm-plan-sha=<SHA256>` copied from the immediately preceding dry-run.
* Apply is permitted only on an isolated Staging clone after DB backup + Service JSON export + payload SHA capture. The full dry-run must PASS before any write; the apply path revalidates every record before the first write and attempts rollback if post-write verification fails. Any unknown schema version, sanitizer payload drift, non-array meta or hash drift is a STOP condition.
* Production data must never be used as a destructive test environment.
