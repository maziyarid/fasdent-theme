<?php
/**
 * Taxonomy Pillar Page (Template A) — service_category — Fasdent
 * @package Fasdent
 */
get_header();
$term     = get_queried_object();
$icon     = fasdent_category_icon( $term );
$faqs     = fasdent_category_faqs( $term->slug );
$children = get_terms( array( 'taxonomy' => 'service_category', 'parent' => $term->term_id, 'hide_empty' => false ) );
?>

<div class="pillar-page">

  <!-- هیرو دسته -->
  <section class="section pillar-hero">
    <div class="container">
      <div class="pillar-hero__inner">
        <div class="pillar-hero__icon" aria-hidden="true">
          <i class="<?php echo esc_attr( $icon ); ?>"></i>
        </div>
        <div>
          <h1>
            <i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
            <?php echo esc_html( $term->name ); ?>
          </h1>
          <p class="pillar-hero__desc"><?php echo wp_kses_post( $term->description ?: 'خدمات تخصصی در این حوزه با بالاترین استانداردهای پزشکی و مراقبت از بیماران ارائه می‌شود.' ); ?></p>
          <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1rem;">
            <?php fasdent_booking_button( 'رزرو نوبت', 'btn-primary' ); ?>
            <?php fasdent_call_button( '', 'btn-secondary' ); ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php if ( $children && ! is_wp_error( $children ) ) : ?>
  <!-- زیر دسته‌ها -->
  <section class="section section--bg" aria-labelledby="subcats-title">
    <div class="container">
      <h2 id="subcats-title"><?php esc_html_e( 'زیردسته‌ها', 'fasdent' ); ?></h2>
      <div class="grid-3">
        <?php foreach ( $children as $child ) : ?>
        <a href="<?php echo esc_url( get_term_link( $child ) ); ?>" class="category-card card">
          <i class="<?php echo esc_attr( fasdent_category_icon( $child ) ); ?>" aria-hidden="true"></i>
          <strong><?php echo esc_html( $child->name ); ?></strong>
          <p><?php echo esc_html( $child->description ?: 'خدمات تخصصی' ); ?></p>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- خدمات این دسته -->
  <section class="section" aria-labelledby="services-title">
    <div class="container">
      <h2 id="services-title"><?php echo esc_html( sprintf( 'خدمات %s', $term->name ) ); ?></h2>
      <div class="grid-3">
        <?php
        $q = new WP_Query( array(
          'post_type'      => 'service',
          'posts_per_page' => 12,
          'post_status'    => 'publish',
          'tax_query'      => array( array( 'taxonomy' => 'service_category', 'field' => 'term_id', 'terms' => $term->term_id, 'include_children' => true ) ),
        ) );
        if ( $q->have_posts() ) : while ( $q->have_posts() ) : $q->the_post();
          $svc_icon  = fasdent_field( 'service_icon' ) ?: 'fa-solid fa-tooth';
          $svc_price = fasdent_field( 'service_price' );
          $svc_dur   = fasdent_field( 'service_duration' );
        ?>
        <article class="service-card card">
          <?php if ( has_post_thumbnail() ) : ?><?php the_post_thumbnail( 'fasdent-card', array( 'loading' => 'lazy' ) ); ?><?php endif; ?>
          <h3><i class="<?php echo esc_attr( $svc_icon ); ?>" aria-hidden="true"></i> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <?php the_excerpt(); ?>
          <?php if ( $svc_price ) : ?><p class="service-price"><i class="fa-solid fa-tag" aria-hidden="true"></i> <?php echo esc_html( $svc_price ); ?></p><?php endif; ?>
          <?php if ( $svc_dur ) : ?><p style="font-size:.8rem;color:var(--color-muted);"><i class="fa-solid fa-clock" aria-hidden="true"></i> <?php echo esc_html( $svc_dur ); ?></p><?php endif; ?>
          <a href="<?php the_permalink(); ?>" class="btn btn-primary" style="margin-top:.75rem;"><?php esc_html_e( 'مشاهده', 'fasdent' ); ?></a>
        </article>
        <?php endwhile; wp_reset_postdata(); else : ?>
        <p><?php esc_html_e( 'خدمتی در این دسته یافت نشد.', 'fasdent' ); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- چرا فس‌دنت -->
  <section class="section section--bg" aria-labelledby="whyus-title">
    <div class="container">
      <h2 id="whyus-title"><?php echo esc_html( sprintf( 'چرا برای %s کلینیک فس‌دنت را انتخاب کنید؟', $term->name ) ); ?></h2>
      <div class="grid-3">
        <div class="card"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i><h3><?php esc_html_e( 'متخصص مجرب', 'fasdent' ); ?></h3><p><?php esc_html_e( 'دکتر با سال‌ها تجربه و آموزش‌های تخصصی بین‌المللی', 'fasdent' ); ?></p></div>
        <div class="card"><i class="fa-solid fa-microscope" aria-hidden="true"></i><h3><?php esc_html_e( 'تجهیزات مدرن', 'fasdent' ); ?></h3><p><?php esc_html_e( 'استفاده از پیشرفته‌ترین فناوری‌های دندانپزشکی دیجیتال', 'fasdent' ); ?></p></div>
        <div class="card"><i class="fa-solid fa-shield-heart" aria-hidden="true"></i><h3><?php esc_html_e( 'ضمانت کیفیت', 'fasdent' ); ?></h3><p><?php esc_html_e( 'پشتیبانی و گارانتی برای تمام درمان‌ها', 'fasdent' ); ?></p></div>
      </div>
    </div>
  </section>

  <!-- نظرات بیماران -->
  <?php
  $testimonials = get_posts( array( 'post_type' => 'testimonial', 'numberposts' => 3, 'post_status' => 'publish' ) );
  if ( $testimonials ) :
  ?>
  <section class="section" aria-labelledby="reviews-title">
    <div class="container">
      <h2 id="reviews-title"><?php esc_html_e( 'نظرات بیماران', 'fasdent' ); ?></h2>
      <div class="grid-3">
        <?php foreach ( $testimonials as $t ) :
          $rating = (float)( get_post_meta( $t->ID, 'rating', true ) ?: 5 );
        ?>
        <article class="testimonial-card card">
          <div class="star-rating" aria-label="امتیاز <?php echo esc_attr( $rating ); ?> از ۵">
            <?php for ( $s = 1; $s <= 5; $s++ ) : ?><i class="fa-<?php echo $s <= $rating ? 'solid' : 'regular'; ?> fa-star" aria-hidden="true"></i><?php endfor; ?>
          </div>
          <p>"<?php echo esc_html( wp_strip_all_tags( $t->post_content ) ); ?>"</p>
          <strong><?php echo esc_html( $t->post_title ); ?></strong>
        </article>
        <?php endforeach; wp_reset_postdata(); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if ( $faqs ) : ?>
  <!-- سوالات متداول -->
  <section class="section section--bg" aria-labelledby="faq-title">
    <div class="container">
      <h2 id="faq-title"><?php echo esc_html( sprintf( 'سوالات متداول %s', $term->name ) ); ?></h2>
      <div class="faq-list">
        <?php foreach ( $faqs as $faq ) : ?>
        <div class="faq-item">
          <button type="button" aria-expanded="false"><?php echo esc_html( $faq['question'] ?? '' ); ?></button>
          <div class="faq-answer"><?php echo wp_kses_post( $faq['answer'] ?? '' ); ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- CTA نهایی -->
  <?php get_template_part( 'template-parts/cta-banner' ); ?>

</div>

<?php get_footer(); ?>