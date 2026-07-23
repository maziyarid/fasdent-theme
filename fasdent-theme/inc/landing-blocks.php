<?php
/**
 * Editable landing-page blocks beyond base ACF page fields.
 * Meta: fasdent_landing_blocks (JSON or ACF flexible)
 * Types: hero, features, stats, text, cta, faq, doctor
 * @package Fasdent
 * @version 2.6.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fasdent_get_landing_blocks( ?int $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		$rows = get_field( 'fasdent_landing_blocks', $post_id );
		if ( is_array( $rows ) && $rows ) { return $rows; }
	}
	$json = get_post_meta( $post_id, 'fasdent_landing_blocks', true );
	if ( is_string( $json ) && $json ) {
		$decoded = json_decode( $json, true );
		if ( is_array( $decoded ) ) { return $decoded; }
	}
	return array();
}

function fasdent_landing_acf(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }
	acf_add_local_field_group( array(
		'key' => 'group_fasdent_landing_blocks',
		'title' => __( 'بلوک‌های لندینگ', 'fasdent' ),
		'fields' => array(
			array(
				'key' => 'field_fasdent_landing_blocks',
				'name' => 'fasdent_landing_blocks',
				'label' => __( 'بلوک‌ها', 'fasdent' ),
				'type' => 'flexible_content',
				'button_label' => __( 'افزودن بلوک', 'fasdent' ),
				'layouts' => array(
					'layout_hero' => array( 'key' => 'layout_hero', 'name' => 'hero', 'label' => 'Hero', 'sub_fields' => array(
						array( 'key' => 'f_hero_title', 'name' => 'title', 'label' => 'عنوان', 'type' => 'text' ),
						array( 'key' => 'f_hero_sub', 'name' => 'subtitle', 'label' => 'زیرعنوان', 'type' => 'textarea', 'rows' => 2 ),
						array( 'key' => 'f_hero_cta', 'name' => 'cta_label', 'label' => 'متن دکمه', 'type' => 'text' ),
						array( 'key' => 'f_hero_cta_url', 'name' => 'cta_url', 'label' => 'لینک', 'type' => 'url' ),
					) ),
					'layout_features' => array( 'key' => 'layout_features', 'name' => 'features', 'label' => 'ویژگی‌ها', 'sub_fields' => array(
						array( 'key' => 'f_feat_title', 'name' => 'section_title', 'label' => 'عنوان بخش', 'type' => 'text' ),
						array( 'key' => 'f_feat_items', 'name' => 'items', 'label' => 'کارت‌ها', 'type' => 'repeater', 'layout' => 'table', 'sub_fields' => array(
							array( 'key' => 'f_fi_icon', 'name' => 'icon', 'label' => 'آیکون', 'type' => 'text' ),
							array( 'key' => 'f_fi_title', 'name' => 'title', 'label' => 'عنوان', 'type' => 'text' ),
							array( 'key' => 'f_fi_text', 'name' => 'text', 'label' => 'متن', 'type' => 'text' ),
						) ),
					) ),
					'layout_stats' => array( 'key' => 'layout_stats', 'name' => 'stats', 'label' => 'آمار', 'sub_fields' => array(
						array( 'key' => 'f_stats_items', 'name' => 'items', 'label' => 'آمار', 'type' => 'repeater', 'layout' => 'table', 'sub_fields' => array(
							array( 'key' => 'f_st_num', 'name' => 'number', 'label' => 'عدد', 'type' => 'text' ),
							array( 'key' => 'f_st_label', 'name' => 'label', 'label' => 'برچسب', 'type' => 'text' ),
						) ),
					) ),
					'layout_text' => array( 'key' => 'layout_text', 'name' => 'text', 'label' => 'متن', 'sub_fields' => array(
						array( 'key' => 'f_text_body', 'name' => 'body', 'label' => 'محتوا', 'type' => 'wysiwyg', 'media_upload' => 0 ),
					) ),
					'layout_cta' => array( 'key' => 'layout_cta', 'name' => 'cta', 'label' => 'CTA', 'sub_fields' => array(
						array( 'key' => 'f_cta_title', 'name' => 'title', 'label' => 'عنوان', 'type' => 'text' ),
						array( 'key' => 'f_cta_text', 'name' => 'text', 'label' => 'متن', 'type' => 'textarea', 'rows' => 2 ),
						array( 'key' => 'f_cta_btn', 'name' => 'button', 'label' => 'دکمه', 'type' => 'text' ),
						array( 'key' => 'f_cta_url', 'name' => 'url', 'label' => 'لینک', 'type' => 'url' ),
					) ),
					'layout_faq' => array( 'key' => 'layout_faq', 'name' => 'faq', 'label' => 'FAQ', 'sub_fields' => array(
						array( 'key' => 'f_faq_items', 'name' => 'items', 'label' => 'سوالات', 'type' => 'repeater', 'layout' => 'block', 'sub_fields' => array(
							array( 'key' => 'f_fq_q', 'name' => 'question', 'label' => 'سوال', 'type' => 'text' ),
							array( 'key' => 'f_fq_a', 'name' => 'answer', 'label' => 'پاسخ', 'type' => 'textarea', 'rows' => 3 ),
						) ),
					) ),
					'layout_doctor' => array( 'key' => 'layout_doctor', 'name' => 'doctor', 'label' => 'پزشک', 'sub_fields' => array(
						array( 'key' => 'f_doc_name', 'name' => 'name', 'label' => 'نام', 'type' => 'text' ),
						array( 'key' => 'f_doc_title', 'name' => 'title', 'label' => 'عنوان', 'type' => 'text' ),
						array( 'key' => 'f_doc_bio', 'name' => 'bio', 'label' => 'بیو', 'type' => 'textarea', 'rows' => 4 ),
						array( 'key' => 'f_doc_img', 'name' => 'image_url', 'label' => 'آدرس تصویر', 'type' => 'url' ),
					) ),
				),
			),
		),
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ) ) ),
		'menu_order' => 20,
	) );
}
add_action( 'acf/init', 'fasdent_landing_acf' );

function fasdent_landing_metabox(): void {
	if ( function_exists( 'acf_add_local_field_group' ) ) { return; }
	add_meta_box( 'fasdent_landing_blocks_box', __( 'بلوک‌های لندینگ (JSON)', 'fasdent' ), 'fasdent_landing_metabox_html', 'page', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'fasdent_landing_metabox' );

function fasdent_landing_metabox_html( WP_Post $post ): void {
	wp_nonce_field( 'fasdent_landing_blocks', 'fasdent_landing_blocks_nonce' );
	$json = (string) get_post_meta( $post->ID, 'fasdent_landing_blocks', true );
	echo '<p>JSON blocks: hero, features, stats, text, cta, faq, doctor</p>';
	echo '<textarea class="widefat" rows="12" dir="ltr" name="fasdent_landing_blocks">' . esc_textarea( $json ) . '</textarea>';
}

function fasdent_landing_save( int $post_id ): void {
	if ( ! isset( $_POST['fasdent_landing_blocks_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fasdent_landing_blocks_nonce'] ), 'fasdent_landing_blocks' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	if ( isset( $_POST['fasdent_landing_blocks'] ) ) {
		$raw = sanitize_textarea_field( wp_unslash( $_POST['fasdent_landing_blocks'] ) );
		if ( '' === $raw || null !== json_decode( $raw, true ) ) {
			update_post_meta( $post_id, 'fasdent_landing_blocks', $raw );
		}
	}
}
add_action( 'save_post_page', 'fasdent_landing_save' );

function fasdent_render_landing_blocks( ?int $post_id = null ): void {
	$blocks = fasdent_get_landing_blocks( $post_id );
	if ( ! $blocks ) { return; }
	foreach ( $blocks as $block ) {
		$type = sanitize_key( $block['acf_fc_layout'] ?? ( $block['type'] ?? '' ) );
		echo '<section class="landing-block landing-block--' . esc_attr( $type ) . ' section"><div class="container">';
		switch ( $type ) {
			case 'hero':
				echo '<h2 class="section-title">' . esc_html( $block['title'] ?? '' ) . '</h2>';
				if ( ! empty( $block['subtitle'] ) ) { echo '<p class="section-desc">' . esc_html( $block['subtitle'] ) . '</p>'; }
				if ( ! empty( $block['cta_label'] ) ) { echo '<a class="btn btn-primary" href="' . esc_url( $block['cta_url'] ?? '#' ) . '">' . esc_html( $block['cta_label'] ) . '</a>'; }
				break;
			case 'cta':
				echo '<div class="card" style="text-align:center;padding:2rem"><h2>' . esc_html( $block['title'] ?? '' ) . '</h2><p>' . esc_html( $block['text'] ?? '' ) . '</p>';
				if ( ! empty( $block['button'] ) ) { echo '<a class="btn btn-primary" href="' . esc_url( $block['url'] ?? home_url( '/appointment/' ) ) . '">' . esc_html( $block['button'] ) . '</a>'; }
				echo '</div>';
				break;
			case 'text':
				echo '<div class="prose">' . wp_kses_post( $block['body'] ?? '' ) . '</div>';
				break;
			case 'doctor':
				echo '<div class="card" style="display:flex;gap:1.5rem;flex-wrap:wrap;align-items:center">';
				if ( ! empty( $block['image_url'] ) ) { echo '<img src="' . esc_url( $block['image_url'] ) . '" alt="" style="width:140px;height:140px;object-fit:cover;border-radius:50%">'; }
				echo '<div><h3>' . esc_html( $block['name'] ?? '' ) . '</h3><p>' . esc_html( $block['title'] ?? '' ) . '</p><p>' . esc_html( $block['bio'] ?? '' ) . '</p></div></div>';
				break;
			case 'features':
				if ( ! empty( $block['section_title'] ) ) { echo '<h2 class="section-title">' . esc_html( $block['section_title'] ) . '</h2>'; }
				echo '<div class="kb-topics__grid">';
				foreach ( (array) ( $block['items'] ?? array() ) as $item ) {
					echo '<div class="card" style="padding:1.25rem"><i class="' . esc_attr( $item['icon'] ?? 'fa-solid fa-check' ) . '" style="color:#0e55b1"></i>';
					echo '<strong style="display:block;margin:.5rem 0">' . esc_html( $item['title'] ?? '' ) . '</strong><p style="margin:0;color:#64748b">' . esc_html( $item['text'] ?? '' ) . '</p></div>';
				}
				echo '</div>';
				break;
			case 'stats':
				echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:1rem;text-align:center">';
				foreach ( (array) ( $block['items'] ?? array() ) as $item ) {
					echo '<div class="card" style="padding:1.25rem"><div style="font-size:1.75rem;font-weight:800;color:#0e55b1">' . esc_html( $item['number'] ?? '' ) . '</div>';
					echo '<div style="color:#64748b">' . esc_html( $item['label'] ?? '' ) . '</div></div>';
				}
				echo '</div>';
				break;
			case 'faq':
				echo '<div class="faq-list card">';
				foreach ( (array) ( $block['items'] ?? array() ) as $item ) {
					echo '<div class="faq-item"><button type="button" aria-expanded="false">' . esc_html( $item['question'] ?? '' ) . '</button>';
					echo '<div class="faq-answer">' . wp_kses_post( $item['answer'] ?? '' ) . '</div></div>';
				}
				echo '</div>';
				break;
		}
		echo '</div></section>';
	}
}
