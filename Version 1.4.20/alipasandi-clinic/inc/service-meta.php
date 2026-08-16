<?php
/**
 * Service page content as registered post meta (portable, theme-switch safe).
 *
 * Data priority (read):
 *   1. Post meta EXISTS (metadata_exists) → use meta as full source (empty fields stay empty).
 *   2. Post meta absent → legacy inc/service-data.php (read-only emergency fallback).
 *
 * Legacy file is never written to. Theme updates never overwrite admin-edited meta.
 *
 * @package Alipasandi_Clinic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALIPASANDI_SERVICE_META_KEY', '_alipasandi_service' );
define( 'ALIPASANDI_SERVICE_META_SCHEMA', 1 );
/** Option: per-page migration status array + completed flag. */
define( 'ALIPASANDI_SERVICE_META_STATUS', 'alipasandi_service_meta_status_v1' );

function alipasandi_service_keys() {
	return array( 'implant', 'crown', 'surgery', 'general' );
}

/**
 * Resolve page for a service key by slug (services/{key} or {key}), not by title.
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
 * Detect service key for a post: slug in allowed keys, optionally under parent slug "services".
 *
 * @param int $post_id Post ID.
 * @return string Key or empty.
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
	if ( $post->post_parent ) {
		$parent = get_post( $post->post_parent );
		if ( $parent && 'services' === $parent->post_name && in_array( $slug, alipasandi_service_keys(), true ) ) {
			return $slug;
		}
	}
	return '';
}

/**
 * Register meta: not exposed to REST; auth is per-object edit_post.
 */
function alipasandi_register_service_meta() {
	register_post_meta(
		'page',
		ALIPASANDI_SERVICE_META_KEY,
		array(
			'type'              => 'object',
			'description'       => 'Structured service landing content (Alipasandi).',
			'single'            => true,
			'default'           => array(),
			'show_in_rest'      => false, // Classic meta box only; no public REST exposure.
			'auth_callback'     => function ( $allowed, $meta_key, $object_id ) {
				$object_id = (int) $object_id;
				if ( $object_id <= 0 ) {
					return false;
				}
				if ( ! current_user_can( 'edit_post', $object_id ) ) {
					return false;
				}
				// Only the four service pages may carry this meta.
				return (bool) alipasandi_service_key_for_post( $object_id );
			},
			'sanitize_callback' => 'alipasandi_sanitize_service_meta',
		)
	);
}
add_action( 'init', 'alipasandi_register_service_meta' );

/**
 * Allowed HTML in longer copy (paragraphs / FAQ answers) — links preserved, scripts stripped.
 *
 * @return array
 */
function alipasandi_service_allowed_html() {
	return array(
		'a'      => array(
			'href'   => true,
			'title'  => true,
			'rel'    => true,
			'target' => true,
		),
		'strong' => array(),
		'em'     => array(),
		'b'      => array(),
		'i'      => array(),
		'br'     => array(),
		'span'   => array( 'class' => true ),
	);
}

/**
 * Field-specific sanitization for the service content object.
 *
 * @param mixed $value Raw.
 * @return array
 */
