<?php
/**
 * Minimal in-memory WordPress environment for unit tests.
 *
 * Sanitization, escaping and hook behaviour come from real WordPress core files
 * (loaded by bootstrap.php); only the stateful layer WordPress normally backs with
 * a database — options, transients, post meta, mail, nonces — is replaced here.
 *
 * These functions are declared before core is loaded, so core never redeclares them.
 */

/** Test-visible state container. */
final class WP_Test_State {
	/** @var array<string,mixed> */
	public static $options = array();

	/** @var array<string,mixed> */
	public static $transients = array();

	/** @var array<string,mixed> */
	public static $theme_mods = array();

	/** @var array<int,array<string,mixed>> */
	public static $posts = array();

	/** @var array<string,mixed> */
	public static $post_meta = array();

	/** @var array<int,array<string,mixed>> */
	public static $mail = array();

	/** @var array<string,mixed> */
	public static $post_request = array();

	/** @var bool */
	public static $mail_succeeds = true;

	/** @var string */
	public static $current_time = '2026-01-15 09:00:00';

	/**
	 * Template-context state behind the conditional tags: admin flag, queried
	 * object and the archive title used by the breadcrumb builder.
	 *
	 * @var array<string,mixed>
	 */
	public static $query = array(
		'is_admin'      => false,
		'queried_id'    => 0,
		'archive_title' => '',
	);

	public static function reset(): void {
		self::$options      = array();
		self::$transients   = array();
		self::$theme_mods   = array();
		self::$posts        = array();
		self::$post_meta    = array();
		self::$mail         = array();
		self::$post_request = array();
		self::$mail_succeeds = true;
		self::$current_time = '2026-01-15 09:00:00';
		self::$query        = array(
			'is_admin'      => false,
			'queried_id'    => 0,
			'archive_title' => '',
		);
		$GLOBALS['wp_filter']         = array();
		$GLOBALS['wp_actions']        = array();
		$GLOBALS['wp_current_filter'] = array();
		$_POST                        = array();
		$_SERVER['REMOTE_ADDR']       = '203.0.113.9';
	}

	/** Register a fake post/page record. */
	public static function add_post( int $id, string $post_type = 'page', array $fields = array() ): void {
		self::$posts[ $id ] = array_merge(
			array(
				'ID'          => $id,
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'post_name'   => 'post-' . $id,
				'post_title'  => 'Post ' . $id,
				'post_parent' => 0,
			),
			$fields
		);
	}

	/** Hydrate a stored record into the WP_Post instance callers expect. */
	public static function post_object( array $record ): WP_Post {
		return new WP_Post( (object) $record );
	}
}

/* -------------------------------------------------------------------------
 * Options / transients / theme mods.
 * ---------------------------------------------------------------------- */

function get_option( $option, $default_value = false ) {
	return array_key_exists( $option, WP_Test_State::$options ) ? WP_Test_State::$options[ $option ] : $default_value;
}

function add_option( $option, $value = '', $deprecated = '', $autoload = true ) {
	if ( array_key_exists( $option, WP_Test_State::$options ) ) {
		return false;
	}
	WP_Test_State::$options[ $option ] = $value;
	return true;
}

function update_option( $option, $value, $autoload = null ) {
	WP_Test_State::$options[ $option ] = $value;
	return true;
}

function delete_option( $option ) {
	if ( ! array_key_exists( $option, WP_Test_State::$options ) ) {
		return false;
	}
	unset( WP_Test_State::$options[ $option ] );
	return true;
}

function get_transient( $transient ) {
	return array_key_exists( $transient, WP_Test_State::$transients ) ? WP_Test_State::$transients[ $transient ] : false;
}

function set_transient( $transient, $value, $expiration = 0 ) {
	WP_Test_State::$transients[ $transient ] = $value;
	return true;
}

function delete_transient( $transient ) {
	unset( WP_Test_State::$transients[ $transient ] );
	return true;
}

function get_theme_mod( $name, $default_value = false ) {
	return array_key_exists( $name, WP_Test_State::$theme_mods ) ? WP_Test_State::$theme_mods[ $name ] : $default_value;
}

