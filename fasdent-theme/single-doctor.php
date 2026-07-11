<?php
/**
 * تک پزشک — Doctor Profile — Fasdent
 * @package Fasdent
 */
get_header();
if ( have_posts() ) : while ( have_posts() ) : the_post();
$doc_title   = fasdent_field( 'doctor_title' )   ?: '';
$doc_edu     = fasdent_field( 'doctor_education' ) ?: '';
$doc_license = fasdent_field( 'doctor_license' )  ?: '';
$doc_years   = fasdent_field( 'doctor_years' )    ?: '';
?>
<div class="doctor-page">
  <!-- هدر پروفایل -->
  <section class="section section--teal">
    <div class="container">
      <div class="doctor-hero">
        <div class="doctor-hero__photo">
          <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'large', array( 'class' => 'doctor-photo', 'loading' => 'eager' ) ); ?>
          <?php endif; ?>
        </div>
        <div class="doctor-hero__info">
          <h1><?php the_title(); ?></h1>
          <?php if ( $doc_title ) : ?><p class="doctor-title"><?php echo esc_html( $doc_title ); ?></p><?php endif; ?>
          <?php if ( $doc_license ) : ?>
          <p class="doctor-license"><i class="fa-solid fa-certificate" aria-hidden="true"></i> <?php esc_html_e( 'شماره نظام پزشکی:', 'fasdent' ); ?> <strong><?php echo esc_html( $doc_license ); ?></strong></p>
          <?php endif; ?>
          <?php if ( $doc_years ) : ?>
          <p><i class="fa-solid fa-star" aria-hidden="true"></i> <?php echo esc_html( $doc_years ); ?> <?php esc_html_e( 'سال تجربه', 'fasdent' ); ?></p>
          <?php endif; ?>
          <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1.25rem;">
            <?php fasdent_booking_button( 'رزرو نوبت', 'btn-primary' ); ?>
            <?php fasdent_call_button( '', 'btn-secondary' ); ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- بیوگرافی و تحصیلات -->
  <section class="section">
    <div class="container">
      <div class="doctor-bio-grid">
        <div class="doctor-bio__content">
          <?php if ( get_the_content() ) : ?>
          <h2><i class="fa-solid fa-user-doctor" aria-hidden="true"></i> بیوگرافی</h2>
          <div class="prose"><?php the_content(); ?></div>
          <?php endif; ?>

          <?php if ( $doc_edu ) : ?>
          <h2 style="margin-top:2rem;"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i> تحصیلات و آموزش</h2>
          <div class="prose"><?php echo wp_kses_post( wpautop( $doc_edu ) ); ?></div>
          <?php endif; ?>
        </div>

        <aside class="doctor-bio__sidebar">
          <div class="card">
            <h3><i class="fa-solid fa-tooth" aria-hidden="true"></i> حوزه‌های تخصصی</h3>
            <?php $specialties = get_terms( array( 'taxonomy' => 'service_category', 'hide_empty' => true, 'parent' => 0 ) );
            if ( $specialties && ! is_wp_error( $specialties ) ) : ?>
            <ul class="specialty-list">
              <?php foreach ( $specialties as $spec ) : ?>
              <li><a href="<?php echo esc_url( get_term_link( $spec ) ); ?>"><i class="<?php echo esc_attr( fasdent_category_icon( $spec ) ); ?>" aria-hidden="true"></i> <?php echo esc_html( $spec->name ); ?></a></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </div>

          <div class="card" style="margin-top:1rem;">
            <h3><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> رزرو نوبت</h3>
            <p><?php esc_html_e( 'برای مشاوره با دکتر همین الان اقدام کنید.', 'fasdent' ); ?></p>
            <?php fasdent_booking_button( 'رزرو نوبت آنلاین', 'btn-primary' ); ?>
          </div>
        </aside>
      </div>
    </div>
  </section>

  <!-- نظرات بیماران -->
  <?php $testimonials = get_posts( array( 'post_type' => 'testimonial', 'numberposts' => 6, 'post_status' => 'publish' ) );
  if ( $testimonials ) : ?>
  <section class="section section--bg">
    <div class="container">
      <h2 class="section-title"><i class="fa-solid fa-star" aria-hidden="true"></i> نظرات بیماران</h2>
      <div class="grid-3">
        <?php foreach ( $testimonials as $t ) :
          $rating = (float) ( get_post_meta( $t->ID, 'rating', true ) ?: 5 ); ?>
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

</div>
<?php endwhile; endif; ?>
<?php get_footer(); ?>