<?php
/**
 * Template Name: قوانین لغو نوبت
 * @package Fasdent
 */
get_header(); ?>
<section class="section">
<div class="container" style="max-width:800px;">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<h1><?php the_title(); ?></h1>
<div class="prose"><?php the_content(); ?></div>
<?php endwhile; else : ?>
<h1>قوانین لغو و تغییر نوبت</h1>
<div class="prose">
<h2>لغو نوبت</h2>
<ul>
  <li>لغو نوبت حداقل ۲۴ ساعت قبل از موعد مقرر: بدون هزینه</li>
  <li>لغو کمتر از ۲۴ ساعت قبل: ممکن است هزینه ثبت نوبت کسر شود</li>
  <li>عدم حضور بدون اطلاع قبلی: نوبت از دست می‌رود</li>
</ul>
<h2>تغییر نوبت</h2>
<ul>
  <li>تغییر نوبت از طریق تماس تلفنی امکان‌پذیر است</li>
  <li>برای هر بیمار حداکثر ۲ بار تغییر نوبت رایگان در ماه مجاز است</li>
</ul>
<h2>نوبت‌های اورژانسی</h2>
<p>نوبت‌های اورژانسی مشمول قوانین متفاوتی هستند. با شماره <?php echo esc_html( fasdent_phone() ); ?> تماس بگیرید.</p>
<h2>تماس</h2>
<p><a href="tel:<?php echo esc_attr( fasdent_phone_link() ); ?>" class="btn"><?php echo esc_html( fasdent_phone() ); ?></a></p>
</div>
<?php endif; ?>
</div>
</section>
<?php get_footer(); ?>