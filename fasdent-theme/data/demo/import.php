<?php
/**
 * Fasdent Demo Data Importer — Main Runner
 *
 * Appearance → بارگذاری نمونه داده
 *
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register admin menu page under Appearance.
 */
add_action( 'admin_menu', 'fasdent_demo_register_admin_page' );
function fasdent_demo_register_admin_page(): void {
	add_theme_page(
		'بارگذاری نمونه داده',
		'بارگذاری نمونه داده',
		'manage_options',
		'fasdent-demo-import',
		'fasdent_demo_render_admin_page'
	);
}

/**
 * Render the admin page.
 */
function fasdent_demo_render_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$results    = array();
	$reset_done = false;

	// Handle reset.
	if ( isset( $_POST['fasdent_demo_reset'] ) && check_admin_referer( 'fasdent_demo_reset_action', 'fasdent_demo_reset_nonce' ) ) {
		fasdent_demo_reset_data();
		$reset_done = true;
	}

	// Handle import.
	if ( isset( $_POST['fasdent_demo_import'] ) && check_admin_referer( 'fasdent_demo_import_action', 'fasdent_demo_import_nonce' ) ) {
		$results = fasdent_demo_run_import();
	}

	$imported = get_option( 'fasdent_demo_imported_ids', array() );
	?>
	<div class="wrap" dir="rtl">
		<h1>بارگذاری نمونه داده — کلینیک فس‌دنت</h1>

		<?php if ( $reset_done ) : ?>
			<div class="notice notice-success is-dismissible"><p>نمونه داده‌ها با موفقیت حذف شدند.</p></div>
		<?php endif; ?>

		<?php if ( ! empty( $results ) ) : ?>
			<div class="notice notice-info">
				<p><strong>نتیجه بارگذاری:</strong></p>
				<ul style="list-style:disc;margin-right:20px;">
					<?php foreach ( $results as $step => $status ) : ?>
						<li><?php echo esc_html( $step ); ?>: <?php echo $status ? '✓ موفق' : '✗ خطا / فایل یافت نشد'; ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="card" style="max-width:700px;padding:20px;margin-top:20px;">
			<h2>بارگذاری داده‌های نمونه</h2>
			<p>با کلیک روی دکمه زیر، تمام صفحات، خدمات، پزشکان، مقالات، سوالات متداول، نظرات بیماران، منوها و تنظیمات نمونه کلینیک فس‌دنت بارگذاری می‌شوند.</p>
			<p><strong>توجه:</strong> این عملیات فقط یک‌بار لازم است. در صورت وجود داده‌های قبلی با همان اسلاگ، از ایجاد مجدد صرف‌نظر می‌شود.</p>

			<form method="post" style="margin-top:16px;">
				<?php wp_nonce_field( 'fasdent_demo_import_action', 'fasdent_demo_import_nonce' ); ?>
				<button type="submit" name="fasdent_demo_import" class="button button-primary button-hero">
					شروع بارگذاری نمونه داده
				</button>
			</form>
		</div>

		<div class="card" style="max-width:700px;padding:20px;margin-top:20px;">
			<h2>حذف نمونه داده</h2>
			<p>تمام پست‌ها، صفحات و ترم‌های ایجادشده توسط این ایمپورتر حذف می‌شوند. این عمل غیرقابل بازگشت است.</p>
			<form method="post" style="margin-top:16px;" onsubmit="return confirm('آیا از حذف تمام نمونه داده‌ها مطمئن هستید؟');">
				<?php wp_nonce_field( 'fasdent_demo_reset_action', 'fasdent_demo_reset_nonce' ); ?>
				<button type="submit" name="fasdent_demo_reset" class="button button-secondary">
					حذف نمونه داده
				</button>
			</form>
		</div>

		<?php if ( ! empty( $imported ) ) : ?>
			<div class="card" style="max-width:700px;padding:20px;margin-top:20px;">
				<h2>وضعیت فعلی</h2>
				<ul style="list-style:disc;margin-right:20px;">
					<?php foreach ( $imported as $group => $ids ) : ?>
						<li><?php echo esc_html( $group ); ?>: <?php echo is_array( $ids ) ? count( $ids ) : 0; ?> مورد</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Run the full import sequence.
 *
 * Content files live directly in data/demo/ (not in a nested inc/ folder).
 *
 * @return array Step label => success boolean
 */
function fasdent_demo_run_import(): array {
	if ( ! defined( 'FASDENT_DEMO_IMPORT' ) ) {
		define( 'FASDENT_DEMO_IMPORT', true );
	}

	$GLOBALS['fasdent_demo_ids'] = array();
	$results = array();
	// Files are in data/demo/ itself.
	$base = get_template_directory() . '/data/demo/';

	$steps = array(
		'طبقه‌بندی خدمات' => 'taxonomy-terms.php',
		'خدمات'           => 'services.php',
		'پزشکان'          => 'doctors.php',
		'نظرات بیماران'   => 'testimonials.php',
		'سوالات متداول'   => 'faqs.php',
		'صفحات'           => 'pages.php',
		'مقالات'          => 'posts.php',
		'منوها'           => 'menus.php',
		'تنظیمات'         => 'options.php',
	);

	foreach ( $steps as $label => $file ) {
		$path = $base . $file;
		if ( file_exists( $path ) ) {
			try {
				require $path;
				$results[ $label ] = true;
			} catch ( Throwable $e ) {
				$results[ $label ] = false;
			}
		} else {
			$results[ $label ] = false;
		}
	}

	// Store all created IDs for later reset.
	update_option( 'fasdent_demo_imported_ids', $GLOBALS['fasdent_demo_ids'], false );

	// Flush rewrite rules after creating CPTs / pages.
	flush_rewrite_rules();

	return $results;
}

/**
 * Delete all demo data previously imported.
 */
function fasdent_demo_reset_data(): void {
	$ids = get_option( 'fasdent_demo_imported_ids', array() );

	// Delete posts of various types.
	$post_groups = array( 'posts', 'services', 'doctors', 'testimonials', 'faqs', 'pages' );
	foreach ( $post_groups as $group ) {
		if ( empty( $ids[ $group ] ) ) {
			continue;
		}
		$group_ids = is_array( $ids[ $group ] ) ? $ids[ $group ] : array();
		foreach ( $group_ids as $id ) {
			if ( is_numeric( $id ) && (int) $id > 0 ) {
				wp_delete_post( (int) $id, true );
			}
		}
	}

	// Delete terms.
	if ( ! empty( $ids['terms'] ) && is_array( $ids['terms'] ) ) {
		foreach ( $ids['terms'] as $term_id ) {
			if ( is_numeric( $term_id ) ) {
				wp_delete_term( (int) $term_id, 'service_category' );
			}
		}
	}

	// Remove menus created by demo.
	$menu_names = array( 'منوی اصلی', 'منوی پاورقی', 'منوی قوانین' );
	foreach ( $menu_names as $name ) {
		$menu = wp_get_nav_menu_object( $name );
		if ( $menu ) {
			wp_delete_nav_menu( $menu->term_id );
		}
	}

	delete_option( 'fasdent_demo_imported_ids' );
	flush_rewrite_rules();
}
