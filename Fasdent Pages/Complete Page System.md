
📁 Package contents

#	File	Purpose	Size
1	
fasdent/page.php	WordPress page template — hero, sticky TOC, disclaimer stack, sidebar, JSON-LD schema	23.0
KB
2	
fasdent/assets/css/page.css	Dedicated stylesheet — RTL logical properties, animated hero, elegant cards, reveal-on-scroll	25.7
KB
3	
fasdent/assets/js/page.js	Dedicated JavaScript — TOC generator, scroll-spy, reveal-on-scroll, cookie banner, honeypot	12.9
KB
4	fasdent/inc/prompts/page-generator.md	Master AI prompt that generates any Fasdent page compliantly	6.0
KB
5	fasdent/sample-pages/about-the-clinic.md	Sample page 1 — About the Clinic (paste-ready META + CONTENT)	9.7
KB
6	fasdent/sample-pages/implants-service.md	Sample page 2 — Dental Implants (paste-ready META + CONTENT)	12.8
KB
7	
fasdent/README.md	Install guide, enqueue snippet, Custom Fields reference	3.8
KB
✨ Design highlights
 	Hero with animated gradient blobs — teal → amber, soft grid overlay, reveal-on-scroll on the hero media.
 	Sticky TOC with scroll-spy — auto-generated from H2 / H3 in the page content, active section highlighted as you scroll, collapsible via aria-expanded.
 	Reveal-on-scroll animations — declarative data-reveal="fade-up|fade-left|fade-right" attribute; honors prefers-reduced-motion.
 	Elegant cards — soft shadows, hover lift, gradient dividers under H2s, polished callouts (info / warn / danger / default).
 	Reading progress bar at the top of the viewport.
 	Back-to-top button with smooth scroll.
 	Copy-link social share — Telegram · WhatsApp · X · LinkedIn · copy (no Facebook by default for FA audience).
 	HIPAA-aware newsletter — honeypot field, PHI-shape guard, HTTPS-only submit.
 	WCAG 2.1 AA — skip link, focus-visible outlines, semantic headings, RTL logical properties, reduced-motion respected.
 
 	FTC-safe copy — no guarantees; «ﺑﺎﺷﺪ ﻣﺘﻔﺎوت اﺳﺖ ﻣﻤﮑﻦ ﻧﺘﺎﯾﺞ» disclaimer baked in.
 	JSON-LD schema — WebPage + BreadcrumbList emitted inline.
 	Cookie consent — opt-in banner; analytics gated behind a fasdent:consent event.
 	Print stylesheet — sidebar / TOC / CTA hidden, hero background flattened for clean printing.

🚀 Installation quick-start
1.	Unzip the download and copy the fasdent/ contents into your active theme, preserving the folder structure (page.php at theme root, assets/…, inc/…).
2.	Enqueue the assets in functions.php — only on pages using the Fasdent Sample Page template, and only after your existing main.css / main.js:
add_action('wp_enqueue_scripts', function () { if (!is_page_template('page.php')) return;
$dir = get_stylesheet_directory();
$uri = get_stylesheet_directory_uri(); wp_enqueue_style(
'fasdent-page',
$uri . '/assets/css/page.css',
['fasdent-main'],	// depends on your main.css handle filemtime($dir . '/assets/css/page.css')
);
wp_enqueue_script( 'fasdent-page',
$uri . '/assets/js/page.js',
[],	// no jQuery needed
filemtime($dir . '/assets/js/page.js'), true
 

});
 
);


3.	Make sure Font Awesome 6 is loaded (the template uses fa-solid, fa-regular, and fa-brands).
4.	In WP Admin, create a page → Page Attributes → Template → Fasdent Sample Page.
5.	Fill Custom Fields (ACF-compatible) using the reference table below.
6.	Publish. Enjoy 🦷
 

 
🔧 Custom Fields reference
Per-page meta (get_post_meta)

Key	Type	Example
fasdent_kicker	text	ﺧﺪﻣﺎت درﻣﺎﻧﯽ
fasdent_subtitle	text	ﯾﮏ ﺟﻤﻠﻪ زﯾﺮﻋﻨﻮان ﮐﻮﺗﺎه
fasdent_quick_answer	textarea (40–70 words)	…ﭘﺎﺳﺦ ﺳﺮﯾﻊ ﺑﻪ ﺳﺆال اﺻﻠﯽ ﺻﻔﺤﻪ
fasdent_reviewer_name	text	دﮐﺘﺮ ﻣﺮﯾﻢ رﺿﺎﯾﯽ
fasdent_reviewer_credentials	text	دﻧﺪاﻧﭙﺰﺷﮏ ﻋﻤﻮﻣﯽ
fasdent_reviewer_license	text	ﻧﻈﺎم ﭘﺰﺷﮑﯽ: ١٢٣۴۵۶
fasdent_review_date	date YYYY-MM-DD	2026-06-20
fasdent_reading_time	number (minutes)	6

fasdent_hero_image	url (fallback if no featured image)	https://…

fasdent_hero_badges	textarea —icon|label per line	fa-solid fa-user-doctor|
ﺑﺎزﺑﯿﻨﯽ ﺷﺪه
fasdent_primary_cta_label	text	درﯾﺎﻓﺖ ﻧﻮﺑﺖ
fasdent_primary_cta_url	url	/reserve/
fasdent_show_toc	text ("1" or empty)	1
 
