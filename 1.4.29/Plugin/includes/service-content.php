<?php
/**
 * Site-level service content. This file intentionally owns data and editing;
 * the active theme owns rendering only.
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
define( 'ALIPASANDI_SERVICE_KEY_META', '_alipasandi_service_key' );
define( 'ALIPASANDI_SERVICE_META_SCHEMA', 1 );
/** Option: per-page migration status array + completed flag. */
define( 'ALIPASANDI_SERVICE_META_STATUS', 'alipasandi_service_meta_status_v1' );
define( 'ALIPASANDI_SERVICE_MIGRATION_LOG', 'alipasandi_service_meta_log_v1' );
define( 'ALIPASANDI_SERVICE_MAX_PARAGRAPHS', 12 );
define( 'ALIPASANDI_SERVICE_MAX_BENEFITS', 12 );
define( 'ALIPASANDI_SERVICE_MAX_STEPS', 12 );
define( 'ALIPASANDI_SERVICE_MAX_FAQS', 20 );

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
	$found = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			'meta_key'               => ALIPASANDI_SERVICE_KEY_META,
			'meta_value'             => $key,
			'posts_per_page'         => 5,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	if ( count( $found ) > 1 ) {
		return null; // Fail closed: duplicate stable identity.
	}
	if ( 1 === count( $found ) ) {
		return $found[0];
	}
	$item = function_exists( 'alipasandi_service_registry_item' ) ? alipasandi_service_registry_item( $key ) : null;
	$slug = is_array( $item ) && ! empty( $item['page_slug'] ) ? $item['page_slug'] : 'services/' . $key;
	$page = get_page_by_path( $slug );
	if ( ! $page ) { $page = get_page_by_path( $key ); }
	return $page instanceof WP_Post ? $page : null;
}

function alipasandi_service_key_posts( $key, $exclude_id = 0 ) {
	$posts = get_posts( array( 'post_type'=>'page', 'post_status'=>array( 'publish','draft','private','pending','future' ), 'meta_key'=>ALIPASANDI_SERVICE_KEY_META, 'meta_value'=>sanitize_key( $key ), 'posts_per_page'=>10, 'no_found_rows'=>true ) );
	return array_values( array_filter( $posts, function ( $post ) use ( $exclude_id ) { return (int) $post->ID !== (int) $exclude_id; } ) );
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
	$stable_key = sanitize_key( (string) get_post_meta( $post_id, ALIPASANDI_SERVICE_KEY_META, true ) );
	if ( in_array( $stable_key, alipasandi_service_keys(), true ) ) {
		return $stable_key;
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
		ALIPASANDI_SERVICE_KEY_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'sanitize_key',
			'auth_callback'     => function ( $allowed, $meta_key, $object_id ) {
				return current_user_can( 'manage_options' ) && current_user_can( 'edit_post', (int) $object_id );
			},
		)
	);
	$args = array(
		'type'              => 'object',
		'description'       => 'Structured service landing content (Alipasandi).',
		'single'            => true,
		'default'           => array(),
		'show_in_rest'      => false,
		'auth_callback'     => function ( $allowed, $meta_key, $object_id ) {
			$object_id = (int) $object_id;
			return $object_id > 0 && current_user_can( 'edit_post', $object_id ) && (bool) alipasandi_service_key_for_post( $object_id );
		},
		'sanitize_callback' => 'alipasandi_sanitize_service_meta',
	);
	if ( version_compare( get_bloginfo( 'version' ), '6.4', '>=' ) ) {
		$args['revisions_enabled'] = true;
	}
	register_post_meta(
		'page',
		ALIPASANDI_SERVICE_META_KEY,
		$args
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
		'span'   => array(),
	);
}

