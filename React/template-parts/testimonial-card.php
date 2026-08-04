<?php
/**
 * Template part: Testimonial Card
 * @package Fasdent
 */
$rating     = (float)( fasdent_field( 'rating' ) ?: 5 );
$service_id = fasdent_field( 'related_service' );
?>
<article class="testimonial-card card">
  <div class="star-rating" aria-label="امتیاز <?php echo esc_attr( round( $rating, 1 ) ); ?> از ۵" role="img">
    <?php for ( $s = 1; $s <= 5; $s++ ) :
      $cls = $s <= floor( $rating ) ? 'fa-solid fa-star' : ( ( $s - $rating < 1 && $s - $rating > 0 ) ? 'fa-solid fa-star-half-stroke' : 'fa-regular fa-star' );
    ?>
    <i class="<?php echo esc_attr( $cls ); ?>" aria-hidden="true"></i>
    <?php endfor; ?>
  </div>
  <blockquote class="testimonial-text">
    <p>"<?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?>"</p>
  </blockquote>
  <footer class="testimonial-footer">
    <strong class="testimonial-author"><?php the_title(); ?></strong>
    <?php if ( $service_id ) : ?>
    <span class="testimonial-service"> — <a href="<?php echo esc_url( get_permalink( $service_id ) ); ?>"><?php echo esc_html( get_the_title( $service_id ) ); ?></a></span>
    <?php endif; ?>
  </footer>
</article>