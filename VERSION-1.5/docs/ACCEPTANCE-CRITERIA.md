# Acceptance Criteria & Production Bug Checklist — 1.5.0

The design was confirmed. The live site showing broken hero / logo / sections is a **deployment / environment** problem until proven otherwise.

## Pre-Deploy Identity Check (P0)

- [ ] Exact theme folder installed on server matches this package’s `Theme/`
- [ ] Exact plugin folder matches this package’s `Plugin/`
- [ ] Active theme version shown in wp-admin = 1.5.0
- [ ] Active plugin version = 1.2.0
- [ ] No files from historical Version folders are present in the active theme directory
- [ ] Production database backup completed and verified

## Visual Integrity (P0)

- [ ] Logo (text “Dr Keyvan Alipasandi” + tooth icon) is visible on desktop header
- [ ] Same logo visible on mobile header
- [ ] Hero section has non-zero height and shows the implant/clinic image
- [ ] Hero image loads without 404 at mobile and desktop widths
- [ ] Hero text is readable (contrast) and correctly RTL-aligned
- [ ] Header does not overlap or clip the hero
- [ ] Service cards, doctor section, footer render without missing images or empty columns
- [ ] Floating contact / chat widget appears with correct channels (or is intentionally hidden)
- [ ] No broken-image placeholders or missing-icon squares

## Technical Integrity (P0)

- [ ] Browser console: zero uncaught JavaScript errors
- [ ] Network panel: theme.css, rtl.css, theme.js, all hero images, font files return HTTP 200
- [ ] No mixed-content (HTTP assets on HTTPS page)
- [ ] No CSS or JS MIME-type errors
- [ ] Cache fully purged (WordPress + server + CDN + browser)
- [ ] Mobile menu opens, closes, Escape key works, backdrop behaves
- [ ] Forms (booking + contact) submit successfully and show user-safe messages

## Data & SEO (P1)

- [ ] Customizer / plugin settings contain production phone, address, hours
- [ ] No test/demo or localhost URLs remain
- [ ] Canonical host is consistent (www vs non-www, HTTPS only)
- [ ] Schema / Rank Math does not emit contradictory NAP

## Evidence to Collect Before Declaring Done

1. Desktop screenshot of homepage (hero + logo visible)
2. Mobile screenshot of homepage
3. Network panel export or list of any 404s
4. Console log (clean)
5. Screenshot of Appearance → Themes showing version 1.5.0
6. Screenshot of Plugins list showing companion plugin active
7. Confirmation that cache was purged

## What Is Explicitly Out of Scope for This Code Release

- Live Core Web Vitals numbers
- Full accessibility audit with axe/WAVE on production
- SPF / DKIM / DMARC mail configuration
- Google Business Profile / Search Console verification
- Backup restore rehearsal (must be done by host)
- Any redesign or new visual direction not already approved

These items are environment or operational tasks. They do not require further theme code changes.