/** Normalize allowed links and force safe rel for new-window links. */
function alipasandi_service_sanitize_html( $html ) {
	$clean = wp_kses( (string) $html, alipasandi_service_allowed_html(), array( 'http', 'https', 'mailto', 'tel' ) );
	return preg_replace_callback(
		'/<a\b([^>]*)>/i',
		function ( $matches ) {
			$attrs = $matches[1];
			if ( preg_match( '/target\s*=\s*(["\'])_blank\1/i', $attrs ) ) {
				$attrs = preg_replace( '/\srel\s*=\s*(["\']).*?\1/i', '', $attrs );
				$attrs .= ' rel="noopener noreferrer"';
			}
			return '<a' . $attrs . '>';
		},
		$clean
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
		$out[ $k ] = alipasandi_service_sanitize_html( $raw );
	}

	// Optional attachment ID (preferred over raw filename for long-term portability).
	$out['image_id'] = 0;
	if ( isset( $value['image_id'] ) ) {
		$image_id = absint( $value['image_id'] );
		if ( $image_id && 'attachment' === get_post_type( $image_id ) && wp_attachment_is_image( $image_id ) ) {
			$out['image_id'] = $image_id;
		}
	}
	$out['image_decorative'] = empty( $value['image_decorative'] ) ? 0 : 1;

	// what_text paragraphs — keep empty strings if intentionally blank lines were not used; only trim.
	$out['what_text'] = array();
	if ( ! empty( $value['what_text'] ) && is_array( $value['what_text'] ) ) {
		foreach ( array_slice( $value['what_text'], 0, ALIPASANDI_SERVICE_MAX_PARAGRAPHS ) as $p ) {
			$p = alipasandi_service_sanitize_html( wp_unslash( (string) $p ) );
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
		foreach ( array_slice( $value['benefits'], 0, ALIPASANDI_SERVICE_MAX_BENEFITS ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$title = isset( $row[0] ) ? sanitize_text_field( wp_unslash( (string) $row[0] ) ) : '';
			$text  = isset( $row[1] ) ? alipasandi_service_sanitize_html( wp_unslash( (string) $row[1] ) ) : '';
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
		foreach ( array_slice( $value['steps'], 0, ALIPASANDI_SERVICE_MAX_STEPS ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$num   = isset( $row[0] ) ? sanitize_text_field( wp_unslash( (string) $row[0] ) ) : '';
			$title = isset( $row[1] ) ? sanitize_text_field( wp_unslash( (string) $row[1] ) ) : '';
			$text  = isset( $row[2] ) ? alipasandi_service_sanitize_html( wp_unslash( (string) $row[2] ) ) : '';
			if ( '' !== $title || '' !== trim( wp_strip_all_tags( $text ) ) ) {
				$out['steps'][] = array( $num, $title, $text );
			}
		}
	}

	$out['faqs'] = array();
	if ( ! empty( $value['faqs'] ) && is_array( $value['faqs'] ) ) {
		foreach ( array_slice( $value['faqs'], 0, ALIPASANDI_SERVICE_MAX_FAQS ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$q = isset( $row[0] ) ? sanitize_text_field( wp_unslash( (string) $row[0] ) ) : '';
			$a = isset( $row[1] ) ? alipasandi_service_sanitize_html( wp_unslash( (string) $row[1] ) ) : '';
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
		$status = get_option( ALIPASANDI_SERVICE_META_STATUS, array() );
		if ( is_array( $status ) && ! empty( $status['completed'] ) ) {
			set_transient( 'alipasandi_legacy_fallback_seen', array( 'key'=>sanitize_key( $key ), 'time'=>current_time( 'mysql', true ) ), DAY_IN_SECONDS );
			error_log( '[Alipasandi] CRITICAL: legacy service fallback used after completed migration for key=' . sanitize_key( $key ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
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

/** Record bounded, non-sensitive migration diagnostics for admins. */
function alipasandi_service_log_migration( $page_id, $key, $status, $reason = '' ) {
	$log   = get_option( ALIPASANDI_SERVICE_MIGRATION_LOG, array() );
	$log   = is_array( $log ) ? $log : array();
	$log[] = array(
		'page_id'  => (int) $page_id,
		'key'      => sanitize_key( $key ),
		'status'   => sanitize_key( $status ),
		'timestamp'=> current_time( 'mysql', true ),
		'reason'   => sanitize_text_field( $reason ),
	);
	update_option( ALIPASANDI_SERVICE_MIGRATION_LOG, array_slice( $log, -100 ), false );
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
			alipasandi_service_log_migration( 0, $key, 'failed', 'page_not_found' );
			continue;
		}
		if ( alipasandi_service_key_posts( $key, $page->ID ) ) {
			$pages_status[ $key ] = 'failed';
			$all_ok = false;
			alipasandi_service_log_migration( $page->ID, $key, 'failed', 'duplicate_service_key' );
			continue;
		}
		update_post_meta( $page->ID, ALIPASANDI_SERVICE_KEY_META, $key );

		if ( alipasandi_service_meta_exists( $page->ID ) ) {
			$pages_status[ $key ] = 'existing';
			alipasandi_service_log_migration( $page->ID, $key, 'existing' );
			continue;
		}

		$legacy = alipasandi_get_service_legacy( $key );
		if ( empty( $legacy ) || ! is_array( $legacy ) ) {
			$pages_status[ $key ] = 'failed';
			$all_ok               = false;
			alipasandi_service_log_migration( $page->ID, $key, 'failed', 'legacy_missing' );
			continue;
		}

		$clean = alipasandi_sanitize_service_meta( $legacy );
		$ok    = update_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, $clean );
		// update_post_meta returns false if value unchanged; treat presence as success.
		if ( false === $ok && ! alipasandi_service_meta_exists( $page->ID ) ) {
			$pages_status[ $key ] = 'failed';
			$all_ok               = false;
			alipasandi_service_log_migration( $page->ID, $key, 'failed', 'meta_write_failed' );
			continue;
		}
		$pages_status[ $key ] = 'migrated';
		alipasandi_service_log_migration( $page->ID, $key, 'migrated' );
	}

	$status['pages'] = $pages_status;
	$status['completed'] = $all_ok;
	update_option( ALIPASANDI_SERVICE_META_STATUS, $status, false );
}

/** Preview migration without writing any post meta or status. */
function alipasandi_service_migration_dry_run() {
	$plan = array( 'schema_version'=>ALIPASANDI_SERVICE_META_SCHEMA, 'write'=>false, 'services'=>array(), 'pass'=>true );
	foreach ( alipasandi_service_keys() as $key ) {
		$stable = alipasandi_service_key_posts( $key );
		$page = alipasandi_get_service_page( $key );
		$conflict = count( $stable ) > 1;
		$action = $conflict ? 'blocked_conflict' : ( ! $page ? 'blocked_missing_page' : ( alipasandi_service_meta_exists( $page->ID ) ? 'skip_existing' : 'migrate' ) );
		$plan['services'][ $key ] = array( 'action'=>$action, 'page_id'=>$page ? (int)$page->ID : 0, 'slug'=>$page ? $page->post_name : '', 'stable_key_page_ids'=>wp_list_pluck( $stable, 'ID' ) );
		if ( 0 === strpos( $action, 'blocked_' ) ) { $plan['pass'] = false; }
	}
	return $plan;
}

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
	$key = $post_id ? alipasandi_service_key_for_post( $post_id ) : '';
	if ( ! $post_id || ! $key ) {
		return;
	}
	if ( count( alipasandi_service_key_posts( $key ) ) > 1 ) {
		add_action( 'admin_notices', function () use ( $key ) {
			echo '<div class="notice notice-error"><p><strong>' . esc_html( 'Service key conflict: ' . $key . '. Editing is disabled until duplicate keys are resolved.' ) . '</strong></p></div>';
		} );
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

	echo '<div class="notice notice-warning inline"><p><strong>' . esc_html__( 'تغییرات این بخش فقط با دکمه Update ذخیره می‌شوند و Autosave وردپرس این فیلدها را بازیابی نمی‌کند.', 'alipasandi-service-content' ) . '</strong></p></div>';
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
	$field( 'image_alt', 'ALT تصویر (برای تصویر محتوایی الزامی)' );
	$decorative = ! empty( $data['image_decorative'] );
	echo '<p><label><input type="checkbox" name="alipasandi_svc[image_decorative]" value="1" ' . checked( $decorative, true, false ) . '> ' . esc_html__( 'این تصویر صرفاً تزئینی است (ALT خالی مجاز)', 'alipasandi-service-content' ) . '</label></p>';

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
	echo '<p class="description">' . esc_html__( 'Structured rows؛ ردیف‌های کاملاً خالی ذخیره نمی‌شوند.', 'alipasandi-service-content' ) . '</p>';
	for ( $i = 0; $i < ALIPASANDI_SERVICE_MAX_BENEFITS; $i++ ) {
		$row = isset( $benefits[ $i ] ) ? $benefits[ $i ] : array( '', '', 'tooth' );
		echo '<fieldset style="border:1px solid #dcdcde;padding:10px;margin:8px 0"><legend>مزیت ' . esc_html( $i + 1 ) . '</legend>';
		echo '<label>عنوان <input class="regular-text" name="alipasandi_svc[benefits][' . esc_attr( $i ) . '][title]" value="' . esc_attr( $row[0] ?? '' ) . '"></label> ';
		echo '<label>آیکن <input dir="ltr" name="alipasandi_svc[benefits][' . esc_attr( $i ) . '][icon]" value="' . esc_attr( $row[2] ?? 'tooth' ) . '"></label>';
		echo '<p><label>توضیح <textarea class="large-text" rows="2" name="alipasandi_svc[benefits][' . esc_attr( $i ) . '][text]">' . esc_textarea( $row[1] ?? '' ) . '</textarea></label></p></fieldset>';
	}

	echo '<h3>' . esc_html__( 'مراحل', 'alipasandi-clinic' ) . '</h3>';
	$field( 'steps_label', 'Label' );
	$steps = isset( $data['steps'] ) && is_array( $data['steps'] ) ? $data['steps'] : array();
	for ( $i = 0; $i < ALIPASANDI_SERVICE_MAX_STEPS; $i++ ) {
		$row = isset( $steps[ $i ] ) ? $steps[ $i ] : array( '', '', '' );
		echo '<fieldset style="border:1px solid #dcdcde;padding:10px;margin:8px 0"><legend>مرحله ' . esc_html( $i + 1 ) . '</legend>';
		echo '<label>شماره <input dir="ltr" size="5" name="alipasandi_svc[steps][' . esc_attr( $i ) . '][number]" value="' . esc_attr( $row[0] ?? '' ) . '"></label> ';
		echo '<label>عنوان <input class="regular-text" name="alipasandi_svc[steps][' . esc_attr( $i ) . '][title]" value="' . esc_attr( $row[1] ?? '' ) . '"></label>';
		echo '<p><label>توضیح <textarea class="large-text" rows="2" name="alipasandi_svc[steps][' . esc_attr( $i ) . '][text]">' . esc_textarea( $row[2] ?? '' ) . '</textarea></label></p></fieldset>';
	}

	echo '<h3>' . esc_html__( 'ارزیابی / توجه', 'alipasandi-clinic' ) . '</h3>';
	$field( 'candidate_title', 'عنوان ارزیابی' );
	$field( 'candidate_text', 'متن ارزیابی', 'textarea' );
	$field( 'notice_title', 'عنوان توجه' );
	$field( 'notice_text', 'متن توجه', 'textarea' );

	echo '<h3>' . esc_html__( 'FAQ — Structured rows', 'alipasandi-clinic' ) . '</h3>';
	$faqs = isset( $data['faqs'] ) && is_array( $data['faqs'] ) ? $data['faqs'] : array();
	for ( $i = 0; $i < ALIPASANDI_SERVICE_MAX_FAQS; $i++ ) {
		$row = isset( $faqs[ $i ] ) ? $faqs[ $i ] : array( '', '' );
		echo '<fieldset style="border:1px solid #dcdcde;padding:10px;margin:8px 0"><legend>FAQ ' . esc_html( $i + 1 ) . '</legend>';
		echo '<label>سؤال <input class="large-text" name="alipasandi_svc[faqs][' . esc_attr( $i ) . '][question]" value="' . esc_attr( $row[0] ?? '' ) . '"></label>';
		echo '<p><label>پاسخ <textarea class="large-text" rows="3" name="alipasandi_svc[faqs][' . esc_attr( $i ) . '][answer]">' . esc_textarea( $row[1] ?? '' ) . '</textarea></label></p></fieldset>';
	}

	echo '<h3>' . esc_html__( 'CTA', 'alipasandi-clinic' ) . '</h3>';
	$field( 'cta_title', 'عنوان CTA' );
	$field( 'cta_text', 'متن CTA', 'textarea' );
	echo '<input type="hidden" name="alipasandi_svc_payload_complete" value="1">';
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
	// End-of-form sentinel: protects existing content when PHP truncates POST.
	if ( empty( $_POST['alipasandi_svc_payload_complete'] ) ) {
		set_transient( 'alipasandi_service_notice_' . get_current_user_id(), 'partial_post', 60 );
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! alipasandi_service_key_for_post( $post_id ) ) {
		return;
	}
	$key = alipasandi_service_key_for_post( $post_id );
	if ( count( alipasandi_service_key_posts( $key ) ) > 1 ) {
		set_transient( 'alipasandi_service_notice_' . get_current_user_id(), 'duplicate_key', 60 );
		return;
	}

	$raw  = wp_unslash( $_POST['alipasandi_svc'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$data = array();

	foreach ( array(
		'eyebrow', 'title', 'title_gold', 'intro', 'image', 'image_alt', 'image_id', 'image_decorative',
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
	foreach ( array_slice( isset( $raw['benefits'] ) && is_array( $raw['benefits'] ) ? $raw['benefits'] : array(), 0, ALIPASANDI_SERVICE_MAX_BENEFITS ) as $row ) {
		if ( is_array( $row ) ) { $data['benefits'][] = array( $row['title'] ?? '', $row['text'] ?? '', $row['icon'] ?? 'tooth' ); }
	}

	$data['steps'] = array();
	foreach ( array_slice( isset( $raw['steps'] ) && is_array( $raw['steps'] ) ? $raw['steps'] : array(), 0, ALIPASANDI_SERVICE_MAX_STEPS ) as $row ) {
		if ( is_array( $row ) ) { $data['steps'][] = array( $row['number'] ?? '', $row['title'] ?? '', $row['text'] ?? '' ); }
	}

	$data['faqs'] = array();
	foreach ( array_slice( isset( $raw['faqs'] ) && is_array( $raw['faqs'] ) ? $raw['faqs'] : array(), 0, ALIPASANDI_SERVICE_MAX_FAQS ) as $row ) {
		if ( is_array( $row ) ) { $data['faqs'][] = array( $row['question'] ?? '', $row['answer'] ?? '' ); }
	}

	$clean = alipasandi_sanitize_service_meta( $data );
	if ( empty( $clean['title'] ) || empty( $clean['intro'] ) ) {
		set_transient( 'alipasandi_service_notice_' . get_current_user_id(), 'required', 60 );
		return;
	}
	if ( ! empty( $clean['image_id'] ) && empty( $clean['image_decorative'] ) && empty( $clean['image_alt'] ) ) {
		$media_alt = trim( (string) get_post_meta( $clean['image_id'], '_wp_attachment_image_alt', true ) );
		if ( '' !== $media_alt ) {
			$clean['image_alt'] = sanitize_text_field( $media_alt );
		} else {
			set_transient( 'alipasandi_service_notice_' . get_current_user_id(), 'image_alt', 60 );
			return;
		}
	}
	update_post_meta( $post_id, ALIPASANDI_SERVICE_META_KEY, $clean );
	clean_post_cache( $post_id );
}
add_action( 'save_post_page', 'alipasandi_save_service_meta' );

/**
 * Copy service meta into revisions so mistaken edits can be restored.
 *
 * @param int $revision_id Revision post ID.
 */
function alipasandi_service_meta_revision_fields( $revision_id ) {
	if ( version_compare( get_bloginfo( 'version' ), '6.4', '>=' ) ) {
		return;
	}
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
	if ( version_compare( get_bloginfo( 'version' ), '6.4', '>=' ) ) {
		return;
	}
	if ( ! alipasandi_service_key_for_post( $post_id ) ) {
		return;
	}
	// An old revision without this key leaves current service meta untouched.
	if ( metadata_exists( 'post', $revision_id, ALIPASANDI_SERVICE_META_KEY ) ) {
		$meta = get_metadata( 'post', $revision_id, ALIPASANDI_SERVICE_META_KEY, true );
		update_post_meta( $post_id, ALIPASANDI_SERVICE_META_KEY, $meta );
	}
}
add_action( 'wp_restore_post_revision', 'alipasandi_service_meta_restore_revision', 10, 2 );

/** Admin validation feedback. */
function alipasandi_service_admin_notice() {
	$key = 'alipasandi_service_notice_' . get_current_user_id();
	$code = get_transient( $key );
	if ( ! $code ) {
		return;
	}
	delete_transient( $key );
	$messages = array(
		'partial_post' => 'درخواست ناقص بود؛ برای جلوگیری از حذف محتوا هیچ تغییری ذخیره نشد. محدودیت max_input_vars/PHP را بررسی کنید.',
		'required'     => 'عنوان H1 و مقدمه الزامی‌اند؛ هیچ تغییری ذخیره نشد.',
		'image_alt'    => 'برای تصویر محتوایی ALT لازم است؛ ALT وارد کنید یا گزینه تزئینی را انتخاب کنید.',
		'duplicate_key'=> 'Service Key تکراری است؛ برای جلوگیری از ویرایش Page اشتباه هیچ تغییری ذخیره نشد.',
	);
	if ( isset( $messages[ $code ] ) ) {
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $messages[ $code ] ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'alipasandi_service_admin_notice' );

/** Return a machine-readable health report for the four service records. */
function alipasandi_service_health_report() {
	$status = alipasandi_get_service_migration_status();
	$report = array(
		'plugin_version' => ALIPASANDI_SERVICE_CONTENT_PLUGIN_VERSION,
		'schema_version' => ALIPASANDI_SERVICE_META_SCHEMA,
		'migration_completed' => ! empty( $status['completed'] ),
		'services' => array(),
	);
	foreach ( alipasandi_service_keys() as $key ) {
		$stable_posts = alipasandi_service_key_posts( $key );
		$page   = alipasandi_get_service_page( $key );
		$meta   = $page ? get_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, true ) : array();
		$issues = array();
		if ( count( $stable_posts ) > 1 ) {
			$issues[] = 'duplicate_service_key';
		}
		if ( ! $page ) {
			$issues[] = 'page_missing';
		} elseif ( ! alipasandi_service_meta_exists( $page->ID ) ) {
			$issues[] = 'meta_missing';
		} else {
			if ( ! is_array( $meta ) || ALIPASANDI_SERVICE_META_SCHEMA !== (int) ( isset( $meta['schema_version'] ) ? $meta['schema_version'] : 0 ) ) {
				$issues[] = 'schema_invalid';
			}
			if ( empty( $meta['title'] ) || empty( $meta['intro'] ) ) {
				$issues[] = 'required_fields_missing';
			}
			if ( ! empty( $meta['image_id'] ) && ( ! wp_attachment_is_image( (int) $meta['image_id'] ) || ! get_attached_file( (int) $meta['image_id'] ) ) ) {
				$issues[] = 'image_invalid';
			}
			if ( strlen( maybe_serialize( $meta ) ) > 102400 ) {
				$issues[] = 'meta_oversized';
			}
			$rendered_copy = wp_strip_all_tags( maybe_serialize( $meta ) );
			if ( preg_match( '/رزرو\s+(?:نوبت|وقت)/u', $rendered_copy ) ) {
				$issues[] = 'booking_language_requires_request_wording';
			}
		}
		$report['services'][ $key ] = array(
			'pass'    => empty( $issues ),
			'page_id' => $page ? (int) $page->ID : 0,
			'issues'  => $issues,
		);
	}
	$report['pass'] = $report['migration_completed'];
	foreach ( $report['services'] as $service ) {
		$report['pass'] = $report['pass'] && $service['pass'];
	}
	if ( function_exists( 'alipasandi_operational_health_report' ) ) {
		$report['operational'] = alipasandi_operational_health_report();
		$report['pass'] = $report['pass'] && ! empty( $report['operational']['pass'] );
	}
	return $report;
}

/** Tools screen for explicit migration, health, export and controlled logs. */
function alipasandi_service_tools_menu() {
	add_management_page( 'Service Content', 'Service Content', 'manage_options', 'alipasandi-service-content', 'alipasandi_service_tools_page' );
}
add_action( 'admin_menu', 'alipasandi_service_tools_menu' );

function alipasandi_service_tools_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$health = alipasandi_service_health_report();
	$log    = get_option( ALIPASANDI_SERVICE_MIGRATION_LOG, array() );
	$dry_run = alipasandi_service_migration_dry_run();
	?>
	<div class="wrap" dir="rtl"><h1>Service Content</h1>
	<p>Migration فقط به‌صورت صریح اجرا می‌شود و Meta موجود را overwrite نمی‌کند.</p>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="alipasandi_service_migrate"><?php wp_nonce_field( 'alipasandi_service_migrate' ); ?>
		<?php submit_button( 'اجرای Migration / Retry', 'primary', 'submit', false ); ?>
	</form>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:12px">
		<input type="hidden" name="action" value="alipasandi_service_export"><?php wp_nonce_field( 'alipasandi_service_export' ); ?>
		<?php submit_button( 'Export JSON', 'secondary', 'submit', false ); ?>
	</form>
	<h2>Health: <?php echo ! empty( $health['pass'] ) ? 'PASS' : 'FAIL'; ?></h2>
	<pre><?php echo esc_html( wp_json_encode( $health, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ); ?></pre>
	<h2>Migration dry-run (بدون Write)</h2><pre><?php echo esc_html( wp_json_encode( $dry_run, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ); ?></pre>
	<h2>Migration log</h2><pre><?php echo esc_html( wp_json_encode( array_slice( (array) $log, -30 ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) ); ?></pre></div>
	<?php
}

function alipasandi_service_admin_migrate() {
	check_admin_referer( 'alipasandi_service_migrate' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Access denied.', 'alipasandi-service-content' ) );
	}
	alipasandi_migrate_service_content_to_meta();
	wp_safe_redirect( admin_url( 'tools.php?page=alipasandi-service-content' ) );
	exit;
}
add_action( 'admin_post_alipasandi_service_migrate', 'alipasandi_service_admin_migrate' );

function alipasandi_service_export_payload() {
	$out = array( 'schema_version' => ALIPASANDI_SERVICE_META_SCHEMA, 'exported_at_utc' => gmdate( 'c' ), 'services' => array() );
	foreach ( alipasandi_service_keys() as $key ) {
		$page = alipasandi_get_service_page( $key );
		$out['services'][ $key ] = array(
			'service_key' => $key,
			'page_id' => $page ? (int) $page->ID : 0,
			'slug'    => $page ? $page->post_name : '',
			'meta'    => $page ? get_post_meta( $page->ID, ALIPASANDI_SERVICE_META_KEY, true ) : null,
		);
	}
	$out['content_sha256'] = hash( 'sha256', wp_json_encode( $out['services'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
	return $out;
}

function alipasandi_service_admin_export() {
	check_admin_referer( 'alipasandi_service_export' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Access denied.', 'alipasandi-service-content' ) );
	}
	nocache_headers();
	header( 'Content-Type: application/json; charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="alipasandi-service-content-' . gmdate( 'Ymd-His' ) . '.json"' );
	echo wp_json_encode( alipasandi_service_export_payload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	exit;
}
add_action( 'admin_post_alipasandi_service_export', 'alipasandi_service_admin_export' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'alipasandi service migrate', function ( $args, $assoc_args ) {
		if ( ! empty( $assoc_args['dry-run'] ) ) {
			$plan = alipasandi_service_migration_dry_run();
			WP_CLI::line( wp_json_encode( $plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
			if ( empty( $plan['pass'] ) ) { WP_CLI::halt( 1 ); }
			return;
		}
		alipasandi_migrate_service_content_to_meta();
		WP_CLI::line( wp_json_encode( alipasandi_get_service_migration_status(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
	} );
	WP_CLI::add_command( 'alipasandi service health', function () {
		$report = alipasandi_service_health_report();
		WP_CLI::line( wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
		if ( empty( $report['pass'] ) ) {
			WP_CLI::halt( 1 );
		}
	} );
}

function alipasandi_plugin_site_health_tests( $tests ) {
	$tests['direct']['alipasandi_service_content_health'] = array(
		'label'=>'Alipasandi service content health',
		'test'=>function () {
			$report = alipasandi_service_health_report();
			return array( 'label'=>!empty($report['pass'])?'Service content سالم است':'Service content نیاز به اقدام دارد', 'status'=>!empty($report['pass'])?'good':'critical', 'badge'=>array('label'=>'Alipasandi','color'=>'blue'), 'description'=>'<pre>'.esc_html(wp_json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)).'</pre>', 'test'=>'alipasandi_service_content_health' );
		},
	);
	$tests['direct']['alipasandi_rank_math_owner'] = array(
		'label'=>'Alipasandi SEO owner availability',
		'test'=>function () {
			$active = defined( 'RANK_MATH_VERSION' );
			return array( 'label'=>$active?'Rank Math فعال است':'Rank Math، مالک SEO Production، غیرفعال است', 'status'=>$active?'good':'critical', 'badge'=>array('label'=>'Alipasandi','color'=>'blue'), 'description'=>'<p>'.esc_html($active?'SEO owner در دسترس است.':'Indexability و sitemap ممکن است از Matrix تولید خارج شوند؛ فعال‌سازی و regression QA لازم است.').'</p>', 'test'=>'alipasandi_rank_math_owner' );
		},
	);
	return $tests;
}
add_filter( 'site_status_tests', 'alipasandi_plugin_site_health_tests' );