function alipasandi_sanitize_service_meta( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$out = array(
		'schema_version' => ALIPASANDI_SERVICE_META_SCHEMA,
	);

	// Plain titles / labels.
	$plain = array(
		'eyebrow', 'title', 'title_gold', 'image', 'image_alt',
		'what_label', 'what_title', 'benefit_label', 'benefit_title', 'steps_label',
		'candidate_title', 'notice_title', 'cta_title',
	);
	foreach ( $plain as $k ) {
		$out[ $k ] = isset( $value[ $k ] ) ? sanitize_text_field( wp_unslash( (string) $value[ $k ] ) ) : '';
	}

	// Longer text: allow safe limited HTML (internal links).
	$text_html = array( 'intro', 'candidate_text', 'notice_text', 'cta_text' );
	foreach ( $text_html as $k ) {
		if ( ! isset( $value[ $k ] ) ) {
			$out[ $k ] = '';
			continue;
		}
		$raw = wp_unslash( (string) $value[ $k ] );
		$out[ $k ] = wp_kses( $raw, alipasandi_service_allowed_html() );
	}

	// Optional attachment ID (preferred over raw filename for long-term portability).
	$out['image_id'] = 0;
	if ( isset( $value['image_id'] ) ) {
		$out['image_id'] = absint( $value['image_id'] );
	}

	// what_text paragraphs — keep empty strings if intentionally blank lines were not used; only trim.
	$out['what_text'] = array();
	if ( ! empty( $value['what_text'] ) && is_array( $value['what_text'] ) ) {
		foreach ( $value['what_text'] as $p ) {
			$p = wp_kses( wp_unslash( (string) $p ), alipasandi_service_allowed_html() );
			// Preserve intentionally non-empty only; empty strings dropped from list.
			if ( '' !== trim( wp_strip_all_tags( $p ) ) ) {
				$out['what_text'][] = $p;
			}
		}
	}

	$out['diagram'] = array();
	if ( ! empty( $value['diagram'] ) && is_array( $value['diagram'] ) ) {
		foreach ( array_slice( $value['diagram'], 0, 6 ) as $d ) {
			$d = sanitize_text_field( wp_unslash( (string) $d ) );
			if ( '' !== $d ) {
				$out['diagram'][] = $d;
			}
		}
	}

	$allowed_icons = array( 'tooth', 'crown', 'general', 'implant', 'shield', 'clock', 'surgery', 'info' );
	$out['benefits'] = array();
	if ( ! empty( $value['benefits'] ) && is_array( $value['benefits'] ) ) {
		foreach ( $value['benefits'] as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$title = isset( $row[0] ) ? sanitize_text_field( wp_unslash( (string) $row[0] ) ) : '';
			$text  = isset( $row[1] ) ? wp_kses( wp_unslash( (string) $row[1] ), alipasandi_service_allowed_html() ) : '';
			$icon  = isset( $row[2] ) ? sanitize_key( (string) $row[2] ) : 'tooth';
			if ( ! in_array( $icon, $allowed_icons, true ) ) {
				$icon = 'tooth';
			}
			// Allow row with empty text if title set (admin may clear body intentionally).
			if ( '' !== $title || '' !== trim( wp_strip_all_tags( $text ) ) ) {
				$out['benefits'][] = array( $title, $text, $icon );
			}
		}
	}

	$out['steps'] = array();
	if ( ! empty( $value['steps'] ) && is_array( $value['steps'] ) ) {
		foreach ( $value['steps'] as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$num   = isset( $row[0] ) ? sanitize_text_field( wp_unslash( (string) $row[0] ) ) : '';
			$title = isset( $row[1] ) ? sanitize_text_field( wp_unslash( (string) $row[1] ) ) : '';
			$text  = isset( $row[2] ) ? wp_kses( wp_unslash( (string) $row[2] ), alipasandi_service_allowed_html() ) : '';
			if ( '' !== $title || '' !== trim( wp_strip_all_tags( $text ) ) ) {
				$out['steps'][] = array( $num, $title, $text );
			}
		}
	}

	$out['faqs'] = array();
	if ( ! empty( $value['faqs'] ) && is_array( $value['faqs'] ) ) {
		foreach ( $value['faqs'] as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$q = isset( $row[0] ) ? sanitize_text_field( wp_unslash( (string) $row[0] ) ) : '';
			$a = isset( $row[1] ) ? wp_kses( wp_unslash( (string) $row[1] ), alipasandi_service_allowed_html() ) : '';
			if ( '' !== $q || '' !== trim( wp_strip_all_tags( $a ) ) ) {
				$out['faqs'][] = array( $q, $a );
			}
		}
	}

	return $out;
}

/**
 * Whether service meta is present for a post (exists ≠ empty fields).
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function alipasandi_service_meta_exists( $post_id ) {
	return metadata_exists( 'post', (int) $post_id, ALIPASANDI_SERVICE_META_KEY );
}

/**
 * Primary getter: if meta EXISTS use it entirely; else legacy fallback.
 *
 * @param string $key Service key.
 * @return array
 */
function alipasandi_get_service( $key ) {
	$key  = sanitize_key( $key );
	$page = alipasandi_get_service_page( $key );
	if ( $page && alipasandi_service_meta_exists( $page->ID ) ) {
		$meta = get_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, true );
		return is_array( $meta ) ? $meta : array();
	}
	if ( function_exists( 'alipasandi_get_service_legacy' ) ) {
		return alipasandi_get_service_legacy( $key );
	}
	return array();
}