/* -------------------------------------------------------------------------
 * Posts and post meta.
 * ---------------------------------------------------------------------- */

function get_post( $post = null ) {
	$id = is_object( $post ) ? (int) $post->ID : (int) $post;
	return isset( WP_Test_State::$posts[ $id ] ) ? WP_Test_State::post_object( WP_Test_State::$posts[ $id ] ) : null;
}

function get_post_type( $post = null ) {
	$record = get_post( $post );
	return $record ? $record->post_type : false;
}

function wp_attachment_is_image( $post = null ) {
	$record = get_post( $post );
	return $record && ! empty( $record->is_image );
}

function metadata_exists( $meta_type, $object_id, $meta_key ) {
	return array_key_exists( $meta_type . ':' . (int) $object_id . ':' . $meta_key, WP_Test_State::$post_meta );
}

function get_post_meta( $post_id, $key = '', $single = false ) {
	$index = 'post:' . (int) $post_id . ':' . $key;
	if ( ! array_key_exists( $index, WP_Test_State::$post_meta ) ) {
		return $single ? '' : array();
	}
	$value = WP_Test_State::$post_meta[ $index ];
	return $single ? $value : array( $value );
}

function update_post_meta( $post_id, $key, $value, $prev_value = '' ) {
	WP_Test_State::$post_meta[ 'post:' . (int) $post_id . ':' . $key ] = $value;
	return true;
}

function delete_post_meta( $post_id, $key, $value = '' ) {
	unset( WP_Test_State::$post_meta[ 'post:' . (int) $post_id . ':' . $key ] );
	return true;
}

/**
 * Minimal get_posts() supporting the slug/meta lookups used by the plugin.
 *
 * @param array $args Query arguments.
 * @return array
 */
function get_posts( $args = array() ) {
	$post_type = isset( $args['post_type'] ) ? (array) $args['post_type'] : array( 'post' );
	$names     = array();
	if ( isset( $args['name'] ) ) {
		$names[] = $args['name'];
	}
	if ( isset( $args['post_name__in'] ) ) {
		$names = array_merge( $names, (array) $args['post_name__in'] );
	}

	$found = array();
	foreach ( WP_Test_State::$posts as $post ) {
		if ( ! in_array( $post['post_type'], $post_type, true ) ) {
			continue;
		}
		if ( $names && ! in_array( $post['post_name'], $names, true ) ) {
			continue;
		}
		if ( isset( $args['meta_key'] ) ) {
			$meta = get_post_meta( $post['ID'], $args['meta_key'], true );
			if ( isset( $args['meta_value'] ) && (string) $meta !== (string) $args['meta_value'] ) {
				continue;
			}
			if ( ! isset( $args['meta_value'] ) && '' === $meta ) {
				continue;
			}
		}
		if ( ! empty( $args['post__not_in'] ) && in_array( $post['ID'], (array) $args['post__not_in'], true ) ) {
			continue;
		}
		$found[] = WP_Test_State::post_object( $post );
	}

	if ( ! empty( $args['fields'] ) && 'ids' === $args['fields'] ) {
		$found = array_map(
			static function ( $post ) {
				return (int) $post->ID;
			},
			$found
		);
	}

	$limit = isset( $args['numberposts'] ) ? (int) $args['numberposts'] : ( isset( $args['posts_per_page'] ) ? (int) $args['posts_per_page'] : -1 );
	return $limit > 0 ? array_slice( $found, 0, $limit ) : $found;
}

function get_metadata( $meta_type, $object_id, $meta_key = '', $single = false ) {
	$index = $meta_type . ':' . (int) $object_id . ':' . $meta_key;
	if ( ! array_key_exists( $index, WP_Test_State::$post_meta ) ) {
		return $single ? '' : array();
	}
	$value = WP_Test_State::$post_meta[ $index ];
	return $single ? $value : array( $value );
}

function update_metadata( $meta_type, $object_id, $meta_key, $meta_value, $prev_value = '' ) {
	WP_Test_State::$post_meta[ $meta_type . ':' . (int) $object_id . ':' . $meta_key ] = $meta_value;
	return true;
}

