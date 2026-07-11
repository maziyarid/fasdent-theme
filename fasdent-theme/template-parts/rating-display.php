<?php
/**
 * Template part: Star Rating Display
 * $args['rating'] — float 0-5
 * $args['count']  — int (optional)
 * @package Fasdent
 */
$rating = (float)( $args['rating'] ?? 5 );
$count  = isset( $args['count'] ) ? (int) $args['count'] : 0;
?>
<div class="star-rating" aria-label="امتیاز <?php echo esc_attr( round( $rating, 1 ) ); ?> از ۵" role="img">
  <?php for ( $s = 1; $s <= 5; $s++ ) :
    if ( $s <= floor( $rating ) ) { $class = 'fa-solid fa-star'; }
    elseif ( $s - $rating < 1 && $s - $rating > 0 ) { $class = 'fa-solid fa-star-half-stroke'; }
    else { $class = 'fa-regular fa-star'; }
  ?>
  <i class="<?php echo esc_attr( $class ); ?>" aria-hidden="true"></i>
  <?php endfor; ?>
  <?php if ( $count ) : ?>
  <span class="rating-count">(<?php echo esc_html( number_format_i18n( $count ) ); ?>)</span>
  <?php endif; ?>
</div>