<?php
/**
 * تک مطلب بلاگ — Fasdent
 *
 * این نسخه شامل ماژول‌های اعتماد و انطباق (نوار اعتماد، یادداشت سردبیر،
 * جعبه بازبینی بالینی، پاسخ کوتاه، فهرست مطالب با پرش سریع، دیتای ساختاریافته
 * Schema.org، و یادآوری‌های آموزشی/اورژانسی/حریم‌خصوصی) است.
 *
 * ------------------------------------------------------------------
 * فیلدهای سفارشی مورد استفاده (اختیاری — هرکدام نبود، بخش مربوطه نمایش
 * داده نمی‌شود و صفحه خطا نمی‌دهد):
 *
 *   ‌روی نوشته (get_post_meta):
 *     quick_answer            پاسخ کوتاه ۴۰ تا ۷۰ کلمه‌ای
 *     reviewer_id              شناسه کاربر بازبین (اگر بازبین کاربر سایت باشد)
 *     reviewer_name             نام بازبین (در صورت نبود reviewer_id)
 *     reviewer_credentials      مدرک/تخصص بازبین
 *     reviewer_license_state    استان/محل مجوز بازبین
 *     reviewer_scope            حوزه بازبینی، مثلاً «دقت بالینی»
 *     reviewer_url              لینک پروفایل کامل بازبین
 *     clinical_review_date      تاریخ آخرین بازبینی بالینی
 *     review_status             reviewed | pending | annual_refresh
 *     emergency_disclaimer      '1' برای نمایش یادآوری اورژانسی
 *     results_may_vary          '1' برای نمایش یادآوری «نتایج متفاوت است»
 *     accessibility_checked     '1' برای نشان «بررسی دسترس‌پذیری»
 *     citations                 آرایه‌ای از ['label'=>'', 'url'=>'']
 *     fasdent_faqs               آرایه‌ای از ['question'=>'', 'answer'=>'']
 *     faq_schema_enabled        '1' برای خروجی Schema سوالات متداول
 *     related_posts              آرایه‌ای از شناسه نوشته‌های مرتبط
 *     primary_cta_text / primary_cta_url   دکمه فراخوان نرم داخل مطلب
 *     meta_description          توضیح متا برای Schema (در نبود آن از خلاصه استفاده می‌شود)
 *
 *   روی کاربر/نویسنده (get_the_author_meta):
 *     credentials, dental_license_number, dental_license_state
 *
 * ------------------------------------------------------------------
 * برای فعال شدن واکنش‌های AJAX (دکمه‌های «مفید/ممنون/دقیق»)، قطعه زیر را
 * در functions.php قرار دهید. این تابع در همین فایل تعریف نشده، چون
 * درخواست‌های admin-ajax.php هرگز single.php را بارگذاری نمی‌کنند:
 *
 * add_action( 'wp_ajax_fasdent_react', 'fasdent_handle_reaction' );
 * add_action( 'wp_ajax_nopriv_fasdent_react', 'fasdent_handle_reaction' );
 * function fasdent_handle_reaction() {
 *     $post_id  = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
 *     $reaction = isset( $_POST['reaction'] ) ? sanitize_key( wp_unslash( $_POST['reaction'] ) ) : '';
 *     if ( ! $post_id ) {
 *         wp_send_json_error( array( 'message' => 'invalid_post' ), 400 );
 *     }
 *     check_ajax_referer( 'fasdent_react_' . $post_id, 'nonce' );
 *     $allowed = array( 'helpful', 'thanks', 'accurate' );
 *     if ( ! in_array( $reaction, $allowed, true ) ) {
 *         wp_send_json_error( array( 'message' => 'invalid_reaction' ), 400 );
 *     }
 *     $meta_key = '_reaction_' . $reaction;
 *     $count    = (int) get_post_meta( $post_id, $meta_key, true );
 *     $count++;
 *     update_post_meta( $post_id, $meta_key, $count );
 *     wp_send_json_success( array( 'count' => $count ) );
 * }
 *
 * @package Fasdent
 */

// --- توابع کمکی (فقط در صورت نبود قبلی تعریف می‌شوند) --------------------

if ( ! function_exists( 'fasdent_meta_true' ) ) {
	/**
	 * بررسی می‌کند مقدار یک فیلد سفارشی بولی («۱»، true، «yes») هست یا نه.
	 */
	function fasdent_meta_true( $post_id, $key ) {
		$value = get_post_meta( $post_id, $key, true );
		return in_array( $value, array( '1', 1, true, 'true', 'yes' ), true );
	}
}