function wp_is_post_revision( $post ) {
	$record = get_post( $post );
	return $record && 'revision' === $record->post_type ? (int) $record->post_parent : false;
}

function wp_is_post_autosave( $post ) {
	$record = get_post( $post );
	return $record && ! empty( $record->is_autosave ) ? (int) $record->post_parent : false;
}

function clean_post_cache( $post ) {
	return null;
}

function get_attached_file( $attachment_id, $unfiltered = false ) {
	$record = get_post( $attachment_id );
	return $record && ! empty( $record->attached_file ) ? $record->attached_file : false;
}

function maybe_serialize( $data ) {
	return is_array( $data ) || is_object( $data ) ? serialize( $data ) : $data;
}

function get_current_user_id() {
	return 7;
}

function admin_url( $path = '', $scheme = 'admin' ) {
	return home_url( '/wp-admin/' . ltrim( $path, '/' ) );
}

function get_page_by_path( $page_path, $output = 'OBJECT', $post_type = 'page' ) {
	foreach ( WP_Test_State::$posts as $post ) {
		if ( $post['post_name'] === $page_path && $post['post_type'] === $post_type ) {
			return WP_Test_State::post_object( $post );
		}
	}
	return null;
}

/* -------------------------------------------------------------------------
 * Template context: conditional tags and queried-object helpers.
 * ---------------------------------------------------------------------- */

function is_admin() {
	return (bool) WP_Test_State::$query['is_admin'];
}

function get_queried_object_id() {
	return (int) WP_Test_State::$query['queried_id'];
}

function get_queried_object() {
	return get_post( get_queried_object_id() );
}

function is_singular( $post_types = '' ) {
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return false;
	}
	return '' === $post_types || in_array( $post->post_type, (array) $post_types, true );
}

function is_page( $page = '' ) {
	if ( ! is_singular( 'page' ) ) {
		return false;
	}
	if ( '' === $page ) {
		return true;
	}
	$post = get_queried_object();
	foreach ( (array) $page as $candidate ) {
		if ( (string) $candidate === (string) $post->ID || (string) $candidate === $post->post_name ) {
			return true;
		}
	}
	return false;
}

function is_home() {
	return 'blog' === WP_Test_State::$query['queried_id'];
}

function is_archive() {
	return '' !== (string) WP_Test_State::$query['archive_title'];
}

function get_the_archive_title() {
	return (string) WP_Test_State::$query['archive_title'];
}

function get_post_status( $post = null ) {
	$record = get_post( $post );
	return $record ? $record->post_status : false;
}

function get_the_title( $post = 0 ) {
	$record = get_post( $post ? $post : get_queried_object_id() );
	return $record ? $record->post_title : '';
}

function get_post_ancestors( $post ) {
	$ancestors = array();
	$record    = get_post( $post );
	while ( $record && $record->post_parent ) {
		$ancestors[] = (int) $record->post_parent;
		$record      = get_post( $record->post_parent );
	}
	return $ancestors;
}

function get_the_excerpt( $post = null ) {
	$record = get_post( $post ? $post : get_queried_object_id() );
	return $record && isset( $record->post_excerpt ) ? $record->post_excerpt : '';
}

function has_post_thumbnail( $post = null ) {
	$record = get_post( $post ? $post : get_queried_object_id() );
	return (bool) ( $record && ! empty( $record->thumbnail_url ) );
}

function get_the_post_thumbnail_url( $post = null, $size = 'post-thumbnail' ) {
	$record = get_post( $post ? $post : get_queried_object_id() );
	return $record && ! empty( $record->thumbnail_url ) ? $record->thumbnail_url : false;
}

function get_post_field( $field, $post = null ) {
	$record = get_post( $post ? $post : get_queried_object_id() );
	return $record && isset( $record->$field ) ? $record->$field : '';
}

function get_the_date( $format = '', $post = null ) {
	$record = get_post( $post ? $post : get_queried_object_id() );
	return $record && ! empty( $record->post_date ) ? gmdate( $format ? $format : 'Y-m-d', strtotime( $record->post_date ) ) : '';
}

