<?php
/**
 * Admin Bookings — table, filters, status, CSV export, API key.
 * @package Fasdent
 * @version 2.6.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fasdent_admin_bookings_menu(): void {
	add_menu_page( __( 'نوبت‌ها', 'fasdent' ), __( 'نوبت‌ها', 'fasdent' ), 'manage_options', 'fasdent-bookings', 'fasdent_admin_bookings_page', 'dashicons-calendar-alt', 26 );
}
add_action( 'admin_menu', 'fasdent_admin_bookings_menu' );

function fasdent_ajax_update_booking_status(): void {
	check_ajax_referer( 'fasdent_admin_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) { wp_send_json_error( array( 'message' => 'دسترسی ندارید.' ) ); }
	global $wpdb;
	$id = (int) ( $_POST['booking_id'] ?? 0 );
	$status = sanitize_key( wp_unslash( $_POST['status'] ?? '' ) );
	$allowed = array( 'pending', 'confirmed', 'completed', 'cancelled' );
	if ( ! $id || ! in_array( $status, $allowed, true ) ) { wp_send_json_error(); }
	$wpdb->update( $wpdb->prefix . 'fasdent_bookings', array( 'status' => $status ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
	wp_send_json_success( array( 'message' => 'وضعیت بروزرسانی شد.' ) );
}
add_action( 'wp_ajax_fasdent_update_booking_status', 'fasdent_ajax_update_booking_status' );

function fasdent_bookings_export_csv(): void {
	if ( ! isset( $_GET['fasdent_export_bookings'] ) || ! current_user_can( 'manage_options' ) ) { return; }
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'fasdent_export_bookings' ) ) { wp_die( 'Invalid nonce' ); }
	global $wpdb;
	$rows = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'fasdent_bookings ORDER BY created_at DESC', ARRAY_A );
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=fasdent-bookings-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fprintf( $out, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
	if ( $rows ) { fputcsv( $out, array_keys( $rows[0] ) ); foreach ( $rows as $r ) { fputcsv( $out, $r ); } }
	fclose( $out );
	exit;
}
add_action( 'admin_init', 'fasdent_bookings_export_csv' );

function fasdent_admin_bookings_page(): void {
	global $wpdb;
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	if ( isset( $_POST['fasdent_booking_api_key_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['fasdent_booking_api_key_nonce'] ), 'fasdent_api_key' ) ) {
		update_option( 'fasdent_booking_api_key', sanitize_text_field( wp_unslash( $_POST['fasdent_booking_api_key'] ?? '' ) ), false );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'کلید API ذخیره شد.', 'fasdent' ) . '</p></div>';
	}
	$table = $wpdb->prefix . 'fasdent_bookings';
	$statuses = array( 'pending' => 'در انتظار', 'confirmed' => 'تأیید شده', 'completed' => 'انجام شده', 'cancelled' => 'لغو شده' );
	$filter = sanitize_key( wp_unslash( $_GET['status'] ?? '' ) );
	$search = sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) );
	$where = 'WHERE 1=1'; $params = array();
	if ( $filter && isset( $statuses[ $filter ] ) ) { $where .= ' AND status = %s'; $params[] = $filter; }
	if ( $search ) { $where .= ' AND (name LIKE %s OR phone LIKE %s OR email LIKE %s)'; $like = '%' . $wpdb->esc_like( $search ) . '%'; $params[] = $like; $params[] = $like; $params[] = $like; }
	$sql = "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT 200";
	$bookings = $params ? $wpdb->get_results( $wpdb->prepare( $sql, $params ) ) : $wpdb->get_results( $sql );
	$nonce = wp_create_nonce( 'fasdent_admin_nonce' );
	$api_key = (string) get_option( 'fasdent_booking_api_key', '' );
	$export = wp_nonce_url( admin_url( 'admin.php?page=fasdent-bookings&fasdent_export_bookings=1' ), 'fasdent_export_bookings' );
	echo '<div class="wrap" dir="rtl"><h1 class="wp-heading-inline">' . esc_html__( 'مدیریت نوبت‌ها', 'fasdent' ) . '</h1>';
	echo ' <a class="page-title-action" href="' . esc_url( $export ) . '">' . esc_html__( 'خروجی CSV', 'fasdent' ) . '</a><hr class="wp-header-end">';
	echo '<div class="card" style="max-width:720px;padding:1rem;margin:1rem 0"><h2 style="margin-top:0">REST API</h2>';
	echo '<p><code>GET ' . esc_html( rest_url( 'fasdent/v1/bookings' ) ) . '</code></p>';
	echo '<p>Header: X-Fasdent-Key</p><form method="post">';
	wp_nonce_field( 'fasdent_api_key', 'fasdent_booking_api_key_nonce' );
	echo '<label>API Key <input type="text" class="regular-text" dir="ltr" name="fasdent_booking_api_key" value="' . esc_attr( $api_key ) . '"></label> ';
	submit_button( __( 'ذخیره کلید', 'fasdent' ), 'secondary', 'submit', false );
	echo '</form></div>';
	echo '<form method="get" style="margin:1rem 0;display:flex;gap:.5rem;flex-wrap:wrap;align-items:center"><input type="hidden" name="page" value="fasdent-bookings">';
	$all_url = admin_url( 'admin.php?page=fasdent-bookings' );
	echo '<a href="' . esc_url( $all_url ) . '" class="button' . ( ! $filter ? ' button-primary' : '' ) . '">همه</a>';
	foreach ( $statuses as $k => $label ) {
		echo '<a href="' . esc_url( add_query_arg( 'status', $k, $all_url ) ) . '" class="button' . ( $filter === $k ? ' button-primary' : '' ) . '">' . esc_html( $label ) . '</a>';
	}
	echo '<input type="search" name="s" value="' . esc_attr( $search ) . '" placeholder="نام / تلفن">'; submit_button( 'جستجو', 'secondary', '', false ); echo '</form>';
	if ( ! $bookings ) { echo '<p>نوبتی یافت نشد.</p></div>'; return; }
	echo '<table class="wp-list-table widefat fixed striped" style="direction:rtl"><thead><tr><th>#</th><th>نام</th><th>تلفن</th><th>خدمت</th><th>تاریخ</th><th>اورژانسی</th><th>ثبت</th><th>وضعیت</th><th>عملیات</th></tr></thead><tbody>';
	foreach ( $bookings as $b ) {
		$svc = $b->service_id ? get_the_title( (int) $b->service_id ) : '—';
		$sl = $statuses[ $b->status ] ?? $b->status;
		$bc = array( 'pending' => '#f59e0b', 'confirmed' => '#3b82f6', 'completed' => '#10b981', 'cancelled' => '#ef4444' )[ $b->status ] ?? '#888';
		echo '<tr><td>' . esc_html( (string) $b->id ) . '</td><td><strong>' . esc_html( $b->name ) . '</strong></td>';
		echo '<td><a href="tel:' . esc_attr( $b->phone ) . '">' . esc_html( $b->phone ) . '</a></td><td>' . esc_html( $svc ) . '</td>';
		echo '<td>' . esc_html( $b->preferred_date ?: '—' ) . '</td><td>' . ( $b->is_emergency ? '<span style="color:#dc2626;font-weight:700">بله</span>' : '—' ) . '</td>';
		echo '<td>' . esc_html( $b->created_at ) . '</td><td><span style="background:' . esc_attr( $bc ) . ';color:#fff;padding:.2rem .6rem;border-radius:999px;font-size:.75rem">' . esc_html( $sl ) . '</span></td><td>';
		foreach ( $statuses as $k => $lbl ) {
			if ( $k === $b->status ) continue;
			echo '<button type="button" class="button button-small fasdent-status-btn" style="margin:.1rem" data-id="' . esc_attr( (string) $b->id ) . '" data-status="' . esc_attr( $k ) . '" data-nonce="' . esc_attr( $nonce ) . '">' . esc_html( $lbl ) . '</button>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
	?>
<script>
document.querySelectorAll('.fasdent-status-btn').forEach(function(btn){
  btn.addEventListener('click', function(){
    if(!confirm('تغییر وضعیت؟')) return;
    btn.disabled = true;
    var fd = new FormData();
    fd.append('action','fasdent_update_booking_status');
    fd.append('booking_id', btn.dataset.id);
    fd.append('status', btn.dataset.status);
    fd.append('nonce', btn.dataset.nonce);
    fetch(ajaxurl,{method:'POST',body:fd,credentials:'same-origin'}).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); else { alert('خطا'); btn.disabled=false; } });
  });
});
</script>
	<?php
	echo '</div>';
}
