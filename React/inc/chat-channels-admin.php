<?php
/**
 * Chaty-style channels admin — add/remove contacts + icon picker.
 * Option: fasdent_chat_channels
 * @package Fasdent
 * @version 2.6.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fasdent_default_chat_channels(): array {
	return array(
		array( 'id' => 'whatsapp', 'label' => 'واتس‌اپ', 'note' => 'پاسخ‌گویی سریع', 'type' => 'whatsapp', 'value' => '989201441469', 'icon' => 'fa-brands fa-whatsapp', 'enabled' => 1, 'order' => 10 ),
		array( 'id' => 'phone', 'label' => 'تماس تلفنی', 'note' => '+98 920 144 1469', 'type' => 'phone', 'value' => '+989201441469', 'icon' => 'fa-solid fa-phone-volume', 'enabled' => 1, 'order' => 20 ),
		array( 'id' => 'instagram', 'label' => 'اینستاگرام', 'note' => '@Dr.keyvan_alipasandi', 'type' => 'url', 'value' => 'https://instagram.com/Dr.keyvan_alipasandi', 'icon' => 'fa-brands fa-instagram', 'enabled' => 1, 'order' => 30 ),
		array( 'id' => 'email', 'label' => 'ایمیل', 'note' => 'Dr.keyvan.alipasandii@gmail.com', 'type' => 'email', 'value' => 'Dr.keyvan.alipasandii@gmail.com', 'icon' => 'fa-solid fa-envelope', 'enabled' => 1, 'order' => 40 ),
	);
}

function fasdent_get_stored_chat_channels(): array {
	$stored = get_option( 'fasdent_chat_channels', null );
	if ( ! is_array( $stored ) || empty( $stored ) ) { return fasdent_default_chat_channels(); }
	usort( $stored, static function ( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
	return $stored;
}

function fasdent_chat_channel_url( array $ch ): string {
	$type = $ch['type'] ?? 'url';
	$value = trim( (string) ( $ch['value'] ?? '' ) );
	if ( '' === $value ) { return '#'; }
	switch ( $type ) {
		case 'whatsapp':
			$num = preg_replace( '/\D+/', '', $value );
			$msg = (string) get_theme_mod( 'fasdent_chat_whatsapp_message', '' );
			return 'https://wa.me/' . $num . ( $msg ? '?text=' . rawurlencode( $msg ) : '' );
		case 'phone': return 'tel:' . preg_replace( '/[^0-9+]/', '', $value );
		case 'telegram': return 'https://t.me/' . rawurlencode( ltrim( $value, '@' ) );
		case 'email': return 'mailto:' . sanitize_email( $value );
		case 'sms': return 'sms:' . preg_replace( '/[^0-9+]/', '', $value );
		default: return esc_url_raw( $value );
	}
}

function fasdent_filter_chat_channels( array $legacy ): array {
	$channels = array();
	foreach ( fasdent_get_stored_chat_channels() as $ch ) {
		if ( empty( $ch['enabled'] ) ) { continue; }
		$id = sanitize_key( $ch['id'] ?? ( 'ch' . wp_rand( 100, 999 ) ) );
		$channels[ $id ] = array(
			'label' => (string) ( $ch['label'] ?? '' ),
			'note'  => (string) ( $ch['note'] ?? '' ),
			'url'   => fasdent_chat_channel_url( $ch ),
			'icon'  => (string) ( $ch['icon'] ?? 'fa-solid fa-comment' ),
		);
	}
	return $channels ?: $legacy;
}
add_filter( 'fasdent_floating_chat_channels', 'fasdent_filter_chat_channels' );

function fasdent_chat_channels_menu(): void {
	add_theme_page( __( 'کانال‌های تماس شناور', 'fasdent' ), __( 'کانال‌های چت', 'fasdent' ), 'manage_options', 'fasdent-chat-channels', 'fasdent_chat_channels_page' );
}
add_action( 'admin_menu', 'fasdent_chat_channels_menu' );

function fasdent_chat_icon_choices(): array {
	return array(
		'fa-brands fa-whatsapp' => 'WhatsApp', 'fa-brands fa-telegram' => 'Telegram', 'fa-brands fa-instagram' => 'Instagram',
		'fa-brands fa-x-twitter' => 'X', 'fa-brands fa-facebook' => 'Facebook', 'fa-solid fa-phone-volume' => 'Phone',
		'fa-solid fa-envelope' => 'Email', 'fa-solid fa-comment-sms' => 'SMS', 'fa-solid fa-globe' => 'Website',
		'fa-solid fa-map-location-dot' => 'Map', 'fa-solid fa-calendar-check' => 'Booking', 'fa-solid fa-headset' => 'Support',
	);
}

function fasdent_chat_channels_save(): void {
	if ( ! isset( $_POST['fasdent_chat_channels_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fasdent_chat_channels_nonce'] ), 'fasdent_chat_channels' ) ) { return; }
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$raw = isset( $_POST['channels'] ) && is_array( $_POST['channels'] ) ? wp_unslash( $_POST['channels'] ) : array();
	$clean = array(); $i = 0;
	$allowed_types = array( 'whatsapp', 'phone', 'telegram', 'email', 'sms', 'url' );
	$icons = array_keys( fasdent_chat_icon_choices() );
	foreach ( $raw as $row ) {
		if ( ! is_array( $row ) ) { continue; }
		$label = sanitize_text_field( $row['label'] ?? '' );
		$value = sanitize_text_field( $row['value'] ?? '' );
		if ( '' === $label && '' === $value ) { continue; }
		$type = sanitize_key( $row['type'] ?? 'url' );
		if ( ! in_array( $type, $allowed_types, true ) ) { $type = 'url'; }
		$icon = sanitize_text_field( $row['icon'] ?? 'fa-solid fa-comment' );
		if ( ! in_array( $icon, $icons, true ) ) { $icon = 'fa-solid fa-comment'; }
		$clean[] = array(
			'id' => sanitize_key( $row['id'] ?? ( 'ch_' . ( $i + 1 ) ) ),
			'label' => $label, 'note' => sanitize_text_field( $row['note'] ?? '' ),
			'type' => $type, 'value' => $value, 'icon' => $icon,
			'enabled' => empty( $row['enabled'] ) ? 0 : 1,
			'order' => (int) ( $row['order'] ?? ( ( $i + 1 ) * 10 ) ),
		);
		$i++;
	}
	update_option( 'fasdent_chat_channels', $clean, false );
	add_settings_error( 'fasdent_chat', 'saved', __( 'کانال‌ها ذخیره شد.', 'fasdent' ), 'updated' );
}
add_action( 'admin_init', 'fasdent_chat_channels_save' );

function fasdent_chat_channels_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$channels = fasdent_get_stored_chat_channels();
	$icons = fasdent_chat_icon_choices();
	$types = array( 'whatsapp' => 'WhatsApp', 'phone' => 'تلفن', 'telegram' => 'تلگرام', 'email' => 'ایمیل', 'sms' => 'پیامک', 'url' => 'لینک' );
	settings_errors( 'fasdent_chat' );
	echo '<div class="wrap" dir="rtl"><h1>کانال‌های تماس شناور (Chaty)</h1>';
	echo '<form method="post">'; wp_nonce_field( 'fasdent_chat_channels', 'fasdent_chat_channels_nonce' );
	echo '<table class="widefat striped" id="fasdent-chat-table" style="max-width:1100px"><thead><tr><th>فعال</th><th>برچسب</th><th>توضیح</th><th>نوع</th><th>مقدار</th><th>آیکون</th><th>ترتیب</th><th></th></tr></thead><tbody>';
	foreach ( $channels as $idx => $ch ) {
		echo '<tr><td><input type="checkbox" name="channels[' . (int) $idx . '][enabled]" value="1" ' . checked( ! empty( $ch['enabled'] ), true, false ) . '></td>';
		echo '<td><input type="hidden" name="channels[' . (int) $idx . '][id]" value="' . esc_attr( $ch['id'] ?? '' ) . '">';
		echo '<input type="text" class="regular-text" name="channels[' . (int) $idx . '][label]" value="' . esc_attr( $ch['label'] ?? '' ) . '"></td>';
		echo '<td><input type="text" class="regular-text" name="channels[' . (int) $idx . '][note]" value="' . esc_attr( $ch['note'] ?? '' ) . '"></td><td><select name="channels[' . (int) $idx . '][type]">';
		foreach ( $types as $k => $lbl ) { echo '<option value="' . esc_attr( $k ) . '" ' . selected( $ch['type'] ?? '', $k, false ) . '>' . esc_html( $lbl ) . '</option>'; }
		echo '</select></td><td><input type="text" class="regular-text" dir="ltr" name="channels[' . (int) $idx . '][value]" value="' . esc_attr( $ch['value'] ?? '' ) . '"></td><td><select name="channels[' . (int) $idx . '][icon]">';
		foreach ( $icons as $cls => $name ) { echo '<option value="' . esc_attr( $cls ) . '" ' . selected( $ch['icon'] ?? '', $cls, false ) . '>' . esc_html( $name ) . '</option>'; }
		echo '</select></td><td><input type="number" style="width:70px" name="channels[' . (int) $idx . '][order]" value="' . esc_attr( (string) ( $ch['order'] ?? 10 ) ) . '"></td>';
		echo '<td><button type="button" class="button fasdent-rm-row">حذف</button></td></tr>';
	}
	echo '</tbody></table><p><button type="button" class="button" id="fasdent-add-channel">+ افزودن کانال</button> ';
	submit_button( __( 'ذخیره کانال‌ها', 'fasdent' ), 'primary', 'submit', false ); echo '</p></form></div>';
	?>
<script>
(function(){
  var tbody=document.querySelector('#fasdent-chat-table tbody'), idx=tbody.querySelectorAll('tr').length;
  document.getElementById('fasdent-add-channel').addEventListener('click',function(){
    var tr=document.createElement('tr');
    tr.innerHTML='<td><input type="checkbox" name="channels['+idx+'][enabled]" value="1" checked></td><td><input type="hidden" name="channels['+idx+'][id]" value="ch_'+idx+'"><input type="text" class="regular-text" name="channels['+idx+'][label]"></td><td><input type="text" class="regular-text" name="channels['+idx+'][note]"></td><td><select name="channels['+idx+'][type]"><option value="whatsapp">WhatsApp</option><option value="phone">تلفن</option><option value="telegram">تلگرام</option><option value="email">ایمیل</option><option value="url" selected>لینک</option></select></td><td><input type="text" class="regular-text" dir="ltr" name="channels['+idx+'][value]"></td><td><select name="channels['+idx+'][icon]"><option value="fa-brands fa-whatsapp">WhatsApp</option><option value="fa-solid fa-phone-volume">Phone</option><option value="fa-brands fa-instagram">Instagram</option><option value="fa-solid fa-envelope">Email</option></select></td><td><input type="number" style="width:70px" name="channels['+idx+'][order]" value="'+((idx+1)*10)+'"></td><td><button type="button" class="button fasdent-rm-row">حذف</button></td>';
    tbody.appendChild(tr); idx++;
  });
  tbody.addEventListener('click',function(e){ if(e.target.classList.contains('fasdent-rm-row')) e.target.closest('tr').remove(); });
})();
</script>
	<?php
}
