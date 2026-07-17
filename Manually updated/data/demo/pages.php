<?php
/**
 * Fasdent Demo — standard WordPress Pages
 */
if ( ! defined( 'FASDENT_DEMO_IMPORT' ) ) {
	exit;
}

if ( ! isset( $GLOBALS['fasdent_demo_ids'] ) ) {
	$GLOBALS['fasdent_demo_ids'] = array();
}
$GLOBALS['fasdent_demo_ids']['pages'] = array();

$pages = array(
	// a) HOME
	array(
		'title'    => 'صفحه اصلی',
		'slug'     => 'home',
		'content'  => '',
		'template' => '',
		'meta'     => array(),
		'after'    => function( $id ) {
			update_option( 'page_on_front', $id );
			update_option( 'show_on_front', 'page' );
		},
	),
	// b) BLOG
	array(
		'title'    => 'مقالات دندانپزشکی',
		'slug'     => 'blog',
		'content'  => '',
		'template' => '',
		'meta'     => array(),
		'after'    => function( $id ) {
			update_option( 'page_for_posts', $id );
		},
	),
	// c) APPOINTMENT
	array(
		'title'    => 'رزرو نوبت آنلاین',
		'slug'     => 'appointment',
		'content'  => '',
		'template' => 'page-templates/appointment.php',
		'meta'     => array(),
	),
	// d) CONTACT
	array(
		'title'    => 'تماس با ما',
		'slug'     => 'contact',
		'content'  => '',
		'template' => 'page-templates/contact.php',
		'meta'     => array(),
	),
	// e) ABOUT
	array(
		'title'    => 'درباره کلینیک فس‌دنت',
		'slug'     => 'about',
		'content'  => '<p>کلینیک دندانپزشکی فس‌دنت با هدف ارائه خدمات پیشرفته و بیمارمحور در حوزه دندانپزشکی تأسیس شده است. ما معتقدیم که هر لبخند داستان منحصر به فردی دارد و وظیفه ما حفظ و زیبایی این داستان با بالاترین استانداردهای علمی و اخلاقی است.</p>
<p>از بدو تأسیس، تیم فس‌دنت بر سه اصل کلیدی تمرکز داشته است: کیفیت بالینی، راحتی بیمار و شفافیت در درمان. دندانپزشکان ما همگی دارای مدارک تخصصی از دانشگاه‌های معتبر داخلی و بین‌المللی هستند و به طور مستمر در دوره‌های آموزشی به‌روز شرکت می‌کنند تا جدیدترین تکنیک‌ها و مواد را در اختیار بیماران قرار دهند.</p>
<p>فضای کلینیک با طراحی مدرن، تجهیزات دیجیتال و رعایت کامل پروتکل‌های استریلیزاسیون، محیطی آرام و ایمن برای تمام سنین فراهم کرده است. از معاینه ساده تا درمان‌های پیچیده ایمپلنت، ارتودنسی و زیبایی، همه خدمات تحت یک سقف و با هماهنگی کامل بین متخصصان ارائه می‌شود.</p>
<p>ماموریت ما بازگرداندن اعتماد به نفس از طریق لبخندی سالم و زیبا است. ارزش‌های ما شامل احترام به بیمار، صداقت در مشاوره، استفاده از مواد باکیفیت و پیگیری پس از درمان می‌باشد. در فس‌دنت هر بیمار یک فرد منحصر به فرد است و طرح درمان کاملاً شخصی‌سازی می‌شود.</p>
<p>ما افتخار می‌کنیم که هزاران بیمار راضی را همراهی کرده‌ایم و همچنان متعهد به ارتقای سطح سلامت دهان و دندان جامعه هستیم. به خانواده بزرگ فس‌دنت خوش آمدید.</p>',
		'template' => 'page-templates/fasdent-page.php',
		'meta'     => array(
			'fasdent_kicker'        => 'کلینیک دندانپزشکی',
			'fasdent_subtitle'      => 'تیم متخصص دندانپزشکان در کنار شما',
			'fasdent_reviewer_name' => 'دکتر کیوان علی‌پسندی',
			'fasdent_review_date'   => '1403/06/01',
		),
	),
	// f) FAQ
	array(
		'title'    => 'سوالات متداول',
		'slug'     => 'faq',
		'content'  => '',
		'template' => 'page-templates/faq.php',
		'meta'     => array(),
	),
	// g) PRICING
	array(
		'title'    => 'تعرفه خدمات',
		'slug'     => 'pricing',
		'content'  => '',
		'template' => 'page-templates/pricing.php',
		'meta'     => array(),
	),
	// h) GALLERY (was missing)
	array(
		'title'    => 'گالری قبل و بعد',
		'slug'     => 'gallery',
		'content'  => '<p>گالری تصاویر قبل و بعد از درمان‌های کلینیک فس‌دنت. نتایج واقعی بیماران با رضایت آن‌ها منتشر شده است.</p>',
		'template' => 'page-templates/gallery.php',
		'meta'     => array(),
	),
	// i) PRIVACY POLICY
	array(
		'title'    => 'سیاست حریم خصوصی',
		'slug'     => 'privacy-policy',
		'content'  => '<p>کلینیک دندانپزشکی فس‌دنت متعهد به حفظ حریم خصوصی و اطلاعات شخصی بیماران خود است. اطلاعاتی که شما در هنگام رزرو نوبت، تکمیل فرم‌ها یا مراجعه حضوری در اختیار ما قرار می‌دهید، صرفاً برای ارائه خدمات درمانی، پیگیری درمان و ارتباط با شما استفاده می‌شود.</p>
<p>ما اطلاعات پزشکی و هویتی شما را بدون رضایت صریح در اختیار هیچ شخص یا سازمان ثالثی قرار نمی‌دهیم، مگر در موارد الزام قانونی. داده‌های شما روی سرورهای امن نگهداری می‌شود و دسترسی به آن‌ها محدود به پرسنل مجاز کلینیک است.</p>
<p>شما حق دارید در هر زمان درخواست مشاهده، اصلاح یا حذف اطلاعات خود را مطرح کنید. برای هرگونه سوال در مورد سیاست حریم خصوصی می‌توانید با شماره ۰۹۲۰۱۴۴۱۴۶۹ یا ایمیل info@fasdent.ir تماس بگیرید. استفاده از خدمات کلینیک به معنای پذیرش این سیاست است.</p>',
		'template' => 'page-templates/privacy-policy.php',
		'meta'     => array(),
	),
	// j) MEDICAL DISCLAIMER
	array(
		'title'    => 'سلب مسئولیت پزشکی',
		'slug'     => 'medical-disclaimer',
		'content'  => '<p>محتوای موجود در وب‌سایت کلینیک دندانپزشکی فس‌دنت صرفاً جنبه اطلاع‌رسانی عمومی دارد و جایگزین مشاوره، تشخیص یا درمان تخصصی دندانپزشکی نیست. هرگونه تصمیم‌گیری درمانی باید پس از معاینه حضوری توسط دندانپزشک واجد شرایط انجام شود.</p>
<p>نتایج درمان‌ها ممکن است در افراد مختلف متفاوت باشد و کلینیک هیچ تضمینی در مورد نتایج خاص ارائه نمی‌دهد. تصاویر و توضیحات موجود در سایت نمونه‌هایی از موارد واقعی هستند اما نتیجه برای هر بیمار منحصر به فرد است. در صورت بروز هرگونه مشکل اورژانسی دندانی، فوراً به مراکز درمانی مراجعه کنید.</p>',
		'template' => 'page-templates/medical-disclaimer.php',
		'meta'     => array(),
	),
	// k) CANCELLATION POLICY
	array(
		'title'    => 'سیاست لغو نوبت',
		'slug'     => 'cancellation-policy',
		'content'  => '<p>کلینیک دندانپزشکی فس‌دنت برای احترام به وقت بیماران و پزشکان، سیاست مشخصی برای لغو و تغییر نوبت دارد. در صورت نیاز به لغو یا جابجایی نوبت، لطفاً حداقل ۲۴ ساعت قبل از موعد مقرر از طریق تلفن، واتساپ یا پنل رزرو آنلاین اطلاع دهید.</p>
<p>لغو دیرهنگام یا عدم حضور بدون اطلاع قبلی ممکن است منجر به محدودیت در رزرو نوبت‌های بعدی شود. در موارد اضطراری پزشکی، با ارائه مدرک معتبر، استثنا قائل خواهیم شد. هدف ما مدیریت بهینه زمان و ارائه خدمات به موقع به همه بیماران است. از همکاری شما سپاسگزاریم.</p>',
		'template' => 'page-templates/cancellation-policy.php',
		'meta'     => array(),
	),
	// l) PATIENT RIGHTS
	array(
		'title'    => 'حقوق بیمار',
		'slug'     => 'patient-rights',
		'content'  => '<p>در کلینیک دندانپزشکی فس‌دنت، احترام به حقوق بیماران یکی از اصول بنیادین ماست. هر بیمار حق دارد اطلاعات کامل و قابل فهم در مورد تشخیص، گزینه‌های درمانی، مزایا، خطرات و هزینه‌ها دریافت کند و با آگاهی کامل تصمیم بگیرد.</p>
<p>بیماران حق دارند در مورد درمان خود سوال بپرسند، نظر دوم دریافت کنند و در هر مرحله از درمان رضایت یا عدم رضایت خود را اعلام نمایند. حریم خصوصی، محرمانگی اطلاعات پزشکی و رفتار محترمانه از سوی تمام پرسنل تضمین می‌شود. همچنین بیماران حق دارند از محیط ایمن، تمیز و بدون تبعیض بهره‌مند شوند.</p>
<p>ما متعهد هستیم که صدای بیمار را بشنویم و هرگونه نگرانی یا شکایت را با دقت بررسی کنیم. هدف ما ایجاد رابطه‌ای مبتنی بر اعتماد و همکاری متقابل است تا بهترین نتیجه درمانی حاصل شود.</p>',
		'template' => 'page-templates/patient-rights.php',
		'meta'     => array(),
	),
	// m) SITEMAP
	array(
		'title'    => 'نقشه سایت',
		'slug'     => 'sitemap',
		'content'  => '',
		'template' => 'page-templates/sitemap.php',
		'meta'     => array(),
	),
	// n) KNOWLEDGE BASE
	array(
		'title'    => 'پایگاه دانش',
		'slug'     => 'knowledge-base',
		'content'  => '',
		'template' => 'page-templates/knowledge-base.php',
		'meta'     => array(),
	),
);

foreach ( $pages as $page ) {
	$existing = get_page_by_path( $page['slug'] );
	if ( $existing ) {
		$GLOBALS['fasdent_demo_ids']['pages'][ $page['slug'] ] = $existing->ID;
		if ( ! empty( $page['after'] ) && is_callable( $page['after'] ) ) {
			call_user_func( $page['after'], $existing->ID );
		}
		continue;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => $page['title'],
			'post_name'    => $page['slug'],
			'post_content' => $page['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => 1,
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		continue;
	}

	if ( ! empty( $page['template'] ) ) {
		update_post_meta( $post_id, '_wp_page_template', $page['template'] );
	}

	foreach ( $page['meta'] as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}

	if ( ! empty( $page['after'] ) && is_callable( $page['after'] ) ) {
		call_user_func( $page['after'], $post_id );
	}

	$GLOBALS['fasdent_demo_ids']['pages'][ $page['slug'] ] = $post_id;
}