if ( ! function_exists( 'fasdent_slugify_heading' ) ) {
	/**
	 * ساخت یک شناسه (id) امن و خوانا از متن یک تیتر، با پشتیبانی از فارسی.
	 */
	function fasdent_slugify_heading( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		$text = trim( $text );
		$text = preg_replace( '/\s+/u', '-', $text );
		$text = preg_replace( '/[^\p{L}\p{N}\-]/u', '', $text );
		$text = trim( $text, '-' );
		return ( '' !== $text ) ? $text : 'bakhsh';
	}
}

if ( ! function_exists( 'fasdent_inject_heading_ids' ) ) {
	/**
	 * به تمام تیترهای H2/H3 داخل محتوا یک id یکتا می‌دهد و فهرست پرش (TOC)
	 * را برمی‌گرداند. اگر DOMDocument در دسترس نباشد یا محتوا خالی باشد،
	 * محتوای اصلی بدون تغییر و یک TOC خالی برگردانده می‌شود (بدون خطا).
	 *
	 * @return array [ string $html, array $toc_items ]
	 */
	function fasdent_inject_heading_ids( $html ) {
		$toc = array();

		if ( '' === trim( (string) $html ) || ! class_exists( 'DOMDocument' ) ) {
			return array( $html, $toc );
		}

		$dom              = new DOMDocument();
		$previous_setting = libxml_use_internal_errors( true );

		$loaded = $dom->loadHTML(
			'<?xml encoding="utf-8"?><div id="fasdent-toc-root">' . $html . '</div>',
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
		);

		libxml_clear_errors();
		libxml_use_internal_errors( $previous_setting );

		if ( ! $loaded ) {
			return array( $html, $toc );
		}

		$xpath    = new DOMXPath( $dom );
		$headings = $xpath->query( '//h2 | //h3' );
		$used_ids = array();

		foreach ( $headings as $heading ) {
			$text = trim( $heading->textContent );
			if ( '' === $text ) {
				continue;
			}

			$id = $heading->getAttribute( 'id' );
			if ( '' === $id ) {
				$id = fasdent_slugify_heading( $text );
			}

			$base_id = $id;
			$suffix  = 2;
			while ( in_array( $id, $used_ids, true ) ) {
				$id = $base_id . '-' . $suffix;
				++$suffix;
			}
			$used_ids[] = $id;

			$heading->setAttribute( 'id', $id );
			// اجازه می‌دهد بعد از کلیک روی لینک پرش، فوکوس به تیتر منتقل شود
			// (مهم برای کاربران صفحه‌خوان و ناوبری با صفحه‌کلید).
			$heading->setAttribute( 'tabindex', '-1' );

			$toc[] = array(
				'id'    => $id,
				'text'  => $text,
				'level' => ( 'h3' === strtolower( $heading->nodeName ) ) ? 3 : 2,
			);
		}

		$root_nodes = $xpath->query( '//div[@id="fasdent-toc-root"]' );
		if ( 0 === $root_nodes->length ) {
			return array( $html, $toc );
		}

		$output = '';
		foreach ( $root_nodes->item( 0 )->childNodes as $child_node ) {
			$output .= $dom->saveHTML( $child_node );
		}

		return array( $output, $toc );
	}
}

