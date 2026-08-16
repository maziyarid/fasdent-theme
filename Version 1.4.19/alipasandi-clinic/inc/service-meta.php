<?php
/**
 * Service page content stored as registered post meta (portable, theme-switch safe).
 *
 * Priority: Post Meta → Legacy service-data.php (read-only fallback).
 * Migration is idempotent and never overwrites admin-edited meta.
 *
 * @package Alipasandi_Clinic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Meta key for structured service content (single array, serialized by WP). */
define( 'ALIPASANDI_SERVICE_META_KEY', '_alipasandi_service' );

/** Option flag: first populate from legacy file completed. */
define( 'ALIPASANDI_SERVICE_META_MIGRATED', 'alipasandi_service_meta_migrated_v1' );

/** Allowed service keys. */
function alipasandi_service_keys() {
	return array( 'implant', 'crown', 'surgery', 'general' );
}

/**
 * Resolve the WordPress page for a service key (slug or services/slug).
 *
 * @param string $key Service key.
 * @return WP_Post|null
 */
function alipasandi_get_service_page( $key ) {
	$key = sanitize_key( $key );
	if ( ! in_array( $key, alipasandi_service_keys(), true ) ) {
		return null;
	}
	$page = get_page_by_path( 'services/' . $key );
	if ( ! $page ) {
		$page = get_page_by_path( $key );
	}
	return $page instanceof WP_Post ? $page : null;
}

/**
 * Whether a post is one of the four service landing pages.
 *
 * @param int $post_id Post ID.
 * @return string Service key or empty string.
 */
function alipasandi_service_key_for_post( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post || 'page' !== $post->post_type ) {
		return '';
	}
	$slug = $post->post_name;
	if ( in_array( $slug, alipasandi_service_keys(), true ) ) {
		return $slug;
	}
	// Child under services/.
	if ( $post->post_parent ) {
		$parent = get_post( $post->post_parent );
		if ( $parent && 'services' === $parent->post_name && in_array( $slug, alipasandi_service_keys(), true ) ) {
			return $slug;
		}
	}
	return '';
}

/**
 * Register post meta for REST and validation.
 */
function alipasandi_register_service_meta() {
	register_post_meta(
		'page',
		ALIPASANDI_SERVICE_META_KEY,
		array(
			'type'              => 'object',
			'description'       => 'Structured service landing content for Alipasandi clinic.',
			'single'            => true,
			'default'           => array(),
			'show_in_rest'      => false, // Edited via classic meta box for structured fields.
			'auth_callback'     => function () {
				return current_user_can( 'edit_pages' );
			},
			'sanitize_callback' => 'alipasandi_sanitize_service_meta',
		)
	);
}
add_action( 'init', 'alipasandi_register_service_meta' );

/**
 * Sanitize full service content array.
 *
 * @param mixed $value Raw value.
 * @return array
 */