/**
 * Per-page migration status option structure.
 *
 * @return array{completed:bool,pages:array<string,string>}
 */
function alipasandi_get_service_migration_status() {
	$status = get_option( ALIPASANDI_SERVICE_META_STATUS, null );
	if ( ! is_array( $status ) ) {
		$status = array(
			'completed' => false,
			'pages'     => array(),
		);
	}
	if ( ! isset( $status['pages'] ) || ! is_array( $status['pages'] ) ) {
		$status['pages'] = array();
	}
	return $status;
}

/**
 * Idempotent migrate: only fills meta when metadata is absent.
 * Global completed=true only when all four pages are migrated or already had meta.
 * Failed/missing pages stay pending and can be retried without overwriting others.
 */
function alipasandi_migrate_service_content_to_meta() {
	$status = alipasandi_get_service_migration_status();
	if ( ! empty( $status['completed'] ) ) {
		return;
	}

	if ( ! function_exists( 'alipasandi_get_service_legacy' ) ) {
		return;
	}

	$pages_status = isset( $status['pages'] ) ? $status['pages'] : array();
	$all_ok       = true;

	foreach ( alipasandi_service_keys() as $key ) {
		// Already successfully handled.
		if ( isset( $pages_status[ $key ] ) && in_array( $pages_status[ $key ], array( 'migrated', 'existing' ), true ) ) {
			continue;
		}

		$page = alipasandi_get_service_page( $key );
		if ( ! $page ) {
			$pages_status[ $key ] = 'failed';
			$all_ok               = false;
			continue;
		}

		if ( alipasandi_service_meta_exists( $page->ID ) ) {
			$pages_status[ $key ] = 'existing';
			continue;
		}

		$legacy = alipasandi_get_service_legacy( $key );
		if ( empty( $legacy ) || ! is_array( $legacy ) ) {
			$pages_status[ $key ] = 'failed';
			$all_ok               = false;
			continue;
		}

		$clean = alipasandi_sanitize_service_meta( $legacy );
		$ok    = update_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, $clean );
		// update_post_meta returns false if value unchanged; treat presence as success.
		if ( false === $ok && ! alipasandi_service_meta_exists( $page->ID ) ) {
			$pages_status[ $key ] = 'failed';
			$all_ok               = false;
			continue;
		}
		$pages_status[ $key ] = 'migrated';
	}

	$status['pages'] = $pages_status;
	$status['completed'] = $all_ok;
	update_option( ALIPASANDI_SERVICE_META_STATUS, $status, false );
}
add_action( 'after_setup_theme', 'alipasandi_migrate_service_content_to_meta', 20 );
add_action( 'admin_init', 'alipasandi_migrate_service_content_to_meta', 5 );

/**
 * Meta box only when post is one of the four service pages (by slug logic).
 */