if ( ! function_exists( 'fasdent_render_disclaimer' ) ) {
	/**
	 * چاپ یکی از بلوک‌های یادآوری استاندارد (آموزشی/اورژانسی/نتایج/حریم‌خصوصی).
	 *
	 * @param string $type       یکی از: educational | emergency | results | privacy.
	 * @param string $extra_html HTML اضافه‌ی از پیش امن‌سازی‌شده (اختیاری).
	 */
	function fasdent_render_disclaimer( $type, $extra_html = '' ) {
		$map = array(
			'educational' => array(
				'icon'  => 'fa-solid fa-graduation-cap',
				'label' => __( 'یادآوری آموزشی', 'fasdent' ),
				'text'  => __( 'محتوای این صفحه صرفاً جنبه آموزشی و اطلاع‌رسانی دارد و جایگزین توصیه، تشخیص یا درمان پزشکی تخصصی نیست. همیشه برای تشخیص و درمان با دندان‌پزشک خود مشورت کنید.', 'fasdent' ),
			),
			'emergency'   => array(
				'icon'  => 'fa-solid fa-triangle-exclamation',
				'label' => __( 'توجه اورژانسی', 'fasdent' ),
				'text'  => __( 'از این صفحه برای مدیریت شرایط اورژانسی دندان‌پزشکی استفاده نکنید. در موارد فوری با اورژانس تماس بگیرید یا سریعاً به نزدیک‌ترین مرکز درمانی مراجعه کنید.', 'fasdent' ),
			),
			'results'     => array(
				'icon'  => 'fa-solid fa-chart-line',
				'label' => __( 'درباره نتایج درمان', 'fasdent' ),
				'text'  => __( 'نتایج درمان بسته به شرایط هر بیمار متفاوت است و هیچ نتیجه مشخصی تضمین نمی‌شود.', 'fasdent' ),
			),
			'privacy'     => array(
				'icon'  => 'fa-solid fa-lock',
				'label' => __( 'یادآوری حریم خصوصی', 'fasdent' ),
				'text'  => __( 'لطفاً اطلاعات حساس، محرمانه یا فوری سلامتی خود را از طریق فرم‌های غیرامن (مانند بخش نظرات) ارسال نکنید.', 'fasdent' ),
			),
		);

		if ( empty( $map[ $type ] ) ) {
			return;
		}

		$data = $map[ $type ];
		?>
		<div class="disclaimer-item disclaimer-item--<?php echo esc_attr( $type ); ?>" role="note">
			<i class="<?php echo esc_attr( $data['icon'] ); ?>" aria-hidden="true"></i>
			<p>
				<strong><?php echo esc_html( $data['label'] ); ?>:</strong>
				<?php echo esc_html( $data['text'] ); ?>
				<?php if ( $extra_html ) : ?>
					<?php echo wp_kses_post( $extra_html ); ?>
				<?php endif; ?>
			</p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'fasdent_get_site_logo_url' ) ) {
	/**
	 * آدرس لوگوی سایت برای استفاده در Schema (اگر تنظیم نشده باشد، رشته خالی).
	 */
	function fasdent_get_site_logo_url() {
		$logo_id = get_theme_mod( 'custom_logo' );
		if ( ! $logo_id ) {
			return '';
		}
		$src = wp_get_attachment_image_src( $logo_id, 'full' );
		return $src ? $src[0] : '';
	}
}

get_header();

if ( ! have_posts() ) {
	get_footer();
	return;
}

