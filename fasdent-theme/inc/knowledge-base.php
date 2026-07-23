<?php
/**
 * Knowledge Base — CPT, taxonomy, fields, helpers.
 *
 * CPT: kb_article  |  Taxonomy: kb_topic
 *
 * @package Fasdent
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fasdent_register_knowledge_base(): void {
	register_post_type(
		'kb_article',
		array(
			'labels' => array(
				'name' => __( 'مرکز آموزش', 'fasdent' ),
				'singular_name' => __( 'مقاله آموزشی', 'fasdent' ),
				'add_new' => __( 'افزودن مقاله', 'fasdent' ),
				'add_new_item' => __( 'افزودن مقاله جدید', 'fasdent' ),
				'edit_item' => __( 'ویرایش مقاله', 'fasdent' ),
				'new_item' => __( 'مقاله جدید', 'fasdent' ),
				'view_item' => __( 'مشاهده مقاله', 'fasdent' ),
				'search_items' => __( 'جستجوی مقالات', 'fasdent' ),
				'not_found' => __( 'مقاله‌ای یافت نشد', 'fasdent' ),
				'not_found_in_trash' => __( 'در زباله‌دان یافت نشد', 'fasdent' ),
				'menu_name' => __( 'مرکز آموزش', 'fasdent' ),
				'all_items' => __( 'همه مقالات', 'fasdent' ),
			),
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon' => 'dashicons-welcome-learn-more',
			'menu_position' => 7,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'author' ),
			'has_archive' => 'knowledge',
			'rewrite' => array( 'slug' => 'knowledge', 'with_front' => false ),
			'capability_type' => 'post',
		)
	);

	register_taxonomy(
		'kb_topic',
		array( 'kb_article' ),
		array(
			'labels' => array(
				'name' => __( 'موضوعات آموزش', 'fasdent' ),
				'singular_name' => __( 'موضوع', 'fasdent' ),
				'add_new_item' => __( 'افزودن موضوع', 'fasdent' ),
				'edit_item' => __( 'ویرایش موضوع', 'fasdent' ),
				'search_items' => __( 'جستجوی موضوعات', 'fasdent' ),
				'all_items' => __( 'همه موضوعات', 'fasdent' ),
			),
			'hierarchical' => true,
			'public' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'show_in_rest' => true,
			'rewrite' => array( 'slug' => 'knowledge/topic', 'with_front' => false, 'hierarchical' => true ),
		)
	);
}
add_action( 'init', 'fasdent_register_knowledge_base' );

function fasdent_kb_acf_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group( array(
		'key' => 'group_fasdent_kb',
		'title' => __( 'تنظیمات مقاله آموزشی', 'fasdent' ),
		'fields' => array(
			array( 'key' => 'field_kb_icon', 'name' => 'kb_icon', 'label' => __( 'آیکون Font Awesome', 'fasdent' ), 'type' => 'text', 'placeholder' => 'fa-solid fa-tooth' ),
			array( 'key' => 'field_kb_reading', 'name' => 'kb_reading_time', 'label' => __( 'زمان مطالعه (دقیقه)', 'fasdent' ), 'type' => 'number', 'min' => 1, 'max' => 60, 'default_value' => 5 ),
			array( 'key' => 'field_kb_quick', 'name' => 'kb_quick_answer', 'label' => __( 'پاسخ سریع (Featured Snippet)', 'fasdent' ), 'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_kb_related_service', 'name' => 'kb_related_service', 'label' => __( 'خدمت مرتبط', 'fasdent' ), 'type' => 'post_object', 'post_type' => array( 'service' ), 'return_format' => 'id', 'allow_null' => 1, 'ui' => 1 ),
			array(
				'key' => 'field_kb_key_points',
				'name' => 'kb_key_points',
				'label' => __( 'نکات کلیدی', 'fasdent' ),
				'type' => 'repeater',
				'layout' => 'table',
				'button_label' => __( 'افزودن نکته', 'fasdent' ),
				'max' => 8,
				'sub_fields' => array(
					array( 'key' => 'field_kb_kp_text', 'name' => 'text', 'label' => __( 'متن', 'fasdent' ), 'type' => 'text' ),
				),
			),
		),
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'kb_article' ) ) ),
	) );
	acf_add_local_field_group( array(
		'key' => 'group_fasdent_kb_topic',
		'title' => __( 'آیکون موضوع', 'fasdent' ),
		'fields' => array(
			array( 'key' => 'field_kb_topic_icon', 'name' => 'kb_topic_icon', 'label' => __( 'آیکون FA', 'fasdent' ), 'type' => 'text', 'placeholder' => 'fa-solid fa-book-medical' ),
		),
		'location' => array( array( array( 'param' => 'taxonomy', 'operator' => '==', 'value' => 'kb_topic' ) ) ),
	) );
}
add_action( 'acf/init', 'fasdent_kb_acf_fields' );

function fasdent_kb_metaboxes(): void {
	if ( function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	add_meta_box( 'fasdent_kb_meta', __( 'تنظیمات مقاله آموزشی', 'fasdent' ), 'fasdent_kb_metabox_html', 'kb_article', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'fasdent_kb_metaboxes' );

function fasdent_kb_metabox_html( WP_Post $post ): void {
	wp_nonce_field( 'fasdent_kb_save', 'fasdent_kb_nonce' );
	$icon = (string) get_post_meta( $post->ID, 'kb_icon', true );
	$reading = (int) get_post_meta( $post->ID, 'kb_reading_time', true );
	$quick = (string) get_post_meta( $post->ID, 'kb_quick_answer', true );
	$svc = (int) get_post_meta( $post->ID, 'kb_related_service', true );
	$points = (string) get_post_meta( $post->ID, 'kb_key_points_json', true );
	$services = get_posts( array( 'post_type' => 'service', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) );
	?>
	<p><label for="kb_icon"><strong><?php esc_html_e( 'آیکون Font Awesome', 'fasdent' ); ?></strong></label><br>
	<input type="text" class="widefat" dir="ltr" name="kb_icon" id="kb_icon" value="<?php echo esc_attr( $icon ); ?>" placeholder="fa-solid fa-tooth"></p>
	<p><label for="kb_reading_time"><strong><?php esc_html_e( 'زمان مطالعه (دقیقه)', 'fasdent' ); ?></strong></label><br>
	<input type="number" min="1" max="60" name="kb_reading_time" id="kb_reading_time" value="<?php echo esc_attr( (string) ( $reading ?: 5 ) ); ?>"></p>
	<p><label for="kb_quick_answer"><strong><?php esc_html_e( 'پاسخ سریع', 'fasdent' ); ?></strong></label><br>
	<textarea class="widefat" rows="3" name="kb_quick_answer" id="kb_quick_answer"><?php echo esc_textarea( $quick ); ?></textarea></p>
	<p><label for="kb_related_service"><strong><?php esc_html_e( 'خدمت مرتبط', 'fasdent' ); ?></strong></label><br>
	<select name="kb_related_service" id="kb_related_service" class="widefat">
	<option value=""><?php esc_html_e( '— بدون ارتباط —', 'fasdent' ); ?></option>
	<?php foreach ( $services as $s ) : ?>
	<option value="<?php echo esc_attr( (string) $s->ID ); ?>" <?php selected( $svc, $s->ID ); ?>><?php echo esc_html( $s->post_title ); ?></option>
	<?php endforeach; ?>
	</select></p>
	<p><label for="kb_key_points_json"><strong><?php esc_html_e( 'نکات کلیدی (JSON)', 'fasdent' ); ?></strong></label><br>
	<textarea class="widefat" rows="4" dir="ltr" name="kb_key_points_json" id="kb_key_points_json"><?php echo esc_textarea( $points ); ?></textarea></p>
	<?php
}

function fasdent_kb_save_metabox( int $post_id ): void {
	if ( ! isset( $_POST['fasdent_kb_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fasdent_kb_nonce'] ), 'fasdent_kb_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'kb_article' !== get_post_type( $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, 'kb_icon', isset( $_POST['kb_icon'] ) ? sanitize_text_field( wp_unslash( $_POST['kb_icon'] ) ) : '' );
	update_post_meta( $post_id, 'kb_reading_time', isset( $_POST['kb_reading_time'] ) ? absint( $_POST['kb_reading_time'] ) : 5 );
	update_post_meta( $post_id, 'kb_quick_answer', isset( $_POST['kb_quick_answer'] ) ? sanitize_textarea_field( wp_unslash( $_POST['kb_quick_answer'] ) ) : '' );
	update_post_meta( $post_id, 'kb_related_service', isset( $_POST['kb_related_service'] ) ? absint( $_POST['kb_related_service'] ) : 0 );
	if ( isset( $_POST['kb_key_points_json'] ) ) {
		$raw = sanitize_textarea_field( wp_unslash( $_POST['kb_key_points_json'] ) );
		if ( '' === $raw || null !== json_decode( $raw, true ) ) {
			update_post_meta( $post_id, 'kb_key_points_json', $raw );
		}
	}
}
add_action( 'save_post_kb_article', 'fasdent_kb_save_metabox' );

function fasdent_kb_meta( string $key, ?int $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		$v = get_field( $key, $post_id );
		if ( null !== $v && '' !== $v && false !== $v ) {
			return $v;
		}
	}
	return get_post_meta( $post_id, $key, true );
}

function fasdent_kb_key_points( ?int $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		$rows = get_field( 'kb_key_points', $post_id );
		if ( is_array( $rows ) && $rows ) {
			return array_values( array_filter( array_map( static function ( $r ) {
				return is_array( $r ) ? (string) ( $r['text'] ?? '' ) : (string) $r;
			}, $rows ) ) );
		}
	}
	$json = get_post_meta( $post_id, 'kb_key_points_json', true );
	if ( $json ) {
		$decoded = json_decode( $json, true );
		if ( is_array( $decoded ) ) {
			return array_map( 'strval', $decoded );
		}
	}
	return array();
}

function fasdent_kb_topic_icon( $term ): string {
	$term_id = $term instanceof WP_Term ? $term->term_id : (int) $term;
	if ( function_exists( 'get_field' ) ) {
		$icon = get_field( 'kb_topic_icon', 'kb_topic_' . $term_id );
		if ( $icon ) {
			return (string) $icon;
		}
	}
	$meta = get_term_meta( $term_id, 'kb_topic_icon', true );
	return $meta ? (string) $meta : 'fa-solid fa-book-medical';
}

function fasdent_kb_columns( array $cols ): array {
	$new = array();
	foreach ( $cols as $k => $v ) {
		$new[ $k ] = $v;
		if ( 'title' === $k ) {
			$new['kb_reading'] = __( 'مطالعه', 'fasdent' );
		}
	}
	return $new;
}
add_filter( 'manage_kb_article_posts_columns', 'fasdent_kb_columns' );

function fasdent_kb_column_content( string $col, int $post_id ): void {
	if ( 'kb_reading' === $col ) {
		$t = (int) fasdent_kb_meta( 'kb_reading_time', $post_id );
		echo $t ? esc_html( $t . ' ' . __( 'دقیقه', 'fasdent' ) ) : '—';
	}
}
add_action( 'manage_kb_article_posts_custom_column', 'fasdent_kb_column_content', 10, 2 );
