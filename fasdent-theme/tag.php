<?php
/**
 * آرشیو برچسب — Tag Hub — Fasdent
 * noindex از inc/seo.php مدیریت می‌شود.
 * @package Fasdent
 */
get_header();
$tag = get_queried_object();
?>
<section class="section">
  <div class="container">
    <header class="archive-header">
      <h1 class="archive-title">
        <i class="fa-solid fa-tag" aria-hidden="true"></i>
        <?php esc_html_e( 'برچسب:', 'fasdent' ); ?> <span><?php echo esc_html( $tag->name ); ?></span>
      </h1>
      <?php if ( $tag->description ) : ?><p class="archive-description"><?php echo wp_kses_post( $tag->description ); ?></p><?php endif; ?>
      <p class="archive-count"><?php printf( esc_html( '%s مطلب با این برچسب' ), number_format_i18n( $tag->count ) ); ?></p>
    </header>

    <?php if ( have_posts() ) : ?>
    <div class="grid-3">
      <?php while ( have_posts() ) : the_post(); ?>
      <article class="card post-card">
        <?php if ( has_post_thumbnail() ) : ?><a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'fasdent-card', array( 'loading' => 'lazy' ) ); ?></a><?php endif; ?>
        <div class="post-card__body">
          <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <?php the_excerpt(); ?>
        </div>
      </article>
      <?php endwhile; ?>
    </div>
    <?php the_posts_pagination( array(
      'prev_text' => '<i class="fa-solid fa-angle-right" aria-hidden="true"></i> قبلی',
      'next_text' => 'بعدی <i class="fa-solid fa-angle-left" aria-hidden="true"></i>',
    ) ); ?>
    <?php else : ?>
    <p class="no-results"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> <?php esc_html_e( 'هیچ مطلبی با این برچسب یافت نشد.', 'fasdent' ); ?></p>
    <?php endif; ?>
  </div>
</section>
<?php get_footer(); ?>