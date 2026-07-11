<?php
/**
 * صفحه نویسنده / پروفایل پزشک — Fasdent
 * @package Fasdent
 */
get_header();
$author = get_queried_object();
if ( ! $author ) { get_footer(); return; }

$doctor_posts = get_posts( array( 'post_type' => 'doctor', 'numberposts' => 1, 'author' => $author->ID, 'post_status' => 'publish' ) );
if ( ! $doctor_posts ) {
	$doctor_posts = get_posts( array( 'post_type' => 'doctor', 'numberposts' => 1, 'post_status' => 'publish' ) );
}
$doctor_post  = $doctor_posts ? $doctor_posts[0] : null;
$doctor_id    = $doctor_post ? $doctor_post->ID : 0;
$doc_title    = $doctor_id ? ( fasdent_field( 'doctor_title', $doctor_id ) ?: '' ) : '';
$doc_edu      = $doctor_id ? ( fasdent_field( 'doctor_education', $doctor_id ) ?: '' ) : '';
$doc_license  = $doctor_id ? ( fasdent_field( 'doctor_license', $doctor_id ) ?: '' ) : '';
$doc_years    = $doctor_id ? ( fasdent_field( 'doctor_years', $doctor_id ) ?: '' ) : '';
$avatar_url   = get_avatar_url( $author->ID, array( 'size' => 200 ) );
if ( $doctor_id && has_post_thumbnail( $doctor_id ) ) {
	$avatar_url = get_the_post_thumbnail_url( $doctor_id, 'large' );
}
?>
<div class="author-page">
  <section class="section section--teal author-hero">
    <div class="container">
      <div class="author-hero__grid">
        <div class="author-hero__photo">
          <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $author->display_name ); ?>" class="author-avatar" loading="eager" width="200" height="200">
        </div>
        <div class="author-hero__info">
          <h1><?php echo esc_html( $author->display_name ); ?></h1>
          <?php if ( $doc_title ) : ?><p class="author-title"><?php echo esc_html( $doc_title ); ?></p><?php endif; ?>
          <?php if ( $doc_license ) : ?><p class="author-license"><i class="fa-solid fa-certificate" aria-hidden="true"></i> <?php esc_html_e( 'شماره نظام پزشکی:', 'fasdent' ); ?> <strong><?php echo esc_html( $doc_license ); ?></strong></p><?php endif; ?>
          <?php if ( $doc_years ) : ?><p><i class="fa-solid fa-star" aria-hidden="true"></i> <?php echo esc_html( $doc_years ); ?> <?php esc_html_e( 'سال تجربه', 'fasdent' ); ?></p><?php endif; ?>
          <div class="author-social">
            <?php $ig = get_theme_mod( 'fasdent_instagram' ); if ( $ig ) : ?><a href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener noreferrer" aria-label="اینستاگرام"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a><?php endif; ?>
            <?php $tg = get_theme_mod( 'fasdent_telegram' ); if ( $tg ) : ?><a href="<?php echo esc_url( $tg ); ?>" target="_blank" rel="noopener noreferrer" aria-label="تلگرام"><i class="fa-brands fa-telegram" aria-hidden="true"></i></a><?php endif; ?>
          </div>
          <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1.25rem;">
            <?php fasdent_booking_button( 'رزرو نوبت', 'btn-primary' ); ?>
            <?php fasdent_call_button( 'تماس مستقیم', 'btn-secondary' ); ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="author-bio-grid">
        <div class="author-bio__content">
          <h2><i class="fa-solid fa-user-doctor" aria-hidden="true"></i> درباره دکتر</h2>
          <div class="prose">
            <?php
            if ( $doctor_id ) {
              $bio = apply_filters( 'the_content', get_post_field( 'post_content', $doctor_id ) );
              echo $bio ? wp_kses_post( $bio ) : wpautop( esc_html( $author->description ) );
            } else {
              echo wpautop( esc_html( $author->description ) );
            }
            ?>
          </div>
          <?php if ( $doc_edu ) : ?>
          <h2 style="margin-top:2rem;"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i> تحصیلات</h2>
          <div class="prose"><?php echo wp_kses_post( wpautop( $doc_edu ) ); ?></div>
          <?php endif; ?>
        </div>
        <aside class="author-bio__sidebar">
          <div class="card">
            <h3><i class="fa-solid fa-tooth" aria-hidden="true"></i> تخصص‌ها</h3>
            <?php $specialties = get_terms( array( 'taxonomy' => 'service_category', 'hide_empty' => true, 'parent' => 0 ) );
            if ( $specialties && ! is_wp_error( $specialties ) ) : ?>
            <ul class="specialty-list">
              <?php foreach ( $specialties as $spec ) : ?>
              <li><a href="<?php echo esc_url( get_term_link( $spec ) ); ?>"><i class="<?php echo esc_attr( fasdent_category_icon( $spec ) ); ?>" aria-hidden="true"></i> <?php echo esc_html( $spec->name ); ?></a></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </div>
        </aside>
      </div>
    </div>
  </section>

  <?php
  $testimonials = get_posts( array( 'post_type' => 'testimonial', 'numberposts' => 6, 'post_status' => 'publish' ) );
  if ( $testimonials ) : ?>
  <section class="section section--bg">
    <div class="container">
      <h2 class="section-title"><i class="fa-solid fa-star" aria-hidden="true"></i> نظرات بیماران</h2>
      <div class="grid-3">
        <?php foreach ( $testimonials as $t ) :
          $rating = (float) ( get_post_meta( $t->ID, 'rating', true ) ?: 5 );
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

  <?php if ( have_posts() ) : ?>
  <section class="section">
    <div class="container">
      <h2 class="section-title"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i> مطالب منتشرشده</h2>
      <div class="grid-3">
        <?php while ( have_posts() ) : the_post(); ?>
        <article class="card post-card">
          <?php if ( has_post_thumbnail() ) : ?><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'fasdent-card', array( 'loading' => 'lazy' ) ); ?></a><?php endif; ?>
          <div class="post-card__body">
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <?php the_excerpt(); ?>
          </div>
        </article>
        <?php endwhile; ?>
      </div>
      <?php the_posts_pagination(); ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="section section--cta">
    <div class="container" style="text-align:center;">
      <h2><?php esc_html_e( 'نوبت درمان خود را رزرو کنید', 'fasdent' ); ?></h2>
      <p><?php esc_html_e( 'مشاوره تخصصی رایگان — تماس یا رزرو آنلاین', 'fasdent' ); ?></p>
      <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;">
        <?php fasdent_booking_button( 'رزرو نوبت آنلاین', 'btn-primary' ); ?>
        <?php fasdent_call_button( '', 'btn-secondary' ); ?>
      </div>
    </div>
  </section>
</div>
<?php get_footer(); ?>