function alipasandi_register_service_meta_box() {
	$post_id = 0;
	if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = absint( $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	if ( ! $post_id && isset( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof WP_Post ) {
		$post_id = (int) $GLOBALS['post']->ID;
	}
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
 * @param WP_Post $post Post.
 */
function alipasandi_render_service_meta_box( $post ) {
	wp_nonce_field( 'alipasandi_save_service_meta', 'alipasandi_service_meta_nonce' );
	$key  = alipasandi_service_key_for_post( $post->ID );
	$data = array();
	if ( alipasandi_service_meta_exists( $post->ID ) ) {
		$meta = get_post_meta( $post->ID, ALIPASANDI_SERVICE_META_KEY, true );
		$data = is_array( $meta ) ? $meta : array();
	} elseif ( function_exists( 'alipasandi_get_service_legacy' ) ) {
		$data = alipasandi_get_service_legacy( $key );
	}

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

	echo '<p class="description">' . esc_html__( 'ذخیره در Post Meta (_alipasandi_service). اولویت: Meta موجود = منبع کامل صفحه. فقط اگر Meta اصلاً وجود نداشته باشد Fallback به فایل legacy فعال می‌شود. خالی‌کردن عمدی یک فیلد باعث بازگشت متن قدیمی نمی‌شود. H1 یک تگ واحد است.', 'alipasandi-clinic' ) . '</p>';

	$status = alipasandi_get_service_migration_status();
	if ( ! empty( $status['pages'][ $key ] ) ) {
		echo '<p><em>' . esc_html( sprintf( /* translators: %s status */ __( 'وضعیت Migration این صفحه: %s', 'alipasandi-clinic' ), $status['pages'][ $key ] ) ) . '</em></p>';
	}

	echo '<h3>' . esc_html__( 'Hero', 'alipasandi-clinic' ) . '</h3>';
	$field( 'eyebrow', 'Eyebrow' );
	$field( 'title', 'عنوان H1 — بخش اول' );
	$field( 'title_gold', 'عنوان H1 — بخش طلایی (همان H1)' );
	$field( 'intro', 'Intro (لینک HTML ساده مجاز)', 'textarea' );
	$field( 'image', 'نام فایل bundled (legacy) مثلاً implant-hero.jpg' );
	$field( 'image_id', 'Attachment ID تصویر (ترجیحی برای آینده — عدد Media Library)' );
	$field( 'image_alt', 'ALT تصویر (خالی = تزئینی)' );

	echo '<h3>' . esc_html__( 'بخش چیست؟', 'alipasandi-clinic' ) . '</h3>';
	$field( 'what_label', 'Label' );
	$field( 'what_title', 'عنوان H2' );
	$what_text = isset( $data['what_text'] ) && is_array( $data['what_text'] ) ? $data['what_text'] : array();
	echo '<p><strong>' . esc_html__( 'پاراگراف‌ها (جدا با خط خالی؛ لینک <a> مجاز)', 'alipasandi-clinic' ) . '</strong><br>';
	echo '<textarea class="large-text" rows="5" name="alipasandi_svc[what_text_raw]">' . esc_textarea( implode( "\n\n", $what_text ) ) . '</textarea></p>';

	$diagram = isset( $data['diagram'] ) && is_array( $data['diagram'] ) ? $data['diagram'] : array();
	echo '<p><strong>' . esc_html__( 'Diagram (ویرگول)', 'alipasandi-clinic' ) . '</strong><br>';
	echo '<input class="large-text" type="text" name="alipasandi_svc[diagram_raw]" value="' . esc_attr( implode( '، ', $diagram ) ) . '"></p>';

	echo '<h3>' . esc_html__( 'مزایا', 'alipasandi-clinic' ) . '</h3>';
	$field( 'benefit_label', 'Label' );
	$field( 'benefit_title', 'عنوان H2' );
	$benefits = isset( $data['benefits'] ) && is_array( $data['benefits'] ) ? $data['benefits'] : array();
	echo '<p class="description">' . esc_html__( 'هر خط: عنوان | توضیح | آیکن', 'alipasandi-clinic' ) . '</p>';
	$ben_lines = array();
	foreach ( $benefits as $b ) {
		$ben_lines[] = ( isset( $b[0] ) ? $b[0] : '' ) . ' | ' . ( isset( $b[1] ) ? $b[1] : '' ) . ' | ' . ( isset( $b[2] ) ? $b[2] : 'tooth' );
	}
	echo '<textarea class="large-text" rows="6" name="alipasandi_svc[benefits_raw]">' . esc_textarea( implode( "\n", $ben_lines ) ) . '</textarea>';

	echo '<h3>' . esc_html__( 'مراحل', 'alipasandi-clinic' ) . '</h3>';
	$field( 'steps_label', 'Label' );
	$steps = isset( $data['steps'] ) && is_array( $data['steps'] ) ? $data['steps'] : array();
	$step_lines = array();
	foreach ( $steps as $s ) {
		$step_lines[] = ( isset( $s[0] ) ? $s[0] : '' ) . ' | ' . ( isset( $s[1] ) ? $s[1] : '' ) . ' | ' . ( isset( $s[2] ) ? $s[2] : '' );
	}
	echo '<textarea class="large-text" rows="6" name="alipasandi_svc[steps_raw]">' . esc_textarea( implode( "\n", $step_lines ) ) . '</textarea>';

	echo '<h3>' . esc_html__( 'ارزیابی / توجه', 'alipasandi-clinic' ) . '</h3>';
	$field( 'candidate_title', 'عنوان ارزیابی' );
	$field( 'candidate_text', 'متن ارزیابی', 'textarea' );
	$field( 'notice_title', 'عنوان توجه' );
	$field( 'notice_text', 'متن توجه', 'textarea' );

	echo '<h3>' . esc_html__( 'FAQ — هر خط: سؤال || پاسخ', 'alipasandi-clinic' ) . '</h3>';
	$faqs = isset( $data['faqs'] ) && is_array( $data['faqs'] ) ? $data['faqs'] : array();
	$faq_lines = array();
	foreach ( $faqs as $f ) {
		$faq_lines[] = ( isset( $f[0] ) ? $f[0] : '' ) . ' || ' . ( isset( $f[1] ) ? $f[1] : '' );
	}
	echo '<textarea class="large-text" rows="8" name="alipasandi_svc[faqs_raw]">' . esc_textarea( implode( "\n", $faq_lines ) ) . '</textarea>';

	echo '<h3>' . esc_html__( 'CTA', 'alipasandi-clinic' ) . '</h3>';
	$field( 'cta_title', 'عنوان CTA' );
	$field( 'cta_text', 'متن CTA', 'textarea' );
}

/**
 * Save: nonce, edit_post capability, service page only, skip autosave/revision,
 * skip if meta box payload absent (do not clear existing meta).
 *
 * @param int $post_id Post ID.
 */
function alipasandi_save_service_meta( $post_id ) {
	// Autosave must not write incomplete meta.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Revisions: handled by revision copy hooks; skip direct save on revision ID.
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['alipasandi_service_meta_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alipasandi_service_meta_nonce'] ) ), 'alipasandi_save_service_meta' ) ) {
		return;
	}
	// Incomplete request (meta box not submitted) → leave existing meta untouched.
	if ( empty( $_POST['alipasandi_svc'] ) || ! is_array( $_POST['alipasandi_svc'] ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! alipasandi_service_key_for_post( $post_id ) ) {
		return;
	}

	$raw  = wp_unslash( $_POST['alipasandi_svc'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$data = array();

	foreach ( array(
		'eyebrow', 'title', 'title_gold', 'intro', 'image', 'image_alt', 'image_id',
		'what_label', 'what_title', 'benefit_label', 'benefit_title', 'steps_label',
		'candidate_title', 'candidate_text', 'notice_title', 'notice_text',
		'cta_title', 'cta_text',
	) as $k ) {
		$data[ $k ] = isset( $raw[ $k ] ) ? $raw[ $k ] : '';
	}

	$data['what_text'] = array();
	if ( ! empty( $raw['what_text_raw'] ) ) {
		foreach ( preg_split( '/\n\s*\n/', (string) $raw['what_text_raw'] ) as $p ) {
			$p = trim( $p );
			if ( '' !== $p ) {
				$data['what_text'][] = $p;
			}
		}
	}

	$data['diagram'] = array();
	if ( ! empty( $raw['diagram_raw'] ) ) {
		foreach ( preg_split( '/[,،]/u', (string) $raw['diagram_raw'] ) as $p ) {
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

/**
 * Copy service meta into revisions so mistaken edits can be restored.
 *
 * @param int $revision_id Revision post ID.
 */
function alipasandi_service_meta_revision_fields( $revision_id ) {
	$revision = get_post( $revision_id );
	if ( ! $revision || ! $revision->post_parent ) {
		return;
	}
	$parent_id = (int) $revision->post_parent;
	if ( ! alipasandi_service_key_for_post( $parent_id ) ) {
		return;
	}
	if ( ! alipasandi_service_meta_exists( $parent_id ) ) {
		return;
	}
	$meta = get_post_meta( $parent_id, ALIPASANDI_SERVICE_META_KEY, true );
	if ( is_array( $meta ) ) {
		update_metadata( 'post', $revision_id, ALIPASANDI_SERVICE_META_KEY, $meta );
	}
}
add_action( '_wp_put_post_revision', 'alipasandi_service_meta_revision_fields' );

/**
 * Restore service meta when a revision is restored.
 *
 * @param int $post_id     Parent post ID.
 * @param int $revision_id Revision ID.
 */
function alipasandi_service_meta_restore_revision( $post_id, $revision_id ) {
	if ( ! alipasandi_service_key_for_post( $post_id ) ) {
		return;
	}
	$meta = get_metadata( 'post', $revision_id, ALIPASANDI_SERVICE_META_KEY, true );
	if ( is_array( $meta ) ) {
		update_post_meta( $post_id, ALIPASANDI_SERVICE_META_KEY, $meta );
	}
}
add_action( 'wp_restore_post_revision', 'alipasandi_service_meta_restore_revision', 10, 2 );
