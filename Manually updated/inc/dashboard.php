<?php
/**
 * Admin Dashboard Widgets — Fasdent
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'wp_dashboard_setup', 'fasdent_register_dashboard_widgets' );

function fasdent_register_dashboard_widgets(): void {
	wp_add_dashboard_widget( 'fasdent_bookings',    __( '📅 آمار نوبت‌ها', 'fasdent' ),      'fasdent_widget_booking_stats' );
	wp_add_dashboard_widget( 'fasdent_submissions', __( '📩 فرم‌های دریافتی', 'fasdent' ),   'fasdent_widget_submissions' );
	wp_add_dashboard_widget( 'fasdent_popular',     __( '🔥 خدمات پرمخاطب', 'fasdent' ),    'fasdent_widget_popular_services' );
	wp_add_dashboard_widget( 'fasdent_feedback',    __( '💬 نظرات اخیر بیماران', 'fasdent' ),'fasdent_widget_recent_feedback' );
	wp_add_dashboard_widget( 'fasdent_quick',       __( '⚡ دسترسی سریع', 'fasdent' ),       'fasdent_widget_quick_actions' );
	wp_add_dashboard_widget( 'fasdent_seo',         __( '🔍 وضعیت سئو', 'fasdent' ),         'fasdent_widget_seo_health' );
	wp_add_dashboard_widget( 'fasdent_overview',    __( '📊 خلاصه سایت', 'fasdent' ),        'fasdent_widget_site_overview' );
}

function fasdent_widget_booking_stats(): void {
	global $wpdb;
	$table = $wpdb->prefix . 'fasdent_bookings';
	// Table may not exist yet before first activation.
	if ( ! $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) ) {
		echo '<p>' . esc_html__( 'جدول نوبت‌ها هنوز ایجاد نشده.', 'fasdent' ) . '</p>';
		return;
	}
	$today  = wp_date( 'Y-m-d' );
	$week   = wp_date( 'Y-m-d', strtotime( '-7 days' ) );
	$month  = wp_date( 'Y-m-d', strtotime( '-30 days' ) );
	$counts = [
		'امروز'    => $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE DATE(created_at)=%s", $today ) ),
		'۷ روز'   => $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE created_at>=%s", $week ) ),
		'۳۰ روز'  => $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE created_at>=%s", $month ) ),
	];
	$pending   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status='pending'" );
	$confirmed = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status='confirmed'" );
	echo '<table width="100%" style="border-collapse:collapse;margin-bottom:.75rem;">';
	foreach ( $counts as $lbl => $cnt ) {
		echo '<tr><td>' . esc_html( $lbl ) . '</td><td style="text-align:left;font-weight:700;font-size:1.1rem;">' . esc_html( (int) $cnt ) . '</td></tr>';
	}
	echo '</table>';
	echo '<p>⏳ در انتظار: <strong>' . esc_html( $pending ) . '</strong> &nbsp; ✅ تأیید: <strong>' . esc_html( $confirmed ) . '</strong></p>';
	echo '<a href="' . esc_url( admin_url( 'admin.php?page=fasdent-bookings' ) ) . '" class="button button-primary">' . esc_html__( 'مشاهده همه نوبت‌ها', 'fasdent' ) . '</a>';
}

function fasdent_widget_submissions(): void {
	$total   = wp_count_posts( 'fasdent_submission' )->publish ?? 0;
	$recent  = get_posts( array( 'post_type' => 'fasdent_submission', 'numberposts' => 5, 'post_status' => 'publish' ) );
	echo '<p><strong>' . esc_html( $total ) . '</strong> ' . esc_html__( 'فرم ثبت‌شده', 'fasdent' ) . '</p>';
	if ( $recent ) {
		echo '<ul>';
		foreach ( $recent as $sub ) {
			$phone = get_post_meta( $sub->ID, '_submission_phone', true );
			echo '<li><strong>' . esc_html( $sub->post_title ) . '</strong>';
			if ( $phone ) echo ' — ' . esc_html( $phone );
			echo '</li>';
		}
		echo '</ul>';
	}
	echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=fasdent_submission' ) ) . '">' . esc_html__( 'مشاهده همه فرم‌ها', 'fasdent' ) . '</a>';
}

function fasdent_widget_popular_services(): void {
	$services = get_posts( array( 'post_type' => 'service', 'numberposts' => 5, 'post_status' => 'publish', 'meta_key' => '_view_count', 'orderby' => 'meta_value_num', 'order' => 'DESC' ) );
	if ( ! $services ) {
		$services = get_posts( array( 'post_type' => 'service', 'numberposts' => 5 ) );
	}
	echo '<ol>';
	foreach ( $services as $s ) {
		$views = fasdent_get_view_count( $s->ID );
		echo '<li><a href="' . esc_url( get_permalink( $s ) ) . '">' . esc_html( $s->post_title ) . '</a>';
		if ( $views ) echo ' — <em>' . esc_html( number_format( $views ) ) . ' بازدید</em>';
		echo '</li>';
	}
	echo '</ol>';
	echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=service' ) ) . '">' . esc_html__( 'مدیریت خدمات', 'fasdent' ) . '</a>';
}

function fasdent_widget_recent_feedback(): void {
	$testimonials = get_posts( array( 'post_type' => 'testimonial', 'numberposts' => 5, 'post_status' => 'publish' ) );
	if ( ! $testimonials ) { echo '<p>' . esc_html__( 'نظری ثبت نشده.', 'fasdent' ) . '</p>'; return; }
	foreach ( $testimonials as $t ) {
		$rating = (int)( get_post_meta( $t->ID, 'rating', true ) ?: 5 );
		echo '<div style="border-bottom:1px solid #eee;padding:.4rem 0;">';
		echo '<strong>' . esc_html( $t->post_title ) . '</strong>';
		echo ' ' . str_repeat( '⭐', $rating );
		echo '<p style="margin:.25rem 0 0;font-size:.85rem;">' . esc_html( wp_trim_words( $t->post_content, 12 ) ) . '</p>';
		echo '</div>';
	}
	echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=testimonial' ) ) . '">' . esc_html__( 'مشاهده همه', 'fasdent' ) . '</a>';
}

function fasdent_widget_quick_actions(): void {
	$links = array(
		array( admin_url( 'post-new.php?post_type=service' ),     '➕ افزودن خدمت' ),
		array( admin_url( 'post-new.php' ),                        '📝 افزودن مقاله' ),
		array( admin_url( 'post-new.php?post_type=testimonial' ), '💬 افزودن نظر' ),
		array( admin_url( 'post-new.php?post_type=doctor' ),      '👤 افزودن پزشک' ),
		array( admin_url( 'customize.php' ),                       '🎨 تنظیمات' ),
		array( home_url( '/' ),                                    '🌐 مشاهده سایت' ),
	);
	echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;">';
	foreach ( $links as $link ) {
		echo '<a href="' . esc_url( $link[0] ) . '" class="button button-secondary" style="text-align:center;">' . esc_html( $link[1] ) . '</a>';
	}
	echo '</div>';
}

function fasdent_widget_seo_health(): void {
	$has_yoast    = defined( 'WPSEO_VERSION' );
	$has_rankmath = defined( 'RANK_MATH_VERSION' );
	echo '<ul>';
	echo '<li>' . ( $has_yoast || $has_rankmath ? '✅' : '⚠️' ) . ' ' . esc_html__( 'افزونه سئو:', 'fasdent' ) . ' ' . ( $has_yoast ? 'Yoast SEO' : ( $has_rankmath ? 'Rank Math' : 'نصب نشده' ) ) . '</li>';
	echo '<li>' . ( function_exists( 'acf_add_local_field_group' ) ? '✅' : '⚠️' ) . ' ACF</li>';
	echo '<li>' . ( is_ssl() ? '✅' : '⚠️' ) . ' ' . esc_html__( 'SSL', 'fasdent' ) . '</li>';
	echo '<li>✅ ' . esc_html__( 'Schema Markup فعال', 'fasdent' ) . '</li>';
	echo '</ul>';
	echo '<a href="' . esc_url( admin_url( 'options-reading.php' ) ) . '">' . esc_html__( 'تنظیمات خواندن', 'fasdent' ) . '</a>';
}

function fasdent_widget_site_overview(): void {
	$services      = wp_count_posts( 'service' )->publish ?? 0;
	$testimonials  = wp_count_posts( 'testimonial' )->publish ?? 0;
	$posts         = wp_count_posts( 'post' )->publish ?? 0;
	$submissions   = wp_count_posts( 'fasdent_submission' )->publish ?? 0;
	echo '<table width="100%" style="border-collapse:collapse;">';
	foreach ( array(
		array( 'خدمات منتشرشده', $services ),
		array( 'نظرات بیماران', $testimonials ),
		array( 'مقالات بلاگ', $posts ),
		array( 'فرم‌های دریافتی', $submissions ),
	) as $row ) {
		echo '<tr><td>' . esc_html( $row[0] ) . '</td><td style="text-align:left;font-weight:bold;">' . esc_html( number_format( $row[1] ) ) . '</td></tr>';
	}
	echo '</table>';
}
