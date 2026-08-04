<?php
/**
 * Before/After gallery — CPT, fields, admin, helpers.
 *
 * Backend: separate before_image + after_image (attachment IDs).
 * Frontend: comparison slider (template-parts/before-after-slider.php).
 *
 * Security: nonces, capability checks, absint for IDs, esc_* on output.
 *
 * @package Fasdent
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fasdent_register_before_after(): void {
	register_post_type(
		'before_after',
		array(
			'labels' => array(
				'name' => __( 'گالری قبل/بعد', 'fasdent' ),
				'singular_name' => __( 'نمونه قبل/بعد', 'fasdent' ),
				'add_new' => __( 'افزودن نمونه', 'fasdent' ),
				'add_new_item' => __( 'افزودن نمونه جدید', 'fasdent' ),
				'edit_item' => __( 'ویرایش نمونه', 'fasdent' ),
				'new_item' => __( 'نمونه جدید', 'fasdent' ),
				'view_item' => __( 'مشاهده نمونه', 'fasdent' ),
				'search_items' => __( 'جستجوی نمونه‌ها', 'fasdent' ),
				'not_found' => __( 'نمونه‌ای یافت نشد', 'fasdent' ),
				'not_found_in_trash' => __( 'در زباله‌دان یافت نشد', 'fasdent' ),
				'menu_name' => __( 'قبل و بعد', 'fasdent' ),
			),
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon' => 'dashicons-images-alt2',
			'menu_position' => 6,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'has_archive' => 'gallery',
			'rewrite' => array( 'slug' => 'gallery/case', 'with_front' => false ),
			'capability_type' => 'post',
		)
	);

	register_taxonomy(
		'ba_category',
		array( 'before_after' ),
		array(
			'labels' => array(
				'name' => __( 'دسته‌های گالری', 'fasdent' ),
				'singular_name' => __( 'دسته گالری', 'fasdent' ),
				'add_new_item' => __( 'افزودن دسته', 'fasdent' ),
				'edit_item' => __( 'ویرایش دسته', 'fasdent' ),
			),
			'hierarchical' => true,
			'public' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'show_in_rest' => true,
			'rewrite' => array( 'slug' => 'gallery/category', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'fasdent_register_before_after' );

function fasdent_ba_acf_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group( array(
		'key' => 'group_fasdent_before_after',
		'title' => __( 'تصاویر قبل و بعد', 'fasdent' ),
		'fields' => array(
			array(
				'key' => 'field_ba_before',
				'name' => 'ba_before_image',
				'label' => __( 'تصویر قبل از درمان', 'fasdent' ),
				'type' => 'image',
				'return_format' => 'id',
				'preview_size' => 'medium',
				'library' => 'all',
				'required' => 1,
				'instructions' => __( 'عکس وضعیت قبل از درمان — ترجیحاً WebP، نسبت ۴:۳', 'fasdent' ),
			),
			array(
				'key' => 'field_ba_after',
				'name' => 'ba_after_image',
				'label' => __( 'تصویر بعد از درمان', 'fasdent' ),
				'type' => 'image',
				'return_format' => 'id',
				'preview_size' => 'medium',
				'library' => 'all',
				'required' => 1,
				'instructions' => __( 'عکس نتیجه بعد از درمان — همان زاویه و نور', 'fasdent' ),
			),
			array(
				'key' => 'field_ba_service',
				'name' => 'ba_related_service',
				'label' => __( 'خدمت مرتبط', 'fasdent' ),
				'type' => 'post_object',
				'post_type' => array( 'service' ),
				'return_format' => 'id',
				'allow_null' => 1,
				'ui' => 1,
			),
			array(
				'key' => 'field_ba_treatment',
				'name' => 'ba_treatment_label',
				'label' => __( 'نوع درمان (نمایشی)', 'fasdent' ),
				'type' => 'text',
				'placeholder' => __( 'مثال: ایمپلنت دندان جلو', 'fasdent' ),
			),
		),
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'before_after' ) ) ),
		'position' => 'acf_after_title',
		'style' => 'default',
	) );
}
add_action( 'acf/init', 'fasdent_ba_acf_fields' );

function fasdent_ba_metaboxes(): void {
	if ( function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	add_meta_box( 'fasdent_ba_images', __( 'تصاویر قبل و بعد', 'fasdent' ), 'fasdent_ba_metabox_html', 'before_after', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'fasdent_ba_metaboxes' );

function fasdent_ba_metabox_html( WP_Post $post ): void {
	wp_nonce_field( 'fasdent_ba_save', 'fasdent_ba_nonce' );
	$before_id = (int) get_post_meta( $post->ID, 'ba_before_image', true );
	$after_id  = (int) get_post_meta( $post->ID, 'ba_after_image', true );
	$service   = (int) get_post_meta( $post->ID, 'ba_related_service', true );
	$label     = (string) get_post_meta( $post->ID, 'ba_treatment_label', true );
	$services  = get_posts( array( 'post_type' => 'service', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) );
	?>
	<style>
		.fasdent-ba-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.25rem}
		.fasdent-ba-box{border:1px solid #c3c4c7;border-radius:8px;padding:1rem;background:#f6f7f7;text-align:center}
		.fasdent-ba-box img{max-width:100%;height:auto;border-radius:6px;margin-bottom:.75rem}
		.fasdent-ba-box .button{margin:.25rem}
		@media(max-width:782px){.fasdent-ba-grid{grid-template-columns:1fr}}
	</style>
	<div class="fasdent-ba-grid">
		<div class="fasdent-ba-box">
			<strong><?php esc_html_e( 'قبل از درمان', 'fasdent' ); ?></strong>
			<div class="fasdent-ba-preview" id="ba-before-preview">
				<?php if ( $before_id ) { echo wp_get_attachment_image( $before_id, 'medium' ); } else { echo '<p style="color:#646970;margin:2rem 0">' . esc_html__( 'هنوز تصویری انتخاب نشده', 'fasdent' ) . '</p>'; } ?>
			</div>
			<input type="hidden" name="ba_before_image" id="ba_before_image" value="<?php echo esc_attr( (string) $before_id ); ?>">
			<button type="button" class="button fasdent-ba-upload" data-target="ba_before_image" data-preview="ba-before-preview"><?php esc_html_e( 'انتخاب / تغییر تصویر', 'fasdent' ); ?></button>
			<button type="button" class="button fasdent-ba-remove" data-target="ba_before_image" data-preview="ba-before-preview"><?php esc_html_e( 'حذف', 'fasdent' ); ?></button>
		</div>
		<div class="fasdent-ba-box">
			<strong><?php esc_html_e( 'بعد از درمان', 'fasdent' ); ?></strong>
			<div class="fasdent-ba-preview" id="ba-after-preview">
				<?php if ( $after_id ) { echo wp_get_attachment_image( $after_id, 'medium' ); } else { echo '<p style="color:#646970;margin:2rem 0">' . esc_html__( 'هنوز تصویری انتخاب نشده', 'fasdent' ) . '</p>'; } ?>
			</div>
			<input type="hidden" name="ba_after_image" id="ba_after_image" value="<?php echo esc_attr( (string) $after_id ); ?>">
			<button type="button" class="button fasdent-ba-upload" data-target="ba_after_image" data-preview="ba-after-preview"><?php esc_html_e( 'انتخاب / تغییر تصویر', 'fasdent' ); ?></button>
			<button type="button" class="button fasdent-ba-remove" data-target="ba_after_image" data-preview="ba-after-preview"><?php esc_html_e( 'حذف', 'fasdent' ); ?></button>
		</div>
	</div>
	<p><label for="ba_treatment_label"><strong><?php esc_html_e( 'نوع درمان (نمایشی)', 'fasdent' ); ?></strong></label><br>
	<input type="text" class="widefat" name="ba_treatment_label" id="ba_treatment_label" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( 'مثال: ایمپلنت دندان جلو', 'fasdent' ); ?>"></p>
	<p><label for="ba_related_service"><strong><?php esc_html_e( 'خدمت مرتبط', 'fasdent' ); ?></strong></label><br>
	<select name="ba_related_service" id="ba_related_service" class="widefat">
		<option value=""><?php esc_html_e( '— بدون ارتباط —', 'fasdent' ); ?></option>
		<?php foreach ( $services as $svc ) : ?>
		<option value="<?php echo esc_attr( (string) $svc->ID ); ?>" <?php selected( $service, $svc->ID ); ?>><?php echo esc_html( $svc->post_title ); ?></option>
		<?php endforeach; ?>
	</select></p>
	<script>
	(function($){
		var frame;
		$(document).on('click','.fasdent-ba-upload',function(e){
			e.preventDefault();
			var btn=$(this), target=btn.data('target'), preview=btn.data('preview');
			if(frame){frame.open();return;}
			frame=wp.media({title:'<?php echo esc_js( __( 'انتخاب تصویر', 'fasdent' ) ); ?>',button:{text:'<?php echo esc_js( __( 'استفاده از این تصویر', 'fasdent' ) ); ?>'},multiple:false});
			frame.on('select',function(){
				var att=frame.state().get('selection').first().toJSON();
				$('#'+target).val(att.id);
				var url=(att.sizes&&att.sizes.medium)?att.sizes.medium.url:att.url;
				$('#'+preview).html('<img src="'+url+'" alt="">');
			});
			frame.open();
		});
		$(document).on('click','.fasdent-ba-remove',function(e){
			e.preventDefault();
			var btn=$(this);
			$('#'+btn.data('target')).val('');
			$('#'+btn.data('preview')).html('<p style="color:#646970;margin:2rem 0"><?php echo esc_js( __( 'هنوز تصویری انتخاب نشده', 'fasdent' ) ); ?></p>');
		});
	})(jQuery);
	</script>
	<?php
}

function fasdent_ba_save_metabox( int $post_id ): void {
	if ( ! isset( $_POST['fasdent_ba_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fasdent_ba_nonce'] ), 'fasdent_ba_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'before_after' !== get_post_type( $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, 'ba_before_image', isset( $_POST['ba_before_image'] ) ? absint( $_POST['ba_before_image'] ) : 0 );
	update_post_meta( $post_id, 'ba_after_image', isset( $_POST['ba_after_image'] ) ? absint( $_POST['ba_after_image'] ) : 0 );
	update_post_meta( $post_id, 'ba_related_service', isset( $_POST['ba_related_service'] ) ? absint( $_POST['ba_related_service'] ) : 0 );
	update_post_meta( $post_id, 'ba_treatment_label', isset( $_POST['ba_treatment_label'] ) ? sanitize_text_field( wp_unslash( $_POST['ba_treatment_label'] ) ) : '' );
}
add_action( 'save_post_before_after', 'fasdent_ba_save_metabox' );

function fasdent_ba_admin_scripts( string $hook ): void {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'before_after' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'fasdent_ba_admin_scripts' );

function fasdent_ba_get_images( ?int $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();
	$before = 0;
	$after  = 0;
	if ( function_exists( 'get_field' ) ) {
		$b = get_field( 'ba_before_image', $post_id );
		$a = get_field( 'ba_after_image', $post_id );
		$before = is_array( $b ) ? (int) ( $b['ID'] ?? 0 ) : absint( $b );
		$after  = is_array( $a ) ? (int) ( $a['ID'] ?? 0 ) : absint( $a );
	}
	if ( ! $before ) {
		$before = absint( get_post_meta( $post_id, 'ba_before_image', true ) );
	}
	if ( ! $after ) {
		$after = absint( get_post_meta( $post_id, 'ba_after_image', true ) );
	}
	$label = function_exists( 'get_field' ) ? ( get_field( 'ba_treatment_label', $post_id ) ?: get_post_meta( $post_id, 'ba_treatment_label', true ) ) : get_post_meta( $post_id, 'ba_treatment_label', true );
	$svc   = function_exists( 'get_field' ) ? ( get_field( 'ba_related_service', $post_id ) ?: get_post_meta( $post_id, 'ba_related_service', true ) ) : get_post_meta( $post_id, 'ba_related_service', true );
	return array( 'before' => $before, 'after' => $after, 'label' => (string) $label, 'service' => absint( $svc ) );
}

function fasdent_ba_columns( array $cols ): array {
	$new = array();
	foreach ( $cols as $k => $v ) {
		$new[ $k ] = $v;
		if ( 'title' === $k ) {
			$new['ba_thumbs'] = __( 'قبل / بعد', 'fasdent' );
			$new['ba_label']  = __( 'نوع درمان', 'fasdent' );
		}
	}
	return $new;
}
add_filter( 'manage_before_after_posts_columns', 'fasdent_ba_columns' );

function fasdent_ba_column_content( string $col, int $post_id ): void {
	$data = fasdent_ba_get_images( $post_id );
	if ( 'ba_thumbs' === $col ) {
		echo '<div style="display:flex;gap:4px">';
		if ( $data['before'] ) { echo wp_get_attachment_image( $data['before'], array( 48, 48 ) ); }
		if ( $data['after'] ) { echo wp_get_attachment_image( $data['after'], array( 48, 48 ) ); }
		echo '</div>';
	}
	if ( 'ba_label' === $col ) {
		echo esc_html( $data['label'] ?: '—' );
	}
}
add_action( 'manage_before_after_posts_custom_column', 'fasdent_ba_column_content', 10, 2 );
