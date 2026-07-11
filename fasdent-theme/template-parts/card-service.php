<?php
/**
 * Template part: Service Card
 * @package Fasdent
 */
$icon  = fasdent_field( 'service_icon' ) ?: 'fa-solid fa-tooth';
$price = fasdent_field( 'service_price' );
$dur   = fasdent_field( 'service_duration' );
?>
<article class="service-card card">
  <?php if ( has_post_thumbnail() ) : ?>
  <a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
    <?php the_post_thumbnail( 'fasdent-card', array( 'loading' => 'lazy' ) ); ?>
  </a>
  <?php endif; ?>
  <div class="service-card__body">
    <h3>
      <i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>
    <?php the_excerpt(); ?>
    <div class="service-card__meta">
      <?php if ( $price ) : ?><span class="service-price"><i class="fa-solid fa-tag" aria-hidden="true"></i> <?php echo esc_html( $price ); ?></span><?php endif; ?>
      <?php if ( $dur ) : ?><span class="service-duration"><i class="fa-solid fa-clock" aria-hidden="true"></i> <?php echo esc_html( $dur ); ?></span><?php endif; ?>
    </div>
    <a href="<?php the_permalink(); ?>" class="btn btn-primary" style="margin-top:.75rem;"><?php esc_html_e( 'مشاهده', 'fasdent' ); ?></a>
  </div>
</article>