<?php
/**
 * Fasdent Demo — blog posts loader (29 posts across batches)
 */
if ( ! defined( 'FASDENT_DEMO_IMPORT' ) ) {
	exit;
}

if ( ! isset( $GLOBALS['fasdent_demo_ids'] ) ) {
	$GLOBALS['fasdent_demo_ids'] = array();
}
$GLOBALS['fasdent_demo_ids']['posts'] = array();

$categories = array(
	'implant' => 'ایمپلنت',
	'insurance' => 'بیمه',
	'tooth-replacement' => 'کاشت دندان',
	'cosmetic' => 'زیبایی',
	'dentistry' => 'دندانپزشکی',
	'tehran' => 'تهران',
	'restoration' => 'ترمیم',
	'cost' => 'هزینه',
);
$cat_ids = array();
foreach ( $categories as $slug => $name ) {
	$term = term_exists( $slug, 'category' );
	if ( ! $term ) {
		$term = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
	}
	$cat_ids[ $slug ] = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
}

$cat_map = array(
	'ایمپلنت' => 'implant',
	'بیمه' => 'insurance',
	'کاشت دندان' => 'tooth-replacement',
	'زیبایی' => 'cosmetic',
	'دندانپزشکی' => 'dentistry',
	'تهران' => 'tehran',
	'ترمیم' => 'restoration',
	'هزینه' => 'cost',
);

$posts = array();
$batch_dir = __DIR__ . '/posts-data';
foreach ( glob( $batch_dir . '/batch-*.php' ) as $batch_file ) {
	$batch = require $batch_file;
	if ( is_array( $batch ) ) {
		$posts = array_merge( $posts, $batch );
	}
}

foreach ( $posts as $post ) {
	$existing = get_page_by_path( $post['slug'], OBJECT, 'post' );
	if ( $existing ) {
		$GLOBALS['fasdent_demo_ids']['posts'][] = $existing->ID;
		continue;
	}

	$post_date = gmdate( 'Y-m-d H:i:s', strtotime( '-' . (int) $post['days_ago'] . ' days' ) );
	$cat_key   = isset( $cat_map[ $post['category'] ] ) ? $cat_map[ $post['category'] ] : 'dentistry';
	$cat_id    = isset( $cat_ids[ $cat_key ] ) ? $cat_ids[ $cat_key ] : 0;

	$post_id = wp_insert_post(
		array(
			'post_title'    => $post['title'],
			'post_name'     => $post['slug'],
			'post_content'  => $post['content'],
			'post_excerpt'  => $post['excerpt'],
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_author'   => 1,
			'post_date'     => $post_date,
			'post_category' => $cat_id ? array( $cat_id ) : array(),
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		continue;
	}

	wp_update_post( array(
		'ID'            => $post_id,
		'post_date'     => $post_date,
		'post_date_gmt' => get_gmt_from_date( $post_date ),
	) );

	if ( ! empty( $post['tags'] ) ) {
		wp_set_post_tags( $post_id, $post['tags'], false );
	}

	update_post_meta( $post_id, 'quick_answer', $post['quick_answer'] );
	update_post_meta( $post_id, 'reviewer_name', 'دکتر کیوان علی‌پسندی' );
	update_post_meta( $post_id, 'reviewer_credentials', 'جراح و دندانپزشک' );
	update_post_meta( $post_id, 'reviewer_license_state', 'نظام پزشکی ایران' );
	update_post_meta( $post_id, 'reviewer_scope', 'دقت بالینی و شفافیت برای بیمار' );
	update_post_meta( $post_id, 'clinical_review_date', '1404/04/01' );
	update_post_meta( $post_id, 'review_status', 'reviewed' );
	update_post_meta( $post_id, 'emergency_disclaimer', '1' );
	update_post_meta( $post_id, 'results_may_vary', '1' );
	update_post_meta( $post_id, 'accessibility_checked', '1' );
	update_post_meta( $post_id, 'primary_cta_text', 'برای بررسی وضعیت خود نوبت مشاوره بگیرید' );
	update_post_meta( $post_id, 'primary_cta_url', home_url( '/appointment/' ) );
	update_post_meta( $post_id, 'meta_description', $post['excerpt'] );
	update_post_meta( $post_id, 'related_posts_slugs', $post['related_slugs'] );
	update_post_meta( $post_id, 'citations', array(
		array( 'label' => 'WHO Oral Health', 'url' => 'https://www.who.int/health-topics/oral-health' ),
		array( 'label' => 'وزارت بهداشت ایران', 'url' => 'https://behdasht.gov.ir/' ),
		array( 'label' => 'American Dental Association', 'url' => 'https://www.ada.org/' ),
	) );
	update_post_meta( $post_id, 'fasdent_faqs', array(
		array(
			'question' => 'آیا این مطلب جایگزین معاینه است؟',
			'answer'   => 'خیر. محتوای آموزشی است و تشخیص یا طرح درمان فقط پس از معاینه حضوری ممکن است.',
		),
		array(
			'question' => 'نتایج درمان برای همه یکسان است؟',
			'answer'   => 'خیر. نتایج بسته به شرایط هر بیمار متفاوت است و هیچ نتیجه مشخصی تضمین نمی‌شود.',
		),
	) );
	update_post_meta( $post_id, 'faq_schema_enabled', '0' );

	$GLOBALS['fasdent_demo_ids']['posts'][] = $post_id;
}
