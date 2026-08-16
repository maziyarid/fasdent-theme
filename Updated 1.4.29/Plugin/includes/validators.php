<?php
/**
 * Canonical operational validators.
 *
 * Booking ranges use end-exclusive semantics: 09:00-17:00 permits 09:00 but
 * rejects 17:00. All date calculations use the WordPress timezone.
 *
 * @package Alipasandi_Service_Content
 * @since 1.3.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Strict E.164 validation. */
function alipasandi_valid_e164( $value ) {
	$value = function_exists( 'alipasandi_normalize_e164_value' ) ? alipasandi_normalize_e164_value( $value ) : alipasandi_normalize_digits( $value );
	return 1 === preg_match( '/^\+[1-9][0-9]{7,14}$/', (string) $value );
}

/** ISO 3166-1 alpha-2 shape validation. */
function alipasandi_valid_country_code( $value ) {
	return 1 === preg_match( '/^[A-Za-z]{2}$/', trim( (string) $value ) );
}

/** Latitude/longitude boundary validation. */
function alipasandi_valid_geo( $value, $axis ) {
	if ( ! is_numeric( $value ) ) {
		return false;
	}
	$value = (float) $value;
	return 'lat' === $axis ? $value >= -90 && $value <= 90 : ( 'lng' === $axis && $value >= -180 && $value <= 180 );
}

/** Seven canonical day codes. */
function alipasandi_day_codes() {
	return array( 'MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU' );
}

/** Convert a WordPress DateTime weekday to the canonical code. */
function alipasandi_datetime_day_code( DateTimeInterface $date ) {
	$map = array( 'Mon'=>'MO', 'Tue'=>'TU', 'Wed'=>'WE', 'Thu'=>'TH', 'Fri'=>'FR', 'Sat'=>'SA', 'Sun'=>'SU' );
	$key = $date->format( 'D' );
	return isset( $map[ $key ] ) ? $map[ $key ] : '';
}

