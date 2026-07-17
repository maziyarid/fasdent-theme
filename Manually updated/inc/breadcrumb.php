<?php
/**
 * Breadcrumb + BreadcrumbList Schema — Fasdent
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ساخت آیتم‌های Breadcrumb صفحه فعلی.
 *
 * @return array{name:string,url:string}[]
 */
function fasdent_breadcrumb_items(): array {
	$items = array(
		array( 'name' => 'خانه', 'url' => home_url( '/' ) ),
	);

	if ( is_singular( 'service' ) ) {
		$items[] = array( 'name' => 'خدمات', 'url' => home_url( '/services/' ) );
		$terms   = get_the_terms( get_the_ID(), 'service_category' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$term = $terms[0];
			while ( $term->parent ) {
				$parent = get_term( $term->parent, 'service_category' );
				if ( is_wp_error( $parent ) ) {
					break;
				}
				$term = $parent;
			}
			$items[] = array( 'name' => $term->name, 'url' => get_term_link( $term ) );
		}
		$items[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
	} elseif ( is_tax( 'service_category' ) ) {
		$items[] = array( 'name' => 'خدمات', 'url' => home_url( '/services/' ) );
		$term    = get_queried_object();
		if ( $term->parent ) {
			$parent = get_term( $term->parent, 'service_category' );
			if ( ! is_wp_error( $parent ) ) {
				$items[] = array( 'name' => $parent->name, 'url' => get_term_link( $parent ) );
			}
		}
		$items[] = array( 'name' => $term->name, 'url' => get_term_link( $term ) );
	} elseif ( is_post_type_archive( 'service' ) ) {
		$items[] = array( 'name' => 'خدمات', 'url' => home_url( '/services/' ) );
	} elseif ( is_singular( 'doctor' ) ) {
		$items[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
	} elseif ( is_singular( 'post' ) ) {
		$items[] = array( 'name' => 'بلاگ', 'url' => home_url( '/blog/' ) );
		$cats    = get_the_category();
		if ( $cats ) {
			$items[] = array( 'name' => $cats[0]->name, 'url' => get_category_link( $cats[0] ) );
		}
		$items[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
	} elseif ( is_page() ) {
		$items[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
	} elseif ( is_home() ) {
		$items[] = array( 'name' => 'بلاگ', 'url' => home_url( '/blog/' ) );
	} elseif ( is_category() ) {
		$items[] = array( 'name' => 'بلاگ', 'url' => home_url( '/blog/' ) );
		$items[] = array( 'name' => single_cat_title( '', false ), 'url' => get_category_link( get_queried_object() ) );
	} elseif ( is_search() ) {
		$items[] = array( 'name' => 'نتایج جستجو', 'url' => home_url( '/' ) );
	}

	return $items;
}

/**
 * نمایش HTML بردکرامب.
 */
function fasdent_breadcrumb(): void {
	if ( is_front_page() ) {
		return;
	}
	$items = fasdent_breadcrumb_items();
	$last  = count( $items ) - 1;

	echo '<nav class="breadcrumb" aria-label="مسیر صفحه"><div class="container"><ol>';
	foreach ( $items as $i => $item ) {
		if ( $i === $last ) {
			printf( '<li aria-current="page">%s</li>', esc_html( $item['name'] ) );
		} else {
			printf(
				'<li><a href="%s">%s</a><i class="fa-solid fa-angle-left" aria-hidden="true"></i></li>',
				esc_url( $item['url'] ),
				esc_html( $item['name'] )
			);
		}
	}
	echo '</ol></div></nav>';
}

/**
 * اسکیمای BreadcrumbList در head همه صفحات.
 */
function fasdent_schema_breadcrumb(): void {
	if ( is_front_page() || is_404() ) {
		return;
	}
	$items    = fasdent_breadcrumb_items();
	$elements = array();
	foreach ( $items as $i => $item ) {
		$elements[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item['name'],
			'item'     => is_string( $item['url'] ) ? $item['url'] : '',
		);
	}
	fasdent_print_schema( array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $elements,
	) );
}
add_action( 'wp_head', 'fasdent_schema_breadcrumb', 6 );
