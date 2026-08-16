<?php
/** Site-owned operational settings, validation and Rank Math NAP integration. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function alipasandi_nap_fields() {
	return array(
		'clinic_business_name'   => array( 'نام رسمی Entity/کسب‌وکار', 'text', 'کلینیک دندانپزشکی دکتر کیوان علی‌پسندی' ),
		'clinic_phone'           => array( 'شماره تماس نمایشی', 'text', '' ),
		'clinic_phone_e164'      => array( 'شماره E.164 برای tel/Schema', 'text', '' ),
		'clinic_address'         => array( 'آدرس نمایشی', 'text', '' ),
		'clinic_street'          => array( 'خیابان/پلاک Schema', 'text', '' ),
		'clinic_maps'            => array( 'لینک نقشه رسمی', 'url', '' ),
		'clinic_city'            => array( 'شهر عملیاتی', 'text', 'نوشهر' ),
		'clinic_region'          => array( 'استان', 'text', 'مازندران' ),
		'clinic_country'         => array( 'کد کشور ISO 3166-1 alpha-2', 'text', 'IR' ),
		'clinic_email'           => array( 'ایمیل عمومی کلینیک', 'email', '' ),
		'clinic_notify_email'    => array( 'ایمیل دریافت فرم (اجباری برای فرم)', 'email', '' ),
		'clinic_mail_from'       => array( 'From ثابت دامنه Production (اجباری برای فرم)', 'email', '' ),
		'clinic_mail_from_name'  => array( 'نام فرستنده ایمیل', 'text', '' ),
		'clinic_booking_times'   => array( 'ساعت‌های پیشنهادی درخواست نوبت؛ هر خط HH:MM', 'textarea', '' ),
		'clinic_opening_hours'   => array( 'ساعات کاری رسمی Schema؛ طبق Grammar مستند', 'textarea', '' ),
		'clinic_postal_code'     => array( 'کدپستی رسمی', 'text', '' ),
		'clinic_geo_lat'         => array( 'عرض جغرافیایی رسمی', 'text', '' ),
		'clinic_geo_lng'         => array( 'طول جغرافیایی رسمی', 'text', '' ),
		'clinic_instagram'       => array( 'اینستاگرام رسمی', 'url', '' ),
		'clinic_whatsapp'        => array( 'واتساپ رسمی', 'url', '' ),
		'clinic_telegram'        => array( 'تلگرام رسمی', 'url', '' ),
		'clinic_address_legacy'  => array( 'آدرس سابقه؛ غیرعملیاتی', 'text', '' ),
		'designer_credit'        => array( 'اعتبار طراحی', 'text', '' ),
	);
}

function alipasandi_normalize_digits( $value ) {
	return strtr( (string) $value, array( '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9' ) );
}

function alipasandi_setting_previous_value( $key ) {
	$fields = alipasandi_nap_fields();
	$default = isset( $fields[ $key ][2] ) ? $fields[ $key ][2] : '';
	return get_option( 'alipasandi_' . $key, $default );
}

function alipasandi_setting_reject( $key, $message ) {
	add_settings_error( 'alipasandi_operational', 'alipasandi_' . $key, $message, 'error' );
	return alipasandi_setting_previous_value( $key );
}

/** Semantic validation for Settings API writes. Invalid values do not replace the last known value. */
function alipasandi_sanitize_operational_setting( $key, $value ) {
	$key = sanitize_key( $key );
	if ( in_array( $key, array( 'clinic_maps', 'clinic_instagram', 'clinic_whatsapp', 'clinic_telegram' ), true ) ) {
		$value = trim( (string) $value );
		if ( '' === $value ) { return ''; }
		$clean = esc_url_raw( $value, array( 'http', 'https' ) );
		return $clean ? $clean : alipasandi_setting_reject( $key, 'URL معتبر نیست.' );
	}
	if ( in_array( $key, array( 'clinic_email', 'clinic_notify_email', 'clinic_mail_from' ), true ) ) {
		$value = trim( (string) $value );
		if ( '' === $value ) { return ''; }
		$clean = sanitize_email( $value );
		return is_email( $clean ) ? $clean : alipasandi_setting_reject( $key, 'ایمیل معتبر نیست.' );
	}
	if ( 'clinic_phone_e164' === $key ) {
		$value = trim( alipasandi_normalize_digits( $value ) );
		if ( '' === $value ) { return ''; }
		return alipasandi_valid_e164( $value ) ? $value : alipasandi_setting_reject( $key, 'شماره E.164 باید با + شروع شود و 8 تا 15 رقم داشته باشد.' );
	}
	if ( 'clinic_country' === $key ) {
		$value = strtoupper( trim( sanitize_text_field( $value ) ) );
		return alipasandi_valid_country_code( $value ) ? $value : alipasandi_setting_reject( $key, 'کد کشور باید دو حرف انگلیسی مانند IR باشد.' );
	}
	if ( 'clinic_geo_lat' === $key || 'clinic_geo_lng' === $key ) {
		$value = trim( alipasandi_normalize_digits( $value ) );
		if ( '' === $value ) { return ''; }
		$kind = 'clinic_geo_lat' === $key ? 'lat' : 'lng';
		return alipasandi_valid_geo( $value, $kind ) ? (string) (float) $value : alipasandi_setting_reject( $key, 'مختصات خارج از بازه معتبر است.' );
	}
	if ( 'clinic_booking_times' === $key ) {
		$value = trim( (string) $value );
		if ( '' === $value ) { return ''; }
		$valid = alipasandi_validate_booking_slots( $value );
		return is_wp_error( $valid ) ? alipasandi_setting_reject( $key, $valid->get_error_message() ) : $valid;
	}
	if ( 'clinic_opening_hours' === $key ) {
		$value = trim( (string) $value );
		if ( '' === $value ) { return ''; }
		$valid = alipasandi_validate_opening_hours( $value );
		return is_wp_error( $valid ) ? alipasandi_setting_reject( $key, $valid->get_error_message() ) : $valid;
	}

	$value = in_array( $key, array( 'clinic_booking_times', 'clinic_opening_hours' ), true ) ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
	if ( in_array( $key, array( 'clinic_business_name', 'clinic_city', 'clinic_region' ), true ) && '' === trim( $value ) ) {
		return alipasandi_setting_reject( $key, 'این فیلد برای NAP Production الزامی است.' );
	}
	return $value;
}