function get_the_modified_date( $format = '', $post = null ) {
	$record = get_post( $post ? $post : get_queried_object_id() );
	$date   = $record && ! empty( $record->post_modified ) ? $record->post_modified : ( $record ? $record->post_date : '' );
	return $date ? gmdate( $format ? $format : 'Y-m-d', strtotime( $date ) ) : '';
}

function get_the_author_meta( $field = '', $user_id = false ) {
	return 'display_name' === $field ? 'دکتر کیوان علی‌پسندی' : '';
}

function wp_get_document_title() {
	return get_the_title() . ' – Fasdent';
}

function add_meta_box( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null ) {
	return true;
}

function wp_nonce_field( $action = -1, $name = '_wpnonce', $referer = true, $display = true ) {
	$field = '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( wp_create_nonce( $action ) ) . '">';
	if ( $display ) {
		echo $field;
	}
	return $field;
}

/*
 * Theme/functions.php is not loaded by the harness (it boots widgets, menus and
 * enqueues); these two helpers from it are mirrored for the modules under test.
 */

function alipasandi_articles_url() {
	return home_url( '/articles/' );
}

function alipasandi_page_url( $slug ) {
	if ( 'home' === $slug ) {
		return home_url( '/' );
	}
	$page = get_page_by_path( $slug );
	if ( ! $page && 0 === strpos( $slug, 'services/' ) ) {
		$page = get_page_by_path( wp_basename( $slug ) );
	}
	return $page ? get_permalink( $page ) : home_url( '/' . trim( $slug, '/' ) . '/' );
}

/* -------------------------------------------------------------------------
 * Mail, URLs, nonces, capabilities.
 * ---------------------------------------------------------------------- */

function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
	WP_Test_State::$mail[] = compact( 'to', 'subject', 'message', 'headers', 'attachments' );
	return WP_Test_State::$mail_succeeds;
}

function home_url( $path = '', $scheme = null ) {
	return 'https://fasdent.ir' . ( '' !== $path ? '/' . ltrim( $path, '/' ) : '' );
}

function site_url( $path = '', $scheme = null ) {
	return home_url( $path );
}

function get_permalink( $post = 0, $leavename = false ) {
	$record = get_post( $post ? $post : get_queried_object_id() );
	return $record ? home_url( '/' . $record->post_name . '/' ) : false;
}

function get_template_directory() {
	return dirname( __DIR__ ) . '/Theme';
}

function get_template_directory_uri() {
	return 'https://fasdent.ir/wp-content/themes/alipasandi-clinic';
}

function wp_verify_nonce( $nonce, $action = -1 ) {
	return 'valid-nonce-' . $action === $nonce ? 1 : false;
}

function wp_create_nonce( $action = -1 ) {
	return 'valid-nonce-' . $action;
}

function wp_salt( $scheme = 'auth' ) {
	return 'test-salt-' . $scheme;
}

function current_user_can( $capability, ...$args ) {
	return true;
}

function current_time( $type, $gmt = 0 ) {
	if ( 'timestamp' === $type || 'U' === $type ) {
		return strtotime( WP_Test_State::$current_time );
	}
	if ( 'mysql' === $type ) {
		return WP_Test_State::$current_time;
	}
	return gmdate( $type, strtotime( WP_Test_State::$current_time ) );
}

function register_setting( $option_group, $option_name, $args = array() ) {
	return true;
}

function register_post_meta( $post_type, $meta_key, $args = array() ) {
	return true;
}

function add_options_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return $menu_slug;
}

/* -------------------------------------------------------------------------
 * Helpers that live in wp-includes/functions.php, which the bootstrap does not
 * load because it pulls in the database-backed option layer.
 * ---------------------------------------------------------------------- */

function is_utf8_charset( $blog_charset = null ) {
	$charset = null === $blog_charset ? get_option( 'blog_charset', 'UTF-8' ) : $blog_charset;
	return _is_utf8_charset( $charset );
}

function _canonical_charset( $charset ) {
	return _is_utf8_charset( $charset ) ? 'UTF-8' : $charset;
}

function absint( $maybeint ) {
	return abs( (int) $maybeint );
}