function alipasandi_sanitize_service_meta( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$out = array();

	$string_keys = array(
		'eyebrow', 'title', 'title_gold', 'intro', 'image', 'image_alt',
		'what_label', 'what_title', 'benefit_label', 'benefit_title', 'steps_label',
		'candidate_title', 'candidate_text', 'notice_title', 'notice_text',
		'cta_title', 'cta_text',
	);
	foreach ( $string_keys as $k ) {
		$out[ $k ] = isset( $value[ $k ] ) ? sanitize_text_field( wp_unslash( (string) $value[ $k ] ) ) : '';
	}
	// Longer text fields.
	foreach ( array( 'intro', 'candidate_text', 'notice_text', 'cta_text' ) as $k ) {
		if ( isset( $value[ $k ] ) ) {
			$out[ $k ] = sanitize_textarea_field( wp_unslash( (string) $value[ $k ] ) );
		}
	}

	// what_text: list of paragraphs.
	$out['what_text'] = array();
	if ( ! empty( $value['what_text'] ) && is_array( $value['what_text'] ) ) {
		foreach ( $value['what_text'] as $p ) {
			$p = sanitize_textarea_field( wp_unslash( (string) $p ) );
			if ( '' !== $p ) {
				$out['what_text'][] = $p;
			}
		}
	}

	// diagram: up to 6 short labels.
	$out['diagram'] = array();
	if ( ! empty( $value['diagram'] ) && is_array( $value['diagram'] ) ) {
		foreach ( array_slice( $value['diagram'], 0, 6 ) as $d ) {
			$d = sanitize_text_field( wp_unslash( (string) $d ) );
			if ( '' !== $d ) {
				$out['diagram'][] = $d;
			}
		}
	}

	// benefits: [ title, text, icon ].
	$out['benefits'] = array();
	$allowed_icons   = array( 'tooth', 'crown', 'general', 'implant', 'shield', 'clock', 'surgery', 'info' );
	if ( ! empty( $value['benefits'] ) && is_array( $value['benefits'] ) ) {
		foreach ( $value['benefits'] as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$title = isset( $row[0] ) ? sanitize_text_field( wp_unslash( (string) $row[0] ) ) : ( isset( $row['title'] ) ? sanitize_text_field( wp_unslash( (string) $row['title'] ) ) : '' );
			$text  = isset( $row[1] ) ? sanitize_textarea_field( wp_unslash( (string) $row[1] ) ) : ( isset( $row['text'] ) ? sanitize_textarea_field( wp_unslash( (string) $row['text'] ) ) : '' );
			$icon  = isset( $row[2] ) ? sanitize_key( (string) $row[2] ) : ( isset( $row['icon'] ) ? sanitize_key( (string) $row['icon'] ) : 'tooth' );
			if ( ! in_array( $icon, $allowed_icons, true ) ) {
				$icon = 'tooth';
			}
			if ( '' !== $title || '' !== $text ) {
				$out['benefits'][] = array( $title, $text, $icon );
			}
		}
	}

	// steps: [ num, title, text ].
	$out['steps'] = array();
	if ( ! empty( $value['steps'] ) && is_array( $value['steps'] ) ) {
		foreach ( $value['steps'] as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$num   = isset( $row[0] ) ? sanitize_text_field( wp_unslash( (string) $row[0] ) ) : ( isset( $row['num'] ) ? sanitize_text_field( wp_unslash( (string) $row['num'] ) ) : '' );
			$title = isset( $row[1] ) ? sanitize_text_field( wp_unslash( (string) $row[1] ) ) : ( isset( $row['title'] ) ? sanitize_text_field( wp_unslash( (string) $row['title'] ) ) : '' );
			$text  = isset( $row[2] ) ? sanitize_textarea_field( wp_unslash( (string) $row[2] ) ) : ( isset( $row['text'] ) ? sanitize_textarea_field( wp_unslash( (string) $row['text'] ) ) : '' );
			if ( '' !== $title || '' !== $text ) {
				$out['steps'][] = array( $num, $title, $text );
			}
		}
	}

	// faqs: [ question, answer ].
	$out['faqs'] = array();
	if ( ! empty( $value['faqs'] ) && is_array( $value['faqs'] ) ) {
		foreach ( $value['faqs'] as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$q = isset( $row[0] ) ? sanitize_text_field( wp_unslash( (string) $row[0] ) ) : ( isset( $row['question'] ) ? sanitize_text_field( wp_unslash( (string) $row['question'] ) ) : '' );
			$a = isset( $row[1] ) ? sanitize_textarea_field( wp_unslash( (string) $row[1] ) ) : ( isset( $row['answer'] ) ? sanitize_textarea_field( wp_unslash( (string) $row['answer'] ) ) : '' );
			if ( '' !== $q || '' !== $a ) {
				$out['faqs'][] = array( $q, $a );
			}
		}
	}

	return $out;
}

/**
 * Primary getter: Post Meta first, then legacy file (read-only).
 *
 * @param string $key Service key.
 * @return array
 */
function alipasandi_get_service( $key ) {
	$key  = sanitize_key( $key );
	$page = alipasandi_get_service_page( $key );
	if ( $page ) {
		$meta = get_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, true );
		if ( is_array( $meta ) && ( ! empty( $meta['title'] ) || ! empty( $meta['intro'] ) ) ) {
			return $meta;
		}
	}
	// Read-only fallback — never written back by theme updates.
	if ( function_exists( 'alipasandi_get_service_legacy' ) ) {
		return alipasandi_get_service_legacy( $key );
	}
	return array();
}

