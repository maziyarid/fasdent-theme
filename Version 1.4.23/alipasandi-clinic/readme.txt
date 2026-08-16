=== Dr Keyvan Alipasandi Dental Clinic ===
Contributors: clinic-web-team
Requires at least: 6.4
Requires PHP: 8.0
Stable tag: 1.4.23
License: GPLv2 or later

A native RTL WordPress theme for the Dr Keyvan Alipasandi dental clinic. React, Vite, Tailwind, Node, and a front-end build step are not required.

== Installation ==
1. In WordPress, open Appearance > Themes > Add New > Upload Theme.
2. Upload alipasandi-clinic.zip and activate it.
3. Missing starter pages are created once on activation. Existing pages are not overwritten.
4. The approved primary navigation is intentionally locked to Home, About, Services, and Contact so desktop and mobile cannot drift apart.
5. Install Site Plugin 1.1.0 and edit operational NAP in Settings > اطلاعات کلینیک.
6. Configure the domain From address and SMTP; contact and appointment requests use wp_mail().

== Included pages ==
- Home
- About
- Services
- Implant, zirconia crown, surgery and gum, and general dentistry
- Contact
- Appointment request
- FAQ
- Native WordPress post archives and single posts

== Notes ==
- The home menu intentionally contains only Home, About, Services, and Contact.
- Articles remain available through the footer and internal links, but are intentionally omitted from the premium primary navigation. Gallery promotional surfaces remain omitted.
- The appointment is a request until clinic staff contacts the patient to confirm it.
- All client-provided core images and the Vazirmatn webfont are packaged locally; the public site has no font CDN dependency.
- Built-in SEO fields support title, meta description, canonical URL and Open Graph data. When a recognized SEO plugin is active, the plugin owns public metadata to prevent duplicates.
- Breadcrumb, Dentist, Service and truthful Article schema are included. Review/rating schema is never fabricated.
- A complete Persian client-requirements audit is included in docs/CLIENT-REQUIREMENTS-AUDIT-FA.md.
- Read docs/SEO-LAUNCH-CHECKLIST-FA.md before replacing an existing live site.

== Changelog ==
= 1.4.23 =
* Fixed fatal in SEO fallback by using the WordPress canonical API `wp_get_canonical_url()` with a safe permalink fallback.

= 1.4.22 =
* Moved operational NAP UI, form processing and Rank Math NAP integration to Site Plugin 1.1.0.
* Added plugin compatibility warnings and production health integration.

= 1.4.21 =
* Added compatibility with the dedicated Alipasandi Service Content plugin.
* Hardened optional-section rendering and image/ALT handling.
* Added release and production-preflight documentation.

= 1.4.20 =
- Service meta: auth per-object edit_post; metadata_exists fallback (empty fields no longer fall back to legacy); schema_version; per-page migration status; revision copy/restore; autosave/incomplete POST do not clear meta; field-specific sanitization (wp_kses for body/FAQ); optional image_id (Attachment ID); show_in_rest=false.
- No design/URL/layout changes.

= 1.4.19 =
- Service Content Migration: structured content for Implant/Crown/Surgery/General stored as registered post meta (_alipasandi_service). Editable in Page editor meta box.
- Priority: Post Meta → legacy service-data.php (read-only fallback). Migration is idempotent; never overwrites admin-edited meta on theme update.
- Single semantic H1 unchanged (title + title_gold is presentation only via span.gold-text).
- No URL, slug, layout, CSS, design, or SEO metadata changes.

= 1.4.18 =
- Added Indexability Matrix (docs/INDEXABILITY-MATRIX-FA.md).
- Expanded Maintenance Guide: exact option names, form data retention (email-only), redirect/tracking owners, update process.
- No design, layout, token, or functional template changes.

= 1.4.17 =
- Added Persian Maintenance Guide (docs/MAINTENANCE-GUIDE-FA.md): how to change NAP, SEO owner, blog, redirects, backup/rollback, theme update, tracking ownership, target WP/PHP versions.
- Design System explicitly locked. No functional, design, token, layout, or composition changes.
- Asset audit: no unused/deprecated image files in package; all referenced assets retained.