function wp_parse_args( $args, $defaults = array() ) {
	if ( is_object( $args ) ) {
		$parsed = get_object_vars( $args );
	} elseif ( is_array( $args ) ) {
		$parsed = $args;
	} else {
		wp_parse_str( $args, $parsed );
	}
	return is_array( $defaults ) && $defaults ? array_merge( $defaults, $parsed ) : $parsed;
}

function wp_list_pluck( $list, $field, $index_key = null ) {
	$values = array();
	foreach ( $list as $item ) {
		$values[] = is_object( $item ) ? $item->$field : $item[ $field ];
	}
	return $values;
}

function wp_json_encode( $data, $options = 0, $depth = 512 ) {
	return json_encode( $data, $options, $depth );
}

function add_query_arg( ...$args ) {
	$uri = is_array( $args[0] ) ? ( isset( $args[1] ) ? $args[1] : '' ) : ( isset( $args[2] ) ? $args[2] : '' );
	$pairs = is_array( $args[0] ) ? $args[0] : array( $args[0] => $args[1] );
	$parts = explode( '#', (string) $uri, 2 );
	$base  = $parts[0];
	$glue  = false === strpos( $base, '?' ) ? '?' : '&';
	$query = http_build_query( $pairs );
	return $base . $glue . $query . ( isset( $parts[1] ) ? '#' . $parts[1] : '' );
}

function wp_parse_url( $url, $component = -1 ) {
	return parse_url( $url, $component );
}

function wp_get_referer() {
	return isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : false;
}

/* -------------------------------------------------------------------------
 * Theme lookup and translation helpers.
 * ---------------------------------------------------------------------- */

/** Stand-in for WP_Theme with the accessors the compatibility module uses. */
final class WP_Test_Theme {
	/** @var string */
	public $stylesheet = 'alipasandi-clinic';

	/** @var string */
	public $version = '1.5.0';

	public function get_stylesheet() {
		return $this->stylesheet;
	}

	public function get( $header ) {
		return 'Version' === $header ? $this->version : '';
	}
}

function get_bloginfo( $show = '', $filter = 'raw' ) {
	if ( 'version' === $show ) {
		return '6.7.1';
	}
	if ( 'charset' === $show ) {
		return 'UTF-8';
	}
	return 'name' === $show ? 'Fasdent' : '';
}

function wp_get_theme( $stylesheet = '', $theme_root = '' ) {
	if ( ! isset( $GLOBALS['alipasandi_test_theme'] ) ) {
		$GLOBALS['alipasandi_test_theme'] = new WP_Test_Theme();
	}
	return $GLOBALS['alipasandi_test_theme'];
}

function __( $text, $domain = 'default' ) {
	return $text;
}

function _x( $text, $context, $domain = 'default' ) {
	return $text;
}

function esc_html__( $text, $domain = 'default' ) {
	return esc_html( $text );
}

function esc_attr__( $text, $domain = 'default' ) {
	return esc_attr( $text );
}

function esc_attr_e( $text, $domain = 'default' ) {
	echo esc_attr( $text );
}

/**
 * Thrown instead of redirecting so tests can assert on the response of handlers
 * that end with wp_safe_redirect() followed by exit.
 */
final class WP_Test_Redirect extends RuntimeException {
	/** @var string */
	public $location;

	/** @var int */
	public $status;

	public function __construct( string $location, int $status ) {
		parent::__construct( 'Redirected to ' . $location );
		$this->location = $location;
		$this->status   = $status;
	}
}

function wp_safe_redirect( $location, $status = 302, $x_redirect_by = 'WordPress' ) {
	throw new WP_Test_Redirect( (string) $location, (int) $status );
}

function wp_redirect( $location, $status = 302, $x_redirect_by = 'WordPress' ) {
	return wp_safe_redirect( $location, $status, $x_redirect_by );
}

function wp_allowed_protocols() {
	return array( 'http', 'https', 'mailto', 'tel' );
}

function esc_html_e( $text, $domain = 'default' ) {
	echo esc_html( $text );
}

function _e( $text, $domain = 'default' ) {
	echo $text;
}

function rest_sanitize_boolean( $value ) {
	if ( is_string( $value ) && in_array( strtolower( $value ), array( 'false', '0', '' ), true ) ) {
		return false;
	}
	return (bool) $value;
}