/**
 * Idempotent one-time populate from legacy file into empty post meta.
 * Does not overwrite existing meta (admin edits are preserved across theme updates).
 */
function alipasandi_migrate_service_content_to_meta() {
	if ( get_option( ALIPASANDI_SERVICE_META_MIGRATED ) ) {
		return;
	}
	if ( ! function_exists( 'alipasandi_get_service_legacy' ) ) {
		return;
	}

	foreach ( alipasandi_service_keys() as $key ) {
		$page = alipasandi_get_service_page( $key );
		if ( ! $page ) {
			continue;
		}
		$existing = get_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, true );
		if ( is_array( $existing ) && ( ! empty( $existing['title'] ) || ! empty( $existing['intro'] ) ) ) {
			continue; // Already has content — do not overwrite.
		}
		$legacy = alipasandi_get_service_legacy( $key );
		if ( empty( $legacy ) || ! is_array( $legacy ) ) {
			continue;
		}
		$clean = alipasandi_sanitize_service_meta( $legacy );
		update_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, $clean );
	}

	update_option( ALIPASANDI_SERVICE_META_MIGRATED, ALIPASANDI_THEME_VERSION, false );
}
add_action( 'after_setup_theme', 'alipasandi_migrate_service_content_to_meta', 20 );
add_action( 'admin_init', 'alipasandi_migrate_service_content_to_meta', 5 );

/**
 * Meta box only on the four service pages.
 */