= 1.4.16 =
- NAP and operational clinic settings now stored as WordPress options (site-level), not theme_mod. Data survives theme switch. Customizer UI unchanged. One-time migration copies any existing theme_mod values.
- HTML email template: removed jsDelivr Vazirmatn CDN; uses Tahoma/Arial/sans-serif only for reliability. Frontend fonts remain fully local.
- No design, token, layout, or composition changes.

= 1.4.15 =
- Hero location text now reads fully from Customizer (clinic_city + clinic_street) — complete NAP Single Source of Truth.
- Designer credit in footer is fully optional: empty Customizer value = no output (no fallback text, no forced credit).
- Added optional Customizer fields: clinic_postal_code, clinic_geo_lat, clinic_geo_lng. Schema outputs postalCode / GeoCoordinates only when real values are provided (never fabricated).
- No design, token, layout, or composition changes.

= 1.4.2 =
- Kept all six approved color values unchanged while adding restrained Brown Light depth to the homepage hero.
- Integrated the doctor and implant imagery into the mobile hero with CSS edge blending; the doctor portrait pixels and identity remain untouched.
- Replaced the decorative implant render with a clean titanium implant and white ceramic crown on the approved brown background.
- Renamed the doctor asset to the palette-neutral doctor-keyvan-alipasandi filename and updated every reference, including SEO fallback imagery.
- Verified 320, 360, 375, 390, 414 and 430px widths for overflow, CTA height and first-view priority.
- Added a dual-color focus ring that remains visible on both light and dark surfaces.

= 1.4.1 =
- Added the approved brown, espresso, cream and caramel tokens as the shared WordPress/Figma color contract.
- Applied the recolor to the homepage only; internal templates intentionally remain pending client color approval.
- Kept the approved v1.4.0 layout, content hierarchy, responsive behavior and component geometry unchanged.
- Replaced Figma Home external placeholders with the same locally packaged doctor, clinic and implant assets used by the theme.
- Verified key text/background combinations against WCAG AA and kept caramel out of normal-size text on cream surfaces.

= 1.4.0 =
- Locked the header and footer to the same approved Dr Keyvan Alipasandi / DENTAL CLINIC wordmark.
- Added the approved tooth mark as SVG, PNG and Apple touch favicons.
- Added accessible desktop and mobile Services submenus for the four independent service pages.
- Established explicit dark-text contexts for white and bone sections to prevent white-on-white content.

= 1.3.0 =
- Completed the homepage below the approved header with a premium treatment-approach section, a three-step patient journey, and a crawlable FAQ preview.
- Strengthened the alternating charcoal, bone, and white visual rhythm while preserving the four-service card system.
- Made the doctor identification visible inside the homepage hero composition on both desktop and mobile.
- Added mobile-first full-width actions and responsive two-column/three-column reflow for the new sections.
- Kept all new interactions dependency-free and reused the existing accessible accordion behavior.

= 1.2.2 =
- Kept the doctor portrait mandatory in both desktop and mobile homepage heroes and corrected its responsive image sizing hint.
- Prioritized the doctor portrait as the single high-priority homepage hero image while keeping the implant visual eager.
- Made the mobile overlay independently scrollable in short viewports and disabled all background interactions, including the header logo.
- Added the scoped phase-one Figma handoff prompt under docs.

= 1.2.1 =
- Corrected the approved RTL header order: logo right and booking action left on desktop; logo right and menu toggle left on mobile.
- Updated the client review reference while preserving the approved homepage composition.

= 1.2.0 =
- Unified desktop and mobile navigation from one four-link source.
- Added per-page SEO metadata, canonical, Open Graph, Breadcrumb, Dentist, Service and Article schema foundations.
- Added responsive WebP image sources, explicit dimensions and deliberate hero loading priorities.
- Preserved articles outside the main navigation and kept four independent service landing pages.
- Changed the desktop breakpoint to 1200px and kept service cards at one column below 768px.
- Added a real modal mobile-menu focus trap/inert background and appointment loading/disabled feedback.
- Treatment-count claims are now hidden by default until explicitly approved.

= 1.1.0 =
- Rebuilt the RTL layout foundation and removed reliance on inherited direction.
- Bundled Vazirmatn locally and versioned assets to prevent stale CSS caching.
- Fixed mobile menu stacking, logged-in administrator header offsets, and no-JavaScript content visibility.
- Added a persistent mobile call/booking bar and improved responsive form, navigation, and card behavior.

= 1.0.0 =
- Initial native WordPress release.
