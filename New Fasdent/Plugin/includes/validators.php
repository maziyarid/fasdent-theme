<?php
/** Semantic validators for NAP, booking slots, opening hours and appointment dates. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! defined( 'ALIPASANDI_BOOKING_HORIZON_DAYS' ) ) {
	define( 'ALIPASANDI_BOOKING_HORIZON_DAYS', 365 );
}

/** Single source of truth for server validation and the HTML date max. */
function alipasandi_booking_horizon_days() {
	$days = (int) apply_filters( 'alipasandi_booking_horizon_days', ALIPASANDI_BOOKING_HORIZON_DAYS );
	return max( 1, min( 3650, $days ) );
}

if ( ! defined( 'ALIPASANDI_BOOKING_MIN_LEAD_MINUTES' ) ) {
	// Current versioned policy: same-day requests are allowed, but the selected
	// minute must be strictly in the future. A positive clinic buffer requires
	// Owner approval and a versioned behavior change.
	define( 'ALIPASANDI_BOOKING_MIN_LEAD_MINUTES', 0 );
}

/** Versioned minimum lead-time policy shared by backend and appointment UI. */
function alipasandi_booking_min_lead_minutes() {
	$minutes = (int) apply_filters( 'alipasandi_booking_min_lead_minutes', ALIPASANDI_BOOKING_MIN_LEAD_MINUTES );
	return max( 0, min( 10080, $minutes ) );
}

/** WordPress-timezone clock seam; tests may inject a deterministic instant. */
function alipasandi_booking_now() {
	$now = current_datetime();
	$filtered = apply_filters( 'alipasandi_booking_now', $now );
	return $filtered instanceof DateTimeInterface ? DateTimeImmutable::createFromInterface( $filtered ) : $now;
}

function alipasandi_valid_e164( $value ) {
	return 1 === preg_match( '/^\+[1-9][0-9]{7,14}$/', trim( (string) $value ) );
}

/**
 * Patient callback phone: intentionally format-flexible for local/international
 * callers, but must contain 7..15 digits and no letters/other characters.
 */
function alipasandi_valid_patient_phone( $value ) {
	$value = trim( alipasandi_normalize_digits( (string) $value ) );
	if ( 1 !== preg_match( '/^[0-9+()\-\s]{7,25}$/', $value ) ) { return false; }
	$digits = preg_replace( '/\D+/', '', $value );
	return strlen( $digits ) >= 7 && strlen( $digits ) <= 15;
}

function alipasandi_valid_country_code( $value ) {
	return 1 === preg_match( '/^[A-Z]{2}$/', strtoupper( trim( (string) $value ) ) );
}

function alipasandi_valid_geo( $value, $kind ) {
	if ( '' === trim( (string) $value ) || ! is_numeric( $value ) ) { return false; }
	$n = (float) $value;
	return 'lat' === $kind ? ( $n >= -90 && $n <= 90 ) : ( $n >= -180 && $n <= 180 );
}

/** Return canonical HH:MM lines or WP_Error. */
function alipasandi_validate_booking_slots( $raw ) {
	$raw = alipasandi_normalize_digits( (string) $raw );
	$lines = preg_split( '/\r\n|\r|\n/', $raw );
	$times = array();
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' === $line ) { continue; }
		if ( ! preg_match( '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $line ) ) {
			return new WP_Error( 'invalid_booking_time', 'هر ساعت باید با فرمت HH:MM و ساعت 24 ساعته باشد.' );
		}
		$times[] = $line;
	}
	$times = array_values( array_unique( $times ) );
	sort( $times, SORT_STRING );
	if ( count( $times ) > 48 ) {
		return new WP_Error( 'too_many_booking_times', 'حداکثر 48 ساعت پیشنهادی مجاز است.' );
	}
	return implode( "\n", $times );
}

/**
 * Opening-hours grammar (one day per line):
 *   MO=09:00-13:00,14:00-18:00
 *   TU=09:00-17:00
 *   FR=CLOSED
 * Days: MO TU WE TH FR SA SU. Times use 24h HH:MM.
 * Timezone is the WordPress site timezone; Production contract = Asia/Tehran.
 */
