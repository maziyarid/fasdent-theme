<?php
/**
 * Fasdent Demo — service_category taxonomy terms
 *
 * Creates all 10 service_category taxonomy terms for کلینیک دندانپزشکی فس‌دنت.
 */
if ( ! defined( 'FASDENT_DEMO_IMPORT' ) ) {
	exit;
}

/**
 * Create service_category terms and return slug => term_id map.
 *
 * @return array
 */
function fasdent_demo_create_terms() {
	$terms = array(
		'general-dentistry'   => array(
			'name'        => 'دندانپزشکی عمومی',
			'description' => 'خدمات پایه و درمانی دندانپزشکی',
			'icon'        => 'fa-solid fa-tooth',
		),
		'cosmetic-dentistry'  => array(
			'name'        => 'دندانپزشکی زیبایی',
			'description' => 'بهبود ظاهر لبخند و دندان‌ها',
			'icon'        => 'fa-solid fa-face-smile',
		),
		'dental-implant'      => array(
			'name'        => 'ایمپلنت دندانی',
			'description' => 'جایگزینی دندان از دست رفته',
			'icon'        => 'fa-solid fa-screwdriver-wrench',
		),
		'orthodontics'        => array(
			'name'        => 'ارتودنسی',
			'description' => 'اصلاح جای‌گیری دندان‌ها',
			'icon'        => 'fa-solid fa-teeth',
		),
		'oral-surgery'        => array(
			'name'        => 'جراحی دهان و فک',
			'description' => 'اعمال جراحی دهان، فک و صورت',
			'icon'        => 'fa-solid fa-user-doctor',
		),
		'endodontics'         => array(
			'name'        => 'درمان ریشه (اندودنتیکس)',
			'description' => 'درمان عفونت و التهاب پالپ دندان',
			'icon'        => 'fa-solid fa-syringe',
		),
		'periodontics'        => array(
			'name'        => 'لثه‌درمانی (پریودنتیکس)',
			'description' => 'درمان بیماری‌های لثه',
			'icon'        => 'fa-solid fa-teeth-open',
		),
		'pediatric-dentistry' => array(
			'name'        => 'دندانپزشکی کودکان',
			'description' => 'مراقبت تخصصی از دندان‌های کودکان',
			'icon'        => 'fa-solid fa-child',
		),
		'prosthodontics'      => array(
			'name'        => 'پروتز دندانی',
			'description' => 'ساخت و نصب انواع پروتز',
			'icon'        => 'fa-solid fa-crown',
		),
		'dental-emergency'    => array(
			'name'        => 'اورژانس دندانپزشکی',
			'description' => 'درمان فوری دردهای دندانی',
			'icon'        => 'fa-solid fa-truck-medical',
		),
	);

	$term_ids = array();

	foreach ( $terms as $slug => $data ) {
		$existing = term_exists( $slug, 'service_category' );

		if ( $existing && ! is_wp_error( $existing ) ) {
			$term_id = is_array( $existing ) ? (int) $existing['term_id'] : (int) $existing;
		} else {
			$result = wp_insert_term(
				$data['name'],
				'service_category',
				array(
					'slug'        => $slug,
					'description' => $data['description'],
				)
			);

			if ( is_wp_error( $result ) ) {
				continue;
			}

			$term_id = (int) $result['term_id'];
		}

		if ( $term_id > 0 ) {
			update_term_meta( $term_id, 'fasdent_icon', $data['icon'] );
			$term_ids[ $slug ] = $term_id;
		}
	}

	return $term_ids;
}

// Execute and expose for other files.
global $fasdent_demo_term_ids;
$fasdent_demo_term_ids = fasdent_demo_create_terms();

if ( ! isset( $GLOBALS['fasdent_demo_ids'] ) ) {
	$GLOBALS['fasdent_demo_ids'] = array();
}
$GLOBALS['fasdent_demo_ids']['terms'] = $fasdent_demo_term_ids;
