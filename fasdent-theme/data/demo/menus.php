<?php
/**
 * Fasdent Demo — Navigation Menus
 */
if ( ! defined( 'FASDENT_DEMO_IMPORT' ) ) {
	exit;
}

/**
 * Helper: get page ID by path.
 */
function fasdent_demo_get_page_id( $path ) {
	$page = get_page_by_path( $path );
	return $page ? (int) $page->ID : 0;
}

/**
 * Helper: get term link by slug.
 */
function fasdent_demo_get_term_link( $slug ) {
	$term = get_term_by( 'slug', $slug, 'service_category' );
	if ( $term && ! is_wp_error( $term ) ) {
		$link = get_term_link( $term );
		return is_wp_error( $link ) ? '' : $link;
	}
	return '';
}

// ========== Primary Menu ==========
$primary_name = 'منوی اصلی';
$primary_menu = wp_get_nav_menu_object( $primary_name );

if ( ! $primary_menu ) {
	$primary_id = wp_create_nav_menu( $primary_name );
} else {
	$primary_id = (int) $primary_menu->term_id;
}

if ( $primary_id && ! is_wp_error( $primary_id ) ) {
	// Clear existing items to avoid duplicates on re-import.
	$existing_items = wp_get_nav_menu_items( $primary_id );
	if ( $existing_items ) {
		foreach ( $existing_items as $item ) {
			wp_delete_post( $item->ID, true );
		}
	}

	// a. Home
	wp_update_nav_menu_item( $primary_id, 0, array(
		'menu-item-title'  => 'خانه',
		'menu-item-url'    => home_url( '/' ),
		'menu-item-status' => 'publish',
		'menu-item-type'   => 'custom',
	) );

	// b. About
	$about_id = fasdent_demo_get_page_id( 'about' );
	if ( $about_id ) {
		wp_update_nav_menu_item( $primary_id, 0, array(
			'menu-item-title'     => 'درباره ما',
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $about_id,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		) );
	}

	// c. Services parent (custom link)
	$services_parent = wp_update_nav_menu_item( $primary_id, 0, array(
		'menu-item-title'  => 'خدمات',
		'menu-item-url'    => home_url( '/services/' ),
		'menu-item-status' => 'publish',
		'menu-item-type'   => 'custom',
	) );

	// Sub-items under Services
	$service_cats = array(
		'general-dentistry'  => 'دندانپزشکی عمومی',
		'cosmetic-dentistry' => 'دندانپزشکی زیبایی',
		'dental-implant'     => 'ایمپلنت دندانی',
		'orthodontics'       => 'ارتودنسی',
		'dental-emergency'   => 'اورژانس دندانپزشکی',
	);

	foreach ( $service_cats as $slug => $title ) {
		$term_link = fasdent_demo_get_term_link( $slug );
		if ( $term_link ) {
			wp_update_nav_menu_item( $primary_id, 0, array(
				'menu-item-title'     => $title,
				'menu-item-url'       => $term_link,
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'custom',
				'menu-item-parent-id' => $services_parent,
			) );
		}
	}

	// d. Blog
	$blog_id = fasdent_demo_get_page_id( 'blog' );
	if ( $blog_id ) {
		wp_update_nav_menu_item( $primary_id, 0, array(
			'menu-item-title'     => 'مقالات دندانپزشکی',
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $blog_id,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		) );
	}

	// e. FAQ
	$faq_id = fasdent_demo_get_page_id( 'faq' );
	if ( $faq_id ) {
		wp_update_nav_menu_item( $primary_id, 0, array(
			'menu-item-title'     => 'سوالات متداول',
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $faq_id,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		) );
	}

	// f. Contact
	$contact_id = fasdent_demo_get_page_id( 'contact' );
	if ( $contact_id ) {
		wp_update_nav_menu_item( $primary_id, 0, array(
			'menu-item-title'     => 'تماس با ما',
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $contact_id,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		) );
	}

	// Assign to theme location.
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $primary_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

// ========== Footer Menu ==========
$footer_name = 'منوی پاورقی';
$footer_menu = wp_get_nav_menu_object( $footer_name );

if ( ! $footer_menu ) {
	$footer_id = wp_create_nav_menu( $footer_name );
} else {
	$footer_id = (int) $footer_menu->term_id;
}

if ( $footer_id && ! is_wp_error( $footer_id ) ) {
	// Clear existing.
	$existing_items = wp_get_nav_menu_items( $footer_id );
	if ( $existing_items ) {
		foreach ( $existing_items as $item ) {
			wp_delete_post( $item->ID, true );
		}
	}

	$footer_pages = array(
		'about'                => 'درباره کلینیک فس‌دنت',
		'appointment'          => 'رزرو نوبت آنلاین',
		'faq'                  => 'سوالات متداول',
		'privacy-policy'       => 'سیاست حریم خصوصی',
		'medical-disclaimer'   => 'سلب مسئولیت پزشکی',
		'cancellation-policy'  => 'سیاست لغو نوبت',
		'patient-rights'       => 'حقوق بیمار',
		'sitemap'              => 'نقشه سایت',
	);

	foreach ( $footer_pages as $path => $title ) {
		$page_id = fasdent_demo_get_page_id( $path );
		if ( $page_id ) {
			wp_update_nav_menu_item( $footer_id, 0, array(
				'menu-item-title'     => $title,
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page_id,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			) );
		}
	}

	// Assign to theme location.
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$locations['footer'] = $footer_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}
