<?php
/**
 * Responsive image helpers for the bundled, client-approved theme imagery.
 *
 * @package Alipasandi_Clinic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'alipasandi_log' ) ) {
	function alipasandi_log( $message, $context = array() ) {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}
		$details = is_array( $context ) && $context ? ' ' . wp_json_encode( $context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : '';
		error_log( '[Alipasandi] ' . sanitize_text_field( $message ) . $details ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}

/**
 * Render a bundled image with explicit dimensions and available WebP sources.
 *
 * @param string $filename Image filename inside assets/images.
 * @param string $alt      Alternative text. Use an empty string for decoration.
 * @param array  $args     Optional class, sizes, loading, fetchpriority and decorative values.
 */
function alipasandi_theme_image( $filename, $alt = '', $args = array() ) {
	$defaults = array(
		'class'         => '',
		'sizes'         => '100vw',
		'loading'       => 'lazy',
		'fetchpriority' => '',
		'decorative'    => false,
	);
	$args     = wp_parse_args( $args, $defaults );
	$filename = wp_basename( $filename );
	$path     = get_template_directory() . '/assets/images/' . $filename;

	if ( ! file_exists( $path ) ) {
		alipasandi_log( 'Theme image unavailable', array( 'filename' => $filename, 'reason' => 'file_missing' ) );
		return;
	}

	$size = @getimagesize( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( ! $size ) {
		alipasandi_log( 'Theme image unavailable', array( 'filename' => $filename, 'reason' => 'invalid_image' ) );
		return;
	}

	$width     = (int) $size[0];
	$height    = (int) $size[1];
	$extension = pathinfo( $filename, PATHINFO_EXTENSION );
	$basename  = pathinfo( $filename, PATHINFO_FILENAME );
	$base_uri  = get_template_directory_uri() . '/assets/images/';
	$srcset    = array();

	foreach ( array( 480, 768, 900, 920, 960, 1050, 1150, 1320 ) as $candidate_width ) {
		$candidate = $basename . '-' . $candidate_width . '.webp';
		if ( file_exists( get_template_directory() . '/assets/images/' . $candidate ) ) {
			$srcset[] = esc_url( $base_uri . $candidate ) . ' ' . $candidate_width . 'w';
		}
	}

	$alt = $args['decorative'] ? '' : $alt;
	?>
	<picture>
		<?php if ( $srcset ) : ?>
			<source type="image/webp" srcset="<?php echo esc_attr( implode( ', ', $srcset ) ); ?>" sizes="<?php echo esc_attr( $args['sizes'] ); ?>">
		<?php endif; ?>
		<img src="<?php echo esc_url( $base_uri . $basename . '.' . $extension ); ?>" alt="<?php echo esc_attr( $alt ); ?>" width="<?php echo esc_attr( $width ); ?>" height="<?php echo esc_attr( $height ); ?>" sizes="<?php echo esc_attr( $args['sizes'] ); ?>" decoding="async"<?php echo $args['class'] ? ' class="' . esc_attr( $args['class'] ) . '"' : ''; ?><?php echo $args['loading'] ? ' loading="' . esc_attr( $args['loading'] ) . '"' : ''; ?><?php echo $args['fetchpriority'] ? ' fetchpriority="' . esc_attr( $args['fetchpriority'] ) . '"' : ''; ?>>
	</picture>
	<?php
}
