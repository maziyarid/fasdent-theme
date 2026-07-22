<?php
/**
 * صفحه اصلی — Front Page — Fasdent
 * @package Fasdent
 */
get_header();
?>

<!-- ═══ هیرو ══════════════════════════════════════════ -->
<section class="hero">
  <div class="container hero-grid">
    <div class="hero__text">
      <p class="hero__tag"><i class="fa-solid fa-certificate" aria-hidden="true"></i> <?php echo esc_html( get_theme_mod( 'fasdent_doctor_name', 'دکتر کیوان علی‌پسندی' ) ); ?></p>
      <h1><?php echo esc_html( get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' ) ); ?></h1>
      <p class="hero__lead"><?php esc_html_e( 'ایمپلنت، لمینت، ارتودنسی، درمان ریشه و اورژانس دندانپزشکی با کادر حرفه‌ای و تجهیزات مدرن.', 'fasdent' ); ?></p>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
        <?php fasdent_booking_button( 'رزرو نوبت رایگان', 'btn-primary' ); ?>
        <?php fasdent_call_button( '', 'btn-secondary' ); ?>
      </div>
    </div>
    <div class="hero__card card">
      <h2><?php esc_html_e( 'چرا فس‌دنت؟', 'fasdent' ); ?></h2>
      <ul style="padding-right:1.25rem;">
        <li><?php esc_html_e( 'پشتیبانی ۲۴ ساعته برای اورژانس دندانپزشکی', 'fasdent' ); ?></li>
        <li><?php esc_html_e( 'درمان‌های تخصصی با کیفیت بالا و تضمین نتیجه', 'fasdent' ); ?></li>
        <li><?php esc_html_e( 'مشاوره رایگان و رزرو آنلاین سریع', 'fasdent' ); ?></li>
        <li><?php esc_html_e( 'تجهیزات پیشرفته دیجیتال', 'fasdent' ); ?></li>
      </ul>
    </div>
  </div>
</section>

<!-- ═══ آمار ════════════════════════════════════════════ -->
<section class="section section--bg">
  <div class="container">
    <div class="stats">
      <div class="stat-box"><strong><?php echo esc_html( number_format( (int) get_theme_mod( 'fasdent_stat_patients', '12000' ) ) ); ?>+</strong><div><?php esc_html_e( 'بیمار درمان‌شده', 'fasdent' ); ?></div></div>
      <div class="stat-box"><strong><?php echo esc_html( get_theme_mod( 'fasdent_stat_years', '15' ) ); ?></strong><div><?php esc_html_e( 'سال تجربه', 'fasdent' ); ?></div></div>
      <div class="stat-box"><strong><?php echo esc_html( number_format( (int) get_theme_mod( 'fasdent_stat_implants', '3500' ) ) ); ?>+</strong><div><?php esc_html_e( 'ایمپلنت موفق', 'fasdent' ); ?></div></div>
      <div class="stat-box"><strong><?php echo esc_html( get_theme_mod( 'fasdent_stat_rating', '4.9' ) ); ?>/۵</strong><div><?php esc_html_e( 'رضایت بیماران', 'fasdent' ); ?></div></div>
    </div>
  </div>
</section>

<!-- ═══ دسته‌های خدمات ════════════════════════════════ -->
<?php
$service_cats = get_terms( array( 'taxonomy' => 'service_category', 'hide_empty' => false, 'parent' => 0, 'number' => 10 ) );
if ( $service_cats && ! is_wp_error( $service_cats ) ) :
?>
<section class="section" aria-labelledby="services-title">
  <div class="container">
    <h2 id="services-title" class="section-title"><?php esc_html_e( 'خدمات دندانپزشکی', 'fasdent' ); ?></h2>
    <p class="section-desc"><?php esc_html_e( 'از دندانپزشکی عمومی تا ایمپلنت و زیبایی — همه خدمات در یک مکان', 'fasdent' ); ?></p>
    <div class="grid-3" style="margin-top:1.5rem;">
      <?php foreach ( $service_cats as $cat ) :
        $icon  = fasdent_category_icon( $cat );
        $count = $cat->count;
      ?>
      <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="category-card card" style="display:flex;align-items:flex-start;gap:1rem;text-decoration:none;color:inherit;">
        <div class="category-icon" style="flex-shrink:0;width:3rem;height:3rem;background:var(--color-primary);color:#fff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;">
          <i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
        </div>
        <div>
          <strong><?php echo esc_html( $cat->name ); ?></strong>
          <p style="font-size:.82rem;color:var(--color-muted);margin:.25rem 0 0;"><?php echo esc_html( $cat->description ?: 'خدمات تخصصی در این حوزه' ); ?></p>
          <?php if ( $count ) : ?><span style="font-size:.75rem;color:var(--color-primary);"><?php echo esc_html( $count ); ?> خدمت</span><?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:2rem;"><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'مشاهده همه خدمات', 'fasdent' ); ?></a></p>
  </div>
</section>
<?php endif; ?>

<!-- ═══ خدمات محبوب ══════════════════════════════════ -->
<?php
$popular_services = get_posts( array(
  'post_type'      => 'service',
  'numberposts'    => 5,
  'post_status'    => 'publish',
  'meta_key'       => 'service_price',
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
) );
if ( $popular_services ) :
?>
<section class="section section--bg" aria-labelledby="popular-title">
  <div class="container">
    <h2 id="popular-title" class="section-title"><?php esc_html_e( 'پرطرفدارترین خدمات', 'fasdent' ); ?></h2>
    <div class="grid-3">
      <?php foreach ( $popular_services as $service ) :
        $svc_icon  = fasdent_field( 'service_icon', $service->ID ) ?: 'fa-solid fa-tooth';
        $svc_price = fasdent_field( 'service_price', $service->ID );
        $svc_dur   = fasdent_field( 'service_duration', $service->ID );
      ?>
      <article class="service-card card">
        <?php if ( has_post_thumbnail( $service->ID ) ) : ?><?php echo get_the_post_thumbnail( $service->ID, 'fasdent-card', array( 'loading' => 'lazy' ) ); ?><?php endif; ?>
        <h3><i class="<?php echo esc_attr( $svc_icon ); ?>" aria-hidden="true"></i> <a href="<?php echo esc_url( get_permalink( $service ) ); ?>"><?php echo esc_html( $service->post_title ); ?></a></h3>
        <p><?php echo esc_html( wp_trim_words( $service->post_excerpt ?: $service->post_content, 18 ) ); ?></p>
        <?php if ( $svc_price ) : ?><p class="service-price"><i class="fa-solid fa-tag" aria-hidden="true"></i> <?php echo esc_html( $svc_price ); ?></p><?php endif; ?>
        <?php if ( $svc_dur ) : ?><p style="font-size:.8rem;color:var(--color-muted);"><i class="fa-solid fa-clock" aria-hidden="true"></i> <?php echo esc_html( $svc_dur ); ?></p><?php endif; ?>
        <a href="<?php echo esc_url( get_permalink( $service ) ); ?>" class="btn btn-primary" style="margin-top:.75rem;"><?php esc_html_e( 'اطلاعات بیشتر', 'fasdent' ); ?></a>
      </article>
      <?php endforeach; wp_reset_postdata(); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══ معرفی پزشک ═══════════════════════════════════ -->
<?php
$doctor = get_posts( array( 'post_type' => 'doctor', 'numberposts' => 1, 'post_status' => 'publish' ) );
$doctor = $doctor ? $doctor[0] : null;
if ( $doctor ) :
  $d_title   = fasdent_field( 'doctor_title', $doctor->ID ) ?: 'جراح و متخصص دندانپزشکی';
  $d_license = fasdent_field( 'doctor_license', $doctor->ID );
  $d_years   = fasdent_field( 'doctor_years', $doctor->ID );
?>
<section class="section">
  <div class="container">
    <div class="doctor-intro-grid">
      <?php if ( has_post_thumbnail( $doctor->ID ) ) : ?>
      <div class="doctor-intro__photo"><?php echo get_the_post_thumbnail( $doctor->ID, 'large', array( 'loading' => 'lazy', 'class' => 'doctor-photo' ) ); ?></div>
      <?php endif; ?>
      <div class="doctor-intro__content">
        <p class="eyebrow"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i> <?php esc_html_e( 'متخصص کلینیک', 'fasdent' ); ?></p>
        <h2><a href="<?php echo esc_url( get_permalink( $doctor ) ); ?>"><?php echo esc_html( $doctor->post_title ); ?></a></h2>
        <p class="doctor-speciality"><?php echo esc_html( $d_title ); ?></p>
        <?php if ( $d_license ) : ?><p><i class="fa-solid fa-certificate" aria-hidden="true"></i> <?php esc_html_e( 'شماره نظام پزشکی:', 'fasdent' ); ?> <?php echo esc_html( $d_license ); ?></p><?php endif; ?>
        <?php if ( $d_years ) : ?><p><i class="fa-solid fa-star" aria-hidden="true"></i> <?php echo esc_html( $d_years ); ?> <?php esc_html_e( 'سال تجربه', 'fasdent' ); ?></p><?php endif; ?>
        <div class="prose"><?php echo wp_kses_post( wp_trim_words( get_post_field( 'post_content', $doctor->ID ), 40 ) ); ?></div>
        <a href="<?php echo esc_url( get_permalink( $doctor ) ); ?>" class="btn btn-primary" style="margin-top:1rem;"><?php esc_html_e( 'پروفایل کامل', 'fasdent' ); ?></a>
      </div>
    </div>
  </div>
</section>
<?php wp_reset_postdata(); endif; ?>

<!-- ═══ نظرات بیماران ════════════════════════════════ -->
<?php
$testimonials = get_posts( array( 'post_type' => 'testimonial', 'numberposts' => 6, 'post_status' => 'publish' ) );
if ( $testimonials ) :
?>
<section class="section section--bg" aria-labelledby="testimonials-title">
  <div class="container">
    <h2 id="testimonials-title" class="section-title"><?php esc_html_e( 'بیماران درباره ما می‌گویند', 'fasdent' ); ?></h2>
    <div class="grid-3">
      <?php foreach ( $testimonials as $t ) :
        $rating = (float) ( get_post_meta( $t->ID, 'rating', true ) ?: 5 );
      ?>
      <article class="testimonial-card card">
        <div class="star-rating" aria-label="امتیاز <?php echo esc_attr( $rating ); ?> از ۵">
          <?php for ( $s = 1; $s <= 5; $s++ ) : ?><i class="fa-<?php echo $s <= $rating ? 'solid' : 'regular'; ?> fa-star" aria-hidden="true"></i><?php endfor; ?>
        </div>
        <p class="testimonial-text">"<?php echo esc_html( wp_strip_all_tags( $t->post_content ) ); ?>"</p>
        <strong class="testimonial-author"><?php echo esc_html( $t->post_title ); ?></strong>
      </article>
      <?php endforeach; wp_reset_postdata(); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══ آخرین مقالات ══════════════════════════════════ -->
<?php
$blog_posts = get_posts( array( 'post_type' => 'post', 'numberposts' => 4, 'post_status' => 'publish' ) );
if ( $blog_posts ) :
?>
<section class="section" aria-labelledby="blog-title">
  <div class="container">
    <h2 id="blog-title" class="section-title"><?php esc_html_e( 'آخرین مقالات', 'fasdent' ); ?></h2>
    <div class="grid-4">
      <?php foreach ( $blog_posts as $post ) : ?>
      <article class="card post-card">
        <?php if ( has_post_thumbnail( $post->ID ) ) : ?><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo get_the_post_thumbnail( $post->ID, 'fasdent-card', array( 'loading' => 'lazy' ) ); ?></a><?php endif; ?>
        <div class="post-card__body">
          <h3><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( $post->post_title ); ?></a></h3>
          <p style="font-size:.82rem;color:var(--color-muted);"><?php echo esc_html( wp_trim_words( $post->post_excerpt ?: $post->post_content, 12 ) ); ?></p>
        </div>
      </article>
      <?php endforeach; wp_reset_postdata(); ?>
    </div>
    <p style="text-align:center;margin-top:2rem;"><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'مشاهده همه مقالات', 'fasdent' ); ?></a></p>
  </div>
