<?php
/**
 * Template Name: رزرو نوبت
 * Multi-step medical booking form with DB storage.
 * @package Fasdent
 */
get_header(); ?>

<div class="booking-page section">
<div class="container" style="max-width:720px;">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<h1 class="section-title" style="text-align:right;"><?php the_title(); ?></h1>
<?php the_content(); endwhile; endif; ?>

<!-- مراحل -->
<div class="booking-steps" aria-label="مراحل رزرو">
  <div class="step-indicator" aria-hidden="true">
    <span class="step-dot active" data-step="1">۱</span>
    <span class="step-line"></span>
    <span class="step-dot" data-step="2">۲</span>
    <span class="step-line"></span>
    <span class="step-dot" data-step="3">۳</span>
    <span class="step-line"></span>
    <span class="step-dot" data-step="4">۴</span>
  </div>
  <div class="step-labels" aria-hidden="true">
    <span>اطلاعات</span><span>پزشکی</span><span>نوبت</span><span>تایید</span>
  </div>
</div>

<form id="fasdent-booking-form" class="booking-form" data-booking-form novalidate>
  <?php wp_nonce_field( 'fasdent_form_nonce', 'fasdent_form_nonce' ); ?>
  <input type="hidden" name="action"       value="fasdent_submit_booking">
  <input type="hidden" name="form_type"    value="appointment">
  <input type="hidden" name="ga_session"   id="ga_session_field" value="">
  <input type="text"   name="_hp_website"  class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">

  <!-- مرحله ۱: اطلاعات شخصی -->
  <fieldset class="booking-step active" data-step-panel="1">
    <legend><i class="fa-regular fa-user" aria-hidden="true"></i> مرحله ۱ — اطلاعات شخصی</legend>
    <div class="form-grid">
      <label class="form-field">
        <span>نام و نام خانوادگی <abbr title="الزامی">*</abbr></span>
        <input type="text" name="name" required autocomplete="name" placeholder="مثال: کیوان علی‌پسندی">
      </label>
      <label class="form-field">
        <span>شماره تماس <abbr title="الزامی">*</abbr></span>
        <input type="tel" name="phone" required autocomplete="tel" placeholder="09xxxxxxxxx" dir="ltr">
      </label>
      <label class="form-field">
        <span>ایمیل (اختیاری)</span>
        <input type="email" name="email" autocomplete="email" placeholder="example@mail.com" dir="ltr">
      </label>
      <label class="form-field">
        <span>سن</span>
        <input type="number" name="age" min="1" max="120" placeholder="مثال: ۳۵">
      </label>
      <label class="form-field">
        <span>جنسیت</span>
        <select name="gender">
          <option value="">انتخاب کنید</option>
          <option value="male">مرد</option>
          <option value="female">زن</option>
        </select>
      </label>
    </div>
    <div class="booking-nav">
      <button type="button" class="btn booking-next">مرحله بعد <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
    </div>
  </fieldset>

  <!-- مرحله ۲: اطلاعات پزشکی -->
  <fieldset class="booking-step" data-step-panel="2" hidden>
    <legend><i class="fa-regular fa-notes-medical" aria-hidden="true"></i> مرحله ۲ — اطلاعات پزشکی</legend>
    <div class="form-grid">
      <label class="form-field form-field--full">
        <span>شرح مشکل / علت مراجعه <abbr title="الزامی">*</abbr></span>
        <textarea name="symptoms" required rows="3" placeholder="مثال: دردناکی دندان عقل، شکستگی، نیاز به ایمپلنت..."></textarea>
      </label>
      <label class="form-field form-field--full">
        <span>سابقه بیماری (دیابت، فشار خون، بیماری قلبی...)</span>
        <textarea name="medical_hist" rows="2" placeholder="در صورت نداشتن، خالی بگذارید"></textarea>
      </label>
      <label class="form-field">
        <span>داروهای مصرفی فعلی</span>
        <input type="text" name="medications" placeholder="مثال: آسپرین، وارفارین">
      </label>
      <label class="form-field">
        <span>آلرژی‌های شناخته‌شده</span>
        <input type="text" name="allergies" placeholder="مثال: پنی‌سیلین، لاتکس">
      </label>
    </div>
    <div class="booking-nav">
      <button type="button" class="btn btn-secondary booking-prev"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i> مرحله قبل</button>
      <button type="button" class="btn booking-next">مرحله بعد <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
    </div>
  </fieldset>

  <!-- مرحله ۳: انتخاب خدمت و نوبت -->
  <fieldset class="booking-step" data-step-panel="3" hidden>
    <legend><i class="fa-regular fa-calendar-check" aria-hidden="true"></i> مرحله ۳ — انتخاب نوبت</legend>
    <div class="form-grid">
      <label class="form-field">
        <span>خدمت مورد نیاز</span>
        <select name="service_id">
          <option value="">انتخاب کنید (اختیاری)</option>
          <?php
          $services = get_posts( [ 'post_type' => 'service', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ] );
          foreach ( $services as $svc ) {
            echo '<option value="' . esc_attr( $svc->ID ) . '">' . esc_html( $svc->post_title ) . '</option>';
          }
          ?>
        </select>
      </label>
      <label class="form-field">
        <span>پزشک مورد نظر</span>
        <select name="doctor_id">
          <option value="">هر دکتری (اختیاری)</option>
          <?php
          $doctors = get_posts( [ 'post_type' => 'doctor', 'numberposts' => -1, 'post_status' => 'publish' ] );
          foreach ( $doctors as $doc ) {
            echo '<option value="' . esc_attr( $doc->ID ) . '">' . esc_html( $doc->post_title ) . '</option>';
          }
          ?>
        </select>
      </label>
      <label class="form-field">
        <span>تاریخ ترجیحی (شمسی)</span>
        <input type="text" name="preferred_date" id="booking-date-picker" placeholder="مثال: ۱۴۰۴/۰۵/۱۵" autocomplete="off" dir="ltr">
      </label>
      <label class="form-field">
        <span>بازه زمانی ترجیحی</span>
        <select name="time_range">
          <option value="">مهم نیست</option>
          <option value="morning">صبح (۹–۱۲)</option>
          <option value="afternoon">بعداز ظهر (۱۲–۱۷)</option>
          <option value="evening">عصر (۱۷–۲۱)</option>
        </select>
      </label>
      <label class="form-field form-field--full" style="display:flex;align-items:center;gap:.75rem;">
        <input type="checkbox" name="is_emergency" value="1" style="width:auto;">
        <span><i class="fa-solid fa-triangle-exclamation" aria-hidden="true" style="color:#dc2626;"></i> این یک وضعیت اورژانسی است</span>
      </label>
    </div>
    <div class="booking-nav">
      <button type="button" class="btn btn-secondary booking-prev"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i> مرحله قبل</button>
      <button type="button" class="btn booking-next">بررسی نهایی <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
    </div>
  </fieldset>

  <!-- مرحله ۴: تأیید -->
  <fieldset class="booking-step" data-step-panel="4" hidden>
    <legend><i class="fa-regular fa-circle-check" aria-hidden="true"></i> مرحله ۴ — تأیید و ارسال</legend>
    <div id="booking-summary" class="booking-summary card" aria-live="polite"></div>
    <label class="form-field form-field--full privacy-check" style="display:flex;align-items:flex-start;gap:.75rem;margin-top:1rem;">
      <input type="checkbox" name="privacy_ok" value="1" required style="width:auto;margin-top:.2rem;">
      <span>با <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" target="_blank" rel="noopener">سیاست حریم خصوصی</a> و <a href="<?php echo esc_url( home_url( '/patient-rights/' ) ); ?>" target="_blank" rel="noopener">حقوق بیمار</a> موافقم و مجوز ذخیره اطلاعات پزشکی‌ام را می‌دهم.</span>
    </label>
    <p class="privacy-note"><i class="fa-solid fa-lock" aria-hidden="true"></i> اطلاعات شما محرمانه است و صرفاً برای برنامه‌ریزی درمان استفاده می‌شود.</p>
    <div class="booking-nav">
      <button type="button" class="btn btn-secondary booking-prev"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i> ویرایش</button>
      <button type="submit" class="btn btn-primary booking-submit">
        <i class="fa-solid fa-paper-plane" aria-hidden="true"></i> ثبت نوبت
      </button>
    </div>
    <div class="form-message" role="alert" aria-live="polite"></div>
  </fieldset>

</form>

<!-- بعد از موفقیت -->
<div id="booking-success" class="booking-success card" hidden role="status" aria-live="polite">
  <i class="fa-solid fa-circle-check" aria-hidden="true" style="font-size:3rem;color:var(--color-primary);"></i>
  <h2>نوبت شما با موفقیت ثبت شد!</h2>
  <p id="booking-success-msg"></p>
  <p>در صورت نیاز با ما تماس بگیرید: <a href="tel:<?php echo esc_attr( fasdent_phone_link() ); ?>" class="btn"><?php echo esc_html( fasdent_phone() ); ?></a></p>
</div>

</div><!-- .container -->
</div><!-- .booking-page -->

<?php get_footer(); ?>