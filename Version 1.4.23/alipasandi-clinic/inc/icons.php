<?php
/** Inline, dependency-free SVG icons. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a decorative SVG icon.
 *
 * @param string $name Icon key.
 * @param int    $size Pixel size.
 * @return string
 */
function alipasandi_icon( $name, $size = 24 ) {
	$size  = max( 12, min( 96, absint( $size ) ) );
	$attrs = 'width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"';
	$icons = array(
		'tooth'     => '<path d="M7 4.8C5.6 6.1 5 8 5.2 10.1c.2 2.4 1.4 3.5 1.8 5.7.5 2.8.8 5.2 2.2 5.2 1.2 0 1.8-2.1 2.2-4.4.2-1.1.4-1.8.6-1.8s.4.7.6 1.8c.4 2.3 1 4.4 2.2 4.4 1.4 0 1.7-2.4 2.2-5.2.4-2.2 1.6-3.3 1.8-5.7.2-2.1-.4-4-1.8-5.3-1.6-1.5-3.2-.8-5-.1-1.8-.7-3.4-1.4-5 .1Z" stroke="currentColor" stroke-width="1.45" stroke-linecap="round" stroke-linejoin="round"/>',
		'implant'   => '<path d="M9 4.2c0-1.2 1.3-2.2 3-2.2s3 1 3 2.2c0 1-.4 1.7-.8 2.5H9.8C9.4 5.9 9 5.2 9 4.2Z" stroke="currentColor" stroke-width="1.4"/><path d="M9 7h6l-.7 3.1H9.7L9 7Z" stroke="currentColor" stroke-width="1.4"/><path d="M10 10v9l2 3 2-3v-9M10 12l4 1.4M10 15l4 1.4M10 18l4 1.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
		'crown'     => '<path d="M6.3 7.5C6.6 4.4 8.6 3 12 3s5.4 1.4 5.7 4.5c.2 2.2-.7 3.6-1.3 5.2-.7 1.9-.5 4.6-1.8 6.2-.7.9-1.8.4-2.1-.7L12 16l-.5 2.2c-.3 1.1-1.4 1.6-2.1.7-1.3-1.6-1.1-4.3-1.8-6.2-.6-1.6-1.5-3-1.3-5.2Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M9.2 7.5c.8.5 1.7.8 2.8.8s2-.3 2.8-.8" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>',
		'surgery'   => '<path d="M6 14.5c1.3-2 3.4-3.2 6-3.2s4.7 1.2 6 3.2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M8.5 13.2V8.5a3.5 3.5 0 0 1 7 0v4.7M12 5V2.8M9.4 5.9 8 4.2m6.6 1.7L16 4.2M5 17h14M7 20h10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
		'general'   => '<circle cx="10" cy="9" r="5" stroke="currentColor" stroke-width="1.4"/><path d="m13.7 12.7 6.1 6.1M6.4 17.8c2.2-1.2 5-1.2 7.2 0" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
		'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M7 3v4m10-4v4M3 10h18M8 14h3v3H8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
		'phone'     => '<path d="M8.2 3.5 5.7 4.7c-1.1.5-1.6 1.8-1.2 2.9 2 5.5 6.4 9.9 11.9 11.9 1.1.4 2.4-.1 2.9-1.2l1.2-2.5-4.2-2-1.6 2c-2.7-1.2-5.3-3.8-6.5-6.5l2-1.6-2-4.2Z" stroke="currentColor" stroke-width="1.45" stroke-linecap="round" stroke-linejoin="round"/>',
		'location'  => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.5"/>',
		'check'     => '<path d="m5 12.5 4.2 4.2L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'arrow'     => '<path d="M19 12H5m6-6-6 6 6 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
		'menu'      => '<path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
		'close'     => '<path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
		'chevron'   => '<path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>',
		'info'      => '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.4"/><path d="M12 10v6m0-9.2v.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
		'shield'    => '<path d="M12 3 5.5 5.6v5.8c0 4.2 2.7 7.8 6.5 9.6 3.8-1.8 6.5-5.4 6.5-9.6V5.6L12 3Z" stroke="currentColor" stroke-width="1.4"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
		'clipboard' => '<rect x="5" y="4" width="14" height="17" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M9 4.5V3h6v1.5M8.5 10h7M8.5 14h7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
		'clock'     => '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.4"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
		'chat'      => '<path d="M20 11.5a8 8 0 0 1-8.5 8L4 21l1.5-4.7A8 8 0 1 1 20 11.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.5"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor"/>',
		'whatsapp'  => '<path d="M20.5 11.7a8.5 8.5 0 0 1-12.6 7.5L3.5 20.5l1.3-4.3a8.5 8.5 0 1 1 15.7-4.5Z" stroke="currentColor" stroke-width="1.4"/><path d="M8.5 7.8c.4 3.7 2.3 5.7 5.9 6.8l1.2-1.8-2.2-1-1 1c-1.2-.7-2.2-1.7-2.9-2.9l1-1-1-2.2-1 .6v.5Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/>',
		'telegram'  => '<path d="m3 11 17-7-4.5 16-4.1-5.2L8.5 18v-4.3L17 7.2 7.3 12.5 3 11Z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	return '<svg class="icon icon-' . esc_attr( $name ) . '" ' . $attrs . '>' . $icons[ $name ] . '</svg>';
}