function alipasandi_register_service_meta_box() {
	$screen = get_current_screen();
	if ( ! $screen || 'page' !== $screen->id ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $post_id || ! alipasandi_service_key_for_post( $post_id ) ) {
		return;
	}
	add_meta_box(
		'alipasandi-service-content',
		__( 'محتوای صفحه خدمت (قابل ویرایش)', 'alipasandi-clinic' ),
		'alipasandi_render_service_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'alipasandi_register_service_meta_box' );

/**
 * Render structured editor for service content.
 *
 * @param WP_Post $post Post.
 */
function alipasandi_render_service_meta_box( $post ) {
	wp_nonce_field( 'alipasandi_save_service_meta', 'alipasandi_service_meta_nonce' );
	$key = alipasandi_service_key_for_post( $post->ID );
	$data = get_post_meta( $post->ID, ALIPASANDI_SERVICE_META_KEY, true );
	if ( ! is_array( $data ) || ( empty( $data['title'] ) && empty( $data['intro'] ) ) ) {
		$data = function_exists( 'alipasandi_get_service_legacy' ) ? alipasandi_get_service_legacy( $key ) : array();
	}
	$data = is_array( $data ) ? $data : array();

	$field = function ( $name, $label, $type = 'text' ) use ( $data ) {
		$val = isset( $data[ $name ] ) ? $data[ $name ] : '';
		echo '<p><label for="alipasandi_svc_' . esc_attr( $name ) . '"><strong>' . esc_html( $label ) . '</strong></label><br>';
		if ( 'textarea' === $type ) {
			echo '<textarea class="large-text" rows="3" id="alipasandi_svc_' . esc_attr( $name ) . '" name="alipasandi_svc[' . esc_attr( $name ) . ']">' . esc_textarea( $val ) . '</textarea>';
		} else {
			echo '<input class="large-text" type="text" id="alipasandi_svc_' . esc_attr( $name ) . '" name="alipasandi_svc[' . esc_attr( $name ) . ']" value="' . esc_attr( $val ) . '">';
		}
		echo '</p>';
	};

	echo '<p class="description">' . esc_html__( 'این محتوا در دیتابیس (Post Meta) ذخیره می‌شود و با تعویض Theme از بین نمی‌رود. Layout و Design توسط Template کنترل می‌شود. H1 همچنان یک تگ واحد است (title + title_gold فقط ظاهر).', 'alipasandi-clinic' ) . '</p>';

	echo '<h3>' . esc_html__( 'Hero', 'alipasandi-clinic' ) . '</h3>';
	$field( 'eyebrow', 'Eyebrow' );
	$field( 'title', 'عنوان H1 — بخش اول' );
	$field( 'title_gold', 'عنوان H1 — بخش طلایی (همان H1، فقط استایل)' );
	$field( 'intro', 'Intro', 'textarea' );
	$field( 'image', 'نام فایل تصویر (مثلاً implant-hero.jpg)' );
	$field( 'image_alt', 'ALT تصویر' );

	echo '<h3>' . esc_html__( 'بخش «چیست؟»', 'alipasandi-clinic' ) . '</h3>';
	$field( 'what_label', 'Label' );
	$field( 'what_title', 'عنوان H2' );
	$what_text = isset( $data['what_text'] ) && is_array( $data['what_text'] ) ? $data['what_text'] : array( '', '' );
	echo '<p><strong>' . esc_html__( 'پاراگراف‌ها (هر خط یک پاراگراف)', 'alipasandi-clinic' ) . '</strong><br>';
	echo '<textarea class="large-text" rows="5" name="alipasandi_svc[what_text_raw]">' . esc_textarea( implode( "\n\n", $what_text ) ) . '</textarea></p>';

	$diagram = isset( $data['diagram'] ) && is_array( $data['diagram'] ) ? $data['diagram'] : array();
	echo '<p><strong>' . esc_html__( 'Diagram labels (با ویرگول جدا کنید)', 'alipasandi-clinic' ) . '</strong><br>';
	echo '<input class="large-text" type="text" name="alipasandi_svc[diagram_raw]" value="' . esc_attr( implode( '، ', $diagram ) ) . '"></p>';

	echo '<h3>' . esc_html__( 'مزایا', 'alipasandi-clinic' ) . '</h3>';
	$field( 'benefit_label', 'Label' );
	$field( 'benefit_title', 'عنوان H2' );
	$benefits = isset( $data['benefits'] ) && is_array( $data['benefits'] ) ? $data['benefits'] : array();
	echo '<p class="description">' . esc_html__( 'هر مزیت: عنوان | توضیح | آیکن (tooth/crown/general/implant/shield/clock/surgery) — یک مزیت در هر خط', 'alipasandi-clinic' ) . '</p>';
	$ben_lines = array();
	foreach ( $benefits as $b ) {
		$ben_lines[] = ( isset( $b[0] ) ? $b[0] : '' ) . ' | ' . ( isset( $b[1] ) ? $b[1] : '' ) . ' | ' . ( isset( $b[2] ) ? $b[2] : 'tooth' );
	}
	echo '<textarea class="large-text" rows="6" name="alipasandi_svc[benefits_raw]">' . esc_textarea( implode( "\n", $ben_lines ) ) . '</textarea>';

	echo '<h3>' . esc_html__( 'مراحل', 'alipasandi-clinic' ) . '</h3>';
	$field( 'steps_label', 'Label' );
	$steps = isset( $data['steps'] ) && is_array( $data['steps'] ) ? $data['steps'] : array();
	echo '<p class="description">' . esc_html__( 'هر مرحله: شماره | عنوان | توضیح — یک مرحله در هر خط', 'alipasandi-clinic' ) . '</p>';
	$step_lines = array();
	foreach ( $steps as $s ) {
		$step_lines[] = ( isset( $s[0] ) ? $s[0] : '' ) . ' | ' . ( isset( $s[1] ) ? $s[1] : '' ) . ' | ' . ( isset( $s[2] ) ? $s[2] : '' );
	}
	echo '<textarea class="large-text" rows="6" name="alipasandi_svc[steps_raw]">' . esc_textarea( implode( "\n", $step_lines ) ) . '</textarea>';

	echo '<h3>' . esc_html__( 'ارزیابی / توجه', 'alipasandi-clinic' ) . '</h3>';
	$field( 'candidate_title', 'عنوان ارزیابی' );
	$field( 'candidate_text', 'متن ارزیابی', 'textarea' );
	$field( 'notice_title', 'عنوان توجه پزشکی' );
	$field( 'notice_text', 'متن توجه پزشکی', 'textarea' );

	echo '<h3>' . esc_html__( 'FAQ (سؤال | پاسخ — هر FAQ دو خط یا جدا با || )', 'alipasandi-clinic' ) . '</h3>';
	$faqs = isset( $data['faqs'] ) && is_array( $data['faqs'] ) ? $data['faqs'] : array();
	$faq_lines = array();
	foreach ( $faqs as $f ) {
		$faq_lines[] = ( isset( $f[0] ) ? $f[0] : '' ) . ' || ' . ( isset( $f[1] ) ? $f[1] : '' );
	}
	echo '<textarea class="large-text" rows="8" name="alipasandi_svc[faqs_raw]">' . esc_textarea( implode( "\n", $faq_lines ) ) . '</textarea>';

	echo '<h3>' . esc_html__( 'CTA نهایی', 'alipasandi-clinic' ) . '</h3>';
	$field( 'cta_title', 'عنوان CTA' );
	$field( 'cta_text', 'متن CTA', 'textarea' );
}

/**
 * Save service meta from meta box.
 *
 * @param int $post_id Post ID.
 */
function alipasandi_save_service_meta( $post_id ) {
	if ( ! isset( $_POST['alipasandi_service_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alipasandi_service_meta_nonce'] ) ), 'alipasandi_save_service_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	if ( ! alipasandi_service_key_for_post( $post_id ) ) {
		return;
	}
	if ( empty( $_POST['alipasandi_svc'] ) || ! is_array( $_POST['alipasandi_svc'] ) ) {
		return;
	}

	$raw = wp_unslash( $_POST['alipasandi_svc'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$data = array();

	foreach ( array(
		'eyebrow', 'title', 'title_gold', 'intro', 'image', 'image_alt',
		'what_label', 'what_title', 'benefit_label', 'benefit_title', 'steps_label',
		'candidate_title', 'candidate_text', 'notice_title', 'notice_text',
		'cta_title', 'cta_text',
	) as $k ) {
		$data[ $k ] = isset( $raw[ $k ] ) ? $raw[ $k ] : '';
	}

	// what_text from paragraphs separated by blank lines.
	$data['what_text'] = array();
	if ( ! empty( $raw['what_text_raw'] ) ) {
		$parts = preg_split( '/\n\s*\n/', (string) $raw['what_text_raw'] );
		foreach ( $parts as $p ) {
			$p = trim( $p );
			if ( '' !== $p ) {
				$data['what_text'][] = $p;
			}
		}
	}

	$data['diagram'] = array();
	if ( ! empty( $raw['diagram_raw'] ) ) {
		$parts = preg_split( '/[,،]/u', (string) $raw['diagram_raw'] );
		foreach ( $parts as $p ) {
			$p = trim( $p );
			if ( '' !== $p ) {
				$data['diagram'][] = $p;
			}
		}
	}

	$data['benefits'] = array();
	if ( ! empty( $raw['benefits_raw'] ) ) {
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw['benefits_raw'] ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$bits = array_map( 'trim', explode( '|', $line ) );
			$data['benefits'][] = array(
				isset( $bits[0] ) ? $bits[0] : '',
				isset( $bits[1] ) ? $bits[1] : '',
				isset( $bits[2] ) ? $bits[2] : 'tooth',
			);
		}
	}

	$data['steps'] = array();
	if ( ! empty( $raw['steps_raw'] ) ) {
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw['steps_raw'] ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$bits = array_map( 'trim', explode( '|', $line ) );
			$data['steps'][] = array(
				isset( $bits[0] ) ? $bits[0] : '',
				isset( $bits[1] ) ? $bits[1] : '',
				isset( $bits[2] ) ? $bits[2] : '',
			);
		}
	}

	$data['faqs'] = array();
	if ( ! empty( $raw['faqs_raw'] ) ) {
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw['faqs_raw'] ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$bits = array_map( 'trim', explode( '||', $line, 2 ) );
			$data['faqs'][] = array(
				isset( $bits[0] ) ? $bits[0] : '',
				isset( $bits[1] ) ? $bits[1] : '',
			);
		}
	}

	$clean = alipasandi_sanitize_service_meta( $data );
	update_post_meta( $post_id, ALIPASANDI_SERVICE_META_KEY, $clean );
}
add_action( 'save_post_page', 'alipasandi_save_service_meta' );
