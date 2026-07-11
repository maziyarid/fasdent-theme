<?php
/**
 * سئوی تکنیکال داخلی — Fasdent
 * Meta Description, Canonical, Open Graph, Twitter Card, robots.txt, hreflang-ready
 * (در صورت فعال بودن Yoast/RankMath این خروجی‌ها غیرفعال می‌شوند تا تداخل پیش نیاید.)
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * آیا افزونه سئو فعال است؟
 */
function fasdent_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' );
}

/**
 * تولید Meta Description بهینه (< ۱۵۵ کاراکتر، شامل کلیدواژه + شماره تماس).
 */
function fasdent_meta_description(): string {
	$phone = fasdent_phone();

	if ( is_front_page() ) {
		$desc = 'کلینیک دندانپزشکی فس‌دنت دکتر کیوان علی‌پسندی؛ ایمپلنت، لمینت، ارتودنسی و اورژانس ۲۴ ساعته. نوبت: ' . $phone;
	} elseif ( is_singular() ) {
		$excerpt = get_the_excerpt();
		$desc    = $excerpt ? wp_strip_all_tags( $excerpt ) : wp_strip_all_tags( get_the_title() ) . ' در کلینیک فس‌دنت.';
		$desc    = mb_substr( $desc, 0, 120 ) . ' نوبت: ' . $phone;
	} elseif ( is_tax( 'service_category' ) ) {
		$term = get_queried_object();
		$desc = $term->description ? wp_strip_all_tags( $term->description ) : $term->name . ' در کلینیک فس‌دنت.';
		$desc = mb_substr( $desc, 0, 120 ) . ' نوبت: ' . $phone;
	} elseif ( is_post_type_archive( 'service' ) ) {
		$desc = 'لیست کامل خدمات دندانپزشکی کلینیک فس‌دنت: ایمپلنت، زیبایی، ارتودنسی، جراحی و اورژانس. رزرو نوبت: ' . $phone;
	} elseif ( is_home() || is_archive() ) {
		$desc = 'مقالات آموزشی دندانپزشکی کلینیک فس‌دنت — دکتر کیوان علی‌پسندی. مشاوره و نوبت: ' . $phone;
	} else {
		$desc = 'کلینیک دندانپزشکی فس‌دنت — دکتر کیوان علی‌پسندی. تماس: ' . $phone;
	}
	return mb_substr( $desc, 0, 155 );
}

/**
 * خروجی متاتگ‌های سئو در head.
 */
function fasdent_seo_head(): void {
	if ( fasdent_seo_plugin_active() ) {
		return;
	}

	$desc = fasdent_meta_description();

	// Canonical.
	if ( is_singular() ) {
		$canonical = get_permalink();
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$canonical = get_term_link( get_queried_object() );
	} elseif ( is_post_type_archive() ) {
		$canonical = get_post_type_archive_link( get_query_var( 'post_type' ) );
	} else {
		$canonical = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
		$canonical = trailingslashit( $canonical );
	}
	if ( is_wp_error( $canonical ) ) {
		$canonical = home_url( '/' );
	}

	echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";

	// Open Graph.
	$title = wp_get_document_title();
	$image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( null, 'large' );
	}
	echo '<meta property="og:locale" content="fa_IR">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $canonical ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' ) ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}

	// Twitter Card.
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	if ( $image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}

	// hreflang — آماده چندزبانگی (فعلاً فقط fa-IR).
	$hreflangs = apply_filters( 'fasdent_hreflangs', array( 'fa-IR' => $canonical ) );
	if ( count( $hreflangs ) > 1 ) {
		foreach ( $hreflangs as $lang => $url ) {
			echo '<link rel="alternate" hreflang="' . esc_attr( $lang ) . '" href="' . esc_url( $url ) . '">' . "\n";
		}
	}
}
add_action( 'wp_head', 'fasdent_seo_head', 2 );