/** Validate strict 24-hour HH:MM and return minutes from midnight. */
function alipasandi_time_minutes( $value ) {
	$value = trim( alipasandi_normalize_digits( $value ) );
	if ( 1 !== preg_match( '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $value ) ) {
		return false;
	}
	list( $hour, $minute ) = array_map( 'intval', explode( ':', $value ) );
	return ( $hour * 60 ) + $minute;
}

/**
 * Parse booking times.
 *
 * Backward-compatible `HH:MM` lines apply to every open day. Optional
 * day-specific lines use `MO=09:00,10:00`; `FR=CLOSED` is allowed. This keeps
 * one canonical option while making closed-day and per-day behavior testable.
 */
function alipasandi_parse_booking_times( $raw ) {
	$by_day = array( '*'=>array() );
	foreach ( alipasandi_day_codes() as $day ) { $by_day[ $day ] = array(); }
	$errors = array();

	if ( is_array( $raw ) ) {
		if ( array_is_list( $raw ) ) {
			$lines = array();
			foreach ( $raw as $item ) {
				if ( is_array( $item ) && isset( $item['time'] ) ) {
					$day = isset( $item['day'] ) ? strtoupper( sanitize_key( $item['day'] ) ) : '*';
					$lines[] = ( '*' === $day ? '' : $day . '=' ) . $item['time'];
				} elseif ( is_scalar( $item ) ) {
					$lines[] = (string) $item;
				}
			}
		} else {
			$lines = array();
			foreach ( $raw as $day => $times ) {
				$lines[] = strtoupper( (string) $day ) . '=' . implode( ',', (array) $times );
			}
		}
	} else {
		$lines = preg_split( '/\r\n|\r|\n/', trim( (string) $raw ) );
	}

	$seen_day_lines = array();
	foreach ( (array) $lines as $line_number => $line ) {
		$line = trim( alipasandi_normalize_digits( $line ) );
		if ( '' === $line ) { continue; }
		$day = '*';
		$times_raw = $line;
		if ( false !== strpos( $line, '=' ) ) {
			list( $day, $times_raw ) = array_map( 'trim', explode( '=', $line, 2 ) );
			$day = strtoupper( $day );
			if ( ! in_array( $day, alipasandi_day_codes(), true ) ) {
				$errors[] = 'invalid_booking_day:' . ( $line_number + 1 );
				continue;
			}
			if ( isset( $seen_day_lines[ $day ] ) ) {
				$errors[] = 'duplicate_booking_day:' . $day;
				continue;
			}
			$seen_day_lines[ $day ] = true;
		}
		if ( 'CLOSED' === strtoupper( $times_raw ) ) {
			$by_day[ $day ] = array();
			continue;
		}
		$times = '*' === $day ? array( $times_raw ) : preg_split( '/\s*,\s*/', $times_raw );
		foreach ( $times as $time ) {
			$time = trim( $time );
			if ( false === alipasandi_time_minutes( $time ) ) {
				$errors[] = 'invalid_booking_time:' . ( $line_number + 1 );
				continue;
			}
			if ( in_array( $time, $by_day[ $day ], true ) ) {
				$errors[] = 'duplicate_booking_time:' . $day . ':' . $time;
				continue;
			}
			$by_day[ $day ][] = $time;
		}
	}
	foreach ( $by_day as &$times ) {
		usort( $times, static function ( $a, $b ) { return alipasandi_time_minutes( $a ) <=> alipasandi_time_minutes( $b ); } );
	}
	unset( $times );
	$flat = array();
	foreach ( $by_day as $times ) { $flat = array_merge( $flat, $times ); }
	$flat = array_values( array_unique( $flat ) );
	usort( $flat, static function ( $a, $b ) { return alipasandi_time_minutes( $a ) <=> alipasandi_time_minutes( $b ); } );
	if ( count( $flat ) > 48 ) { $errors[] = 'too_many_booking_times'; }
	return array( 'errors'=>array_values( array_unique( $errors ) ), 'by_day'=>$by_day, 'specified_days'=>array_keys( $seen_day_lines ), 'times'=>$flat );
}

/** Parse the canonical weekly opening-hours grammar. */
function alipasandi_parse_opening_hours( $raw ) {
	$schedule = array();
	foreach ( alipasandi_day_codes() as $day ) { $schedule[ $day ] = array(); }
	$errors = array();
	$specs = array();
	if ( is_array( $raw ) ) {
		$lines = array();
		foreach ( $raw as $day => $ranges ) {
			if ( is_scalar( $ranges ) && '' !== trim( (string) $ranges ) ) { $lines[] = strtoupper( (string) $day ) . '=' . trim( (string) $ranges ); continue; }
			if ( ! is_array( $ranges ) || empty( $ranges ) ) { $lines[] = strtoupper( (string) $day ) . '=CLOSED'; continue; }
			$parts = array();
			foreach ( $ranges as $range ) {
				if ( is_array( $range ) && isset( $range['open'], $range['close'] ) ) { $parts[] = $range['open'] . '-' . $range['close']; }
			}
			$lines[] = strtoupper( (string) $day ) . '=' . implode( ',', $parts );
		}
	} else {
		$lines = preg_split( '/\r\n|\r|\n/', trim( (string) $raw ) );
	}
	$seen = array();
	$schema_days = array( 'MO'=>'Monday', 'TU'=>'Tuesday', 'WE'=>'Wednesday', 'TH'=>'Thursday', 'FR'=>'Friday', 'SA'=>'Saturday', 'SU'=>'Sunday' );
	foreach ( (array) $lines as $line_number => $line ) {
		$line = trim( alipasandi_normalize_digits( $line ) );
		if ( '' === $line ) { continue; }
		if ( 1 !== preg_match( '/^([A-Za-z]{2})=(.+)$/', $line, $match ) ) {
			$errors[] = 'invalid_opening_line:' . ( $line_number + 1 );
			continue;
		}
		$day = strtoupper( $match[1] );
		if ( ! in_array( $day, alipasandi_day_codes(), true ) ) { $errors[] = 'invalid_opening_day:' . ( $line_number + 1 ); continue; }
		if ( isset( $seen[ $day ] ) ) { $errors[] = 'duplicate_opening_day:' . $day; continue; }
		$seen[ $day ] = true;
		if ( 'CLOSED' === strtoupper( trim( $match[2] ) ) ) { $schedule[ $day ] = array(); continue; }
		$ranges = preg_split( '/\s*,\s*/', trim( $match[2] ) );
		foreach ( $ranges as $range_index => $range ) {
			if ( 1 !== preg_match( '/^([^\s-]+)-([^\s-]+)$/', $range, $time_match ) ) {
				$errors[] = 'invalid_opening_range:' . $day . ':' . $range_index;
				continue;
			}
			$open = trim( $time_match[1] );
			$close = trim( $time_match[2] );
			$start = alipasandi_time_minutes( $open );
			$end = alipasandi_time_minutes( $close );
			if ( false === $start || false === $end || $end <= $start ) {
				$errors[] = 'invalid_opening_boundary:' . $day . ':' . $range_index;
				continue;
			}
			$schedule[ $day ][] = array( 'open'=>$open, 'close'=>$close, 'start'=>$start, 'end'=>$end );
		}
		usort( $schedule[ $day ], static function ( $a, $b ) { return $a['start'] <=> $b['start']; } );
		$previous_end = -1;
		foreach ( $schedule[ $day ] as $range_index => $range ) {
			if ( $range['start'] < $previous_end ) { $errors[] = 'overlapping_opening_range:' . $day . ':' . $range_index; }
			$previous_end = max( $previous_end, $range['end'] );
			$specs[] = array( '@type'=>'OpeningHoursSpecification', 'dayOfWeek'=>'https://schema.org/' . $schema_days[ $day ], 'opens'=>$range['open'], 'closes'=>$range['close'] );
		}
	}
	return array( 'errors'=>array_values( array_unique( $errors ) ), 'schedule'=>$schedule, 'specs'=>$specs );
}

/** Filtered 1..3650-day horizon; default 365 pending owner approval. */
function alipasandi_booking_horizon_days() {
	$value = absint( get_option( 'alipasandi_booking_horizon_days', 365 ) );
	$value = $value > 0 ? $value : 365;
	return min( 3650, max( 1, (int) apply_filters( 'alipasandi_booking_horizon_days', $value ) ) );
}

/** Require explicit owner-approved booking policy before enabling the form. */
function alipasandi_booking_policy_configuration() {
	$horizon_raw = alipasandi_clinic_option( 'booking_horizon_days' );
	$lead_raw = alipasandi_clinic_option( 'booking_lead_minutes' );
	$errors = array();
	if ( '' === trim( (string) $horizon_raw ) || ! ctype_digit( (string) $horizon_raw ) || (int) $horizon_raw < 1 || (int) $horizon_raw > 3650 ) {
		$errors[] = 'booking_horizon_missing_or_invalid';
	}
	if ( '' === trim( (string) $lead_raw ) || ! ctype_digit( (string) $lead_raw ) || (int) $lead_raw < 0 || (int) $lead_raw > 43200 ) {
		$errors[] = 'booking_lead_missing_or_invalid';
	}
	return array( 'pass'=>empty( $errors ), 'errors'=>$errors, 'horizon_days'=>alipasandi_booking_horizon_days(), 'lead_minutes'=>alipasandi_booking_lead_minutes() );
}

/** Filtered booking lead time; default 0 pending owner approval. */
function alipasandi_booking_lead_minutes() {
	$value = absint( get_option( 'alipasandi_booking_lead_minutes', 0 ) );
	return min( 43200, max( 0, (int) apply_filters( 'alipasandi_booking_lead_minutes', $value ) ) );
}

/** Return the canonical, validated frontend time allowlist. */
function alipasandi_allowed_times() {
	$parsed = alipasandi_parse_booking_times( alipasandi_clinic_option( 'clinic_booking_times' ) );
	return empty( $parsed['errors'] ) ? $parsed['times'] : array();
}

/** Resolve applicable global + per-day slots. */
function alipasandi_slots_for_day( $booking, $day ) {
	$specified = isset( $booking['specified_days'] ) && is_array( $booking['specified_days'] ) ? $booking['specified_days'] : array();
	$times = in_array( $day, $specified, true )
		? ( isset( $booking['by_day'][ $day ] ) ? $booking['by_day'][ $day ] : array() )
		: ( isset( $booking['by_day']['*'] ) ? $booking['by_day']['*'] : array() );
	$times = array_values( array_unique( $times ) );
	usort( $times, static function ( $a, $b ) { return alipasandi_time_minutes( $a ) <=> alipasandi_time_minutes( $b ); } );
	return $times;
}

/** End-exclusive slot/range comparison. */
function alipasandi_slot_within_schedule( $day, $time, $schedule ) {
	$minute = alipasandi_time_minutes( $time );
	if ( false === $minute || empty( $schedule[ $day ] ) ) { return false; }
	foreach ( $schedule[ $day ] as $range ) {
		if ( $minute >= $range['start'] && $minute < $range['end'] ) { return true; }
	}
	return false;
}

/** Single weekly cross-validator consumed by both Forms and Health. */
function alipasandi_validate_booking_hours_interoperability( $booking_raw = null, $hours_raw = null ) {
	if ( null === $booking_raw ) { $booking_raw = alipasandi_clinic_option( 'clinic_booking_times' ); }
	if ( null === $hours_raw ) { $hours_raw = alipasandi_clinic_option( 'clinic_opening_hours' ); }
	$booking = alipasandi_parse_booking_times( $booking_raw );
	$hours = alipasandi_parse_opening_hours( $hours_raw );
	$errors = array_merge( $booking['errors'], $hours['errors'] );
	if ( ! empty( $errors ) ) { return array( 'pass'=>false, 'status'=>'critical', 'code'=>'malformed_schedule', 'errors'=>$errors, 'usable'=>array() ); }
	if ( empty( $booking['times'] ) ) { return array( 'pass'=>false, 'status'=>'critical', 'code'=>'no_booking_slots', 'usable'=>array() ); }
	$has_range = false;
	foreach ( $hours['schedule'] as $ranges ) { if ( ! empty( $ranges ) ) { $has_range = true; break; } }
	if ( ! $has_range ) { return array( 'pass'=>false, 'status'=>'critical', 'code'=>'no_opening_hours', 'usable'=>array() ); }
	$usable = array();
	$open_days_without_slots = array();
	foreach ( alipasandi_day_codes() as $day ) {
		if ( empty( $hours['schedule'][ $day ] ) ) { continue; }
		$day_usable = array();
		foreach ( alipasandi_slots_for_day( $booking, $day ) as $time ) {
			if ( alipasandi_slot_within_schedule( $day, $time, $hours['schedule'] ) ) { $day_usable[] = $time; }
		}
		if ( empty( $day_usable ) ) { $open_days_without_slots[] = $day; }
		$usable[ $day ] = $day_usable;
	}
	$total = 0;
	foreach ( $usable as $times ) { $total += count( $times ); }
	if ( 0 === $total ) { return array( 'pass'=>false, 'status'=>'critical', 'code'=>'zero_weekly_overlap', 'usable'=>$usable, 'open_days_without_slots'=>$open_days_without_slots ); }
	return array( 'pass'=>true, 'status'=>empty( $open_days_without_slots )?'pass':'warning', 'code'=>empty( $open_days_without_slots )?'booking_hours_aligned':'open_day_without_usable_slot', 'usable'=>$usable, 'usable_count'=>$total, 'open_days_without_slots'=>$open_days_without_slots );
}

/** Strict WordPress-timezone appointment date validation. */
function alipasandi_validate_appointment_date( $value ) {
	$value = trim( alipasandi_normalize_digits( $value ) );
	$timezone = wp_timezone();
	$date = DateTimeImmutable::createFromFormat( '!Y-m-d', $value, $timezone );
	$errors = DateTimeImmutable::getLastErrors();
	if ( false === $date || ( is_array( $errors ) && ( $errors['warning_count'] || $errors['error_count'] ) ) || $date->format( 'Y-m-d' ) !== $value ) {
		return new WP_Error( 'invalid_date', 'Invalid appointment date.' );
	}
	$today = current_datetime()->setTime( 0, 0, 0 );
	$max = $today->modify( '+' . alipasandi_booking_horizon_days() . ' days' );
	if ( $date < $today ) { return new WP_Error( 'date_in_past', 'Appointment date is in the past.' ); }
	if ( $date > $max ) { return new WP_Error( 'date_beyond_horizon', 'Appointment date is beyond the approved horizon.' ); }
	return $date;
}

/** Validate an exact appointment date/time against every operational rule. */
function alipasandi_validate_appointment_datetime( $date_value, $time_value ) {
	$date = alipasandi_validate_appointment_date( $date_value );
	if ( is_wp_error( $date ) ) { return $date; }
	$time_value = trim( alipasandi_normalize_digits( $time_value ) );
	if ( false === alipasandi_time_minutes( $time_value ) ) { return new WP_Error( 'invalid_time', 'Invalid appointment time.' ); }
	$booking = alipasandi_parse_booking_times( alipasandi_clinic_option( 'clinic_booking_times' ) );
	$hours = alipasandi_parse_opening_hours( alipasandi_clinic_option( 'clinic_opening_hours' ) );
	if ( ! empty( $booking['errors'] ) || ! empty( $hours['errors'] ) ) { return new WP_Error( 'malformed_schedule', 'Booking schedule is malformed.' ); }
	$day = alipasandi_datetime_day_code( $date );
	if ( ! in_array( $time_value, alipasandi_slots_for_day( $booking, $day ), true ) ) { return new WP_Error( 'time_not_configured', 'Time is not configured for this day.' ); }
	if ( ! alipasandi_slot_within_schedule( $day, $time_value, $hours['schedule'] ) ) { return new WP_Error( 'outside_opening_hours', 'Time is outside opening hours.' ); }
	$slot = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $date->format( 'Y-m-d' ) . ' ' . $time_value, wp_timezone() );
	$earliest = current_datetime()->modify( '+' . alipasandi_booking_lead_minutes() . ' minutes' );
	if ( false === $slot || $slot < $earliest ) { return new WP_Error( 'inside_lead_time', 'Time is no longer available within the approved lead time.' ); }
	return $slot;
}

