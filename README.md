# Fasdent Theme — UI (v3) & Project Overview

A responsive, RTL-first WordPress theme UI used for Fasdent.ir. This repository contains two approaches:

- The canonical PHP-based theme UI (Fasdent UI v3) — authoritative global stylesheet, JS, and PHP helpers.
- A React-based theme (in the `React/` folder) built with Vite + Tailwind for SPA-like frontends and WordPress integration.

This README covers the v3 UI package, quick installation, integration notes, and where to find the React variant.

---

## Quick summary (what changed in v3)
- main.css rebuilt as a single predictable cascade (replace previous main.css completely).
- Unified spacing, tokens, accessible focus, reduced-motion support.
- Fully RTL-aware header/navigation with accessible desktop dropdowns and a mobile off-canvas drawer.
- Font Awesome menu icon injection (server-side; auto-mapping with override).
- Floating contact widget (Customizer-controlled) with channels for WhatsApp, phone, Telegram, email.
- Improved single-post layout, hero, TOC, FAQ, and reusable featured-image prompts.

---

## Package layout (fasdent-ui-v3)
assets/
- css/main.css — authoritative UI and responsive rules
- css/page.css — optional page styles (load after main.css)
- css/single-post.css — optional post styles (load after main.css)
- js/fasdent-ui.js — mobile menu, submenu toggles, floating-chat interactions

inc/
- menu-icons.php — Font Awesome menu icon injection & admin field
- floating-chat.php — Customizer controls + renderer for floating contact

template-parts/
- site-navigation.php — header and navigation markup

featured-image-prompts.txt — AI prompts for featured images  
MAIN-CSS-CHANGELOG.txt

---

## Requirements
- WordPress theme that calls: wp_head(), wp_body_open(), wp_footer()
- A registered 'primary' navigation location
- Licensed Font Awesome 7+ Pro (Kit or self-hosted) when using Pro icons
- PHP 7.4+ recommended
- JavaScript enabled for mobile drawer and interactive widgets

---

## Install & integrate (core steps)
1. Backup your theme files (functions.php, header.php, template-parts, existing CSS/JS).
2. Copy files into the active theme preserving paths (see Package layout).
3. Replace any previous global Fasdent stylesheet — do NOT load two different main.css copies.
4. Include PHP modules from functions.php:
   - require_once get_template_directory() . '/inc/menu-icons.php';
   - require_once get_template_directory() . '/inc/floating-chat.php';
   (Use get_stylesheet_directory() for child themes.)
5. Enqueue assets (example pattern included in the repository — use filemtime() for cache busting).
6. Use the navigation template:
   - <?php get_template_part( 'template-parts/site-navigation' ); ?>
7. Assign the Primary menu in Appearance → Menus and provide phone/booking values in Customizer.

Key selectors required by JS/CSS:
- #primary-menu-toggle, #primary-navigation, .site-header, .site-nav, .nav-backdrop
- .menu-item-has-children / .page_item_has_children, .sub-menu

---

## Floating contact widget
Configure under Appearance → Customize → دکمه تماس شناور. Channels: WhatsApp (use international number), Phone, Telegram (@optional), Email. The widget will not render if disabled or when all channels are empty. Add channels via the `fasdent_floating_chat_channels` filter.

---

## Font Awesome icons
- The theme injects Font Awesome classes into menu items. Provide your licensed Kit separately.
- You may set a custom icon class for each menu item via Appearance → Menus (field: کلاس آیکن Font Awesome).
- Default automatic mappings exist for common menu concepts (Home, Services, Blog, Contact, Appointment, etc.).

---

## RTL & Chaty compatibility
- The v3 stylesheet enforces RTL-friendly layout using logical properties and direction: rtl.
- Patches included for Chaty plugin z-index compatibility and mobile drawer stacking.
- Recommended commit message for these fixes is provided in the repository.

---

## Stylesheet recommendations
Cascade order:
1. Licensed fonts & Font Awesome
2. assets/css/main.css
3. page.css OR single-post.css where needed
4. Child-theme overrides loaded after main.css
5. print.css with media="print" if used

Override tokens in a small child stylesheet instead of editing main.css directly.

---

## React variant & production build
This repo also contains a React-based version in `React/` (Vite + Tailwind) — see `React/README.md` for build instructions. Production build assets are in `React/dist/` (README there).

---

## Contributing & support
- Fork → feature branch → PR
- License: GNU GPL v2 or later
- For support or questions, open an issue or contact the Fasdent development team.

---

## Where to find more
- theme PHP UI: root folders and `inc/`, `assets/`, `template-parts/`
- React theme: `React/` (source), `React/dist/` (production assets)
