<?php
/** Site-owned operational settings and integrations. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function alipasandi_nap_fields() {
	return array(
		'clinic_business_name'  => array( 'نام رسمی Entity/کسب‌وکار', 'text', 'sanitize_text_field', 'کلینیک دندانپزشکی دکتر کیوان علی‌پسندی' ),
		'clinic_phone'          => array( 'شماره تماس', 'text', 'sanitize_text_field', '0920 144 1469' ),
		'clinic_phone_e164'     => array( 'شماره بین‌المللی Schema/tel', 'text', 'sanitize_text_field', '+989201441469' ),
		'clinic_address'        => array( 'آدرس نمایشی', 'text', 'sanitize_text_field', 'نوشهر، ستارخان، امیراد ۱، طبقه ۵' ),
		'clinic_street'         => array( 'خیابان/پلاک Schema', 'text', 'sanitize_text_field', 'ستارخان، امیراد ۱، طبقه ۵' ),
		'clinic_maps'           => array( 'لینک نقشه', 'url', 'esc_url_raw', '' ),
		'clinic_city'           => array( 'شهر عملیاتی', 'text', 'sanitize_text_field', 'نوشهر' ),
		'clinic_region'         => array( 'استان', 'text', 'sanitize_text_field', 'مازندران' ),
		'clinic_country'        => array( 'کد کشور ISO', 'text', 'sanitize_text_field', 'IR' ),
		'clinic_notify_email'   => array( 'ایمیل دریافت فرم', 'email', 'sanitize_email', 'clinic@fasdent.ir' ),
		'clinic_notification_cc'=> array( 'ایمیل دوم دریافت فرم', 'email', 'sanitize_email', 'Dr.keyvan.alipasandii@gmail.com' ),
		'clinic_mail_from'      => array( 'From ثابت دامنه Production', 'email', 'sanitize_email', 'noreply@fasdent.ir' ),
		'clinic_booking_times'  => array( 'ساعات رزرو؛ هر خط یک ساعت', 'textarea', 'sanitize_textarea_field', '' ),
		'clinic_opening_hours'  => array( 'ساعات کاری رسمی Schema؛ مستقل از Slot رزرو', 'textarea', 'sanitize_textarea_field', '' ),
		'clinic_postal_code'    => array( 'کدپستی رسمی', 'text', 'sanitize_text_field', '' ),
		'clinic_geo_lat'        => array( 'عرض جغرافیایی رسمی', 'text', 'sanitize_text_field', '' ),
		'clinic_geo_lng'        => array( 'طول جغرافیایی رسمی', 'text', 'sanitize_text_field', '' ),
		'clinic_website'        => array( 'وب‌سایت', 'url', 'esc_url_raw', '' ),
		'clinic_instagram'      => array( 'اینستاگرام', 'url', 'esc_url_raw', '' ),
		'clinic_whatsapp'       => array( 'واتساپ', 'url', 'esc_url_raw', '' ),
		'clinic_telegram'       => array( 'تلگرام', 'url', 'esc_url_raw', '' ),
		'clinic_address_legacy' => array( 'آدرس سابقه؛ غیرعملیاتی', 'text', 'sanitize_text_field', '' ),
		'designer_credit'       => array( 'اعتبار طراحی', 'text', 'sanitize_text_field', '' ),
	);
}

function alipasandi_register_site_settings() {
	foreach ( alipasandi_nap_fields() as $key => $field ) {
		register_setting(
			'alipasandi_operational',
			'alipasandi_' . $key,
			array( 'type' => 'string', 'sanitize_callback' => $field[2], 'default' => $field[3], 'show_in_rest' => false )
		);
	}
	register_setting( 'alipasandi_operational', 'alipasandi_show_treatment_count', array( 'type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean', 'default' => false, 'show_in_rest' => false ) );
}
add_action( 'admin_init', 'alipasandi_register_site_settings' );

function alipasandi_site_settings_migrate_legacy_theme_mods() {
	if ( get_option( 'alipasandi_nap_migrated', false ) ) { return; }
	foreach ( alipasandi_nap_fields() as $key => $field ) {
		$option = 'alipasandi_' . $key;
		if ( null === get_option( $option, null ) ) {
			$legacy = get_theme_mod( $key, null );
			if ( null !== $legacy && '' !== $legacy ) { add_option( $option, call_user_func( $field[2], $legacy ), '', true ); }
		}
	}
	if ( null === get_option( 'alipasandi_show_treatment_count', null ) ) {
		add_option( 'alipasandi_show_treatment_count', (bool) get_theme_mod( 'show_treatment_count', false ), '', true );
	}
	add_option( 'alipasandi_nap_migrated', true, '', false );
}
add_action( 'admin_init', 'alipasandi_site_settings_migrate_legacy_theme_mods', 2 );

function alipasandi_operational_settings_menu() {
	add_options_page( 'اطلاعات عملیاتی کلینیک', 'اطلاعات کلینیک', 'manage_options', 'alipasandi-operational', 'alipasandi_operational_settings_page' );
}
add_action( 'admin_menu', 'alipasandi_operational_settings_menu' );

function alipasandi_operational_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap" dir="rtl"><h1>اطلاعات عملیاتی کلینیک</h1>
	<p><strong>این Settings مستقل از Theme و منبع اصلی NAP، فرم‌ها و Integration محلی است.</strong> Geo/Postal/Hours فقط پس از تأیید رسمی وارد شوند.</p>
	<form method="post" action="options.php"><?php settings_fields( 'alipasandi_operational' ); ?>
	<table class="form-table" role="presentation"><tbody>
	<?php foreach ( alipasandi_nap_fields() as $key => $field ) : $id = 'alipasandi_' . $key; $value = get_option( $id, $field[3] ); ?>
	<tr><th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field[0] ); ?></label></th><td>
	<?php if ( 'textarea' === $field[1] ) : ?><textarea class="large-text" rows="7" dir="ltr" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
	<?php else : ?><input class="regular-text" dir="ltr" type="<?php echo esc_attr( $field[1] ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>"><?php endif; ?>
	</td></tr><?php endforeach; ?>
	<tr><th scope="row">Claim +۱۰٬۰۰۰</th><td><label><input type="checkbox" name="alipasandi_show_treatment_count" value="1" <?php checked( get_option( 'alipasandi_show_treatment_count', false ) ); ?>> فقط پس از ثبت Claim approval فعال شود</label></td></tr>
	</tbody></table><?php submit_button(); ?></form></div>
	<?php
}

function alipasandi_clinic_option( $key ) {
	$fields = alipasandi_nap_fields();
	$default = isset( $fields[ $key ][3] ) ? $fields[ $key ][3] : '';
	$value = get_option( 'alipasandi_' . sanitize_key( $key ), null );
	return null !== $value && '' !== $value ? $value : $default;
}

function alipasandi_form_recipients() {
	$recipients = array(
		sanitize_email( alipasandi_clinic_option( 'clinic_notify_email' ) ),
		sanitize_email( alipasandi_clinic_option( 'clinic_notification_cc' ) ),
	);
	return array_values( array_unique( array_filter( $recipients, 'is_email' ) ) );
}

function alipasandi_normalize_digits( $value ) {
	return strtr( $value, array( '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9' ) );
}

function alipasandi_phone_href() {
	return preg_replace( '/[^0-9+]/', '', alipasandi_normalize_digits( alipasandi_clinic_option( 'clinic_phone_e164' ) ) );
}

function alipasandi_show_treatment_count() {
	return (bool) get_option( 'alipasandi_show_treatment_count', false );
}

/** Rank Math integration remains guarded and site-owned. */
function alipasandi_rank_math_central_nap( $data ) {
	if ( ! defined( 'RANK_MATH_VERSION' ) || ! is_array( $data ) ) {
		return $data;
	}
	foreach ( $data as $index => $entity ) {
		$types = is_array( $entity ) && isset( $entity['@type'] ) ? (array) $entity['@type'] : array();
		if ( ! array_intersect( array( 'Dentist', 'LocalBusiness' ), $types ) ) {
			continue;
		}
		$data[ $index ]['name'] = alipasandi_clinic_option( 'clinic_business_name' );
		$data[ $index ]['url'] = home_url( '/' );
		$data[ $index ]['telephone'] = alipasandi_phone_href();
		$data[ $index ]['hasMap'] = alipasandi_clinic_option( 'clinic_maps' );
		$data[ $index ]['address'] = array( '@type'=>'PostalAddress', 'streetAddress'=>alipasandi_clinic_option( 'clinic_street' ), 'addressLocality'=>alipasandi_clinic_option( 'clinic_city' ), 'addressRegion'=>alipasandi_clinic_option( 'clinic_region' ), 'addressCountry'=>strtoupper( alipasandi_clinic_option( 'clinic_country' ) ) );
		$postal = trim( (string) alipasandi_clinic_option( 'clinic_postal_code' ) );
		if ( '' !== $postal ) { $data[ $index ]['address']['postalCode'] = $postal; }
		$lat = trim( (string) alipasandi_clinic_option( 'clinic_geo_lat' ) );
		$lng = trim( (string) alipasandi_clinic_option( 'clinic_geo_lng' ) );
		if ( '' !== $lat && '' !== $lng && is_numeric( $lat ) && is_numeric( $lng ) ) {
			$data[ $index ]['geo'] = array( '@type'=>'GeoCoordinates', 'latitude'=>(float)$lat, 'longitude'=>(float)$lng );
		} else { unset( $data[ $index ]['geo'] ); }
	}
	return $data;
}
add_filter( 'rank_math/json_ld', 'alipasandi_rank_math_central_nap', 90 );

function alipasandi_domain_mail_from( $from ) {
	$configured = sanitize_email( alipasandi_clinic_option( 'clinic_mail_from' ) );
	return is_email( $configured ) ? $configured : $from;
}
add_filter( 'wp_mail_from', 'alipasandi_domain_mail_from' );
