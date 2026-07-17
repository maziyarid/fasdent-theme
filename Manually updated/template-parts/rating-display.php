<?php
/**
 * Template part: Star rating display.
 *
 * @package Fasdent
 * @var array $args Template arguments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rating = isset( $args['rating'] ) ? (float) $args['rating'] : 5.0;
$count  = isset( $args['count'] ) ? absint( $args['count'] ) : 0;
$rating = max( 0, min( 5, $rating ) );
$label  = sprintf( __( 'امتیاز %1$s از ۵ بر اساس %2$s رأی', 'fasdent' ), number_format_i18n( $rating, 1 ), number_format_i18n( $count ) );
?>
<div class="star-rating" aria-label="<?php echo esc_attr( $label ); ?>">
	<span class="star-rating__icons" aria-hidden="true">
		<?php for ( $star = 1; $star <= 5; $star++ ) :
			if ( $star <= floor( $rating ) ) {
				$icon_class = 'fa-solid fa-star';
			} elseif ( $star - $rating > 0 && $star - $rating < 1 ) {
				$icon_class = 'fa-solid fa-star-half-stroke';
			} else {
				$icon_class = 'fa-regular fa-star';
			}
		?>
			<i class="<?php echo esc_attr( $icon_class ); ?>"></i>
		<?php endfor; ?>
	</span>
	<span class="star-rating__value"><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></span>
	<?php if ( $count ) : ?><span class="rating-count"><?php echo esc_html( sprintf( __( '(%s رأی)', 'fasdent' ), number_format_i18n( $count ) ) ); ?></span><?php endif; ?>
</div>