function alipasandi_register_site_settings() {
	foreach ( alipasandi_nap_fields() as $key => $field ) {
		register_setting(
			'alipasandi_operational',
			'alipasandi_' . $key,
			array(
				'type'              => 'string',
				'sanitize_callback' => function ( $value ) use ( $key ) { return alipasandi_sanitize_operational_setting( $key, $value ); },
				'default'           => $field[2],
				'show_in_rest'      => false,
			)
		);
	}
	register_setting( 'alipasandi_operational', 'alipasandi_show_treatment_count', array( 'type'=>'boolean', 'sanitize_callback'=>'rest_sanitize_boolean', 'default'=>false, 'show_in_rest'=>false ) );
}
add_action( 'admin_init', 'alipasandi_register_site_settings' );

/** One-time import of legacy theme_mods/options; future writes belong only to this plugin. */
function alipasandi_site_settings_migrate_legacy_theme_mods() {
	if ( get_option( 'alipasandi_nap_migrated_v2', false ) ) { return; }
	foreach ( alipasandi_nap_fields() as $key => $field ) {
		$option = 'alipasandi_' . $key;
		if ( null !== get_option( $option, null ) ) { continue; }
		$legacy = get_theme_mod( $key, null );
		if ( null !== $legacy && '' !== $legacy ) {
			$clean = alipasandi_sanitize_operational_setting( $key, $legacy );
			add_option( $option, $clean, '', false );
		}
	}
	if ( null === get_option( 'alipasandi_show_treatment_count', null ) ) {
		add_option( 'alipasandi_show_treatment_count', (bool) get_theme_mod( 'show_treatment_count', false ), '', false );
	}
	add_option( 'alipasandi_nap_migrated_v2', true, '', false );
}
add_action( 'admin_init', 'alipasandi_site_settings_migrate_legacy_theme_mods', 2 );

function alipasandi_operational_settings_menu() {
	add_options_page( 'اطلاعات عملیاتی کلینیک', 'اطلاعات کلینیک', 'manage_options', 'alipasandi-operational', 'alipasandi_operational_settings_page' );
}
add_action( 'admin_menu', 'alipasandi_operational_settings_menu' );

function alipasandi_operational_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	?>
	<div class="wrap" dir="rtl"><h1>اطلاعات عملیاتی کلینیک</h1>
	<p><strong>این Settings مستقل از Theme و SSOT عملیاتی NAP، فرم‌ها و Integration محلی است.</strong> URL سایت از <code>home_url('/')</code> مشتق می‌شود و Option جدا ندارد.</p>
	<p>Grammar ساعات کاری: هر خط <code>MO=09:00-13:00,14:00-18:00</code> یا <code>FR=CLOSED</code>. روزها: MO/TU/WE/TH/FR/SA/SU. Timezone همان WordPress timezone است؛ Production باید Asia/Tehran باشد.</p>
	<?php settings_errors( 'alipasandi_operational' ); ?>
	<form method="post" action="options.php"><?php settings_fields( 'alipasandi_operational' ); ?>
	<table class="form-table" role="presentation"><tbody>
	<?php foreach ( alipasandi_nap_fields() as $key => $field ) : $id = 'alipasandi_' . $key; $value = get_option( $id, $field[2] ); ?>
	<tr><th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field[0] ); ?></label></th><td>
	<?php if ( 'textarea' === $field[1] ) : ?><textarea class="large-text" rows="7" dir="ltr" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
	<?php else : ?><input class="regular-text" dir="ltr" type="<?php echo esc_attr( $field[1] ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>"><?php endif; ?>
	</td></tr><?php endforeach; ?>
	<tr><th scope="row">Claim +۱۰٬۰۰۰</th><td><label><input type="checkbox" name="alipasandi_show_treatment_count" value="1" <?php checked( get_option( 'alipasandi_show_treatment_count', false ) ); ?>> فقط پس از تأیید مستند Claim فعال شود</label></td></tr>
	</tbody></table><?php submit_button(); ?></form></div><?php
}

