<?php
/**
 * Shared template rendering helpers.
 *
 * @package Alipasandi_Clinic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Echo an inline icon without repeating the output-escaping exception.
 *
 * @param string $name Icon key.
 * @param int    $size Pixel size.
 */
function alipasandi_the_icon( $name, $size = 24 ) {
	echo alipasandi_icon( $name, $size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Render a compact inner-page hero.
 *
 * The title, intro, and extra HTML arguments must already be escaped or
 * sanitized for direct HTML output. This helper does not escape them.
 *
 * @param string $tag         Wrapper tag: section or header.
 * @param string $eyebrow     Already-escaped eyebrow text.
 * @param string $title_html  Already-escaped title HTML.
 * @param string $intro_html  Optional already-escaped intro HTML.
 * @param string $extra_html  Optional already-safe HTML after the title.
 */
function alipasandi_inner_hero( $tag, $eyebrow, $title_html, $intro_html = '', $extra_html = '' ) {
	$tag = in_array( $tag, array( 'section', 'header' ), true ) ? $tag : 'section';
	echo '<' . $tag . ' class="inner-hero"><div class="narrow-container section-heading" style="padding-block:64px;position:relative"><span class="eyebrow">' . $eyebrow . '</span><h1>' . $title_html . '</h1>' . $extra_html . ( '' !== $intro_html ? '<p>' . $intro_html . '</p>' : '' ) . '</div></' . $tag . '>';
}

/**
 * Read the sanitized form status query argument.
 *
 * @return string
 */
function alipasandi_form_status() {
	return isset( $_GET['form_status'] ) ? sanitize_key( wp_unslash( $_GET['form_status'] ) ) : '';
}

/**
 * Echo a form status notice.
 *
 * Message arguments must already be safe HTML; this helper preserves the
 * existing notice attributes and does not escape message contents.
 *
 * @param string $status       Sanitized form status.
 * @param string $success_html Safe success message HTML.
 * @param string $rate_html    Safe rate-limit message HTML.
 * @param string $error_html   Safe error message HTML.
 */
function alipasandi_form_status_notice( $status, $success_html, $rate_html, $error_html ) {
	if ( 'success' === $status ) {
		echo '<div class="form-message success" role="status">' . $success_html . '</div>';
	} elseif ( 'rate_limited' === $status ) {
		echo '<div class="form-message error" role="alert" tabindex="-1" autofocus>' . $rate_html . '</div>';
	} elseif ( in_array( $status, array( 'error', 'invalid', 'mail_error' ), true ) ) {
		echo '<div class="form-message error" role="alert">' . $error_html . '</div>';
	}
}

/**
 * Echo a clinic phone link in one of the existing markup shapes.
 *
 * @param string $class      Optional class attribute.
 * @param int    $icon_size  Icon size.
 * @param string $label      Visible text for text and text-span modes.
 * @param string $label_mode phone, text-span, or text.
 */
function alipasandi_phone_link( $class = '', $icon_size = 18, $label = '', $label_mode = 'phone' ) {
	$class_attr = '' !== $class ? ' class="' . esc_attr( $class ) . '"' : '';
	echo '<a' . $class_attr . ' href="tel:' . esc_attr( alipasandi_phone_href() ) . '">';
	alipasandi_the_icon( 'phone', $icon_size );
	if ( 'phone' === $label_mode ) {
		echo '<span dir="ltr">' . esc_html( alipasandi_clinic_option( 'clinic_phone' ) ) . '</span>';
	} elseif ( 'text-span' === $label_mode ) {
		echo '<span>' . esc_html( $label ) . '</span>';
	} else {
		echo ' ' . esc_html( $label );
	}
	echo '</a>';
}

/**
 * Echo the shared trust-strip stats grid.
 *
 * Each stat is an array with number, label, note, and optional icon keys.
 * Text and icon names come from trusted template data and are escaped here
 * exactly as in the original call sites.
 *
 * @param array  $stats          Stats after the shared treatment-count item.
 * @param string $treatment_icon Optional treatment-count icon.
 */
function alipasandi_trust_strip( $stats, $treatment_icon = '' ) {
	echo '<section class="section-compact section-bone trust-strip"><div class="site-container stats-grid ' . ( alipasandi_show_treatment_count() ? 'stats-grid--4' : 'stats-grid--3' ) . '">';
	if ( alipasandi_show_treatment_count() ) {
		echo '<div class="stat-item">';
		if ( '' !== $treatment_icon ) {
			echo '<span class="stat-icon" aria-hidden="true">';
			alipasandi_the_icon( $treatment_icon, 24 );
			echo '</span>';
		}
		echo '<strong class="stat-number">+۱۰٬۰۰۰</strong><span class="stat-label">کیس درمانی</span><span class="stat-note">تجربه انجام درمان‌های دندانپزشکی</span></div>';
	}
	foreach ( $stats as $stat ) {
		echo '<div class="stat-item">';
		if ( ! empty( $stat['icon'] ) ) {
			echo '<span class="stat-icon" aria-hidden="true">';
			alipasandi_the_icon( $stat['icon'], 24 );
			echo '</span>';
		}
		echo '<strong class="stat-number">' . esc_html( $stat['number'] ) . '</strong><span class="stat-label">' . esc_html( $stat['label'] ) . '</span><span class="stat-note">' . esc_html( $stat['note'] ) . '</span></div>';
	}
	echo '</div></section>';
}

/**
 * Echo one accordion item while preserving each surface's attributes.
 *
 * Question and answer arguments must already be escaped or sanitized HTML.
 *
 * @param string $panel_id   Already-safe panel ID.
 * @param string $question   Already-safe question HTML.
 * @param string $answer     Already-safe answer HTML.
 * @param string $trigger_id Optional already-safe trigger ID.
 * @param bool   $region     Add the service-detail region attributes.
 */
function alipasandi_accordion_item( $panel_id, $question, $answer, $trigger_id = '', $region = false ) {
	$trigger_id_attr = '' !== $trigger_id ? ' id="' . esc_attr( $trigger_id ) . '"' : '';
	echo '<div class="accordion-item"><button' . $trigger_id_attr . ' class="accordion-trigger" type="button" aria-expanded="false" aria-controls="' . esc_attr( $panel_id ) . '" data-accordion-trigger><span>' . $question . '</span>';
	alipasandi_the_icon( 'chevron', 19 );
	echo '</button>';
	$region_attrs = $region ? ' role="region" aria-labelledby="' . esc_attr( $trigger_id ) . '"' : '';
	echo '<div class="accordion-panel" id="' . esc_attr( $panel_id ) . '"' . $region_attrs . ' hidden>' . $answer . '</div>';
	echo '</div>';
}

/**
 * Echo a shared accordion list.
 *
 * Items contain panel_id, question, answer, and optional trigger_id/region.
 *
 * @param array  $items Accordion item data.
 * @param string $extra_class Optional extra list class.
 */
function alipasandi_accordion_list( $items, $extra_class = '' ) {
	$class = 'accordion-list' . ( '' !== $extra_class ? ' ' . sanitize_html_class( $extra_class ) : '' );
	echo '<div class="' . esc_attr( $class ) . '">';
	foreach ( $items as $item ) {
		alipasandi_accordion_item(
			$item['panel_id'],
			$item['question'],
			$item['answer'],
			isset( $item['trigger_id'] ) ? $item['trigger_id'] : '',
			! empty( $item['region'] )
		);
	}
	echo '</div>';
}

/**
 * Return the shared contact-channel definitions.
 *
 * Surface-specific order remains explicit at each call site.
 *
 * @return array
 */
function alipasandi_contact_channels() {
	return array(
		'instagram' => array(
			'option' => 'clinic_instagram',
			'icon'   => 'instagram',
			'label'  => 'اینستاگرام',
		),
		'whatsapp'  => array(
			'option' => 'clinic_whatsapp',
			'icon'   => 'whatsapp',
			'label'  => 'واتساپ',
		),
		'telegram'  => array(
			'option' => 'clinic_telegram',
			'icon'   => 'telegram',
			'label'  => 'تلگرام',
		),
	);
}

/**
 * Echo one contact-channel anchor in the requested existing surface shape.
 *
 * @param string $key     Channel definition key.
 * @param string $surface social or float.
 */
function alipasandi_contact_channel( $key, $surface ) {
	$channels = alipasandi_contact_channels();
	if ( ! isset( $channels[ $key ] ) ) {
		return;
	}
	$channel = $channels[ $key ];
	$url     = esc_url( alipasandi_clinic_option( $channel['option'] ) );
	if ( 'social' === $surface ) {
		echo '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $channel['label'] ) . '">';
		alipasandi_the_icon( $channel['icon'], 18 );
		echo '</a>';
		return;
	}
	echo '<a class="contact-option ' . esc_attr( $key ) . '" href="' . $url . '" target="_blank" rel="noopener noreferrer">';
	alipasandi_the_icon( $channel['icon'], 20 );
	echo ' ' . esc_html( $channel['label'] ) . '</a>';
}
