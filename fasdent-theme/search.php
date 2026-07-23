<?php
/**
 * Search Results — Fasdent
 * @package Fasdent
 */
get_header(); ?>
<section class="section">
<div class="container">
  <?php $term = get_search_query(); $count = $GLOBALS['wp_query']->found_posts; ?>
  <div class="archive-header">
    <h1><?php printf( 'نتایج جستجو: <span>%s</span>', esc_html( $term ) ); ?></h1>
    <?php if ( $count ) : ?>
      <p class="archive-count"><?php echo esc_html( $count ); ?> نتیجه یافت شد</p>
    <?php endif; ?>
  </div>

  <!-- جستجوی مجدد -->
  <div style="max-width:500px;margin-bottom:2rem;"><?php get_search_form(); ?></div>

  <?php if ( have_posts() ) : ?>
    <div class="search-results">
    <?php while ( have_posts() ) : the_post(); ?>
      <article class="card search-result-card" style="margin-bottom:1rem;">
        <div class="search-result-inner">
          <?php if ( has_post_thumbnail() ) : ?>
          <a href="<?php the_permalink(); ?>" class="search-thumb" tabindex="-1" aria-hidden="true">
            <?php the_post_thumbnail( 'thumbnail', [ 'loading' => 'lazy', 'decoding' => 'async' ] ); ?>
          </a>
          <?php endif; ?>
          <div class="search-result-body">
            <?php $type_label = [ 'post' => 'مقاله', 'service' => 'خدمت', 'doctor' => 'پزشک', 'faq' => 'سوال' ][ get_post_type() ] ?? ''; ?>
            <?php if ( $type_label ) : ?>
              <span class="post-cat-badge"><?php echo esc_html( $type_label ); ?></span>
            <?php endif; ?>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p class="search-excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
            <a href="<?php the_permalink(); ?>" class="search-read-more">بیشتر بخوانید <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
          </div>
        </div>
      </article>
    <?php endwhile; ?>
    </div>
    <div class="pagination"><?php the_posts_pagination( [ 'mid_size' => 2 ] ); ?></div>
  <?php else : ?>
    <div class="no-results card">
      <i class="fa-regular fa-face-sad-cry" style="font-size:2.5rem;color:var(--color-muted);" aria-hidden="true"></i>
      <p>موردی برای «<?php echo esc_html( $term ); ?>» پیدا نشد.</p>
      <p>پیشنهادها: کلمات کوتاه‌تر امتحان کنید، یا از لینک‌های زیر استفاده نمایید:</p>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap;justify-content:center;margin-top:1rem;">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-secondary">صفحه اصلی</a>
        <a href="<?php echo esc_url( home_url( '/services/' ) ); ?>" class="btn btn-secondary">خدمات</a>
        <a href="<?php echo esc_url( home_url( '/appointment/' ) ); ?>" class="btn">رزرو نوبت</a>
      </div>
    </div>
  <?php endif; ?>
</div>
</section>
<?php get_footer(); ?>