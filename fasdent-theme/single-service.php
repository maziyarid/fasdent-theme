<?php
/**
 * تک خدمت (Template B + C) — Fasdent
 * @package Fasdent
 */
get_header();
if ( ! have_posts() ) { get_footer(); return; }
while ( have_posts() ) : the_post();

$price     = fasdent_field( 'service_price' );
$duration  = fasdent_field( 'service_duration' );
$icon      = fasdent_field( 'service_icon' ) ?: 'fa-solid fa-tooth';
$aftercare = fasdent_field( 'service_aftercare' );
$gallery   = fasdent_field( 'service_gallery' );
$steps     = fasdent_get_service_steps();
$benefits  = fasdent_get_service_benefits();
$faqs      = fasdent_get_service_faqs();
$related   = fasdent_get_related_services();
$is_emerg  = fasdent_is_emergency_context();
$terms     = get_the_terms( get_the_ID(), 'service_category' );
$term      = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
?>

<div class="service-single<?php echo $is_emerg ? ' is-emergency-service' : ''; ?>">

  <?php if ( $is_emerg ) : ?>
  <!-- نوار اورژانس -->
  <div class="emergency-bar" role="alert">
    <div class="container">
      <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
      <strong><?php esc_html_e( 'اورژانس دندانپزشکی ۲۴ ساعته', 'fasdent' ); ?></strong>
      — <?php esc_html_e( 'همین الان تماس بگیرید:', 'fasdent' ); ?>
      <?php fasdent_call_button( '', 'btn-emergency' ); ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- هدر خدمت -->
  <section class="service-hero section">
    <div class="container">
      <div class="service-hero__grid">
        <div>
          <?php if ( $term ) : ?>
          <p class="service-category-label">
            <i class="<?php echo esc_attr( fasdent_category_icon( $term ) ); ?>" aria-hidden="true"></i>
            <a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
          </p>
          <?php endif; ?>
          <h1>
            <i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
            <?php the_title(); ?>
          </h1>
          <?php the_excerpt(); ?>
          <div class="service-meta" style="display:flex;gap:1.5rem;flex-wrap:wrap;margin:1.25rem 0;">
            <?php if ( $price ) : ?>
            <div class="service-meta__item"><i class="fa-solid fa-tag" aria-hidden="true"></i> <strong><?php esc_html_e( 'قیمت:', 'fasdent' ); ?></strong> <?php echo esc_html( $price ); ?></div>
            <?php endif; ?>
            <?php if ( $duration ) : ?>
            <div class="service-meta__item"><i class="fa-solid fa-clock" aria-hidden="true"></i> <strong><?php esc_html_e( 'مدت:', 'fasdent' ); ?></strong> <?php echo esc_html( $duration ); ?></div>
            <?php endif; ?>
          </div>
          <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <?php fasdent_booking_button( 'رزرو نوبت', 'btn-primary' ); ?>
            <?php fasdent_call_button( '', 'btn-secondary' ); ?>
          </div>
        </div>
        <?php if ( has_post_thumbnail() ) : ?>
        <div class="service-hero__image"><?php the_post_thumbnail( 'fasdent-card', array( 'loading' => 'eager' ) ); ?></div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- محتوای اصلی -->
  <article class="section">
    <div class="container">
      <div class="service-content prose">
        <?php the_content(); ?>
      </div>
    </div>
  </article>

  <?php if ( $benefits ) : ?>
  <!-- مزایا -->
  <section class="section section--bg" aria-labelledby="benefits-title">
    <div class="container">
      <h2 id="benefits-title"><i class="fa-solid fa-shield-heart" aria-hidden="true"></i> <?php esc_html_e( 'مزایا و کاربردها', 'fasdent' ); ?></h2>
      <div class="grid-3">
        <?php foreach ( $benefits as $b ) : ?>
        <div class="benefit-card card">
          <i class="<?php echo esc_attr( $b['icon'] ?? 'fa-solid fa-check-circle' ); ?>" aria-hidden="true"></i>
          <span><?php echo esc_html( $b['text'] ?? '' ); ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if ( $steps ) : ?>
  <!-- مراحل درمان -->
  <section class="section" aria-labelledby="steps-title">
    <div class="container">
      <h2 id="steps-title"><i class="fa-solid fa-list-check" aria-hidden="true"></i> <?php esc_html_e( 'مراحل درمان', 'fasdent' ); ?></h2>
      <ol class="steps-timeline">
        <?php foreach ( $steps as $i => $step ) : ?>
        <li class="step-item">
          <span class="step-number" aria-hidden="true"><?php echo esc_html( $i + 1 ); ?></span>
          <div class="step-content">
            <strong class="step-title"><?php echo esc_html( $step['title'] ?? '' ); ?></strong>
            <p class="step-desc"><?php echo esc_html( $step['description'] ?? '' ); ?></p>
          </div>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>
  <?php endif; ?>

  <?php if ( $gallery && is_array( $gallery ) && count( $gallery ) > 0 ) : ?>
  <!-- گالری قبل/بعد -->
  <section class="section section--bg" aria-labelledby="gallery-title">
    <div class="container">
      <h2 id="gallery-title"><i class="fa-solid fa-images" aria-hidden="true"></i> <?php esc_html_e( 'گالری قبل و بعد', 'fasdent' ); ?></h2>
      <div class="gallery-grid grid-4">
        <?php foreach ( $gallery as $img ) :
          if ( is_array( $img ) ) {
            $src   = $img['url'] ?? '';
            $alt   = $img['alt'] ?? get_the_title();
            $thumb = $img['sizes']['fasdent-gallery'] ?? $src;
          } elseif ( is_numeric( $img ) ) {
            $src   = wp_get_attachment_url( $img ) ?: '';
            $alt   = get_post_meta( $img, '_wp_attachment_image_alt', true ) ?: get_the_title();
            $thumb = ( wp_get_attachment_image_src( $img, 'fasdent-gallery' ) )[0] ?? $src;
          } else { continue; }
          if ( ! $src ) continue;
        ?>
        <figure class="gallery-item">
          <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" data-lightbox data-full="<?php echo esc_url( $src ); ?>">
        </figure>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if ( $aftercare ) : ?>
  <!-- مراقبت‌های بعد از درمان -->
  <section class="section" aria-labelledby="aftercare-title">
    <div class="container">
      <div class="card aftercare-card">
        <h2 id="aftercare-title"><i class="fa-solid fa-notes-medical" aria-hidden="true"></i> <?php esc_html_e( 'مراقبت‌های بعد از درمان', 'fasdent' ); ?></h2>
        <div class="prose"><?php echo wp_kses_post( $aftercare ); ?></div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if ( $faqs ) : ?>
  <!-- سوالات متداول -->
  <section class="section section--bg" aria-labelledby="faq-title">
    <div class="container">
      <h2 id="faq-title"><i class="fa-solid fa-circle-question" aria-hidden="true"></i> <?php esc_html_e( 'سوالات متداول', 'fasdent' ); ?></h2>
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

  <?php if ( $related ) : ?>
  <!-- خدمات مرتبط -->
  <section class="section" aria-labelledby="related-title">
    <div class="container">
      <h2 id="related-title"><i class="fa-solid fa-circle-nodes" aria-hidden="true"></i> <?php esc_html_e( 'خدمات مرتبط', 'fasdent' ); ?></h2>
      <div class="grid-3">
        <?php foreach ( $related as $rel ) :
          $rel_icon = fasdent_field( 'service_icon', $rel->ID ) ?: 'fa-solid fa-tooth';
          $rel_price = fasdent_field( 'service_price', $rel->ID );
        ?>
        <article class="service-card card">
          <?php if ( has_post_thumbnail( $rel->ID ) ) : ?><?php echo get_the_post_thumbnail( $rel->ID, 'fasdent-card', array( 'loading' => 'lazy' ) ); ?><?php endif; ?>
          <h3>
            <i class="<?php echo esc_attr( $rel_icon ); ?>" aria-hidden="true"></i>
            <a href="<?php echo esc_url( get_permalink( $rel ) ); ?>"><?php echo esc_html( $rel->post_title ); ?></a>
          </h3>
          <p><?php echo esc_html( wp_trim_words( $rel->post_excerpt ?: $rel->post_content, 15 ) ); ?></p>
          <?php if ( $rel_price ) : ?><p class="service-price"><i class="fa-solid fa-tag" aria-hidden="true"></i> <?php echo esc_html( $rel_price ); ?></p><?php endif; ?>
        </article>
        <?php endforeach; wp_reset_postdata(); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- CTA نهایی -->
  <?php get_template_part( 'template-parts/cta-banner' ); ?>

</div>
<?php endwhile; ?>
<?php get_footer(); ?>