/** Generate actual future date/slot availability under the same validator. */
function alipasandi_available_future_slots( $days_ahead = null ) {
	$interop = alipasandi_validate_booking_hours_interoperability();
	if ( empty( $interop['pass'] ) ) { return array( 'pass'=>false, 'code'=>$interop['code'], 'dates'=>array() ); }
	$days_ahead = null === $days_ahead ? alipasandi_booking_horizon_days() : min( alipasandi_booking_horizon_days(), max( 0, absint( $days_ahead ) ) );
	$booking = alipasandi_parse_booking_times( alipasandi_clinic_option( 'clinic_booking_times' ) );
	$hours = alipasandi_parse_opening_hours( alipasandi_clinic_option( 'clinic_opening_hours' ) );
	$today = current_datetime()->setTime( 0, 0, 0 );
	$earliest = current_datetime()->modify( '+' . alipasandi_booking_lead_minutes() . ' minutes' );
	$dates = array();
	for ( $offset = 0; $offset <= $days_ahead; $offset++ ) {
		$date = $today->modify( '+' . $offset . ' days' );
		$day = alipasandi_datetime_day_code( $date );
		$valid = array();
		foreach ( alipasandi_slots_for_day( $booking, $day ) as $time ) {
			if ( ! alipasandi_slot_within_schedule( $day, $time, $hours['schedule'] ) ) { continue; }
			$slot = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $date->format( 'Y-m-d' ) . ' ' . $time, wp_timezone() );
			if ( false !== $slot && $slot >= $earliest ) { $valid[] = $time; }
		}
		if ( ! empty( $valid ) ) { $dates[ $date->format( 'Y-m-d' ) ] = $valid; }
	}
	return array( 'pass'=>! empty( $dates ), 'code'=>empty( $dates )?'no_future_dates':'future_dates_available', 'timezone'=>wp_timezone_string(), 'lead_minutes'=>alipasandi_booking_lead_minutes(), 'horizon_days'=>alipasandi_booking_horizon_days(), 'dates'=>$dates );
}