</section>
<?php endif; ?>

<!-- ═══ نقشه و فرم سریع ════════════════════════════════ -->
<section class="section section--bg" aria-labelledby="contact-quick">
  <div class="container">
    <div class="contact-quick-grid">
      <?php $map = get_theme_mod( 'fasdent_map_embed', '' ); if ( $map ) : ?>
      <div class="contact-quick__map">
        <iframe src="<?php echo esc_url( $map ); ?>" width="100%" height="350" frameborder="0" allowfullscreen loading="lazy" title="<?php esc_attr_e( 'موقعیت کلینیک فس‌دنت', 'fasdent' ); ?>"></iframe>
      </div>
      <?php endif; ?>
      <div class="contact-quick__form card">
        <h2 id="contact-quick"><?php esc_html_e( 'رزرو سریع نوبت', 'fasdent' ); ?></h2>
        <form class="contact-form" data-ajax-form method="post" novalidate>
          <input type="hidden" name="action" value="fasdent_handle_form">
          <input type="hidden" name="form_type" value="appointment">
          <input type="hidden" name="fasdent_form_nonce" value="<?php echo esc_attr( wp_create_nonce( 'fasdent_form_nonce' ) ); ?>">
          <input type="text" name="_hp_website" style="display:none;" tabindex="-1" autocomplete="off">
          <p><label><?php esc_html_e( 'نام', 'fasdent' ); ?> <span aria-hidden="true">*</span><br><input type="text" name="name" required autocomplete="name"></label></p>
          <p><label><?php esc_html_e( 'شماره تماس', 'fasdent' ); ?> <span aria-hidden="true">*</span><br><input type="tel" name="phone" required autocomplete="tel"></label></p>
          <p><label><?php esc_html_e( 'علت مراجعه', 'fasdent' ); ?><br><textarea name="message" rows="3"></textarea></label></p>
          <div class="form-message" role="status" aria-live="polite"></div>
          <p><button type="submit" class="btn btn-primary"><?php esc_html_e( 'ارسال درخواست', 'fasdent' ); ?></button></p>
        </form>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>