while ( have_posts() ) :
	the_post();

	$post_id       = get_the_ID();
	$reading_time  = fasdent_reading_time();

	// --- داده‌های ماژول‌های اعتماد و انطباق ------------------------------

	$reviewer_id = (int) get_post_meta( $post_id, 'reviewer_id', true );
	if ( $reviewer_id ) {
		$reviewer_name        = get_the_author_meta( 'display_name', $reviewer_id );
		$reviewer_credentials = get_the_author_meta( 'credentials', $reviewer_id );
		$reviewer_url         = get_author_posts_url( $reviewer_id );
	} else {
		$reviewer_name        = get_post_meta( $post_id, 'reviewer_name', true );
		$reviewer_credentials = get_post_meta( $post_id, 'reviewer_credentials', true );
		$reviewer_url         = get_post_meta( $post_id, 'reviewer_url', true );
	}
	$reviewer_license_state = get_post_meta( $post_id, 'reviewer_license_state', true );
	$reviewer_scope         = get_post_meta( $post_id, 'reviewer_scope', true );
	$clinical_review_date   = get_post_meta( $post_id, 'clinical_review_date', true );
	$review_status          = get_post_meta( $post_id, 'review_status', true );

	$emergency_disclaimer  = fasdent_meta_true( $post_id, 'emergency_disclaimer' );
	$results_may_vary      = fasdent_meta_true( $post_id, 'results_may_vary' );
	$accessibility_checked = fasdent_meta_true( $post_id, 'accessibility_checked' );

	$citations = get_post_meta( $post_id, 'citations', true );
	$citations = is_array( $citations ) ? $citations : array();

	$quick_answer = get_post_meta( $post_id, 'quick_answer', true );

	// --- نوار اعتماد -----------------------------------------------------

	$trust_badges = array();
	if ( $reviewer_name && 'reviewed' === $review_status ) {
		$trust_badges[] = array(
			'icon'  => 'fa-solid fa-user-doctor',
			'label' => __( 'بازبینی بالینی شده', 'fasdent' ),
		);
	}
	$modified_date = get_the_modified_date();
	if ( $modified_date && $modified_date !== get_the_date() ) {
		$trust_badges[] = array(
			'icon'  => 'fa-regular fa-calendar-check',
			'label' => sprintf(
				/* translators: %s: modified date */
				__( 'بروزرسانی %s', 'fasdent' ),
				$modified_date
			),
		);
	}
	if ( ! empty( $citations ) ) {
		$trust_badges[] = array(
			'icon'  => 'fa-solid fa-book',
			'label' => __( 'منابع‌دار', 'fasdent' ),
		);
	}
	if ( $emergency_disclaimer ) {
		$trust_badges[] = array(
			'icon'  => 'fa-solid fa-kit-medical',
			'label' => __( 'راهنمای اورژانسی گنجانده شده', 'fasdent' ),
		);
	}
	if ( $accessibility_checked ) {
		$trust_badges[] = array(
			'icon'  => 'fa-solid fa-universal-access',
			'label' => __( 'دسترس‌پذیری بررسی شده', 'fasdent' ),
		);
	}
	$trust_badges[] = array(
		'icon'  => 'fa-solid fa-hand-holding-medical',
		'label' => __( 'راهنمای بیمار', 'fasdent' ),
	);

	// --- فهرست پرش سریع (TOC) از تیترهای H2/H3 محتوا ----------------------

	$raw_content              = apply_filters( 'the_content', get_the_content() );
	list( $post_content, $toc_items ) = fasdent_inject_heading_ids( $raw_content );

	// --- دیتای ساختاریافته (Schema.org) -----------------------------------

	$schema_images = array();
	if ( has_post_thumbnail( $post_id ) ) {
		$thumb_id = get_post_thumbnail_id( $post_id );
		foreach ( array( 'fasdent-hero', 'large', 'medium' ) as $image_size ) {
			$image_src = wp_get_attachment_image_src( $thumb_id, $image_size );
			if ( $image_src ) {
				$schema_images[] = $image_src[0];
			}
		}
	}

	$meta_description   = get_post_meta( $post_id, 'meta_description', true );
	$schema_description = $meta_description ? $meta_description : wp_strip_all_tags( get_the_excerpt( $post_id ) );

	$publisher = array(
		'@type' => 'Organization',
		'name'  => get_bloginfo( 'name' ),
	);
	$logo_url = fasdent_get_site_logo_url();
	if ( $logo_url ) {
		$publisher['logo'] = array(
			'@type' => 'ImageObject',
			'url'   => $logo_url,
		);
	}

	$article_schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'headline'         => get_the_title( $post_id ),
		'description'      => $schema_description,
		'datePublished'    => get_the_date( 'c', $post_id ),
		'dateModified'     => get_the_modified_date( 'c', $post_id ),
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => get_permalink( $post_id ),
		),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author(),
			'url'   => get_author_posts_url( get_the_author_meta( 'ID' ) ),
		),
		'publisher'        => $publisher,
	);
	if ( ! empty( $schema_images ) ) {
		$article_schema['image'] = $schema_images;
	}
	if ( $reviewer_name ) {
		$article_schema['reviewedBy'] = array(
			'@type' => 'Person',
			'name'  => $reviewer_name,
		);
		if ( $reviewer_url ) {
			$article_schema['reviewedBy']['url'] = $reviewer_url;
		}
	}

	// Breadcrumb schema.
	$breadcrumb_list = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => __( 'خانه', 'fasdent' ),
			'item'     => home_url( '/' ),
		),
	);
	$blog_page_id = (int) get_option( 'page_for_posts' );
	if ( $blog_page_id ) {
		$breadcrumb_list[] = array(
			'@type'    => 'ListItem',
			'position' => count( $breadcrumb_list ) + 1,
			'name'     => get_the_title( $blog_page_id ),
			'item'     => get_permalink( $blog_page_id ),
		);
	}
	$post_categories = get_the_category();
	if ( ! empty( $post_categories ) ) {
		$breadcrumb_list[] = array(
			'@type'    => 'ListItem',
			'position' => count( $breadcrumb_list ) + 1,
			'name'     => $post_categories[0]->name,
			'item'     => get_category_link( $post_categories[0] ),
		);
	}
	$breadcrumb_list[] = array(
		'@type'    => 'ListItem',
		'position' => count( $breadcrumb_list ) + 1,
		'name'     => get_the_title( $post_id ),
		'item'     => get_permalink( $post_id ),
	);
	$breadcrumb_schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $breadcrumb_list,
	);

	// FAQ schema (فقط اگر صریحاً فعال شده باشد).
	$faqs                = get_post_meta( $post_id, 'fasdent_faqs', true );
	$faqs                = is_array( $faqs ) ? $faqs : array();
	$faq_schema_enabled  = fasdent_meta_true( $post_id, 'faq_schema_enabled' );
	$faq_schema          = null;
	if ( $faq_schema_enabled && ! empty( $faqs ) ) {
		$faq_items = array();
		foreach ( $faqs as $faq ) {
			if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) {
				continue;
			}
			$faq_items[] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $faq['question'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $faq['answer'] ),
				),
			);
		}
		if ( ! empty( $faq_items ) ) {
			$faq_schema = array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $faq_items,
			);
		}
	}

	$json_flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG;
	?>

