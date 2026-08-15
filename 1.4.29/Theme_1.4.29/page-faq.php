<?php
/** FAQ page. */
get_header();

$faqs = array(
	array( 'قبل از مراجعه', 'آیا برای اولین مراجعه نیاز به تصویربرداری دارم؟', 'در صورت داشتن تصاویر قبلی، آن‌ها را همراه بیاورید. نیاز به تصویر جدید پس از معاینه و بر اساس نوع مشکل مشخص می‌شود.' ),
	array( 'قبل از مراجعه', 'چطور درخواست نوبت ثبت کنم؟', 'از صفحه درخواست نوبت، تماس تلفنی یا راه‌های ارتباطی پایین سایت استفاده کنید. زمان پیشنهادی پس از تماس کلینیک نهایی می‌شود.' ),
	array( 'درمان', 'آیا می‌توان هزینه درمان را قبل از معاینه اعلام کرد؟', 'هزینه به تشخیص، نوع درمان، مواد موردنیاز و پیچیدگی شرایط بستگی دارد. برآورد دقیق پس از معاینه ارائه می‌شود.' ),
	array( 'درمان', 'آیا همه افراد کاندید ایمپلنت هستند؟', 'خیر. وضعیت استخوان و لثه، سلامت عمومی و عوامل دیگری باید بررسی شوند. گزینه مناسب برای هر فرد پس از ارزیابی تعیین می‌شود.' ),
	array( 'مراقبت', 'بعد از درمان چه مراقبت‌هایی لازم است؟', 'مراقبت‌ها به نوع درمان بستگی دارد. دستورالعمل اختصاصی در پایان جلسه ارائه می‌شود و در صورت نیاز پیگیری انجام خواهد شد.' ),
	array( 'مراقبت', 'هر چند وقت یک‌بار باید معاینه شوم؟', 'فاصله معاینات برای همه یکسان نیست و بر اساس وضعیت سلامت دهان، سابقه درمان و عوامل خطر تعیین می‌شود.' ),
);
?>
<main id="main-content">
	<section class="inner-hero"><div class="narrow-container section-heading" style="padding-block:64px;position:relative"><span class="eyebrow">راهنمای مراجعه</span><h1>سوالات متداول</h1><p>پاسخ‌های عمومی زیر جایگزین معاینه و تشخیص فردی نیستند.</p></div></section>
	<section class="section section-bone"><div class="narrow-container"><div class="accordion-list">
		<?php foreach ( $faqs as $index => $faq ) : $id = 'general-faq-' . $index; ?>
			<div class="accordion-item"><button class="accordion-trigger" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $id ); ?>" data-accordion-trigger><span><small style="display:block;color:#a87622;margin-bottom:4px"><?php echo esc_html( $faq[0] ); ?></small><?php echo esc_html( $faq[1] ); ?></span><?php echo alipasandi_icon( 'chevron', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button><div class="accordion-panel" id="<?php echo esc_attr( $id ); ?>" hidden><?php echo esc_html( $faq[2] ); ?></div></div>
		<?php endforeach; ?>
	</div></div></section>
	<section class="section-compact section-dark"><div class="site-container final-cta"><h2>پاسخ خود را پیدا نکردید؟</h2><p>برای راهنمایی اولیه با کلینیک تماس بگیرید یا درخواست نوبت ثبت کنید.</p><div class="button-row"><a class="button button-gold" href="<?php echo esc_url( alipasandi_page_url( 'appointments' ) ); ?>">درخواست نوبت</a><a class="button button-outline-light" href="<?php echo esc_url( alipasandi_page_url( 'contact' ) ); ?>">تماس با ما</a></div></div></section>
</main>
<?php get_footer(); ?>

