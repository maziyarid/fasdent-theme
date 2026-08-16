# Fasdent / Alipasandi Clinic Theme — Release 1.5.0

**Canonical Production Release**

This package is the single source of truth for the Fasdent.ir / Dr. Keyvan Alipasandi Dental Clinic WordPress site.

## Package Contents

```
Fasdent-Theme-1.5.0/
├── Theme/                  # WordPress theme (presentation layer)
│   ├── style.css           # Theme header — Version 1.5.0
│   ├── functions.php
│   ├── front-page.php
│   ├── header.php / footer.php
│   ├── assets/
│   │   ├── css/theme.css + rtl.css
│   │   ├── js/theme.js
│   │   └── images/         # Approved hero, doctor, clinic assets + favicons
│   ├── inc/                # icons, images, seo, forms helpers
│   └── template-parts/
├── Plugin/                 # Companion Site Functionality plugin (v1.2.0)
│   ├── alipasandi-service-content.php
│   ├── includes/           # forms, validators, health-checks, service-content, settings
│   └── readme.txt
└── docs/
    ├── CANONICAL.md
    ├── REQUIREMENTS-REGISTER.md
    ├── ACCEPTANCE-CRITERIA.md
    ├── DEPLOYMENT.md
    └── CLIENT-HANDOFF-PROMPT.md
```

## Version Identity

| Component              | Version | Role                                      |
|------------------------|---------|-------------------------------------------|
| Theme                  | 1.5.0   | Presentation, RTL layout, assets, SEO meta |
| Plugin                 | 1.2.0   | Services CPT, forms, validation, settings  |
| WordPress minimum      | 6.4     |                                           |
| PHP target             | 8.0+    |                                           |
| Language               | fa_IR   | RTL, Persian                              |

**All previous folders (Version 1.4.x, Updated 1.4.29, New Fasdent, Manually updated, React experiments, historical reports) are archived and must not be deployed.**

## What 1.5.0 Is

- Clean extraction of the last stable theme baseline (1.4.24) + plugin 1.2.0
- Version numbers synchronized
- Single canonical folder structure
- Explicit acceptance criteria and requirement register
- Deployment and cache-purge instructions
- Client hand-off prompt designed to stop open-ended AI re-audits

## What 1.5.0 Is Not

- A redesign
- A new feature set
- A full production environment validation (DNS, SMTP, Rank Math live, Core Web Vitals, backup restore)
- A claim that the live server currently runs this exact package

## Installation (Summary)

1. Backup current production (files + database).
2. Upload **only** the `Theme/` folder contents into `wp-content/themes/alipasandi-clinic/` (or the active theme slug).
3. Upload **only** the `Plugin/` folder contents into `wp-content/plugins/alipasandi-service-content/`.
4. Activate the plugin, then activate the theme.
5. Purge all caches (WordPress, object, page, CDN, browser).
6. Verify logo (text + tooth icon), hero images, navigation, forms.
7. Confirm Customizer values (phone, address, hours, floating chat channels).
8. Record the exact theme and plugin versions shown in wp-admin.

Full steps: see `docs/DEPLOYMENT.md`.

## Critical Known Live-Site Risks (Environment, not code)

The design was approved. Observed broken hero / logo on the live site are almost always caused by one or more of:

- Wrong or incomplete theme package uploaded
- Stale CSS/JS/image cache
- Missing production media or Customizer logo assignment
- Mixed HTTP/HTTPS asset URLs
- PHP fatals or missing plugin that prevent full template output
- Multiple competing navigation/JS controllers from earlier experimental branches

These cannot be fixed by more code changes in the repository. They require server-side verification using the checklist in `docs/ACCEPTANCE-CRITERIA.md`.

## License & Ownership

Client deliverable for Fasdent.ir / Dr. Keyvan Alipasandi clinic. Source retained in the public repository for transparency.
