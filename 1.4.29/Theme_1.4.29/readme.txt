=== Dr Keyvan Alipasandi Dental Clinic ===
Contributors: clinic-web-team
Requires at least: 6.4
Requires PHP: 8.2
Stable tag: 1.4.26
License: GPLv2 or later

Native RTL presentation theme. Design/Color/Layout/Grid/Typography/Hero/URL remain locked in 1.4.26.

== Production Contract ==
* Production pairing: Theme 1.4.26 + Alipasandi Service Content 1.3.1.
* Theme is presentation/read-only compatibility only. It does not own Service writes, NAP writes, form processing, mail, migration, or SEO admin fields.
* Plugin missing/outdated: forms fail closed and render an unavailable/direct-contact state; Site Health is Critical.
* Rank Math is the production metadata/canonical/schema/sitemap owner. Theme keeps a read-only emergency fallback only when no SEO owner is active.
* Appointment UI means REQUEST, not real-time availability or confirmed reservation.
* HTML date maximum consumes Plugin 1.3.1 booking-horizon SSOT; Theme does not maintain a second horizon constant.
* No starter page is auto-created on theme activation.

== Changelog ==
= 1.4.26 =
* Targeted patch after client review: HTML booking max now consumes the Plugin-owned horizon SSOT.
* Raised minimum supported PHP to 8.2 rather than claim unexecuted PHP 8.0 compatibility.
* Appointment unavailable state now exposes explicit polite live-region semantics.
* Production pairing raised to Plugin 1.3.1; no Design/URL/Layout change.

= 1.4.25 =
* Restricted theme fallback to read-only rendering and disabled duplicate form/business logic.
* Removed theme page provisioning and theme SEO admin/write ownership.
* Added fail-closed plugin-missing form UX and request-only appointment wording.
* Removed unverified runtime domain/Gmail/map/social defaults and moved site URL to home_url().
* Added dedicated editor stylesheet and removed accessibility-ready claim pending evidence.
* Added permanent SEO fallback regression test and production contracts/runbooks.

= 1.4.24 =
* Fixed the 1.4.22 canonical fallback fatal by using wp_get_canonical_url(). Superseded by later releases.

= 1.4.22 =
* Historical known-critical release: unavailable canonical API in SEO fallback. Do not deploy.
