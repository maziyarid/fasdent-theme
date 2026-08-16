<?php
/**
 * Canonical clinic settings, NAP SSOT, Rank Math integration and release freeze.
 *
 * @package Alipasandi_Service_Content
 * @since 1.3.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return the canonical option name for a clinic setting key. */
function alipasandi_clinic_option_name( $key ) {
	return 'alipasandi_' . sanitize_key( $key );
}

/** Read one clinic setting without operational fallbacks. */
function alipasandi_clinic_option( $key ) {
	$key = sanitize_key( $key );
	if ( 'clinic_website' === $key ) {
		return home_url( '/' );
	}
	return get_option( alipasandi_clinic_option_name( $key ), '' );
}

/** Normalize Persian/Arabic digits for machine validation. */
if ( ! function_exists( 'alipasandi_normalize_digits' ) ) {
	function alipasandi_normalize_digits( $value ) {
		return strtr(
			(string) $value,
			array(
				'۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
				'٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
			)
		);
	}
}

/** Remove presentation separators/bidi marks without forgiving letters. */
if ( ! function_exists( 'alipasandi_normalize_e164_value' ) ) {
	function alipasandi_normalize_e164_value( $value ) {
		$value = alipasandi_normalize_digits( $value );
		$value = preg_replace( '/[\s().-]+/u', '', $value );
		$value = preg_replace( '/[\x{200E}\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', (string) $value );
		return is_string( $value ) ? $value : '';
	}
}

/** Strict E.164 sanitizer. Invalid values are stored as empty. */
function alipasandi_sanitize_e164_option( $value ) {
	$value = alipasandi_normalize_e164_value( $value );
	return 1 === preg_match( '/^\+[1-9][0-9]{7,14}$/', $value ) ? $value : '';
}

/** Textarea sanitizer that preserves newlines for schedule grammars. */
function alipasandi_sanitize_multiline_option( $value ) {
	return sanitize_textarea_field( wp_unslash( (string) $value ) );
}

/** Strict bounded integer sanitizer shared by owner-approved policy fields. */
function alipasandi_sanitize_bounded_integer_option( $value, $minimum, $maximum ) {
	$value = trim( alipasandi_normalize_digits( $value ) );
	if ( '' === $value || ! ctype_digit( $value ) ) {
		return '';
	}
	$value = (int) $value;
	return $value >= $minimum && $value <= $maximum ? (string) $value : '';
}

function alipasandi_sanitize_booking_horizon_option( $value ) {
	return alipasandi_sanitize_bounded_integer_option( $value, 1, 3650 );
}

function alipasandi_sanitize_booking_lead_option( $value ) {
	return alipasandi_sanitize_bounded_integer_option( $value, 0, 43200 );
}