/**
 * جداکننده عنوان + عنوان کوتاه‌تر از ۶۰ کاراکتر.
 *
 * @param array $title اجزای عنوان.
 * @return array
 */
function fasdent_document_title_parts( array $title ): array {
	if ( is_front_page() ) {
		$title['title']   = 'کلینیک دندانپزشکی فس‌دنت';
		$title['tagline'] = 'دکتر کیوان علی‌پسندی';
	}
	// برند کوتاه برای جلوگیری از عبور از ۶۰ کاراکتر.
	if ( ! is_front_page() ) {
		$title['site'] = 'فس‌دنت';
	}
	return $title;
}
add_filter( 'document_title_parts', 'fasdent_document_title_parts' );

add_filter( 'document_title_separator', static fn() => '|' );

/**
 * robots.txt استاندارد.
 *
 * @param string $output خروجی فعلی.
 * @return string
 */
function fasdent_robots_txt( string $output ): string {
	$sitemap = home_url( '/wp-sitemap.xml' );
	if ( defined( 'WPSEO_VERSION' ) ) {
		$sitemap = home_url( '/sitemap_index.xml' );
	} elseif ( defined( 'RANK_MATH_VERSION' ) ) {
		$sitemap = home_url( '/sitemap_index.xml' );
	}
	$output  = "User-agent: *\n";
	$output .= "Disallow: /wp-admin/\n";
	$output .= "Allow: /wp-admin/admin-ajax.php\n";
	$output .= "Disallow: /?s=\n";
	$output .= "Disallow: /search/\n\n";
	$output .= 'Sitemap: ' . $sitemap . "\n";
	return $output;
}
add_filter( 'robots_txt', 'fasdent_robots_txt', 20 );

/**
 * افزودن CPT ها به Sitemap هسته وردپرس.
 *
 * @param array $post_types انواع پست.
 * @return array
 */
function fasdent_sitemap_post_types( array $post_types ): array {
	if ( post_type_exists( 'service' ) ) {
		$post_types['service'] = get_post_type_object( 'service' );
	}
	if ( post_type_exists( 'doctor' ) ) {
		$post_types['doctor'] = get_post_type_object( 'doctor' );
	}
	return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'fasdent_sitemap_post_types' );

/* ═══════════════════════════════════════════════════
 * بهینه‌سازی بودجه خزش (Crawl Budget Optimization)
 * ═══════════════════════════════════════════════════ */

/**
 * ریدایرکت آرشیو تاریخ → خانه.
 */
function fasdent_redirect_date_archives(): void {
	if ( is_date() ) {
		wp_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'fasdent_redirect_date_archives' );

/**
 * ریدایرکت صفحات پیوست → پست والد.
 */
function fasdent_redirect_attachment_pages(): void {
	if ( is_attachment() ) {
		$parent = get_post_parent();
		wp_redirect( $parent ? get_permalink( $parent ) : home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'fasdent_redirect_attachment_pages' );

/**
 * noindex برای جستجو، برچسب‌های کم‌محتوا، صفحات بعدی.
 */
function fasdent_crawl_budget_noindex(): void {
	if ( fasdent_seo_plugin_active() ) {
		return;
	}
	if ( is_search() ) {
		echo '<meta name="robots" content="noindex, nofollow">' . "\n";
	} elseif ( is_tag() ) {
		$tag = get_queried_object();
		if ( $tag && ( $tag->count ?? 0 ) < 5 ) {
			echo '<meta name="robots" content="noindex, follow">' . "\n";
		}
	} elseif ( is_paged() && get_query_var( 'paged' ) > 1 ) {
		echo '<link rel="prev" href="' . esc_url( get_previous_posts_page_link() ) . '">' . "\n";
		echo '<link rel="next" href="' . esc_url( get_next_posts_page_link() ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'fasdent_crawl_budget_noindex', 3 );

/* حذف لینک‌های غیرضروری از head برای کاهش crawler signals. */
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