function alipasandi_clinic_option( $key ) {
	$key = sanitize_key( $key );
	if ( 'clinic_website' === $key ) { return home_url( '/' ); } // compatibility alias; not a stored SSOT.
	$fields = alipasandi_nap_fields();
	$default = isset( $fields[ $key ][2] ) ? $fields[ $key ][2] : '';
	$value = get_option( 'alipasandi_' . $key, null );
	return null !== $value && '' !== $value ? $value : $default;
}

function alipasandi_phone_href() {
	$e164 = trim( (string) alipasandi_clinic_option( 'clinic_phone_e164' ) );
	if ( alipasandi_valid_e164( $e164 ) ) { return $e164; }
	return preg_replace( '/[^0-9+]/', '', alipasandi_normalize_digits( alipasandi_clinic_option( 'clinic_phone' ) ) );
}

function alipasandi_show_treatment_count() {
	return (bool) get_option( 'alipasandi_show_treatment_count', false );
}

function alipasandi_home_domain() {
	$host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
	return preg_replace( '/^www\./', '', $host );
}

function alipasandi_email_matches_home_domain( $email ) {
	if ( ! is_email( $email ) ) { return false; }
	$domain = strtolower( substr( strrchr( $email, '@' ), 1 ) );
	$home = alipasandi_home_domain();
	return $domain === $home || ( $home && str_ends_with( $domain, '.' . $home ) );
}

/** Rank Math integration: preserve @id and update only the matching local entity. */
function alipasandi_rank_math_central_nap( $data ) {
	if ( ! defined( 'RANK_MATH_VERSION' ) || ! is_array( $data ) ) { return $data; }
	foreach ( $data as $index => $entity ) {
		$types = is_array( $entity ) && isset( $entity['@type'] ) ? (array) $entity['@type'] : array();
		if ( ! array_intersect( array( 'Dentist', 'LocalBusiness' ), $types ) ) { continue; }
		$data[ $index ]['name'] = alipasandi_clinic_option( 'clinic_business_name' );
		$data[ $index ]['url'] = home_url( '/' );
		$phone = trim( (string) alipasandi_clinic_option( 'clinic_phone_e164' ) );
		if ( alipasandi_valid_e164( $phone ) ) { $data[ $index ]['telephone'] = $phone; } else { unset( $data[ $index ]['telephone'] ); }
		$map = trim( (string) alipasandi_clinic_option( 'clinic_maps' ) );
		if ( $map ) { $data[ $index ]['hasMap'] = $map; } else { unset( $data[ $index ]['hasMap'] ); }
		$data[ $index ]['address'] = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => alipasandi_clinic_option( 'clinic_street' ),
			'addressLocality' => alipasandi_clinic_option( 'clinic_city' ),
			'addressRegion'   => alipasandi_clinic_option( 'clinic_region' ),
			'addressCountry'  => strtoupper( alipasandi_clinic_option( 'clinic_country' ) ),
		);
		$postal = trim( (string) alipasandi_clinic_option( 'clinic_postal_code' ) );
		if ( $postal ) { $data[ $index ]['address']['postalCode'] = $postal; }
		$lat = trim( (string) alipasandi_clinic_option( 'clinic_geo_lat' ) );
		$lng = trim( (string) alipasandi_clinic_option( 'clinic_geo_lng' ) );
		if ( alipasandi_valid_geo( $lat, 'lat' ) && alipasandi_valid_geo( $lng, 'lng' ) ) {
			$data[ $index ]['geo'] = array( '@type'=>'GeoCoordinates', 'latitude'=>(float)$lat, 'longitude'=>(float)$lng );
		} else { unset( $data[ $index ]['geo'] ); }
		$hours = alipasandi_parse_opening_hours( alipasandi_clinic_option( 'clinic_opening_hours' ) );
		if ( empty( $hours['errors'] ) && ! empty( $hours['specs'] ) ) {
			$data[ $index ]['openingHoursSpecification'] = $hours['specs'];
		} else { unset( $data[ $index ]['openingHoursSpecification'] ); }
	}
	return $data;
}
add_filter( 'rank_math/json_ld', 'alipasandi_rank_math_central_nap', 90 );

function alipasandi_domain_mail_from( $from ) {
	$configured = sanitize_email( alipasandi_clinic_option( 'clinic_mail_from' ) );
	return is_email( $configured ) ? $configured : $from;
}
add_filter( 'wp_mail_from', 'alipasandi_domain_mail_from' );

function alipasandi_domain_mail_from_name( $name ) {
	$configured = trim( sanitize_text_field( alipasandi_clinic_option( 'clinic_mail_from_name' ) ) );
	if ( '' === $configured ) { $configured = trim( sanitize_text_field( alipasandi_clinic_option( 'clinic_business_name' ) ) ); }
	return '' !== $configured ? $configured : $name;
}
add_filter( 'wp_mail_from_name', 'alipasandi_domain_mail_from_name' );