/** Canonical settings schema. No field has a production-data fallback. */
function alipasandi_site_settings_fields() {
	return array(
		'clinic_business_name'    => array( 'label'=>'نام رسمی کسب‌وکار', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'clinic_phone'            => array( 'label'=>'شماره تلفن نمایشی', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'clinic_phone_e164'       => array( 'label'=>'شماره E.164 برای tel:', 'type'=>'text', 'sanitize'=>'alipasandi_sanitize_e164_option' ),
		'clinic_address'          => array( 'label'=>'آدرس کامل نمایشی', 'type'=>'textarea', 'sanitize'=>'sanitize_textarea_field' ),
		'clinic_street'           => array( 'label'=>'خیابان / streetAddress', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'clinic_city'             => array( 'label'=>'شهر', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'clinic_region'           => array( 'label'=>'استان / منطقه', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'clinic_country'          => array( 'label'=>'کد کشور دوحرفی', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'clinic_postal_code'      => array( 'label'=>'کد پستی', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'clinic_geo_lat'          => array( 'label'=>'Latitude', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'clinic_geo_lng'          => array( 'label'=>'Longitude', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'clinic_maps'             => array( 'label'=>'نشانی نقشه رسمی', 'type'=>'url', 'sanitize'=>'esc_url_raw' ),
		'clinic_email'            => array( 'label'=>'ایمیل عمومی', 'type'=>'email', 'sanitize'=>'sanitize_email' ),
		'clinic_notify_email'     => array( 'label'=>'ایمیل دریافت فرم', 'type'=>'email', 'sanitize'=>'sanitize_email' ),
		'clinic_mail_from'        => array( 'label'=>'Mail From', 'type'=>'email', 'sanitize'=>'sanitize_email' ),
		'clinic_mail_from_name'   => array( 'label'=>'Mail From Name', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'clinic_booking_times'    => array( 'label'=>'زمان‌های پیشنهادی', 'type'=>'textarea', 'sanitize'=>'alipasandi_sanitize_multiline_option' ),
		'clinic_opening_hours'    => array( 'label'=>'ساعات کاری', 'type'=>'textarea', 'sanitize'=>'alipasandi_sanitize_multiline_option' ),
		'booking_horizon_days'    => array( 'label'=>'افق نوبت‌دهی (روز)', 'type'=>'number', 'sanitize'=>'alipasandi_sanitize_booking_horizon_option', 'min'=>1, 'max'=>3650 ),
		'booking_lead_minutes'    => array( 'label'=>'Lead time (دقیقه)', 'type'=>'number', 'sanitize'=>'alipasandi_sanitize_booking_lead_option', 'min'=>0, 'max'=>43200 ),
		'clinic_instagram'        => array( 'label'=>'Instagram URL', 'type'=>'url', 'sanitize'=>'esc_url_raw' ),
		'clinic_whatsapp'         => array( 'label'=>'WhatsApp URL', 'type'=>'url', 'sanitize'=>'esc_url_raw' ),
		'clinic_telegram'         => array( 'label'=>'Telegram URL', 'type'=>'url', 'sanitize'=>'esc_url_raw' ),
		'designer_credit'         => array( 'label'=>'اعتبار طراح', 'type'=>'text', 'sanitize'=>'sanitize_text_field' ),
		'local_schema_owner'      => array( 'label'=>'مالک Local Schema', 'type'=>'select', 'sanitize'=>'alipasandi_sanitize_local_schema_owner' ),
	);
}

/** Local Schema is explicit: disabled or Rank Math. */
function alipasandi_sanitize_local_schema_owner( $value ) {
	$value = sanitize_key( $value );
	return in_array( $value, array( 'disabled', 'rank_math' ), true ) ? $value : 'disabled';
}

/** Register canonical settings. */
function alipasandi_register_site_settings() {
	foreach ( alipasandi_site_settings_fields() as $key => $field ) {
		register_setting(
			'alipasandi_clinic',
			alipasandi_clinic_option_name( $key ),
			array(
				'type'              => 'string',
				'show_in_rest'      => false,
				'default'           => '',
				'sanitize_callback' => $field['sanitize'],
			)
		);
	}
}
add_action( 'admin_init', 'alipasandi_register_site_settings' );

/** One-time safe alias migration for scalar keys used by rejected patch builds. */
function alipasandi_site_settings_migrate_legacy_theme_mods() {
	if ( get_option( 'alipasandi_nap_migrated_v2', false ) ) {
		return;
	}
	$aliases = array(
		'business_name'    => 'clinic_business_name',
		'clinic_postal'    => 'clinic_postal_code',
		'clinic_map_embed' => 'clinic_maps',
		'mail_from'        => 'clinic_mail_from',
		'mail_from_name'   => 'clinic_mail_from_name',
	);
	foreach ( $aliases as $old => $canonical ) {
		$target = alipasandi_clinic_option_name( $canonical );
		if ( '' !== (string) get_option( $target, '' ) ) {
			continue;
		}
		$value = get_option( 'alipasandi_' . $old, '' );
		if ( is_scalar( $value ) && '' !== trim( (string) $value ) ) {
			$fields = alipasandi_site_settings_fields();
			$callback = isset( $fields[ $canonical ]['sanitize'] ) ? $fields[ $canonical ]['sanitize'] : 'sanitize_text_field';
			$clean = is_callable( $callback ) ? call_user_func( $callback, (string) $value ) : sanitize_text_field( (string) $value );
			if ( '' !== (string) $clean ) {
				update_option( $target, $clean, false );
			}
		}
	}
	update_option( 'alipasandi_nap_migrated_v2', gmdate( 'c' ), false );
}
add_action( 'admin_init', 'alipasandi_site_settings_migrate_legacy_theme_mods', 2 );

/** Add the operational settings page. */
function alipasandi_operational_settings_menu() {
	add_options_page(
		'اطلاعات کلینیک',
		'اطلاعات کلینیک',
		'manage_options',
		'alipasandi-clinic-settings',
		'alipasandi_render_operational_settings'
	);
}
add_action( 'admin_menu', 'alipasandi_operational_settings_menu' );

/** Render the canonical settings form. */
function alipasandi_render_operational_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap" dir="rtl">
		<h1>اطلاعات کلینیک</h1>
		<p>این صفحه تنها منبع تنظیمات عملیاتی NAP، فرم، ساعت و شبکه‌های اجتماعی است. تغییر مکان باید همه اجزای آدرس را در یک بازبینی به‌روزرسانی کند و Freeze قبلی را باطل می‌کند.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'alipasandi_clinic' ); ?>
			<table class="form-table" role="presentation"><tbody>
			<?php foreach ( alipasandi_site_settings_fields() as $key => $field ) : $name = alipasandi_clinic_option_name( $key ); $value = get_option( $name, '' ); ?>
				<tr>
					<th scope="row"><label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
					<td>
					<?php if ( 'textarea' === $field['type'] ) : ?>
						<textarea class="large-text code" rows="5" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
					<?php elseif ( 'select' === $field['type'] ) : ?>
						<select id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>"><option value="disabled" <?php selected( $value, 'disabled' ); ?>>Disabled</option><option value="rank_math" <?php selected( $value, 'rank_math' ); ?>>Rank Math</option></select>
					<?php else : ?>
					<input class="regular-text" dir="auto" type="<?php echo esc_attr( $field['type'] ); ?>" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo isset( $field['min'] ) ? ' min="' . esc_attr( $field['min'] ) . '"' : ''; ?><?php echo isset( $field['max'] ) ? ' max="' . esc_attr( $field['max'] ) . '"' : ''; ?>>
					<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody></table>
			<p><strong>گرامر زمان نوبت:</strong> هر خط <code>HH:MM</code> برای همه روزهای باز، یا <code>MO=09:00,10:00</code> برای یک روز؛ <code>FR=CLOSED</code> یعنی آن روز نوبت ندارد.</p>
			<p><strong>گرامر ساعات کاری:</strong> مانند <code>MO=09:00-13:00,14:00-18:00</code>. زمان پایان بازه انحصاری است.</p>
			<p><strong>Local Schema:</strong> فقط <code>disabled</code> یا <code>rank_math</code>. فعال‌کردن Rank Math بدون NAP کامل، LocalBusiness ناقص تولید نمی‌کند.</p>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/** Required structured NAP and shared schema/display contract. */
class Alipasandi_NAP_Helper {
	const ADDRESS_FIELDS = array( 'clinic_street', 'clinic_city', 'clinic_region', 'clinic_country' );
	const REQUIRED_FIELDS = array( 'clinic_business_name', 'clinic_street', 'clinic_city', 'clinic_region', 'clinic_country', 'clinic_phone', 'clinic_phone_e164' );

	public static function required() {
		$out = array();
		foreach ( self::REQUIRED_FIELDS as $key ) {
			$out[ $key ] = trim( (string) alipasandi_clinic_option( $key ) );
		}
		return $out;
	}

	public static function missing() {
		$missing = array_keys( array_filter( self::required(), static function ( $value ) { return '' === $value; } ) );
		if ( ! in_array( 'clinic_country', $missing, true ) && function_exists( 'alipasandi_valid_country_code' ) && ! alipasandi_valid_country_code( alipasandi_clinic_option( 'clinic_country' ) ) ) {
			$missing[] = 'clinic_country_invalid';
		}
		if ( ! in_array( 'clinic_phone_e164', $missing, true ) && function_exists( 'alipasandi_valid_e164' ) && ! alipasandi_valid_e164( alipasandi_clinic_option( 'clinic_phone_e164' ) ) ) {
			$missing[] = 'clinic_phone_e164_invalid';
		}
		return $missing;
	}

	public static function is_complete() {
		return array() === self::missing();
	}

	public static function address_is_complete() {
		foreach ( self::ADDRESS_FIELDS as $key ) {
			if ( '' === trim( (string) alipasandi_clinic_option( $key ) ) ) {
				return false;
			}
		}
		return ! function_exists( 'alipasandi_valid_country_code' ) || alipasandi_valid_country_code( alipasandi_clinic_option( 'clinic_country' ) );
	}

	public static function structured_address() {
		if ( ! self::address_is_complete() ) {
			return array();
		}
		$nap = self::required();
		$address = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $nap['clinic_street'],
			'addressLocality' => $nap['clinic_city'],
			'addressRegion'   => $nap['clinic_region'],
			'addressCountry'  => strtoupper( $nap['clinic_country'] ),
		);
		$postal = trim( (string) alipasandi_clinic_option( 'clinic_postal_code' ) );
		if ( '' !== $postal ) {
			$address['postalCode'] = $postal;
		}
		return $address;
	}

	public static function composed_address() {
		if ( ! self::address_is_complete() ) {
			return '';
		}
		$nap = self::required();
		return implode( '، ', array( $nap['clinic_street'], $nap['clinic_city'], $nap['clinic_region'], strtoupper( $nap['clinic_country'] ) ) );
	}

	public static function display_address() {
		if ( ! self::address_is_complete() ) {
			return '';
		}
		$display = trim( (string) alipasandi_clinic_option( 'clinic_address' ) );
		return '' !== $display ? $display : self::composed_address();
	}

	public static function complete_snapshot() {
		$out = array();
		foreach ( array_keys( alipasandi_site_settings_fields() ) as $key ) {
			$out[ $key ] = alipasandi_clinic_option( $key );
		}
		return $out;
	}
}

/** Public display-address contract for the Theme and diagnostics. */
function alipasandi_display_address() {
	return Alipasandi_NAP_Helper::display_address();
}

/** Canonical values compared across rendered NAP surfaces. */
function alipasandi_nap_surface_fields() {
	return array(
		'name'    => trim( (string) alipasandi_clinic_option( 'clinic_business_name' ) ),
		'address' => Alipasandi_NAP_Helper::display_address(),
		'phone'   => alipasandi_sanitize_e164_option( alipasandi_clinic_option( 'clinic_phone_e164' ) ),
	);
}

/** Stable hash used by actual-render consistency probes. */
function alipasandi_nap_surface_hash( $fields = null ) {
	$fields = is_array( $fields ) ? $fields : alipasandi_nap_surface_fields();
	$fields = array(
		'address' => isset( $fields['address'] ) ? trim( (string) $fields['address'] ) : '',
		'name'    => isset( $fields['name'] ) ? trim( (string) $fields['name'] ) : '',
		'phone'   => isset( $fields['phone'] ) ? trim( (string) $fields['phone'] ) : '',
	);
	return hash( 'sha256', wp_json_encode( $fields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
}

/** Emit a non-visual marker bound to the values rendered by a Theme surface. */
function alipasandi_nap_surface_marker( $surface, $fields = null ) {
	$surface = sanitize_key( $surface );
	if ( '' === $surface ) {
		return;
	}
	echo '<span hidden data-alipasandi-nap-surface="' . esc_attr( $surface ) . '" data-alipasandi-nap-sha256="' . esc_attr( alipasandi_nap_surface_hash( $fields ) ) . '"></span>' . "\n";
}

/** Normalized direct phone route; never falls back to a display string. */
function alipasandi_phone_href() {
	return alipasandi_sanitize_e164_option( alipasandi_clinic_option( 'clinic_phone_e164' ) );
}

/** Set the domain-aligned From address. */
function alipasandi_domain_mail_from( $from ) {
	$configured = sanitize_email( alipasandi_clinic_option( 'clinic_mail_from' ) );
	if ( function_exists( 'alipasandi_mail_configuration' ) ) {
		$config = alipasandi_mail_configuration();
		return ! empty( $config['pass'] ) ? $configured : $from;
	}
	return is_email( $configured ) ? $configured : $from;
}
add_filter( 'wp_mail_from', 'alipasandi_domain_mail_from' );

/** Set the configured From name. */
function alipasandi_domain_mail_from_name( $name ) {
	$configured = trim( (string) alipasandi_clinic_option( 'clinic_mail_from_name' ) );
	if ( function_exists( 'alipasandi_mail_configuration' ) ) {
		$config = alipasandi_mail_configuration();
		return ! empty( $config['pass'] ) ? $configured : $name;
	}
	return '' !== $configured ? $configured : $name;
}
add_filter( 'wp_mail_from_name', 'alipasandi_domain_mail_from_name' );

/** Detect a LocalBusiness-family schema entity. */
function alipasandi_schema_is_local_entity( $type ) {
	$types = is_array( $type ) ? $type : array( $type );
	return (bool) array_intersect( array( 'LocalBusiness', 'Dentist', 'MedicalBusiness' ), $types );
}

/** Recursively remove or normalize local entities. Null means remove this node. */
function alipasandi_filter_local_schema_node( $node, $owner ) {
	if ( ! is_array( $node ) ) {
		return $node;
	}
	$was_list = array_is_list( $node );
	if ( isset( $node['@type'] ) && alipasandi_schema_is_local_entity( $node['@type'] ) ) {
		if ( 'rank_math' !== $owner || ! Alipasandi_NAP_Helper::is_complete() ) {
			return null;
		}
		$required = Alipasandi_NAP_Helper::required();
		$node['name'] = $required['clinic_business_name'];
		$node['url'] = home_url( '/' );
		$node['address'] = Alipasandi_NAP_Helper::structured_address();
		$phone = alipasandi_phone_href();
		if ( '' !== $phone ) { $node['telephone'] = $phone; } else { unset( $node['telephone'] ); }
		$map = esc_url_raw( alipasandi_clinic_option( 'clinic_maps' ) );
		if ( '' !== $map ) { $node['hasMap'] = $map; } else { unset( $node['hasMap'] ); }
		$lat = trim( (string) alipasandi_clinic_option( 'clinic_geo_lat' ) );
		$lng = trim( (string) alipasandi_clinic_option( 'clinic_geo_lng' ) );
		if ( function_exists( 'alipasandi_valid_geo' ) && alipasandi_valid_geo( $lat, 'lat' ) && alipasandi_valid_geo( $lng, 'lng' ) ) {
			$node['geo'] = array( '@type'=>'GeoCoordinates', 'latitude'=>(float)$lat, 'longitude'=>(float)$lng );
		} else {
			unset( $node['geo'] );
		}
		if ( function_exists( 'alipasandi_parse_opening_hours' ) ) {
			$hours = alipasandi_parse_opening_hours( alipasandi_clinic_option( 'clinic_opening_hours' ) );
			if ( empty( $hours['errors'] ) && ! empty( $hours['specs'] ) ) {
				$node['openingHoursSpecification'] = $hours['specs'];
			} else {
				unset( $node['openingHoursSpecification'] );
			}
		}
	}
	foreach ( $node as $key => $value ) {
		if ( ! is_array( $value ) ) { continue; }
		$filtered = alipasandi_filter_local_schema_node( $value, $owner );
		if ( null === $filtered ) { unset( $node[ $key ] ); } else { $node[ $key ] = $filtered; }
	}
	return $was_list ? array_values( $node ) : $node;
}

/** Rank Math integration uses the same required NAP contract as Health. */
function alipasandi_rank_math_central_nap( $data ) {
	if ( ! is_array( $data ) ) {
		return $data;
	}
	$owner = alipasandi_sanitize_local_schema_owner( alipasandi_clinic_option( 'local_schema_owner' ) );
	$filtered = alipasandi_filter_local_schema_node( $data, $owner );
	return is_array( $filtered ) ? $filtered : array();
}
add_filter( 'rank_math/json_ld', 'alipasandi_rank_math_central_nap', 90 );

/** Canonicalize nested snapshot data before hashing. */
function alipasandi_canonicalize_snapshot( $value ) {
	if ( ! is_array( $value ) ) {
		return $value;
	}
	if ( ! array_is_list( $value ) ) {
		ksort( $value, SORT_STRING );
	}
	foreach ( $value as $key => $item ) {
		$value[ $key ] = alipasandi_canonicalize_snapshot( $item );
	}
	return $value;
}

/** Owner-approved, artifact-bound NAP freeze. */
class Alipasandi_NAP_Freeze {
	const OPTION = 'alipasandi_nap_freeze_v3';
	const INVALIDATED_OPTION = 'alipasandi_nap_freeze_invalidated_v3';

	public static function tracked_option_names() {
		return array_map( 'alipasandi_clinic_option_name', array_keys( alipasandi_site_settings_fields() ) );
	}

	public static function current_context() {
		$theme = wp_get_theme();
		return array(
			'environment'    => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'unknown',
			'home_url'       => home_url( '/' ),
			'timezone'       => wp_timezone_string(),
			'theme_version'  => $theme->get( 'Version' ),
			'theme_sha256'   => strtolower( trim( (string) get_option( 'alipasandi_release_theme_sha256', '' ) ) ),
			'plugin_version' => defined( 'ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION' ) ? ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION : '',
			'plugin_sha256'  => strtolower( trim( (string) get_option( 'alipasandi_release_plugin_sha256', '' ) ) ),
		);
	}

	public static function current_data() {
		return Alipasandi_NAP_Helper::complete_snapshot();
	}

	public static function freeze( $owner_name, $note = '' ) {
		$owner_name = sanitize_text_field( (string) $owner_name );
		if ( '' === $owner_name ) {
			return new WP_Error( 'owner_required', 'Owner name is required.' );
		}
		if ( ! Alipasandi_NAP_Helper::is_complete() ) {
			return new WP_Error( 'nap_incomplete', 'Canonical NAP must be complete and valid before approval.' );
		}
		$context = self::current_context();
		foreach ( array( 'theme_sha256', 'plugin_sha256' ) as $hash_key ) {
			if ( 1 !== preg_match( '/^[a-f0-9]{64}$/', $context[ $hash_key ] ) ) {
				return new WP_Error( 'artifact_hash_required', 'Both final artifact SHA-256 values must be set before freeze.' );
			}
		}
		$payload = alipasandi_canonicalize_snapshot( array( 'context'=>$context, 'data'=>self::current_data() ) );
		$json = wp_json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		$state = array(
			'frozen_at_utc' => gmdate( 'c' ),
			'approved_by'   => $owner_name,
			'approval_note' => sanitize_text_field( (string) $note ),
			'payload'       => $payload,
			'freeze_sha256' => hash( 'sha256', $json ),
		);
		update_option( self::OPTION, $state, false );
		delete_option( self::INVALIDATED_OPTION );
		return $state;
	}

	public static function verify() {
		$state = get_option( self::OPTION, array() );
		if ( ! is_array( $state ) || empty( $state['payload'] ) || empty( $state['freeze_sha256'] ) ) {
			return array( 'pass'=>false, 'code'=>'not_frozen' );
		}
		$current = alipasandi_canonicalize_snapshot( array( 'context'=>self::current_context(), 'data'=>self::current_data() ) );
		$invalidated = get_option( self::INVALIDATED_OPTION, array() );
		$pass = hash_equals( hash( 'sha256', wp_json_encode( $current, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ), (string) $state['freeze_sha256'] ) && empty( $invalidated );
		return array(
			'pass'          => $pass,
			'code'          => $pass ? 'freeze_intact' : 'freeze_invalidated',
			'freeze_sha256' => $state['freeze_sha256'],
			'frozen_at_utc' => isset( $state['frozen_at_utc'] ) ? $state['frozen_at_utc'] : '',
			'approved_by'   => isset( $state['approved_by'] ) ? $state['approved_by'] : '',
			'invalidation'  => $invalidated,
		);
	}
}

/** Invalidate a freeze immediately when a tracked option changes. */
function alipasandi_nap_freeze_invalidate( $old_value, $value, $option ) {
	if ( $old_value === $value || false === get_option( Alipasandi_NAP_Freeze::OPTION, false ) ) {
		return;
	}
	update_option( Alipasandi_NAP_Freeze::INVALIDATED_OPTION, array( 'invalidated_at_utc'=>gmdate( 'c' ), 'option'=>sanitize_key( $option ) ), false );
}

/** Invalidate when a previously absent tracked option is added or deleted. */
function alipasandi_nap_freeze_invalidate_added( $option, $value ) {
	if ( false !== get_option( Alipasandi_NAP_Freeze::OPTION, false ) ) {
		update_option( Alipasandi_NAP_Freeze::INVALIDATED_OPTION, array( 'invalidated_at_utc'=>gmdate( 'c' ), 'option'=>sanitize_key( $option ), 'operation'=>'add' ), false );
	}
}

function alipasandi_nap_freeze_invalidate_deleted( $option ) {
	if ( false !== get_option( Alipasandi_NAP_Freeze::OPTION, false ) ) {
		update_option( Alipasandi_NAP_Freeze::INVALIDATED_OPTION, array( 'invalidated_at_utc'=>gmdate( 'c' ), 'option'=>sanitize_key( $option ), 'operation'=>'delete' ), false );
	}
}
foreach ( Alipasandi_NAP_Freeze::tracked_option_names() as $alipasandi_tracked_option ) {
	add_action( 'update_option_' . $alipasandi_tracked_option, 'alipasandi_nap_freeze_invalidate', 10, 3 );
	add_action( 'add_option_' . $alipasandi_tracked_option, 'alipasandi_nap_freeze_invalidate_added', 10, 2 );
	add_action( 'delete_option_' . $alipasandi_tracked_option, 'alipasandi_nap_freeze_invalidate_deleted', 10, 1 );
}
unset( $alipasandi_tracked_option );

/** CLI lifecycle for an evidence-producing freeze. */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'alipasandi nap freeze', function ( $args, $assoc_args ) {
		$owner = isset( $assoc_args['owner'] ) ? $assoc_args['owner'] : '';
		if ( ! empty( $assoc_args['theme-sha256'] ) ) { update_option( 'alipasandi_release_theme_sha256', strtolower( $assoc_args['theme-sha256'] ), false ); }
		if ( ! empty( $assoc_args['plugin-sha256'] ) ) { update_option( 'alipasandi_release_plugin_sha256', strtolower( $assoc_args['plugin-sha256'] ), false ); }
		$state = Alipasandi_NAP_Freeze::freeze( $owner, isset( $assoc_args['note'] ) ? $assoc_args['note'] : '' );
		if ( is_wp_error( $state ) ) { WP_CLI::error( $state->get_error_message() ); }
		WP_CLI::line( wp_json_encode( $state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
	} );
	WP_CLI::add_command( 'alipasandi nap freeze-status', function () {
		$status = Alipasandi_NAP_Freeze::verify();
		WP_CLI::line( wp_json_encode( $status, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
		if ( empty( $status['pass'] ) ) { WP_CLI::halt( 1 ); }
	} );
}