<div class="single-post" data-post-id="<?php echo esc_attr( (string) $post_id ); ?>">

	<?php // دیتای ساختاریافته — می‌تواند در body قرار بگیرد (مورد تایید گوگل). ?>
	<script type="application/ld+json"><?php echo wp_json_encode( $article_schema, $json_flags ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $breadcrumb_schema, $json_flags ); ?></script>
	<?php if ( $faq_schema ) : ?>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, $json_flags ); ?></script>
	<?php endif; ?>

	<!-- نوار پیشرفت مطالعه -->
	<div class="reading-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="<?php esc_attr_e( 'پیشرفت مطالعه', 'fasdent' ); ?>"></div>

	<div class="container single-post__layout">
		<article class="single-post__main">

			<!-- هدر مطلب -->
			<header class="post-header">
				<?php if ( $post_categories ) : ?>
				<div class="post-cats">
					<?php foreach ( $post_categories as $cat ) : ?>
					<a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="post-cat-badge"><?php echo esc_html( $cat->name ); ?></a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

				<h1><?php the_title(); ?></h1>

				<div class="post-meta-bar">
					<span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( get_the_date() ); ?></span>
					<?php if ( get_the_modified_date() !== get_the_date() ) : ?>
					<span><i class="fa-solid fa-pen" aria-hidden="true"></i> <?php esc_html_e( 'بروزرسانی:', 'fasdent' ); ?> <?php echo esc_html( get_the_modified_date() ); ?></span>
					<?php endif; ?>
					<?php if ( $reading_time ) : ?>
					<span><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo esc_html( $reading_time . ' دقیقه مطالعه' ); ?></span>
					<?php endif; ?>
					<span><i class="fa-regular fa-user" aria-hidden="true"></i> <?php the_author(); ?></span>
				</div>

				<p class="post-micro-disclaimer">
					<i class="fa-solid fa-circle-info" aria-hidden="true"></i>
					<?php esc_html_e( 'صرفاً جنبه آموزشی دارد و جایگزین معاینه دندان‌پزشک نیست.', 'fasdent' ); ?>
				</p>

				<?php if ( ! empty( $trust_badges ) ) : ?>
				<ul class="trust-strip" aria-label="<?php esc_attr_e( 'نشان‌های اعتماد این مطلب', 'fasdent' ); ?>">
					<?php foreach ( $trust_badges as $badge ) : ?>
					<li class="trust-badge">
						<i class="<?php echo esc_attr( $badge['icon'] ); ?>" aria-hidden="true"></i>
						<span><?php echo esc_html( $badge['label'] ); ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>

				<?php if ( has_post_thumbnail() ) : ?>
				<div class="post-featured-image"><?php the_post_thumbnail( 'fasdent-hero', array( 'loading' => 'eager' ) ); ?></div>
				<?php endif; ?>
			</header>

			<?php if ( $reviewer_name ) :
				$reviewer_line = $reviewer_credentials ? $reviewer_name . '، ' . $reviewer_credentials : $reviewer_name;
			?>
			<div class="editor-note">
				<p>
					<strong><?php esc_html_e( 'یادداشت سردبیر:', 'fasdent' ); ?></strong>
					<?php if ( $clinical_review_date ) : ?>
						<?php
						printf(
							/* translators: 1: review date, 2: reviewer name and credentials */
							esc_html__( 'این مطلب در تاریخ %1$s از نظر دقت بالینی توسط %2$s بازبینی شده است.', 'fasdent' ),
							esc_html( $clinical_review_date ),
							esc_html( $reviewer_line )
						);
						?>
					<?php else : ?>
						<?php
						printf(
							/* translators: %s: reviewer name and credentials */
							esc_html__( 'این مطلب از نظر دقت بالینی توسط %s بازبینی شده است.', 'fasdent' ),
							esc_html( $reviewer_line )
						);
						?>
					<?php endif; ?>
					<?php esc_html_e( 'این محتوا صرفاً جنبه آموزشی دارد و جایگزین معاینه، تشخیص یا برنامه درمانی اختصاصی نیست.', 'fasdent' ); ?>
				</p>
			</div>
			<?php endif; ?>

			<!-- نکات کلیدی -->
			<?php get_template_part( 'template-parts/key-takeaways' ); ?>

			<?php if ( $quick_answer ) : ?>
			<div class="quick-answer-box" role="note" aria-label="<?php esc_attr_e( 'پاسخ کوتاه', 'fasdent' ); ?>">
				<h2 class="quick-answer-box__title"><i class="fa-solid fa-bolt" aria-hidden="true"></i> <?php esc_html_e( 'پاسخ کوتاه', 'fasdent' ); ?></h2>
				<p><?php echo wp_kses_post( $quick_answer ); ?></p>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $toc_items ) ) : ?>
			<nav class="post-jump-links" aria-label="<?php esc_attr_e( 'فهرست مطالب', 'fasdent' ); ?>">
				<button type="button" class="jump-links-toggle" aria-expanded="true" aria-controls="jump-links-list">
					<i class="fa-solid fa-list-ul" aria-hidden="true"></i>
					<span><?php esc_html_e( 'فهرست مطالب', 'fasdent' ); ?></span>
					<i class="fa-solid fa-chevron-down jump-links-caret" aria-hidden="true"></i>
				</button>
				<ol id="jump-links-list" class="jump-links-list">
					<?php foreach ( $toc_items as $toc_item ) : ?>
					<li class="jump-links-item<?php echo ( 3 === $toc_item['level'] ) ? ' is-sub' : ''; ?>">
						<a href="#<?php echo esc_attr( $toc_item['id'] ); ?>"><?php echo esc_html( $toc_item['text'] ); ?></a>
					</li>
					<?php endforeach; ?>
				</ol>
			</nav>
			<?php endif; ?>

			<!-- محتوا -->
			<div class="post-content prose">
				<?php echo $post_content; // phpcs:ignore WordPress.Security.EscapeOutput -- already passed through the_content filters, same as the_content(). ?>
			</div>

			<?php if ( $reviewer_name ) : ?>
			<div class="clinical-review-box card">
				<div class="clinical-review-box__icon"><i class="fa-solid fa-stethoscope" aria-hidden="true"></i></div>
				<div class="clinical-review-box__body">
					<p class="clinical-review-box__eyebrow"><?php esc_html_e( 'بازبینی بالینی', 'fasdent' ); ?></p>
					<p class="clinical-review-box__name">
						<?php if ( $reviewer_url ) : ?>
						<a href="<?php echo esc_url( $reviewer_url ); ?>"><?php echo esc_html( $reviewer_name ); ?></a>
						<?php else : ?>
						<?php echo esc_html( $reviewer_name ); ?>
						<?php endif; ?>
						<?php if ( $reviewer_credentials ) : ?>
						<span class="clinical-review-box__credentials"><?php echo esc_html( $reviewer_credentials ); ?></span>
						<?php endif; ?>
					</p>
					<ul class="clinical-review-box__meta">
						<?php if ( $reviewer_license_state ) : ?>
						<li><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i> <?php echo esc_html( $reviewer_license_state ); ?></li>
						<?php endif; ?>
						<?php if ( $clinical_review_date ) : ?>
						<li><i class="fa-regular fa-calendar-check" aria-hidden="true"></i> <?php echo esc_html( $clinical_review_date ); ?></li>
						<?php endif; ?>
						<?php if ( $reviewer_scope ) : ?>
						<li><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> <?php echo esc_html( $reviewer_scope ); ?></li>
						<?php endif; ?>
					</ul>
					<?php if ( $reviewer_url ) : ?>
					<a class="clinical-review-box__link" href="<?php echo esc_url( $reviewer_url ); ?>">
						<?php esc_html_e( 'مشاهده پروفایل کامل بازبین', 'fasdent' ); ?>
						<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
					</a>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $faqs ) ) : ?>
			<section class="post-faq" aria-labelledby="post-faq-heading">
				<h2 id="post-faq-heading"><?php esc_html_e( 'سوالات متداول', 'fasdent' ); ?></h2>
				<div class="faq-list">
					<?php foreach ( $faqs as $faq_index => $faq ) :
						if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) {
							continue;
						}
					?>
					<details class="faq-item">
						<summary class="faq-item__question"><?php echo esc_html( $faq['question'] ); ?></summary>
						<div class="faq-item__answer"><?php echo wp_kses_post( wpautop( $faq['answer'] ) ); ?></div>
					</details>
					<?php endforeach; ?>
				</div>
			</section>
			<?php endif; ?>

			<?php
			$related_ids = get_post_meta( $post_id, 'related_posts', true );
			$related_ids = is_array( $related_ids ) ? array_map( 'absint', $related_ids ) : array();
			if ( ! empty( $related_ids ) ) :
			?>
			<section class="related-resources" aria-labelledby="related-resources-heading">
				<h2 id="related-resources-heading"><?php esc_html_e( 'مطالب مرتبط', 'fasdent' ); ?></h2>
				<div class="related-resources__grid">
					<?php foreach ( $related_ids as $related_id ) :
						if ( ! $related_id || 'publish' !== get_post_status( $related_id ) ) {
							continue;
						}
					?>
					<a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>" class="card related-resources__item">
						<?php if ( has_post_thumbnail( $related_id ) ) : ?>
						<?php echo get_the_post_thumbnail( $related_id, 'medium', array( 'loading' => 'lazy', 'alt' => get_the_title( $related_id ) ) ); ?>
						<?php endif; ?>
						<span class="related-resources__title"><?php echo esc_html( get_the_title( $related_id ) ); ?></span>
					</a>
					<?php endforeach; ?>
				</div>
			</section>
			<?php endif; ?>

			<?php
			$cta_text = get_post_meta( $post_id, 'primary_cta_text', true );
			$cta_url  = get_post_meta( $post_id, 'primary_cta_url', true );
			if ( $cta_text && $cta_url ) :
			?>
			<div class="post-soft-cta card">
				<p><?php echo esc_html( $cta_text ); ?></p>
				<a href="<?php echo esc_url( $cta_url ); ?>" class="btn btn-primary"><?php esc_html_e( 'رزرو نوبت', 'fasdent' ); ?></a>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $citations ) ) : ?>
			<section class="post-sources" aria-labelledby="post-sources-heading">
				<h2 id="post-sources-heading"><?php esc_html_e( 'منابع', 'fasdent' ); ?></h2>
				<ul>
					<?php foreach ( $citations as $citation ) :
						if ( empty( $citation['label'] ) ) {
							continue;
						}
					?>
					<li>
						<?php if ( ! empty( $citation['url'] ) ) : ?>
						<a href="<?php echo esc_url( $citation['url'] ); ?>" rel="nofollow noopener" target="_blank">
							<?php echo esc_html( $citation['label'] ); ?>
							<span class="screen-reader-text">(<?php esc_html_e( 'در تب جدید باز می‌شود', 'fasdent' ); ?>)</span>
						</a>
						<?php else : ?>
						<?php echo esc_html( $citation['label'] ); ?>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</section>
			<?php endif; ?>

			<div class="disclaimer-stack" aria-label="<?php esc_attr_e( 'یادآوری‌های مهم', 'fasdent' ); ?>">
				<?php
				fasdent_render_disclaimer( 'educational' );
				if ( $results_may_vary ) {
					fasdent_render_disclaimer( 'results' );
				}
				if ( $emergency_disclaimer ) {
					fasdent_render_disclaimer( 'emergency' );
				}
				?>
			</div>

			<p class="post-copyright">
				<?php
				printf(
					/* translators: 1: current year, 2: site name */
					esc_html__( '© %1$s %2$s. کلیه حقوق این محتوا محفوظ است و بازنشر بدون اجازه کتبی مجاز نیست.', 'fasdent' ),
					esc_html( gmdate( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
				?>
			</p>

			<!-- برچسب‌ها -->
			<?php
			$tags = get_the_tags();
			if ( $tags ) :
			?>
			<div class="post-tags">
				<i class="fa-solid fa-tags" aria-hidden="true"></i>
				<?php foreach ( $tags as $tag ) : ?>
				<a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>" class="tag-pill"><?php echo esc_html( $tag->name ); ?></a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<!-- واکنش‌های بیماران -->
			<?php
			$reactions = array(
				'helpful'  => array( 'fa-solid fa-thumbs-up', __( 'مفید', 'fasdent' ) ),
				'thanks'   => array( 'fa-solid fa-heart', __( 'ممنون', 'fasdent' ) ),
				'accurate' => array( 'fa-solid fa-bullseye', __( 'دقیق', 'fasdent' ) ),
			);
			?>
			<div
				class="post-reactions"
				data-post-reactions
				data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'fasdent_react_' . $post_id ) ); ?>"
				data-post-id="<?php echo esc_attr( (string) $post_id ); ?>"
			>
				<span class="post-reactions__label"><?php esc_html_e( 'این مطلب چقدر مفید بود؟', 'fasdent' ); ?></span>
				<?php foreach ( $reactions as $reaction_key => $reaction_data ) :
					$reaction_count = (int) get_post_meta( $post_id, '_reaction_' . $reaction_key, true );
				?>
				<button
					type="button"
					class="reaction-btn btn btn-secondary"
					data-reaction="<?php echo esc_attr( $reaction_key ); ?>"
					data-count="<?php echo esc_attr( $reaction_count ); ?>"
					aria-pressed="false"
					aria-label="<?php echo esc_attr( sprintf( /* translators: %s: reaction label, e.g. "Helpful" */ __( 'واکنش %s', 'fasdent' ), $reaction_data[1] ) ); ?>"
				>
					<i class="<?php echo esc_attr( $reaction_data[0] ); ?>" aria-hidden="true"></i>
					<span class="reaction-btn__text"><?php echo esc_html( $reaction_data[1] ); ?></span>
					<span class="reaction-btn__count"<?php echo $reaction_count ? '' : ' hidden'; ?>>(<?php echo esc_html( number_format_i18n( $reaction_count ) ); ?>)</span>
				</button>
				<?php endforeach; ?>
			</div>

			<!-- اشتراک‌گذاری -->
			<?php get_template_part( 'template-parts/social-share' ); ?>

			<!-- ناوبری مطالب -->
			<nav class="post-navigation" aria-label="<?php esc_attr_e( 'ناوبری مطالب', 'fasdent' ); ?>">
				<?php
				$prev_post = get_previous_post();
				$next_post = get_next_post();
				?>
				<?php if ( $prev_post ) : ?>
				<a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" class="card post-nav-item post-nav-prev">
					<span class="nav-label"><i class="fa-solid fa-angle-right" aria-hidden="true"></i> <?php esc_html_e( 'مطلب قبلی', 'fasdent' ); ?></span>
					<span class="nav-title"><?php echo esc_html( $prev_post->post_title ); ?></span>
				</a>
				<?php endif; ?>
				<?php if ( $next_post ) : ?>
				<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" class="card post-nav-item post-nav-next">
					<span class="nav-label"><?php esc_html_e( 'مطلب بعدی', 'fasdent' ); ?> <i class="fa-solid fa-angle-left" aria-hidden="true"></i></span>
					<span class="nav-title"><?php echo esc_html( $next_post->post_title ); ?></span>
				</a>
				<?php endif; ?>
			</nav>

			<!-- بیوگرافی نویسنده -->
			<?php
			$author_id              = get_the_author_meta( 'ID' );
			$author_credentials     = get_the_author_meta( 'credentials', $author_id );
			$author_license_number  = get_the_author_meta( 'dental_license_number', $author_id );
			$author_license_state   = get_the_author_meta( 'dental_license_state', $author_id );
			?>
			<div class="author-bio card">
				<?php echo get_avatar( $author_id, 64 ); ?>
				<div>
					<strong><?php the_author(); ?></strong>
					<?php if ( $author_credentials ) : ?>
					<span class="author-bio__credentials"><?php echo esc_html( $author_credentials ); ?></span>
					<?php endif; ?>
					<p><?php echo esc_html( get_the_author_meta( 'description' ) ? get_the_author_meta( 'description' ) : __( 'نویسنده کلینیک فس‌دنت', 'fasdent' ) ); ?></p>
					<?php if ( $author_license_number || $author_license_state ) :
						$license_parts = array();
						if ( $author_license_number ) {
							$license_parts[] = sprintf(
								/* translators: %s: license number */
								esc_html__( 'شماره پروانه: %s', 'fasdent' ),
								esc_html( $author_license_number )
							);
						}
						if ( $author_license_state ) {
							$license_parts[] = esc_html( $author_license_state );
						}
					?>
					<p class="author-bio__license">
						<i class="fa-solid fa-id-card" aria-hidden="true"></i>
						<?php echo implode( ' · ', $license_parts ); // phpcs:ignore WordPress.Security.EscapeOutput -- parts are already escaped above. ?>
					</p>
					<?php endif; ?>
				</div>
			</div>

			<?php fasdent_render_disclaimer( 'privacy', get_privacy_policy_url() ? sprintf( ' <a href="%s">%s</a>', esc_url( get_privacy_policy_url() ), esc_html__( 'مشاهده سیاست حریم خصوصی', 'fasdent' ) ) : '' ); ?>

			<!-- نظرات -->
			<?php comments_template(); ?>

		</article>

		<!-- سایدبار -->
		<aside class="single-post__sidebar">
			<?php get_template_part( 'template-parts/toc-sidebar' ); ?>
			<?php get_template_part( 'template-parts/cta-banner' ); ?>
		</aside>
	</div>

</div>

<?php
endwhile;
get_footer();
