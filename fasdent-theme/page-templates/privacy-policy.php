<?php
/**
 * Template Name: حریم خصوصی
 * @package Fasdent
 */
get_header(); ?>
<section class="section">
<div class="container" style="max-width:800px;">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<h1><?php the_title(); ?></h1>
<p class="post-meta-bar"><i class="fa-regular fa-clock" aria-hidden="true"></i> آخرین بروزرسانی: <?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?></p>
<div class="prose"><?php the_content(); ?></div>
<?php endwhile; else :
echo '<h1>سیاست حریم خصوصی</h1>';
$phone = fasdent_phone(); $clinic = get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' );
?>
<div class="prose">
<h2>۱. مقدمه</h2>
<p><?php echo esc_html( $clinic ); ?> متعهد به حفاظت از اطلاعات شخصی و پزشکی بیماران است. این سیاست توضیح می‌دهد چه اطلاعاتی جمع‌آوری و چگونه استفاده می‌شود.</p>
<h2>۲. اطلاعات جمع‌آوری‌شده</h2>
<ul>
  <li>اطلاعات تماس: نام، شماره موبایل، ایمیل</li>
  <li>اطلاعات پزشکی: شرح حال، سابقه بیماری، داروها، آلرژی‌ها</li>
  <li>اطلاعات فنی: آدرس IP، مرورگر، صفحات بازدیدشده</li>
</ul>
<h2>۳. هدف از جمع‌آوری</h2>
<ul>
  <li>برنامه‌ریزی و مدیریت نوبت‌های درمانی</li>
  <li>ارتباط درمانی با بیمار</li>
  <li>بهبود خدمات سایت (آنالیتیکس ناشناس)</li>
</ul>
<h2>۴. نگهداری اطلاعات</h2>
<p>اطلاعات پزشکی حداکثر ۵ سال و اطلاعات تماس تا زمان درخواست حذف نگهداری می‌شوند.</p>
<h2>۵. اشتراک‌گذاری</h2>
<p>اطلاعات شما بدون رضایت صریح شما با اشخاص ثالث به اشتراک گذاشته نمی‌شود، مگر در موارد الزامی قانونی.</p>
<h2>۶. حقوق شما</h2>
<ul>
  <li>حق دسترسی به اطلاعات ذخیره‌شده</li>
  <li>حق اصلاح اطلاعات نادرست</li>
  <li>حق درخواست حذف اطلاعات</li>
  <li>حق محدودیت پردازش</li>
</ul>
<h2>۷. کوکی‌ها</h2>
<p>این سایت از کوکی برای بهبود تجربه کاربری و آنالیتیکس (Google Analytics، Microsoft Clarity) استفاده می‌کند. می‌توانید از طریق بنر کوکی رضایت خود را مدیریت کنید.</p>
<h2>۸. تماس</h2>
<p>برای سوالات مربوط به حریم خصوصی با ما تماس بگیرید: <a href="tel:<?php echo esc_attr( fasdent_phone_link() ); ?>"><?php echo esc_html( $phone ); ?></a></p>
</div>
<?php endif; ?>
</div>
</section>
<?php get_footer(); ?>