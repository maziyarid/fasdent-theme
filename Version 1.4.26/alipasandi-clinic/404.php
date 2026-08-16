<?php
/** Not found page. */
get_header();
?>
<main id="main-content" class="content-area">
	<section class="section section-dark"><div class="narrow-container final-cta"><span class="eyebrow">خطای ۴۰۴</span><h1 class="display-title">این صفحه پیدا نشد.</h1><p>نشانی واردشده وجود ندارد یا تغییر کرده است.</p><div class="button-row"><a class="button button-gold" href="<?php echo esc_url( home_url( '/' ) ); ?>">بازگشت به صفحه اصلی</a><a class="button button-outline-light" href="<?php echo esc_url( alipasandi_page_url( 'contact' ) ); ?>">تماس با ما</a></div></div></section>
</main>
<?php get_footer(); ?>

