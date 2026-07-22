<?php
/**
 * Template Name: سلب مسئولیت پزشکی
 * @package Fasdent
 */
get_header(); ?>
<section class="section">
<div class="container" style="max-width:800px;">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<h1><?php the_title(); ?></h1>
<div class="prose"><?php the_content(); ?></div>
<?php endwhile; else : ?>
<h1>سلب مسئولیت پزشکی</h1>
<div class="prose">
<div class="alert alert--warn" style="margin-bottom:2rem;">
  <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
  <strong>هشدار مهم:</strong> محتوای این سایت صرفاً برای اهداف اطلاع‌رسانی و آموزشی است.
</div>
<h2>اطلاعات عمومی، نه مشاوره پزشکی</h2>
<p>مطالب ارائه‌شده در سایت <?php echo esc_html( get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' ) ); ?> جایگزین مشاوره، تشخیص یا درمان توسط پزشک متخصص نمی‌شود. هر تصمیم درمانی باید پس از مشاوره مستقیم با پزشک گرفته شود.</p>
<h2>دقت اطلاعات</h2>
<p>اگرچه تمام تلاش می‌شود اطلاعات دقیق و بروز باشند، هیچ‌گونه ضمانتی برای کامل بودن، دقت یا بروز بودن محتوا داده نمی‌شود.</p>
<h2>موارد اورژانسی</h2>
<p>در صورت بروز درد شدید، خونریزی یا هر وضعیت اورژانسی دندانی، فوراً با ما تماس بگیرید:</p>
<p><a href="tel:<?php echo esc_attr( fasdent_phone_link() ); ?>" class="btn"><?php echo esc_html( fasdent_phone() ); ?></a></p>
<h2>محدودیت مسئولیت</h2>
<p>این کلینیک در قبال خسارات ناشی از استفاده یا عدم استفاده از اطلاعات این سایت مسئولیتی ندارد.</p>
</div>
<?php endif; ?>
</div>
</section>
<?php get_footer(); ?>