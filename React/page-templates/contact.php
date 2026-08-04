<?php
/**
 * Template Name: تماس با ما
 * @package Fasdent
 */
get_header(); ?>
<section class="section">
<div class="container">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<h1><?php the_title(); ?></h1>
<?php the_content(); endwhile; endif; ?>

<div class="contact-quick-grid">
  <!-- اطلاعات تماس -->
  <div>
    <div class="card" style="margin-bottom:1.5rem;">
      <h2 style="font-size:1.1rem;margin-bottom:1rem;"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> اطلاعات تماس</h2>
      <ul style="list-style:none;padding:0;line-height:2.2;">
        <li><i class="fa-solid fa-phone" aria-hidden="true" style="color:var(--color-primary);width:1.5rem;"></i> <a href="tel:<?php echo esc_attr( fasdent_phone_link() ); ?>"><?php echo esc_html( fasdent_phone() ); ?></a></li>
        <li><i class="fa-regular fa-envelope" aria-hidden="true" style="color:var(--color-primary);width:1.5rem;"></i> <?php echo esc_html( get_theme_mod( 'fasdent_email', 'info@fasdent.ir' ) ); ?></li>
        <li><i class="fa-solid fa-location-dot" aria-hidden="true" style="color:var(--color-primary);width:1.5rem;"></i> <?php echo esc_html( get_theme_mod( 'fasdent_address', 'تهران' ) ); ?></li>
        <?php $hours = get_theme_mod( 'fasdent_hours', '' ); if ( $hours ) : ?>
        <li><i class="fa-regular fa-clock" aria-hidden="true" style="color:var(--color-primary);width:1.5rem;"></i> <?php echo esc_html( $hours ); ?></li>
        <?php endif; ?>
      </ul>
      <div style="margin-top:1rem;display:flex;gap:.75rem;flex-wrap:wrap;">
        <?php fasdent_call_button(); ?>
        <?php fasdent_booking_button(); ?>
      </div>
    </div>
    <?php $map = get_theme_mod( 'fasdent_map_embed', '' ); if ( $map ) : ?>
    <div class="map-embed card" style="padding:0;overflow:hidden;border-radius:var(--radius);">
      <iframe src="<?php echo esc_url( $map ); ?>" width="100%" height="300" style="border:0;display:block;" allowfullscreen loading="lazy" title="موقعیت کلینیک فس‌دنت"></iframe>
    </div>
    <?php endif; ?>
  </div>

  <!-- فرم تماس -->
  <div class="card">
    <h2 style="font-size:1.1rem;margin-bottom:1rem;"><i class="fa-regular fa-message" aria-hidden="true"></i> ارسال پیام</h2>
    <form class="contact-form" data-ajax-form method="post" novalidate>
      <input type="hidden" name="action"            value="fasdent_handle_form">
      <input type="hidden" name="form_type"          value="contact">
      <?php wp_nonce_field( 'fasdent_form_nonce', 'fasdent_form_nonce' ); ?>
      <input type="text"   name="_hp_website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">
      <label class="form-field">نام<br><input type="text" name="name" required placeholder="نام و نام خانوادگی"></label>
      <label class="form-field" style="margin-top:.75rem;">شماره تماس<br><input type="tel" name="phone" required placeholder="09xxxxxxxxx" dir="ltr"></label>
      <label class="form-field" style="margin-top:.75rem;">پیام<br><textarea name="message" rows="5" placeholder="چطور می‌توانیم کمک کنیم؟"></textarea></label>
      <button type="submit" class="btn" style="margin-top:1rem;width:100%;">
        <i class="fa-solid fa-paper-plane" aria-hidden="true"></i> ارسال پیام
      </button>
      <div class="form-message" role="alert" aria-live="polite"></div>
    </form>
  </div>
</div>
</div>
</section>
<?php get_footer(); ?>