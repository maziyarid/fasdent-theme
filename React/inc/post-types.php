<?php
/**
 * Custom Post Types — Fasdent
 * service (خدمات) | doctor (پزشکان) | testimonial (نظرات بیماران) | faq (سوالات متداول)
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ثبت انواع پست سفارشی.
 */
function fasdent_register_post_types(): void {

	/* ── خدمات (service) ─────────────────────────── */
	register_post_type( 'service', array(
		'labels'        => array(
			'name'          => __( 'خدمات', 'fasdent' ),
			'singular_name' => __( 'خدمت', 'fasdent' ),
			'add_new'       => __( 'افزودن خدمت', 'fasdent' ),
			'add_new_item'  => __( 'افزودن خدمت جدید', 'fasdent' ),
			'edit_item'     => __( 'ویرایش خدمت', 'fasdent' ),
			'search_items'  => __( 'جستجوی خدمات', 'fasdent' ),
		),
		'public'        => true,
		'show_in_rest'  => true, // سازگاری المنتور و گوتنبرگ.
		'menu_icon'     => 'dashicons-plus-alt',
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes' ),
		'has_archive'   => 'services',
		'rewrite'       => array(
			'slug'       => 'services/%service_category%',
			'with_front' => false,
		),
		'taxonomies'    => array( 'service_category' ),
	) );

	/* ── پزشکان (doctor) ─────────────────────────── */
	register_post_type( 'doctor', array(
		'labels'        => array(
			'name'          => __( 'پزشکان', 'fasdent' ),
			'singular_name' => __( 'پزشک', 'fasdent' ),
			'add_new_item'  => __( 'افزودن پزشک جدید', 'fasdent' ),
		),
		'public'        => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-businessperson',
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'doctors', 'with_front' => false ),
	) );

	/* ── نظرات بیماران (testimonial) ─────────────── */
	register_post_type( 'testimonial', array(
		'labels'        => array(
			'name'          => __( 'نظرات بیماران', 'fasdent' ),
			'singular_name' => __( 'نظر بیمار', 'fasdent' ),
			'add_new_item'  => __( 'افزودن نظر جدید', 'fasdent' ),
		),
		'public'             => true,
		'publicly_queryable' => false,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-format-quote',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'has_archive'        => false,
		'rewrite'            => false,
	) );

	/* ── سوالات متداول (faq) ─────────────────────── */
	register_post_type( 'faq', array(
		'labels'        => array(
			'name'          => __( 'سوالات متداول', 'fasdent' ),
			'singular_name' => __( 'سوال متداول', 'fasdent' ),
			'add_new_item'  => __( 'افزودن سوال جدید', 'fasdent' ),
		),
		'public'             => true,
		'publicly_queryable' => false,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-editor-help',
		'supports'           => array( 'title', 'editor', 'custom-fields' ),
		'has_archive'        => false,
		'rewrite'            => false,
	) );
}
add_action( 'init', 'fasdent_register_post_types' );

/**
 * جایگزینی %service_category% در پرمالینک خدمات با اسلاگ دسته والد:
 * /services/{parent-category-slug}/{service-slug}/
 *
 * @param string  $permalink پیوند فعلی.
 * @param WP_Post $post      پست.
 * @return string
 */
function fasdent_service_permalink( string $permalink, WP_Post $post ): string {
	if ( 'service' !== $post->post_type || false === strpos( $permalink, '%service_category%' ) ) {
		return $permalink;
	}
	$slug  = 'general-dentistry'; // پیش‌فرض.
	$terms = get_the_terms( $post->ID, 'service_category' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		// اولویت با ترمی که والد دارد نیست — از عمیق‌ترین ترم، والد ریشه را می‌گیریم.
		$term = $terms[0];
		while ( $term->parent ) {
			$parent = get_term( $term->parent, 'service_category' );
			if ( is_wp_error( $parent ) ) {
				break;
			}
			$term = $parent;
		}
		$slug = $term->slug;
	}
	return str_replace( '%service_category%', $slug, $permalink );
}
add_filter( 'post_type_link', 'fasdent_service_permalink', 10, 2 );

/**
 * قوانین بازنویسی برای /services/{category}/{service}/.
 */
function fasdent_service_rewrite_rules(): void {
	// صفحه تک‌خدمت.
	add_rewrite_rule(
		'^services/([^/]+)/([^/]+)/?$',
		'index.php?service=$matches[2]',
		'top'
	);
	// صفحه‌بندی آرشیو دسته.
	add_rewrite_rule(
		'^services/([^/]+)/page/([0-9]+)/?$',
		'index.php?service_category=$matches[1]&paged=$matches[2]',
		'top'
	);
	// صفحه دسته (Pillar).
	add_rewrite_rule(
		'^services/([^/]+)/?$',
		'index.php?service_category=$matches[1]',
		'top'
	);
}
add_action( 'init', 'fasdent_service_rewrite_rules' );