function alipasandi_parse_opening_hours( $raw ) {
	$day_map = array(
		'MO' => 'https://schema.org/Monday',
		'TU' => 'https://schema.org/Tuesday',
		'WE' => 'https://schema.org/Wednesday',
		'TH' => 'https://schema.org/Thursday',
		'FR' => 'https://schema.org/Friday',
		'SA' => 'https://schema.org/Saturday',
		'SU' => 'https://schema.org/Sunday',
	);
	$raw = strtoupper( alipasandi_normalize_digits( trim( (string) $raw ) ) );
	if ( '' === $raw ) {
		return array( 'canonical'=>'', 'specs'=>array(), 'schedule'=>array(), 'errors'=>array() );
	}
	$seen = array();
	$canonical = array();
	$specs = array();
	$schedule = array();
	$errors = array();
	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line_no => $line ) {
		$line = trim( $line );
		if ( '' === $line ) { continue; }
		if ( ! preg_match( '/^(MO|TU|WE|TH|FR|SA|SU)\s*=\s*(.+)$/', $line, $m ) ) {
			$errors[] = sprintf( 'خط %d: فرمت روز معتبر نیست.', $line_no + 1 );
			continue;
		}
		$day = $m[1];
		if ( isset( $seen[ $day ] ) ) {
			$errors[] = sprintf( 'خط %d: روز %s تکراری است.', $line_no + 1, $day );
			continue;
		}
		$seen[ $day ] = true;
		$value = trim( $m[2] );
		if ( 'CLOSED' === $value ) {
			$canonical[] = $day . '=CLOSED';
			$schedule[ $day ] = array();
			continue;
		}
		$ranges = array();
		foreach ( explode( ',', $value ) as $range ) {
			$range = trim( $range );
			if ( ! preg_match( '/^((?:[01][0-9]|2[0-3]):[0-5][0-9])-((?:[01][0-9]|2[0-3]):[0-5][0-9])$/', $range, $tm ) ) {
				$errors[] = sprintf( 'خط %d: بازه %s معتبر نیست.', $line_no + 1, $range );
				continue 2;
			}
			if ( $tm[1] >= $tm[2] ) {
				$errors[] = sprintf( 'خط %d: ساعت پایان باید بعد از شروع باشد.', $line_no + 1 );
				continue 2;
			}
			$ranges[] = array( $tm[1], $tm[2] );
		}
		usort( $ranges, function ( $a, $b ) { return strcmp( $a[0], $b[0] ); } );
		for ( $i = 1; $i < count( $ranges ); $i++ ) {
			if ( $ranges[ $i ][0] < $ranges[ $i - 1 ][1] ) {
				$errors[] = sprintf( 'خط %d: بازه‌های ساعات کاری هم‌پوشانی دارند.', $line_no + 1 );
				continue 2;
			}
		}
		$schedule[ $day ] = $ranges;
		$parts = array();
		foreach ( $ranges as $range ) {
			$parts[] = $range[0] . '-' . $range[1];
			$specs[] = array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => $day_map[ $day ],
				'opens'      => $range[0],
				'closes'     => $range[1],
			);
		}
		$canonical[] = $day . '=' . implode( ',', $parts );
	}
	return array( 'canonical'=>implode( "\n", $canonical ), 'specs'=>$specs, 'schedule'=>$schedule, 'errors'=>$errors );
}

function alipasandi_validate_opening_hours( $raw ) {
	$parsed = alipasandi_parse_opening_hours( $raw );
	if ( ! empty( $parsed['errors'] ) ) {
		return new WP_Error( 'invalid_opening_hours', implode( ' ', $parsed['errors'] ) );
	}
	return $parsed['canonical'];
}

/** Calendar-valid, WordPress-timezone-aware appointment horizon from one SSOT. */
function alipasandi_validate_appointment_date( $date ) {
	$date = trim( alipasandi_normalize_digits( (string) $date ) );
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
		return new WP_Error( 'invalid_date_format', 'فرمت تاریخ معتبر نیست.' );
	}
	$tz = wp_timezone();
	$dt = DateTimeImmutable::createFromFormat( '!Y-m-d', $date, $tz );
	$errors = DateTimeImmutable::getLastErrors();
	if ( false === $dt || ( is_array( $errors ) && ( $errors['warning_count'] || $errors['error_count'] ) ) || $dt->format( 'Y-m-d' ) !== $date ) {
		return new WP_Error( 'invalid_calendar_date', 'تاریخ تقویمی معتبر نیست.' );
	}
	$today = alipasandi_booking_now()->setTime( 0, 0 );
	$horizon = alipasandi_booking_horizon_days();
	$max = $today->modify( '+' . $horizon . ' days' );
	if ( $dt < $today ) { return new WP_Error( 'past_date', 'تاریخ گذشته پذیرفته نمی‌شود.' ); }
	if ( $dt > $max ) { return new WP_Error( 'date_too_far', sprintf( 'تاریخ درخواستی بیش از %d روز آینده است.', $horizon ) ); }
	return $dt;
}

function alipasandi_weekday_code( DateTimeInterface $date ) {
	$map = array( 1=>'MO', 2=>'TU', 3=>'WE', 4=>'TH', 5=>'FR', 6=>'SA', 7=>'SU' );
	return $map[ (int) $date->format( 'N' ) ];
}

/**
 * Validate the proposed appointment date+time against the current WordPress
 * clock and the versioned lead-time policy. This closes the same-day past-time
 * gap without ever consulting the server/PHP timezone.
 */
function alipasandi_validate_appointment_datetime( DateTimeInterface $date, $time ) {
	$time = alipasandi_normalize_digits( trim( (string) $time ) );
	if ( ! preg_match( '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $time ) ) {
		return new WP_Error( 'invalid_appointment_time', 'ساعت پیشنهادی معتبر نیست.' );
	}
	$selected = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $date->format( 'Y-m-d' ) . ' ' . $time, wp_timezone() );
	if ( ! $selected ) {
		return new WP_Error( 'invalid_appointment_datetime', 'تاریخ و ساعت پیشنهادی معتبر نیست.' );
	}
	$minimum = alipasandi_booking_now()->modify( '+' . alipasandi_booking_min_lead_minutes() . ' minutes' );
	if ( $selected <= $minimum ) {
		return new WP_Error( 'appointment_not_in_future', 'زمان پیشنهادی باید بعد از زمان فعلی کلینیک باشد.' );
	}
	return $selected;
}

/** If official opening hours are configured, reject closed days/times. */
function alipasandi_appointment_within_opening_hours( DateTimeInterface $date, $time ) {
	$raw = (string) alipasandi_clinic_option( 'clinic_opening_hours' );
	if ( '' === trim( $raw ) ) {
		return true; // No official schedule configured: do not invent availability.
	}
	$parsed = alipasandi_parse_opening_hours( $raw );
	if ( ! empty( $parsed['errors'] ) ) { return false; }
	$day = alipasandi_weekday_code( $date );
	if ( ! array_key_exists( $day, $parsed['schedule'] ) || empty( $parsed['schedule'][ $day ] ) ) {
		return false;
	}
	$time = alipasandi_normalize_digits( (string) $time );
	foreach ( $parsed['schedule'][ $day ] as $range ) {
		if ( $time >= $range[0] && $time < $range[1] ) { return true; }
	}
	return false;
}
