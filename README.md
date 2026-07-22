# Fasdent Theme UI

A responsive, RTL-first WordPress theme UI package for [Fasdent.ir](https://fasdent.ir), maintained in the [maziyarid/fasdent-theme](https://github.com/maziyarid/fasdent-theme) repository.

The system uses the Fasdent logo colours as its visual foundation:

- **Fasdent cyan:** `#09D4D6`
- **Fasdent blue:** `#0E55B1`
- **Deep navy:** `#071F3F`
- **Background:** `#F7FAFC`
- **Primary surface:** `#FFFFFF`

The current core release is **Fasdent UI v3**. It replaces the earlier layered `main.css` with one clean cascade and preserves the class names used by the existing WordPress templates and JavaScript.

> [!IMPORTANT]
> Replace the previous `main.css` completely. Do not append v3 after an older `main.css`, and do not enqueue two different copies of the global stylesheet. Duplicate versions will cause conflicting header, card, footer, spacing, and breakpoint rules.

---

## Contents

1. [What was completed](#what-was-completed)
2. [Package contents](#package-contents)
3. [Requirements](#requirements)
4. [Installation](#installation)
5. [WordPress integration](#wordpress-integration)
6. [Header and navigation](#header-and-navigation)
7. [Font Awesome menu icons](#font-awesome-menu-icons)
8. [Floating contact widget](#floating-contact-widget)
9. [RTL header and Chaty fixes](#rtl-header-and-chaty-fixes)
10. [Stylesheet architecture](#stylesheet-architecture)
11. [Responsive behaviour](#responsive-behaviour)
12. [Blog single posts](#blog-single-posts)
13. [Existing template parts](#existing-template-parts)
14. [Featured-image prompts](#featured-image-prompts)
15. [Accessibility](#accessibility)
16. [Customization](#customization)
17. [Testing and deployment](#testing-and-deployment)
18. [Troubleshooting](#troubleshooting)
19. [Security and privacy notes](#security-and-privacy-notes)
20. [Version history](#version-history)

---

## What was completed

### Global UI system

- Rebuilt `main.css` as one predictable cascade.
- Removed duplicate and conflicting header, card, footer, form, and responsive rules.
- Introduced consistent spacing, typography, radii, borders, shadows, focus rings, and transitions.
- Used CSS logical properties where practical for RTL/LTR compatibility.
- Centered visual components such as cards, statistics, CTA content, ratings, and sharing controls.
- Kept long-form article text start-aligned for readability.
- Improved buttons, forms, cards, breadcrumbs, FAQs, tables, galleries, comments, pagination, search, footer, and shared WordPress components.
- Added reduced-motion and increased-contrast support.

### Header and navigation

- Sticky professional header and optional top bar.
- Desktop navigation placed immediately beside the logo in RTL, while telephone and booking actions remain at the far inline end.
- Correct nested dropdown menus instead of displaying child items at the parent level.
- Keyboard-accessible desktop dropdown behaviour.
- Animated hamburger button below the desktop breakpoint.
- Off-canvas mobile navigation drawer with backdrop.
- Mobile submenu toggles and indentation.
- Escape-to-close, outside-click closing, focus trapping, and body scroll locking.
- Header actions for telephone and appointment booking.

### Font Awesome integration

- Font Awesome icon markup is injected into every WordPress menu item.
- Automatic icon selection based on common Persian/English menu titles.
- Editable Font Awesome class field under **Appearance → Menus**.
- Compatible with licensed Font Awesome 7+ Pro assets.
- No proprietary Font Awesome font or CSS files are distributed in this package.

### Floating contact widget

- Customizer-controlled floating contact launcher.
- WhatsApp, telephone, Telegram, and email channels.
- Left/right placement.
- Configurable labels and introductory text.
- Attention-seeking animation that respects reduced-motion settings.
- Expandable panel, close button, Escape handling, outside-click closing, and focus management.
- Filter for adding custom channels later.

### Blog and page layouts

- Improved page hero, content/sidebar layout, cards, table of contents, review notes, quick answers, callouts, and responsive content.
- Improved single-post header, trust modules, article typography, responsive media, tables, FAQ, related content, social sharing, author area, post navigation, comments, and sticky sidebar behaviour.
- Added reusable AI prompts for blog and service featured images. Only `[TITLE]` needs to be replaced.

---

## Package contents

The latest `fasdent-ui-v3` archive contains:

```text
fasdent-ui-v3/
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   ├── page.css
│   │   └── single-post.css
│   └── js/
│       └── fasdent-ui.js
├── inc/
│   ├── floating-chat.php
│   └── menu-icons.php
├── template-parts/
│   └── site-navigation.php
├── featured-image-prompts.txt
├── MAIN-CSS-CHANGELOG.txt
└── README.md
```

### Core files

| File | Purpose |
|---|---|
| `assets/css/main.css` | Authoritative global design system, header, desktop/mobile navigation, buttons, forms, cards, shared components, footer, chat widget, and responsive rules. |
| `assets/css/page.css` | Additional styles for rich page templates. Enqueue after `main.css` only where required. |
| `assets/css/single-post.css` | Additional article-specific styles for blog single posts. Enqueue after `main.css` on posts. |
| `assets/js/fasdent-ui.js` | Mobile navigation, submenu controls, focus trapping, copy-link feedback, and floating-chat interactions. No JavaScript framework is required. |
| `template-parts/site-navigation.php` | Header, branding, top bar, primary menu, hamburger, backdrop, telephone button, and booking button markup. |
| `inc/menu-icons.php` | Font Awesome menu-item icon injection, automatic icon mapping, admin field, sanitization, and storage. |
| `inc/floating-chat.php` | Customizer controls, contact-channel construction, rendering, and extension filter. |
| `featured-image-prompts.txt` | Reusable prompts for blog and service featured images. |

### Earlier supporting files

Earlier work also included `editor.css` and `print.css`. They are optional supporting files and are not included in the current v3 core archive. Keep using them only when they are present in your theme and have been tested with the v3 global stylesheet.

### Existing project template parts

The project also supplied these standalone PHP template parts:

```text
toc-sidebar.php
before-after.php
breadcrumb.php
card-category.php
card-service.php
cta-banner.php
faq-accordion.php
key-takeaways.php
poll.php
rating-display.php
social-share.php
testimonial-card.php
```

They are documented later in this README. They are not part of the current v3 archive, so retain them in the correct theme directory or include your updated copies separately.

---

## Requirements

- A WordPress theme with a registered `primary` navigation location.
- A custom logo configured through WordPress, or suitable site-title fallback text.
- `wp_head()`, `wp_body_open()`, and `wp_footer()` present in the theme.
- JavaScript enabled for the mobile drawer, mobile submenu buttons, copy-link status, and floating-chat panel.
- A licensed Font Awesome 7+ Pro Kit or self-hosted Pro installation when Pro icon classes are used.
- The theme’s text domain is expected to be `fasdent`.

Recommended:

- A current WordPress installation.
- PHP 7.4 or newer.
- HTTPS for the live website.
- Page caching/CDN cache cleared after replacing assets.

---

## Installation

### 1. Back up the theme

Before replacing files, create a backup of:

- `functions.php`
- `header.php`
- Existing CSS/JavaScript files
- Existing `template-parts` and `inc` directories
- Any child-theme overrides

A child theme is recommended for changes to a third-party parent theme.

### 2. Copy the package

Copy the package folders into the active theme while preserving their paths:

```text
assets/css/main.css
assets/css/page.css
assets/css/single-post.css
assets/js/fasdent-ui.js
inc/menu-icons.php
inc/floating-chat.php
template-parts/site-navigation.php
```

### 3. Remove the previous global stylesheet

Replace the previous `main.css`; do not merge the files by concatenating them.

Check `functions.php` and remove any old handle that loads another global Fasdent stylesheet after the new one.

### 4. Load the PHP modules

Add the following to `functions.php`:

```php
require_once get_template_directory() . '/inc/menu-icons.php';
require_once get_template_directory() . '/inc/floating-chat.php';
```

For a child theme, use `get_stylesheet_directory()` instead:

```php
require_once get_stylesheet_directory() . '/inc/menu-icons.php';
require_once get_stylesheet_directory() . '/inc/floating-chat.php';
```

### 5. Enqueue the assets

Use one asset-loading function. This example uses file modification times to invalidate browser caches after updates:

```php
function fasdent_enqueue_ui_assets() {
    $directory = get_template_directory();
    $uri       = get_template_directory_uri();

    $main_css = '/assets/css/main.css';
    $ui_js    = '/assets/js/fasdent-ui.js';

    wp_enqueue_style(
        'fasdent-main',
        $uri . $main_css,
        array(),
        file_exists( $directory . $main_css ) ? filemtime( $directory . $main_css ) : null
    );

    if ( is_page() ) {
        $page_css = '/assets/css/page.css';
        wp_enqueue_style(
            'fasdent-page',
            $uri . $page_css,
            array( 'fasdent-main' ),
            file_exists( $directory . $page_css ) ? filemtime( $directory . $page_css ) : null
        );
    }

    if ( is_singular( 'post' ) ) {
        $single_css = '/assets/css/single-post.css';
        wp_enqueue_style(
            'fasdent-single-post',
            $uri . $single_css,
            array( 'fasdent-main' ),
            file_exists( $directory . $single_css ) ? filemtime( $directory . $single_css ) : null
        );
    }

    wp_enqueue_script(
        'fasdent-ui',
        $uri . $ui_js,
        array(),
        file_exists( $directory . $ui_js ) ? filemtime( $directory . $ui_js ) : null,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'fasdent_enqueue_ui_assets' );
```

For a child theme, replace the directory and URI functions with:

```php
$directory = get_stylesheet_directory();
$uri       = get_stylesheet_directory_uri();
```

### 6. Add the navigation template

Replace the existing header navigation block with:

```php
<?php get_template_part( 'template-parts/site-navigation' ); ?>
```

Do not insert it outside the normal page structure. It should be rendered once near the beginning of the page, before the primary content.

### 7. Assign the primary menu

In WordPress:

1. Open **Appearance → Menus**.
2. Create or select the main menu.
3. Assign it to the **Primary** location.
4. Arrange child menu items underneath their parents.
5. Save the menu.

### 8. Configure theme values

The navigation template reads these theme modifications:

| Setting | Purpose | Fallback |
|---|---|---|
| `fasdent_phone` | Top-bar and header telephone number | Empty |
| `fasdent_booking_url` | Booking button destination | `/booking/` |

Set them through your existing Customizer controls, theme options, or code.

---

## WordPress integration

### Register the menu location

Ensure the theme registers the `primary` location:

```php
function fasdent_register_menus() {
    register_nav_menus(
        array(
            'primary' => __( 'فهرست اصلی', 'fasdent' ),
        )
    );
}
add_action( 'after_setup_theme', 'fasdent_register_menus' );
```

### Enable the custom logo

```php
function fasdent_theme_supports() {
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 120,
            'width'       => 320,
            'flex-height' => true,
            'flex-width'  => true,
        )
    );
}
add_action( 'after_setup_theme', 'fasdent_theme_supports' );
```

### Required theme hooks

Confirm these calls exist:

```php
<?php wp_head(); ?>
```

inside `<head>`;

```php
<?php wp_body_open(); ?>
```

immediately after `<body>`; and:

```php
<?php wp_footer(); ?>
```

before `</body>`.

The floating contact widget is rendered through `wp_footer` with priority `30`.

### Optional print stylesheet

When using the earlier `print.css` file:

```php
wp_enqueue_style(
    'fasdent-print',
    get_template_directory_uri() . '/assets/css/print.css',
    array( 'fasdent-main' ),
    null,
    'print'
);
```

### Optional editor stylesheet

When using the earlier `editor.css` file:

```php
function fasdent_editor_styles() {
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'fasdent_editor_styles' );
```

---

## Header and navigation

### Desktop behaviour

At desktop widths:

- Branding remains on the right in RTL layout.
- The primary menu is placed immediately beside the logo; header actions use the remaining header space at the far inline end.
- Menu text is aligned to the start/right for Persian readability.
- Child menus are displayed as floating dropdown panels.
- Nested menus do not inherit the top-level horizontal flex layout.
- Dropdowns open through hover and keyboard focus.
- Header telephone and booking actions remain available when configured.

### Mobile behaviour

Below `961px`:

- The hamburger button becomes visible.
- The primary menu becomes an off-canvas drawer.
- A backdrop covers the remaining page.
- The document body is locked while the menu is open.
- Parent items receive separate submenu buttons.
- Submenus use `aria-expanded` and visible nesting.
- Pressing Escape closes the menu.
- Keyboard focus is contained within the open drawer.
- Focus returns to the hamburger after closing.
- Moving back to desktop width resets mobile-only state.

### Required IDs and classes

The JavaScript expects the navigation template to retain these contracts:

```text
#primary-menu-toggle
#primary-navigation
.site-header
.site-nav
.nav-backdrop
.menu-item-has-children or .page_item_has_children
.sub-menu or nested ul
```

Do not rename these selectors unless the JavaScript and CSS are updated at the same time.

### JavaScript-created submenu controls

`fasdent-ui.js` adds a `.submenu-toggle` button to parent menu items when one is not already present. Therefore, custom walkers do not need to output these buttons unless you deliberately replace the built-in behaviour.

---

## Font Awesome menu icons

### Load Font Awesome Pro separately

The package references classes such as:

```text
fa-duotone fa-solid fa-house
fa-duotone fa-solid fa-tooth
fa-duotone fa-solid fa-calendar-check
```

A valid Font Awesome 7+ Pro Kit or licensed self-hosted Pro installation must be loaded by the theme or site. The package does not contain or redistribute Font Awesome files.

When using a Kit, add your own licensed Kit through the method provided by Font Awesome. Do not commit a private Kit token to a public repository.

### Automatic icons

When no custom icon is assigned, `inc/menu-icons.php` chooses an icon from the menu title or URL. Built-in matches include:

| Menu concept | Default icon |
|---|---|
| Home / خانه | House |
| About / درباره | Information |
| Services / خدمات / درمان | Tooth |
| Doctor / پزشک / دکتر | User doctor |
| Blog / وبلاگ / مقاله | Newspaper |
| Gallery / نمونه / گالری | Images |
| Contact / تماس | Phone |
| Appointment / نوبت / رزرو | Calendar check |
| FAQ / سوال / پرسش | Circle question |
| Prices / قیمت / هزینه | Tags |
| Location / موقعیت / آدرس | Location dot |

Unmatched items use a circle-dot fallback.

### Assign a custom icon

1. Open **Appearance → Menus**.
2. Expand a menu item.
3. Find **کلاس آیکن Font Awesome**.
4. Enter the complete classes, for example:

```text
fa-duotone fa-solid fa-implant
```

5. Save the menu.

Leave the field empty to restore automatic icon selection.

### Sanitization

Icon values are split into class tokens and sanitized with WordPress class sanitization before storage and output. Only class names should be entered—not HTML markup.

---

## Floating contact widget

### Enable and configure

Open:

**Appearance → Customize → دکمه تماس شناور**

Available controls:

| Control | Theme modification |
|---|---|
| Enable/disable widget | `fasdent_chat_enabled` |
| Position | `fasdent_chat_position` |
| Launcher label | `fasdent_chat_label` |
| Panel title | `fasdent_chat_title` |
| Introductory text | `fasdent_chat_intro` |
| WhatsApp number | `fasdent_chat_whatsapp` |
| WhatsApp default message | `fasdent_chat_whatsapp_message` |
| Telephone number | `fasdent_chat_phone` |
| Telegram username | `fasdent_chat_telegram` |
| Email address | `fasdent_chat_email` |

The widget is not rendered when it is disabled or when every contact channel is empty.

### Number formats

- WhatsApp: enter the number with country code. Non-numeric characters are removed when building the WhatsApp URL.
- Telephone: `+` and digits are supported.
- Telegram: enter the username with or without `@`.
- Email: enter a valid email address.

### Add another channel

Use the `fasdent_floating_chat_channels` filter:

```php
function fasdent_add_instagram_chat_channel( $channels ) {
    $channels['instagram'] = array(
        'label' => __( 'اینستاگرام', 'fasdent' ),
        'note'  => '@fasdent',
        'url'   => 'https://www.instagram.com/fasdent/',
        'icon'  => 'fa-brands fa-instagram',
    );

    return $channels;
}
add_filter( 'fasdent_floating_chat_channels', 'fasdent_add_instagram_chat_channel' );
```

Use a real account URL and escape or sanitize any dynamic values before adding them.

### Required widget selectors

The JavaScript and CSS use:

```text
[data-fasdent-chat]
[data-chat-toggle]
[data-chat-close]
.fasdent-chat__panel
```

The panel opens through `.is-open` state and the native `hidden` attribute.

---


---

## RTL header and Chaty fixes

This section records the header, mobile-navigation, RTL, and Chaty compatibility work completed after the v3 UI layer. These fixes are intended for the current repository rather than a separate plugin.

### Problem summary

The original header used `margin-inline-start: auto` on `.site-nav`. In an RTL document, inline start is the **right** side. That automatic margin pushed the navigation away from the logo, leaving menu items visually disconnected from the branding.

The project already contained the correct hamburger markup in `template-parts/site-navigation.php` and the corresponding interaction logic in `assets/js/fasdent-ui.js`, but a later stylesheet or cascade conflict could keep `.menu-toggle` / `.mobile-toggle` hidden on mobile widths. The repository also includes a theme-native contact widget; this is separate from the third-party **Chaty** WordPress plugin.

### Changes applied

| Area | Change | Result |
|---|---|---|
| RTL document | Reinforced `direction: rtl` and RTL text alignment | Persian layout is explicit and reliable across theme components |
| Desktop header | Removed the automatic inline-start margin from `.site-nav` | Menu items remain directly beside the logo |
| Flex ordering | Assigned branding, navigation, and header actions predictable visual order | Logo → menu → optional phone/booking actions follows the intended RTL header layout |
| Header actions | Moved the automatic inline margin to `.header-actions` | Actions occupy the far available side without separating logo and navigation |
| Mobile toggle | Explicitly displays `.menu-toggle` / `.mobile-toggle` below `960px` | Hamburger control remains visible despite lower-priority conflicting rules |
| Mobile drawer | Raised navigation and backdrop stacking levels | Drawer and overlay appear above page content and fixed UI |
| Mixed direction text | Isolated telephone and deliberate LTR content | Phone numbers, URLs, and technical strings render predictably in Persian pages |
| Chaty | Added narrowly scoped high z-index compatibility selectors | A loaded Chaty widget cannot sit behind the sticky header or navigation overlay |

### Canonical CSS patch

The changes may live directly at the end of `assets/css/main.css`, or in a small override sheet loaded **after** `main.css`. Do not load both a merged file and the patch, because the duplicated rules are unnecessary.

```css
/* RTL header: keep the primary menu directly beside the logo. */
html { direction: rtl; }
body { direction: rtl; text-align: right; }

.header-main { display: flex; align-items: center; }
.site-branding,
.header-brand { flex: 0 0 auto; order: 1; }

.site-nav {
  order: 2;
  flex: 1 1 auto;
  min-inline-size: 0;
  margin-inline-start: 0;
  margin-inline-end: 0;
}

.site-nav > ul,
.site-nav > .menu,
.site-nav .menu-primary-container > ul {
  justify-content: flex-start;
}

.header-actions {
  order: 3;
  margin-inline-start: auto;
}

@media (max-width: 960px) {
  .header-main { justify-content: space-between; }
  .site-branding,
  .header-brand { order: 1; flex: 0 1 auto; }
  .header-actions { order: 2; margin-inline-start: auto; }
  .mobile-toggle,
  .menu-toggle {
    display: grid !important;
    order: 3;
    inline-size: 2.85rem;
    block-size: 2.85rem;
    flex: none;
    place-items: center;
    cursor: pointer;
  }
  .site-nav { order: 4; }
}

.nav-backdrop { z-index: 9970; }
.site-nav { z-index: 9975; }

a[href^="tel:"],
.ltr-content,
[dir="ltr"] {
  direction: ltr;
  unicode-bidi: isolate;
}

#chaty-widget,
.chaty-channels-list,
.chaty-float-button {
  z-index: 99999 !important;
}

@media (max-width: 640px) {
  #chaty-widget { inset-block-end: 5.5rem !important; }
}
```

### Chaty and native chat

The theme-native floating widget is implemented by `inc/floating-chat.php`, styled by `assets/css/main.css`, and controlled through **Appearance → Customize → دکمه تماس شناور**. It can offer WhatsApp, telephone, Telegram, and email without an external plugin.

[Chaty](https://wordpress.org/plugins/chaty/) is a separate third-party WordPress plugin. CSS cannot add Chaty by itself. To use it, install and activate Chaty, enable at least one channel, and confirm that its device visibility settings permit both desktop and mobile where required.

Use **one floating widget as the primary contact experience**. If both Chaty and the native Fasdent widget are intentionally enabled, test their positions on small screens. The mobile offset in the patch prevents the two controls from occupying the same bottom corner.

### Required implementation checks

- The header must include `get_template_part( 'template-parts/site-navigation' );` exactly once.
- Keep `#primary-menu-toggle`, `#primary-navigation`, `.site-nav`, and `.nav-backdrop`; the JavaScript relies on these selectors.
- Enqueue `assets/js/fasdent-ui.js` in the footer or with `defer`; it handles open/close state, focus restoration, Escape, backdrop clicks, and mobile submenu controls.
- Keep the CSS mobile breakpoint at `max-width: 960px` synchronized with the JavaScript desktop query `min-width: 961px`.
- Ensure `wp_footer()` appears before `</body>`; the native widget renders from that hook.
- After deployment, purge WordPress, cache-plugin, CDN, minification, and browser caches.

### Recommended commit

```text
fix(ui): align RTL navigation with logo and harden mobile menu/chat layering
```

Suggested commit body:

```text
- Remove RTL inline-start auto margin that separated navigation from branding
- Force the existing mobile hamburger control to display below 961px
- Raise drawer/backdrop stacking order for fixed-widget compatibility
- Isolate LTR phone and technical content in Persian layouts
- Add Chaty z-index compatibility rules without replacing native chat
- Document setup, troubleshooting, and mobile test requirements
```

## Stylesheet architecture

### `main.css`

`main.css` is the global and authoritative layer. Its major sections are:

1. Design tokens
2. Reset and document defaults
3. Accessibility helpers
4. Titles and section headings
5. Buttons
6. Forms
7. Header and top bar
8. Desktop navigation and submenus
9. Hamburger and mobile navigation
10. Hero and shared layout blocks
11. Cards
12. CTA banner
13. Breadcrumbs
14. FAQ, TOC, takeaways, ratings, and sharing
15. Shared article and WordPress content
16. Author, doctor, steps, gallery, and comments
17. Pagination, search, and utilities
18. Footer
19. Floating contact widget
20. Responsive system
21. Motion and contrast preferences

### Main design tokens

Override tokens in a child-theme stylesheet loaded after `main.css` instead of editing hundreds of selectors:

```css
:root {
  --brand-cyan: #09d4d6;
  --brand-blue: #0e55b1;
  --brand-navy: #071f3f;

  --color-primary: var(--brand-blue);
  --color-secondary: var(--brand-cyan);
  --color-dark: #102a46;
  --color-text: #29435f;
  --color-muted: #657a91;

  --container: 75rem;
  --content-width: 47rem;
  --header-height: 5rem;

  --radius: 1rem;
  --radius-lg: 1.5rem;
  --shadow: 0 16px 40px rgba(7, 31, 63, 0.09);
}
```

### `page.css`

Load `page.css` after `main.css` on templates that use `.fasdent-page` and its page-specific components.

It contains page hero, page layout, buttons, review notes, quick answer, TOC, rich `.prose`, disclaimers, sharing, sidebar, back-to-top, reveal effects, focus states, reduced-motion, and print-related page rules.

> [!NOTE]
> `page.css` originated in the earlier UI layer and includes local fallback tokens. The values inherited from `main.css` take priority where matching CSS custom properties are available. Review any remaining page-specific accent values when doing a final live-site visual pass.

### `single-post.css`

Load `single-post.css` after `main.css` only on single blog posts.

It contains article trust and compliance modules, reading progress, main/sidebar layout, post header elements, jump links, prose, review box, FAQ, related resources, soft CTA, source lists, disclosures, author/medical metadata, navigation, sharing, and responsive refinements.

> [!NOTE]
> `single-post.css` also contains local fallback values for compatibility. The global `main.css` token values are used when available.

### Cascade order

Use this order:

```text
1. Licensed fonts and Font Awesome
2. main.css
3. page.css OR single-post.css where needed
4. Small child-theme/site-specific overrides
5. print.css with media="print" when used
```

Avoid loading `page.css` and `single-post.css globally unless the site genuinely shares those classes across all templates.

---

## Responsive behaviour

### Breakpoints

The main responsive thresholds include:

| Width | Behaviour |
|---|---|
| Above `1100px` | Full desktop layout and generous spacing. |
| `961px–1100px` | Compact desktop/tablet adjustments. |
| Below `961px` | Hamburger and off-canvas navigation. |
| Below `768px` | Mobile grids, more compact sections, stacked content, safer controls. |
| Below `640px` | Single-column cards, full-width actions, reduced padding, mobile footer alignment. |
| Below `380px` | Extra-small phone safeguards. |

### Mobile standards applied

- Touch-friendly controls.
- Safe horizontal page padding.
- No fixed content widths wider than the viewport.
- Cards stack into one column where required.
- CTA actions can expand to full width.
- Tables remain horizontally scrollable.
- Images remain responsive.
- Footer content becomes readable and centered where appropriate.
- Visual boxes are centered while long text remains start-aligned.
- The floating contact widget avoids common mobile-edge collisions.
- Motion is disabled or reduced for users who request reduced motion.

### RTL/LTR support

The global stylesheet uses `direction: rtl`, `text-align: start`, and logical properties such as `margin-inline`, `padding-inline`, and `inset-inline` where practical. This allows many components to adapt when a local `dir="ltr"` context is used.

---

## Blog single posts

### Recommended markup contract

Use a body or article wrapper with `.single-post`, and retain these common structural classes when used by the template:

```text
.single-post
.single-post__layout
.single-post__main
.single-post__sidebar
.post-header
.post-content or .prose
.post-featured-image
.post-meta-bar
.social-share
.post-navigation
.author-bio
```

### Desktop improvements

- Readable content measure.
- Main content/sidebar grid.
- Sticky sidebar below the sticky header.
- Strong title and metadata hierarchy.
- Large responsive featured image.
- Improved table, figure, quotation, link, list, and heading styles.
- More visible trust, review, takeaway, source, and disclosure modules.
- Improved related resources and post navigation.

### Mobile improvements

- Single-column article layout.
- Static sidebar to avoid viewport trapping.
- Reduced article/card padding.
- Safe heading wrapping.
- Responsive media and tables.
- Stacked author and navigation components.
- Centered visual metadata and social sharing where appropriate.
- Touch-friendly FAQ and TOC controls.

### Content-alignment rule

Do not center full article paragraphs. Center the visual header, badges, icon boxes, actions, ratings, and compact metadata; keep paragraphs, lists, quotations, and clinical explanations start-aligned.

---

## Existing template parts

These components were supplied as part of the theme project. Their class names are supported by the shared CSS. Place them under the theme’s `template-parts` directory according to the paths used by your templates.

### `toc-sidebar.php`

Purpose: render the generated table of contents in a sidebar.

Dependencies:

```text
fasdent_toc_extract()
fasdent_toc_render()
fasdent_toc_inject_inline filter
```

Behaviour:

- Defines `FASDENT_TOC_SIDEBAR`.
- Removes the inline TOC content filter.
- Reads the post content.
- Renders only when at least three heading items exist.

Example:

```php
<?php get_template_part( 'template-parts/toc-sidebar' ); ?>
```

### `before-after.php`

Purpose: render one before/after gallery card with the post thumbnail and title.

Expected image size:

```text
fasdent-gallery
```

Place it inside the gallery loop.

### `breadcrumb.php`

Purpose: render a basic Home → current-page breadcrumb.

The current standalone file is intentionally simple. For richer hierarchy, extend it to include parent pages, post type archives, and categories while retaining semantic `<nav>` and `<ol>` markup.

### `card-category.php`

Purpose: render a taxonomy category card.

Dependencies:

```text
$term
fasdent_category_icon( $term )
```

The CSS supports:

```text
.category-card
.category-card__icon
.category-card__body
.category-count
```

### `card-service.php`

Purpose: render a service card with image, icon, excerpt, price, duration, and link.

Dependencies:

```text
fasdent_field( 'service_icon' )
fasdent_field( 'service_price' )
fasdent_field( 'service_duration' )
fasdent-card image size
```

Avoid inline styling in future edits; use the shared `.service-card__button` or button classes.

### `cta-banner.php`

Purpose: display appointment and telephone actions.

Dependencies:

```text
fasdent_booking_button()
fasdent_call_button()
```

Use semantic CTA classes instead of inline margins when revising the template.

### `faq-accordion.php`

Purpose: render one FAQ item.

For best native accessibility, prefer `<details>` and `<summary>` markup. When retaining the button version, the script must update `aria-expanded`, associate the button with the answer, and support keyboard interaction.

### `key-takeaways.php`

Purpose: render an optional list of key takeaways.

Dependency:

```text
fasdent_field( 'key_takeaways' )
```

Expected item keys:

```text
icon
text
```

### `poll.php`

Purpose: render a post poll and determine whether the visitor has already voted.

Dependencies:

```text
fasdent_get_poll()
fasdent_render_poll()
{$wpdb->prefix}fasdent_poll_votes table
```

The current supplied file identifies votes using a hash derived from the remote address. Review the privacy and retention implications before production use; a cookie, authenticated-user ID, rate limiting, or a privacy-preserving salted identifier may be more appropriate for the site’s legal requirements.

### `rating-display.php`

Purpose: render a 0–5 Font Awesome star rating with an optional count.

Arguments:

```php
$args['rating']; // float
$args['count'];  // optional integer
```

Example:

```php
<?php
get_template_part(
    'template-parts/rating-display',
    null,
    array(
        'rating' => 4.7,
        'count'  => 128,
    )
);
?>
```

### `social-share.php`

Purpose: render Telegram, WhatsApp, X/Twitter, LinkedIn, and copy-link controls.

The copy button requires `fasdent-ui.js` and the `data-copy-url` attribute. The script can update a nearby `.social-share__status` region when present.

### `testimonial-card.php`

Purpose: render a testimonial, star rating, author, and optional related service.

Dependencies:

```text
fasdent_field( 'rating' )
fasdent_field( 'related_service' )
```

The card content should remain concise. Very long testimonial text should be trimmed before output or visually clamped through a deliberate component variation.

---

## Featured-image prompts

The file `featured-image-prompts.txt` includes two production prompts:

1. Blog featured image
2. Dental service featured image

### Usage

1. Attach the Fasdent logo to the image-generation tool.
2. Copy the relevant prompt exactly.
3. Replace only `[TITLE]` with the blog or service title.
4. Do not change the composition, colour, safety, branding, or logo instructions unless a specific campaign requires it.

Example replacement:

```text
[TITLE] → ایمپلنت دندان چقدر طول می‌کشد؟
```

### Image specification

- Landscape `16:9`
- Target size `1600 × 900`
- Safe for cropping to `1200 × 675`
- Important content inside the central 80% safe area
- RTL-friendly centre or centre-left subject placement
- No generated title or other text
- Fasdent logo unchanged in the bottom-right
- Minimum 48px spacing from the right and bottom edges
- No additional watermark, signature, frame, or third-party logo

### Visual standards

- Calm, premium healthcare visual language
- Clinically credible dental anatomy and equipment
- No blood, graphic treatment, pain, fear, or distress
- No malformed teeth, instruments, fingers, or facial features
- No generic beauty-salon or science-fiction appearance
- Natural use of cyan, blue, navy, white, and pale blue-cyan surfaces

---

## Accessibility

The UI includes or supports:

- Skip-link styling.
- Visible `:focus-visible` states.
- Keyboard navigation for desktop menus.
- Mobile focus trapping.
- Escape-to-close behaviour.
- `aria-expanded`, `aria-controls`, and `aria-hidden` state updates.
- Hidden backdrop until required.
- Native button elements for interactive controls.
- Reduced-motion handling.
- Increased-contrast adjustments.
- Logical properties and start alignment for RTL content.
- Responsive text sizing and balanced headings.
- Touch-friendly buttons and controls.
- Decorative icons marked `aria-hidden="true"`.
- Accessible labels on hamburger, backdrop, chat, close, share, and contact controls.

### Content-author responsibilities

CSS cannot guarantee accessible content. Editors should also:

- Use one meaningful `h1` per page.
- Keep heading levels in sequence.
- Add useful image alternative text.
- Avoid writing essential information only inside images.
- Use descriptive link text.
- Add captions and context to clinical images.
- Ensure embedded videos include captions or transcripts.
- Avoid excessively long menu labels.
- Test Persian and English content with keyboard and screen readers.

---

## Customization

### Change the brand palette

Create a small override stylesheet loaded after `main.css`:

```css
:root {
  --brand-cyan: #09d4d6;
  --brand-blue: #0e55b1;
  --brand-navy: #071f3f;
}
```

When changing primary colours, retest text contrast, buttons, focus rings, menu hover states, and the floating widget.

### Change global spacing

```css
:root {
  --space-4: 1rem;
  --space-6: 1.5rem;
  --space-8: 2rem;
  --space-12: 3rem;
}
```

Prefer changing tokens rather than applying unrelated margins to individual templates.

### Change content width

```css
:root {
  --container: 75rem;
  --content-width: 47rem;
}
```

### Change corner radius

```css
:root {
  --radius-sm: 0.75rem;
  --radius: 1rem;
  --radius-lg: 1.5rem;
}
```

### Change mobile breakpoint

The navigation JavaScript uses:

```js
window.matchMedia('(min-width: 961px)')
```

The CSS uses the complementary `max-width: 960px` rules. Update both together if the navigation breakpoint changes.

### Site-specific overrides

Place small overrides in a separate file, for example:

```text
assets/css/site-overrides.css
```

Enqueue it after all Fasdent styles. Do not add a new large override layer to the end of `main.css`; that recreates the conflict that v3 was designed to remove.

---

## Testing and deployment

### Before deployment

- [ ] Back up the active theme.
- [ ] Replace, do not append, `main.css`.
- [ ] Confirm only one `fasdent-main` stylesheet is loaded.
- [ ] Confirm Font Awesome Pro loads successfully.
- [ ] Assign a menu to the `primary` location.
- [ ] Verify the custom logo.
- [ ] Verify telephone and booking URLs.
- [ ] Configure floating contact channels.
- [ ] Clear WordPress, optimization-plugin, browser, and CDN caches.
- [ ] Regenerate minified assets when a performance plugin creates them.

### Desktop tests

- [ ] Header remains stable while scrolling.
- [ ] Menu is positioned correctly and text is RTL-aligned.
- [ ] Every parent menu opens its submenu on hover and keyboard focus.
- [ ] Nested dropdowns remain inside the viewport.
- [ ] Booking and telephone buttons work.
- [ ] Cards have equal visual spacing.
- [ ] Footer columns align consistently.
- [ ] Blog sidebar does not overlap the header.

### Mobile tests

Test at approximately `360px`, `390px`, `430px`, `768px`, and `960px`:

- [ ] Hamburger opens and closes the drawer.
- [ ] Backdrop closes the drawer.
- [ ] Escape closes the drawer.
- [ ] Focus stays inside the open drawer.
- [ ] Submenus expand independently.
- [ ] Long menu labels wrap cleanly.
- [ ] No horizontal page overflow appears.
- [ ] Buttons remain easy to tap.
- [ ] Cards stack correctly.
- [ ] Tables scroll horizontally.
- [ ] Footer is readable and correctly aligned.
- [ ] Floating chat does not cover important controls.
- [ ] Blog featured image and title fit without clipping.

### Accessibility tests

- [ ] Navigate the entire page using only Tab, Shift+Tab, Enter, Space, and Escape.
- [ ] Confirm every visible focus state is easy to see.
- [ ] Test with reduced-motion enabled.
- [ ] Test 200% browser zoom.
- [ ] Check common text/background colour combinations.
- [ ] Verify icons are not the only source of meaning.

### Browser tests

Test the current versions used by the site audience, including:

- Chrome/Chromium
- Firefox
- Safari
- Mobile Safari
- Chrome on Android

---

## Troubleshooting

### The design looks inconsistent after installing v3

Likely cause: an older global stylesheet is still loaded.

Check the browser’s Network and Elements panels for duplicate `main.css` files or a large legacy override file loaded later.

Resolution:

1. Remove the old handle from `functions.php`.
2. Remove concatenated/minified cached copies.
3. Clear every cache layer.
4. Reload with browser cache disabled.

### Submenus display on the same level as parents

Likely causes:

- The old navigation CSS is still active.
- A plugin or theme rule applies `display:flex` to every nested menu `ul`.
- Menu item hierarchy was not saved in WordPress.

Confirm that only the first-level menu is horizontal and that children are nested in WordPress’s menu editor.

### Hamburger is visible but does nothing

Confirm:

- `fasdent-ui.js` loads without a 404 error.
- The page contains `#primary-menu-toggle` and `#primary-navigation`.
- No JavaScript error earlier on the page stops execution.
- The script is printed before `</body>` through `wp_footer()`.

### Mobile menu opens behind the page

Check for a theme/plugin stacking context created by `transform`, `filter`, or an unusually high `z-index` on another fixed element. Remove the conflicting stacking context or create a narrowly scoped override.

### Menu icons are empty squares

Font Awesome Pro is not loaded, the icon family/style is unavailable in the installed version, or the custom class is invalid.

Test with a known icon class from the licensed Font Awesome installation and confirm its CSS/webfont requests succeed.

### Menu items show duplicated icons

A different plugin or walker is also injecting icons. Disable one icon system or exclude the primary menu from the other plugin.

### Floating chat is missing

Confirm:

- The widget is enabled in the Customizer.
- At least one contact channel has a value.
- `inc/floating-chat.php` is required.
- `wp_footer()` exists.
- `main.css` is loaded.

### Floating chat opens but cannot be closed

Confirm `fasdent-ui.js` is loaded and no JavaScript error occurs before its initialization.

### Page or post colours do not fully match the v3 palette

`page.css` and `single-post.css` contain compatibility fallbacks from earlier versions. Global token values should override most of them, but a few template-specific accent rules may need a small site override after visual testing.

### A card is right-aligned when it should be centered

Check whether the markup uses one of the expected component classes. The v3 system centers compact visual content but deliberately keeps long descriptions start-aligned. Add a component modifier rather than globally applying `text-align:center` to article content.

### Horizontal scrolling appears on mobile

Inspect for:

- Fixed-width embedded content
- Inline styles
- Third-party iframes
- Very long unbroken URLs
- Wide tables missing a scroll wrapper
- Images with hardcoded width attributes overridden by plugin CSS

Do not hide overflow globally until the offending element is identified.

---

## Security and privacy notes

- All dynamic URLs, classes, and labels should continue to use the appropriate WordPress escaping functions.
- Do not place secret Font Awesome Kit credentials in a public repository.
- Validate any custom floating-chat channels added through filters.
- Opening external channels in a new tab should use `rel="noopener noreferrer"`.
- Telephone, email, Telegram, and WhatsApp details are public once rendered on the page.
- The supplied poll component uses visitor-network information to detect repeat voting. Review consent, retention, anonymization, and local privacy requirements before using it in production.
- Avoid exposing patient information in testimonials, images, metadata, filenames, or AI prompts.
- Obtain appropriate consent before publishing identifiable patient before/after images.

---

## Version history


### v3.1 — RTL header and Chaty compatibility

- Corrected the RTL flex-margin issue that separated the desktop menu from the logo.
- Standardized logo, navigation, and header-action ordering.
- Hardened hamburger visibility below the `961px` desktop threshold.
- Raised mobile drawer and backdrop stacking order.
- Added LTR isolation for telephone and technical content in Persian pages.
- Added documented Chaty compatibility styles and clarified the distinction between Chaty and the native Fasdent widget.
- Added deployment, cache, selector-contract, and mobile QA guidance for these changes.

### v3

- Replaced the previous multi-layer `main.css` with one clean cascade.
- Removed duplicated and conflicting header, card, footer, and responsive rules.
- Rebuilt desktop dropdowns and the mobile navigation drawer.
- Improved spacing, typography, title hierarchy, forms, buttons, and cards.
- Standardized centered visual components while preserving readable article alignment.
- Refined the `#09D4D6` / `#0E55B1` palette for professional healthcare use.
- Added consistent tablet, mobile, reduced-motion, and high-contrast behaviour.
- Preserved selectors used by existing PHP templates and JavaScript.
- Added reusable featured-image prompts for blog posts and services.

### v2

- Introduced the responsive header/navigation template.
- Added hamburger drawer JavaScript and accessible submenu controls.
- Added editable and automatic Font Awesome menu icons.
- Added the Customizer-driven floating contact widget.
- Added page and single-post responsive refinement layers.

### Initial CSS improvement

- Corrected the original submenu inheritance issue.
- Added foundational header/footer, card, form, print, editor, page, and single-post improvements.
- Established RTL-aware logical properties and shared design tokens.

---

## Maintenance principles

1. Keep `main.css` as the single global source of truth.
2. Use CSS custom properties for site-wide visual changes.
3. Use component modifiers for local variations.
4. Avoid inline styles in PHP templates.
5. Keep JavaScript selectors synchronized with markup and CSS.
6. Load Font Awesome through a valid licensed installation.
7. Keep mobile navigation accessible when changing the header.
8. Test both RTL and any LTR content sections.
9. Clear all generated/minified caches after asset changes.
10. Maintain a changelog for future modifications.

---

## License

This README and the custom Fasdent theme code may be used according to the licensing terms of the website/theme project in which they are installed.

Font Awesome Pro is a separate commercial dependency and is not included. Its use must comply with the Font Awesome license associated with the website’s Kit or self-hosted assets.