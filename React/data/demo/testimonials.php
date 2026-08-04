<?php
/**
 * Fasdent Demo — testimonial CPT posts
 */
if ( ! defined( 'FASDENT_DEMO_IMPORT' ) ) {
	exit;
}

if ( ! isset( $GLOBALS['fasdent_demo_ids'] ) ) {
	$GLOBALS['fasdent_demo_ids'] = array();
}
$GLOBALS['fasdent_demo_ids']['testimonials'] = array();

$testimonials = array(
	array(
		'title'           => 'مریم ح.',
		'slug'            => 'testimonial-maryam-h',
		'content'         => 'تجربه من در کلینیک فس‌دنت فوق‌العاده بود. دکتر علی‌پسندی با صبر و حوصله تمام مراحل ایمپلنت را توضیح دادند و نتیجه نهایی کاملاً طبیعی شد. محیط کلینیک بسیار تمیز و پرسنل مهربان هستند. از روز اول تا آخر درمان احساس امنیت داشتم و الان با اطمینان کامل لبخند می‌زنم.',
		'rating'          => 5,
		'related_service' => 'single-implant',
	),
	array(
		'title'           => 'علی ر.',
		'slug'            => 'testimonial-ali-r',
		'content'         => 'برای ارتودنسی شفاف به کلینیک فس‌دنت مراجعه کردم. دکتر سارا محمدی طرح درمان دقیقی ارائه دادند و در طول درمان همیشه در دسترس بودند. نتیجه عالی شد و دیگر کسی متوجه نمی‌شود که ارتودنسی داشتم. واقعاً از انتخاب این کلینیک راضی هستم.',
		'rating'          => 5,
		'related_service' => 'clear-aligners',
	),
	array(
		'title'           => 'زهرا ک.',
		'slug'            => 'testimonial-zahra-k',
		'content'         => 'فرزندم خیلی از دندانپزشکی می‌ترسید اما دکتر رضا نوری با روش‌های خاص خودشان او را کاملاً آرام کردند. فیشور سیلانت بدون هیچ استرسی انجام شد. ممنون از تیم فس‌دنت که محیط را برای کودکان اینقدر دوستانه کرده‌اند.',
		'rating'          => 5,
		'related_service' => 'fissure-sealant',
	),
	array(
		'title'           => 'حسین م.',
		'slug'            => 'testimonial-hossein-m',
		'content'         => 'عصب‌کشی دندانم را در کلینیک فس‌دنت انجام دادم. برخلاف تصور قبلی‌ام هیچ دردی نداشتم و دکتر خیلی دقیق کار کردند. بعد از درمان کاملاً راحت شدم. کیفیت کار و رفتار حرفه‌ای پرسنل عالی است.',
		'rating'          => 5,
		'related_service' => 'root-canal-treatment',
	),
	array(
		'title'           => 'نازنین س.',
		'slug'            => 'testimonial-nazanin-s',
		'content'         => 'بلیچینگ دندان‌هایم را در فس‌دنت انجام دادم. نتیجه خیلی طبیعی و یکدست شد. دکتر همه نکات مراقبتی را کامل توضیح دادند. قیمت منصفانه و محیط بسیار آرامش‌بخش بود. حتماً دوباره مراجعه می‌کنم.',
		'rating'          => 5,
		'related_service' => 'teeth-whitening',
	),
	array(
		'title'           => 'رضا پ.',
		'slug'            => 'testimonial-reza-p',
		'content'         => 'برای کشیدن دندان عقل نهفته به کلینیک آمدم. جراحی خیلی تمیز و سریع انجام شد و دوره نقاهت کوتاهی داشتم. دکتر و دستیاران خیلی حرفه‌ای بودند. از نتیجه کار کاملاً راضی هستم.',
		'rating'          => 4,
		'related_service' => 'wisdom-tooth-extraction',
	),
	array(
		'title'           => 'سارا ن.',
		'slug'            => 'testimonial-sara-n',
		'content'         => 'لمینت دندان‌های جلویی‌ام را در فس‌دنت گذاشتم. رنگ و فرم کاملاً مطابق خواسته‌ام شد. تیم کلینیک در تمام مراحل همراهی کردند و نتیجه نهایی فراتر از انتظارم بود. لبخندم کاملاً تغییر کرده.',
		'rating'          => 4,
		'related_service' => 'dental-laminate',
	),
	array(
		'title'           => 'امیر و.',
		'slug'            => 'testimonial-amir-v',
		'content'         => 'جرم‌گیری و براق کردن دندان‌ها را در کلینیک فس‌دنت انجام دادم. کار خیلی دقیق و بدون درد بود. پرسنل خوش‌برخورد و کلینیک مجهز است. احساس تازگی و تمیزی فوق‌العاده‌ای دارم.',
		'rating'          => 4,
		'related_service' => 'scaling-root-planing',
	),
);

foreach ( $testimonials as $item ) {
	$existing = get_page_by_path( $item['slug'], OBJECT, 'testimonial' );
	if ( $existing ) {
		$GLOBALS['fasdent_demo_ids']['testimonials'][] = $existing->ID;
		continue;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => $item['title'],
			'post_name'    => $item['slug'],
			'post_content' => $item['content'],
			'post_status'  => 'publish',
			'post_type'    => 'testimonial',
			'post_author'  => 1,
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		continue;
	}

	update_post_meta( $post_id, 'rating', (int) $item['rating'] );
	update_post_meta( $post_id, 'related_service', $item['related_service'] );

	$GLOBALS['fasdent_demo_ids']['testimonials'][] = $post_id;
}
