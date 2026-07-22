<?php
/**
 * Admin Bookings Management — Fasdent
 * List, filter, status-update for wp_fasdent_bookings table.
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Register admin menu page. */
function fasdent_admin_bookings_menu(): void {
	add_menu_page(
		__( 'نوبت‌ها', 'fasdent' ),
		__( 'نوبت‌ها', 'fasdent' ),
		'manage_options',
		'fasdent-bookings',
		'fasdent_admin_bookings_page',
		'dashicons-calendar-alt',
		26
	);
}
add_action( 'admin_menu', 'fasdent_admin_bookings_menu' );

/** AJAX: update booking status. */
function fasdent_ajax_update_booking_status(): void {
	check_ajax_referer( 'fasdent_admin_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) { wp_send_json_error( [ 'message' => 'دسترسی ندارید.' ] ); }
	global $wpdb;
	$id     = (int) sanitize_text_field( wp_unslash( $_POST['booking_id'] ?? '0' ) );
	$status = sanitize_key( wp_unslash( $_POST['status'] ?? '' ) );
	$allowed = [ 'pending', 'confirmed', 'completed', 'cancelled' ];
	if ( ! $id || ! in_array( $status, $allowed, true ) ) { wp_send_json_error(); }
	$wpdb->update( $wpdb->prefix . 'fasdent_bookings', [ 'status' => $status ], [ 'id' => $id ], [ '%s' ], [ '%d' ] );
	wp_send_json_success( [ 'message' => 'وضعیت بروزرسانی شد.' ] );
}
add_action( 'wp_ajax_fasdent_update_booking_status', 'fasdent_ajax_update_booking_status' );

/** Render admin page. */
function fasdent_admin_bookings_page(): void {
	global $wpdb;
	if ( ! current_user_can( 'manage_options' ) ) { return; }

	$table    = $wpdb->prefix . 'fasdent_bookings';
	$statuses = [ 'pending' => 'در انتظار', 'confirmed' => 'تأیید شده', 'completed' => 'انجام شده', 'cancelled' => 'لغو شده' ];
	$filter   = sanitize_key( wp_unslash( $_GET['status'] ?? '' ) );
	$where    = $filter && isset( $statuses[ $filter ] ) ? $wpdb->prepare( 'WHERE status = %s', $filter ) : '';
	$bookings = $wpdb->get_results( "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT 100" );

	$nonce    = wp_create_nonce( 'fasdent_admin_nonce' );
	echo '<div class="wrap" dir="rtl">';
	echo '<h1 class="wp-heading-inline">' . esc_html__( 'مدیریت نوبت‌ها', 'fasdent' ) . '</h1>';

	// Filter tabs.
	echo '<div style="margin:1rem 0;display:flex;gap:.5rem;flex-wrap:wrap;">';
	$all_url = admin_url( 'admin.php?page=fasdent-bookings' );
	echo '<a href="' . esc_url( $all_url ) . '" class="button' . ( ! $filter ? ' button-primary' : '' ) . '">همه</a>';
	foreach ( $statuses as $k => $label ) {
		$url = add_query_arg( 'status', $k, $all_url );
		echo '<a href="' . esc_url( $url ) . '" class="button' . ( $filter === $k ? ' button-primary' : '' ) . '">' . esc_html( $label ) . '</a>';
	}
	echo '</div>';

	if ( ! $bookings ) {
		echo '<p>' . esc_html__( 'نوبتی یافت نشد.', 'fasdent' ) . '</p></div>';
		return;
	}

	echo '<table class="wp-list-table widefat fixed striped" style="direction:rtl;">';
	echo '<thead><tr><th>#</th><th>نام</th><th>تلفن</th><th>خدمت</th><th>تاریخ</th><th>اورژانسی</th><th>ثبت شده</th><th>وضعیت</th><th>عملیات</th></tr></thead><tbody>';
	foreach ( $bookings as $b ) {
		$svc_name = $b->service_id ? get_the_title( (int) $b->service_id ) : '—';
		$status_label = $statuses[ $b->status ] ?? $b->status;
		$badge_color  = [ 'pending' => '#f59e0b', 'confirmed' => '#3b82f6', 'completed' => '#10b981', 'cancelled' => '#ef4444' ][ $b->status ] ?? '#888';
		echo '<tr>';
		echo '<td>' . esc_html( $b->id ) . '</td>';
		echo '<td><strong>' . esc_html( $b->name ) . '</strong>';
		if ( $b->age ) echo ' <small>(' . esc_html( $b->age ) . ' سال)</small>';
		echo '</td>';
		echo '<td><a href="tel:' . esc_attr( $b->phone ) . '">' . esc_html( $b->phone ) . '</a></td>';
		echo '<td>' . esc_html( $svc_name ) . '</td>';
		echo '<td>' . esc_html( $b->preferred_date ?: '—' ) . ( $b->time_range ? ' <small>(' . esc_html( $b->time_range ) . ')</small>' : '' ) . '</td>';
		echo '<td>' . ( $b->is_emergency ? '<span style="color:#dc2626;font-weight:700;">⚠️ بله</span>' : '—' ) . '</td>';
		echo '<td>' . esc_html( $b->created_at ) . '</td>';
		echo '<td><span style="background:' . esc_attr( $badge_color ) . ';color:#fff;padding:.2rem .6rem;border-radius:999px;font-size:.75rem;">' . esc_html( $status_label ) . '</span></td>';
		echo '<td>';
		foreach ( $statuses as $k => $lbl ) {
			if ( $k === $b->status ) continue;
			echo '<button type="button" class="button button-small fasdent-status-btn" style="margin:.1rem;" '
				. 'data-id="' . esc_attr( $b->id ) . '" '
				. 'data-status="' . esc_attr( $k ) . '" '
				. 'data-nonce="' . esc_attr( $nonce ) . '">' . esc_html( $lbl ) . '</button>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';

	// Inline JS for status update buttons.
	?>
<script>
document.querySelectorAll('.fasdent-status-btn').forEach(function(btn){
  btn.addEventListener('click', function(){
    if(!confirm('وضعیت به "' + btn.textContent.trim() + '" تغییر یابد?')) return;
    btn.disabled = true;
    var fd = new FormData();
    fd.append('action','fasdent_update_booking_status');
    fd.append('booking_id', btn.dataset.id);
    fd.append('status',     btn.dataset.status);
    fd.append('nonce',      btn.dataset.nonce);
    fetch(ajaxurl,{method:'POST',body:fd,credentials:'same-origin'})
      .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); else { alert('خطا'); btn.disabled=false; } });
  });
});
</script>
	<?php
	echo '</div>';
}
