<?php
/**
 * Fasdent Demo — doctor CPT posts
 * Real data only: دکتر کیوان علی‌پسندی (Implantologist)
 */
if ( ! defined( 'FASDENT_DEMO_IMPORT' ) ) {
	exit;
}

if ( ! isset( $GLOBALS['fasdent_demo_ids'] ) ) {
	$GLOBALS['fasdent_demo_ids'] = array();
}
$GLOBALS['fasdent_demo_ids']['doctors'] = array();

$doctors = array(
	array(
		'title'   => 'دکتر کیوان علی‌پسندی',
		'slug'    => 'dr-keyvan-alipasandi',
		'excerpt' => 'دکتری حرفه‌ای (ایمپلنتولوژیست) با بیش از ۱۰ سال سابقه تخصصی. شماره نظام پزشکی ۱۹۱۷۴۰. متخصص ایمپلنت با برندهای معتبر Bego، Megagen، Straumann، Sic و 3zahn.',
		'content' => '<p>دکتر کیوان علی‌پسندی، دکتری حرفه‌ای دندانپزشکی و ایمپلنتولوژیست با بیش از ۱۰ سال سابقه بالینی، مسئول کلینیک دندانپزشکی فس‌دنت است.</p>
<p>ایشان با شماره نظام پزشکی <strong>۱۹۱۷۴۰</strong> فعالیت می‌کنند و تمرکز اصلی‌شان بر ایمپلنتولوژی پیشرفته، جراحی‌های دهان و فک و بازسازی لبخند بیماران است. در کلینیک فس‌دنت از معتبرترین برندهای جهانی ایمپلنت شامل <strong>Bego</strong>، <strong>Megagen</strong>، <strong>Straumann</strong>، <strong>Sic</strong> و <strong>3zahn</strong> استفاده می‌شود تا نتایج ماندگار، ایمن و طبیعی برای بیماران فراهم شود.</p>
<p>رویکرد دکتر علی‌پسندی مبتنی بر برنامه‌ریزی دقیق، استفاده از مواد باکیفیت، تکنیک‌های کم‌تهاجمی و پیگیری منظم پس از درمان است. ایشان زمان کافی برای مشاوره اختصاص می‌دهند تا هر بیمار به طور کامل از مراحل درمان، مراقبت‌های بعدی و انتظارات واقعی آگاه شود.</p>
<p>کلینیک فس‌دنت با فضایی مدرن، آرام و مجهز (اتاق‌های درمان پیشرفته، پذیرش شیک و فضای انتظار راحت) تجربه‌ای حرفه‌ای و بدون استرس برای بیماران فراهم می‌کند. ساعات کاری کلینیک از <strong>ساعت ۱۱ صبح تا ۱۹ شب</strong> است.</p>
<p>برای ارتباط مستقیم:</p>
<ul>
<li>تلفن / واتس‌اپ: <a href="tel:+989201441469">+98 920 144 1469</a></li>
<li>ایمیل: <a href="mailto:Dr.keyvan.alipasandii@gmail.com">Dr.keyvan.alipasandii@gmail.com</a></li>
<li>اینستاگرام: <a href="https://instagram.com/Dr.keyvan_alipasandi" target="_blank" rel="noopener">@Dr.keyvan_alipasandi</a> و <a href="https://instagram.com/Fasdent.clinic" target="_blank" rel="noopener">@Fasdent.clinic</a></li>
</ul>
<p>هدف دکتر علی‌پسندی بازگرداندن عملکرد و زیبایی لبخند بیماران با بالاترین استانداردهای علمی و کمترین دوره نقاهت ممکن است.</p>',
		'meta'    => array(
			'doctor_title'     => 'دکتری حرفه‌ای (ایمپلنتولوژیست)',
			'doctor_education' => "دکتری حرفه‌ای دندانپزشکی\nتخصص ایمپلنتولوژی",
			'doctor_license'   => '۱۹۱۷۴۰',
			'doctor_years'     => '۱۰+',
			'doctor_brands'    => 'Bego, Megagen, Straumann, Sic, 3zahn',
		),
	),
);

foreach ( $doctors as $doctor ) {
	$existing = get_page_by_path( $doctor['slug'], OBJECT, 'doctor' );
	if ( $existing ) {
		// Update existing with real data
		wp_update_post( array(
			'ID'           => $existing->ID,
			'post_title'   => $doctor['title'],
			'post_content' => $doctor['content'],
			'post_excerpt' => $doctor['excerpt'],
		) );
		foreach ( $doctor['meta'] as $key => $value ) {
			update_post_meta( $existing->ID, $key, $value );
		}
		$GLOBALS['fasdent_demo_ids']['doctors'][] = $existing->ID;
		continue;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => $doctor['title'],
			'post_name'    => $doctor['slug'],
			'post_content' => $doctor['content'],
			'post_excerpt' => $doctor['excerpt'],
			'post_status'  => 'publish',
			'post_type'    => 'doctor',
			'post_author'  => 1,
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		continue;
	}

	foreach ( $doctor['meta'] as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}

	$GLOBALS['fasdent_demo_ids']['doctors'][] = $post_id;
}