Global options (wp_options)

Key	Purpose	Default

fasdent_emergency_phone	Emergency number rendered in hero + sidebar + emergency disclaimer	٠٢١-XXXXXXXX
fasdent_booking_url	Global booking URL for the sidebar CTA	/reserve/
🎨 Design tokens (respected from your main. css)
The stylesheet reads through to your existing tokens and falls back to sensible defaults if a variable is missing:

Token	Fallback	Purpose
--color-primary	#0f766e	Teal — brand primary
--color-secondary	#f59e0b	Amber — accents / CTA
--color-dark	#0f172a	Body text / dark surfaces
--color-muted	#475569	Muted body text
--bg	#f8fafc	Page background
--surface	#ffffff	Card / surface
--border	#e2e8f0	Dividers
--radius	16px	Card radius
--shadow	0 18px 40px rgba(15,23,42,.08)	Elevated shadow

Additional tokens introduced by page.css: --fd-radius-lg (24px), --fd-gradient
(teal→amber), --fd-hero-gradient, --fd-danger, --fd-warn, --fd-info, --fd-success.

📄 File previews
Below is a compact overview of each file. The full verbatim source of every file is inside the ZIP and in the single-Markdown deliverable.

1	· fasdent/page. php — WordPress template
Renders in this order:
1.	Skip link + reading-progress bar
2.	Breadcrumb (with Schema.org BreadcrumbList)
3.	Hero — kicker · H1 · subtitle · trust badges · meta · CTA + emergency call · media
4.	Two-column body:
 	Main: clinical-review note · quick answer · sticky TOC · the_content() · disclaimer stack · social share
 	Sidebar: booking CTA · emergency card · reviewer card · newsletter (HIPAA-safe)
5.	Back-to-top button
6.	Inline JSON-LD (WebPage + BreadcrumbList)

Every string is escaped (esc_html, esc_url, esc_attr, wp_kses_post); every meta value is optional — sections silently hide when their meta is empty.

2	· fasdent/assets/css/page. css — Stylesheet
 	Scoped under .fasdent-page so it can't leak into other templates.
 	Uses CSS logical properties (margin-inline-start, border-inline-end, etc.) — works identically in RTL and LTR.
 	Animated hero blobs, gradient meshes, blurred glow on hero media.
 
 	.prose typography that styles the_content() output beautifully, including callouts,
.feature-grid, .steps timeline, <details class="faq-item"> FAQ, and tables.
 	Full prefers-reduced-motion and @media print blocks.

3	· fasdent/assets/js/page. js — Behaviors

 	Auto-builds the TOC from H2 / H3 inside .post-content, generating slug IDs (Persian-safe).
 	IntersectionObserver scroll-spy highlights the active TOC entry.
 	Reveal-on-scroll for [data-reveal] elements.
 	Reading-progress bar, back-to-top, copy-link button, smooth in-page anchor scrolling with sticky-header offset.
 	Newsletter submit: honeypot + PHI-shape guard (rejects obvious health keywords in the email field).
 	Opt-in cookie banner that emits a fasdent:consent event so you can gate GA/Matomo behind acceptance.

4	· fasdent/inc/prompts/page-generator. md — Master AI prompt

A drop-in prompt that produces the two outputs your operator needs:
=== META ===
<yaml block of fasdent_* meta values>

=== CONTENT ===
<raw HTML to paste into the WP editor>

Bakes in compliance rules (no PHI, no guarantees, no "best in Tehran", heading order, "results may vary" wherever outcomes are described) and validates a checklist before returning.

5	· fasdent/sample-pages/about-the-clinic. md — Sample page 1

A ready-to-paste About the Clinic page with mission, values (feature grid), specialties, a 5-step first-visit timeline, 4 FAQ items, and a "when to see a dentist" warning callout. Includes a full META block with reviewer name, credentials, license, and hero badges.

6	· fasdent/sample-pages/implants-service. md — Sample page 2

A ready-to-paste Dental Implants service page: what it is, who is a candidate (feature grid), 8-step treatment timeline, risks & complications, aftercare, cost & timeline, 5 FAQ items, and a post-op emergency callout. Reviewed by an oral surgeon meta profile.
7	· fasdent/README. md — Install guide

Compact install guide, functions.php enqueue snippet, and a Custom Fields reference table — mirrors what's on this page but lives inside the ZIP for offline reference.

✅ What to do next

  Unzip fasdent-page-system.zip and drop the folder into your theme.
  Enqueue page.css / page.js (snippet above) and confirm Font Awesome 6 is loaded.
  Create the two sample pages — paste the META + CONTENT from sample-pages/about-the-clinic.md and sample-pages/implants-service.md.
  Set global options — fasdent_emergency_phone and fasdent_booking_url.
  Generate more pages via the master AI prompt at inc/prompts/page-generator.md
— feed it a topic and it produces compliant META + CONTENT for a new page.

 
Compliance posture baked in: HIPAA-aware forms · WCAG 2.1 AA · FTC truth-in-advertising ·
educational / emergency / results / privacy disclaimer stack · license display · opt-in cookies.
