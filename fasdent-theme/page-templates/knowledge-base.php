<?php
/**
 * Template Name: مرکز آموزش
 * @package Fasdent
 */
get_header(); ?>
<section class="section">
  <div class="container">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <h1><?php the_title(); ?></h1>
    <?php the_content(); endwhile; endif; ?>

    <!-- جستجو -->
    <div class="kb-search" style="max-width:500px;margin:2rem auto;">
      <?php get_search_form(); ?>
    </div>

    <!-- دسته‌های FAQ -->
    <h2><?php esc_html_e( 'سوالات متداول بر اساس موضوع', 'fasdent' ); ?></h2>
    <?php
    $cats = get_terms( array( 'taxonomy' => 'service_category', 'hide_empty' => true, 'parent' => 0 ) );
    if ( $cats && ! is_wp_error( $cats ) ) :
      foreach ( $cats as $cat ) :
        $faqs = fasdent_category_faqs( $cat->slug );
        if ( ! $faqs ) continue;
    ?>
    <details class="kb-category card" style="margin-bottom:1rem;">
      <summary style="cursor:pointer;font-weight:700;padding:.75rem;">
        <i class="<?php echo esc_attr( fasdent_category_icon( $cat ) ); ?>" aria-hidden="true"></i>
        <?php echo esc_html( $cat->name ); ?>
        <span style="font-size:.8rem;color:var(--color-muted);">(<?php echo count( $faqs ); ?> سوال)</span>
      </summary>
      <div class="faq-list">
        <?php foreach ( $faqs as $faq ) : ?>
        <div class="faq-item">
          <button type="button" aria-expanded="false"><?php echo esc_html( $faq['question'] ?? '' ); ?></button>
          <div class="faq-answer"><?php echo wp_kses_post( $faq['answer'] ?? '' ); ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </details>
    <?php endforeach; endif; ?>

    <!-- سوالات متداول عمومی -->
    <?php
    $general_faqs = get_posts( array( 'post_type' => 'faq', 'numberposts' => 30, 'post_status' => 'publish' ) );
    if ( $general_faqs ) : ?>
    <h2 style="margin-top:2rem;"><?php esc_html_e( 'سوالات متداول عمومی', 'fasdent' ); ?></h2>
    <div class="faq-list card">
      <?php foreach ( $general_faqs as $faq ) : ?>
      <div class="faq-item">
        <button type="button" aria-expanded="false"><?php echo esc_html( $faq->post_title ); ?></button>
        <div class="faq-answer"><?php echo wp_kses_post( $faq->post_content ); ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php get_footer(); ?>