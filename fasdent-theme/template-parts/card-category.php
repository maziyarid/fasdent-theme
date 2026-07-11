<?php
/**
 * Template part: Category Card
 * @package Fasdent
 */
$icon = isset( $term ) ? fasdent_category_icon( $term ) : 'fa-solid fa-tooth';
?>
<article class="category-card card">
  <div class="category-card__icon" aria-hidden="true">
    <i class="<?php echo esc_attr( $icon ); ?>"></i>
  </div>
  <div class="category-card__body">
    <h3><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></h3>
    <p><?php echo esc_html( $term->description ?: 'خدمات تخصصی در این دسته' ); ?></p>
    <?php if ( $term->count ) : ?>
    <span class="category-count"><?php echo esc_html( $term->count ); ?> <?php esc_html_e( 'خدمت', 'fasdent' ); ?></span>
    <?php endif; ?>
  </div>
</article>