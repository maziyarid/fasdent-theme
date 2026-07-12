<?php
/**
 * صفحه ۴۰۴ — Hub پیدا نشد — Fasdent
 * @package Fasdent
 */
// صفحه 404 نباید توسط موتورهای جستجو ایندکس شود.
add_action( 'wp_head', static function (): void {
	echo '<meta name="robots" content="noindex, follow">' . "\n";
}, 1 );
get_header();
?>
<section class="section section-404">
  <div class="container">

    <!-- تصویر ۴۰۴ + پیام -->
    <div class="error-404-hero" style="text-align:center;padding:3rem 0 2rem;">
      <div class="error-404-number" aria-hidden="true" style="font-size:8rem;font-weight:900;color:var(--color-primary);line-height:1;opacity:.12;">۴۰۴</div>
      <h1 style="margin-top:-3.5rem;"><?php esc_html_e( 'صفحه مورد نظر یافت نشد', 'fasdent' ); ?></h1>
      <p style="color:var(--color-muted);max-width:520px;margin:0 auto 1.5rem;"><?php esc_html_e( 'لینک وارد شده معتبر نیست یا محتوا حذف شده است. می‌توانید از جستجو یا لینک‌های زیر استفاده کنید.', 'fasdent' ); ?></p>
    </div>

    <!-- جستجو -->
    <div class="error-search" style="max-width:560px;margin:0 auto 3rem;">
      <?php get_search_form(); ?>
    </div>

    <!-- دسته‌های محبوب -->
    <?php
    $cats = get_terms( array( 'taxonomy' => 'service_category', 'hide_empty' => true, 'parent' => 0, 'number' => 6 ) );
    if ( $cats && ! is_wp_error( $cats ) ) : ?>
    <section aria-labelledby="cats-title" style="margin-bottom:3rem;">
      <h2 id="cats-title" style="font-size:1.1rem;margin-bottom:1rem;"><i class="fa-solid fa-tooth" aria-hidden="true"></i> <?php esc_html_e( 'دسته‌های اصلی خدمات', 'fasdent' ); ?></h2>
      <div class="grid-3">
        <?php foreach ( $cats as $cat ) : ?>
        <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="card category-card" style="display:flex;align-items:center;gap:.75rem;text-decoration:none;color:inherit;">
          <i class="<?php echo esc_attr( fasdent_category_icon( $cat ) ); ?>" style="font-size:1.5rem;color:var(--color-primary);" aria-hidden="true"></i>
          <span><?php echo esc_html( $cat->name ); ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- لینک‌های سریع -->
    <section aria-labelledby="ql-title" style="margin-bottom:3rem;">
      <h2 id="ql-title" style="font-size:1.1rem;margin-bottom:1rem;"><i class="fa-solid fa-link" aria-hidden="true"></i> <?php esc_html_e( 'لینک‌های مفید', 'fasdent' ); ?></h2>
      <ul style="list-style:none;padding:0;display:flex;flex-wrap:wrap;gap:.5rem 1.25rem;">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="fa-solid fa-house" aria-hidden="true"></i> خانه</a></li>
        <li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><i class="fa-solid fa-tooth" aria-hidden="true"></i> همه خدمات</a></li>
        <li><a href="<?php echo esc_url( home_url( '/appointment/' ) ); ?>"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> رزرو نوبت</a></li>
        <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><i class="fa-solid fa-envelope" aria-hidden="true"></i> تماس</a></li>
        <li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i> مقالات</a></li>
        <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><i class="fa-solid fa-circle-question" aria-hidden="true"></i> سوالات متداول</a></li>
      </ul>
    </section>

    <!-- ابر برچسب‌ها -->
    <?php
    $all_tags = get_tags( array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 30, 'hide_empty' => true ) );
    if ( $all_tags ) : ?>
    <section aria-labelledby="tags-title" style="margin-bottom:3rem;">
      <h2 id="tags-title" style="font-size:1.1rem;margin-bottom:1rem;"><i class="fa-solid fa-tags" aria-hidden="true"></i> <?php esc_html_e( 'برچسب‌ها', 'fasdent' ); ?></h2>
      <div class="tag-cloud" style="display:flex;flex-wrap:wrap;gap:.5rem;">
        <?php
        $max_count = max( array_column( $all_tags, 'count' ) );
        foreach ( $all_tags as $t ) :
          $size = 0.8 + ( $t->count / max( 1, $max_count ) ) * 0.8;
          $hash = crc32( $t->slug ) % 360;
          $color = "hsl({$hash},60%,42%)";
        ?>
        <a href="<?php echo esc_url( get_tag_link( $t ) ); ?>"
           class="tag-pill"
           style="font-size:<?php echo esc_attr( round( $size, 2 ) ); ?>rem;background:<?php echo esc_attr( $color ); ?>1a;color:<?php echo esc_attr( $color ); ?>;border:1px solid <?php echo esc_attr( $color ); ?>33;border-radius:999px;padding:.25rem .75rem;text-decoration:none;">
           <?php echo esc_html( $t->name ); ?>
           <sup style="font-size:.65em;"><?php echo esc_html( $t->count ); ?></sup>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- CTA تماس -->
    <div class="card" style="text-align:center;padding:2rem;margin-top:1rem;">
      <h2><i class="fa-solid fa-headset" aria-hidden="true"></i> <?php esc_html_e( 'نیاز به کمک دارید؟', 'fasdent' ); ?></h2>
      <p><?php esc_html_e( 'تیم ما آماده پاسخگویی است. همین الان تماس بگیرید یا نوبت رزرو کنید.', 'fasdent' ); ?></p>
      <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;margin-top:1rem;">
        <?php fasdent_booking_button( 'رزرو نوبت', 'btn-primary' ); ?>
        <?php fasdent_call_button( '', 'btn-secondary' ); ?>
      </div>
    </div>

  </div>
</section>
<?php get_footer(); ?>