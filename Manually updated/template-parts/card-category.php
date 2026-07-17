<?php
/**
 * Template part: Category card.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $term ) || ! $term instanceof WP_Term ) {
	return;
}

$term_link = get_term_link( $term );
if ( is_wp_error( $term_link ) ) {
	return;
}

$icon        = function_exists( 'fasdent_category_icon' ) ? fasdent_category_icon( $term ) : 'fa-duotone fa-solid fa-tooth';
$description = $term->description ? wp_strip_all_tags( $term->description ) : __( 'خدمات تخصصی و مراقبت حرفه‌ای در این دسته', 'fasdent' );
?>
<article class="category-card card">
	<a class="category-card__link" href="<?php echo esc_url( $term_link ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'مشاهده دسته %s', 'fasdent' ), $term->name ) ); ?>">
		<span class="category-card__icon" aria-hidden="true"><i class="<?php echo esc_attr( $icon ); ?>"></i></span>
		<span class="category-card__body">
			<span class="category-card__title"><?php echo esc_html( $term->name ); ?></span>
			<span class="category-card__description"><?php echo esc_html( wp_trim_words( $description, 20 ) ); ?></span>
			<?php if ( $term->count ) : ?>
				<span class="category-count"><i class="fa-duotone fa-solid fa-layer-group" aria-hidden="true"></i> <?php echo esc_html( number_format_i18n( $term->count ) ); ?> <?php esc_html_e( 'خدمت', 'fasdent' ); ?></span>
			<?php endif; ?>
			<span class="category-card__action"><?php esc_html_e( 'مشاهده خدمات', 'fasdent' ); ?> <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></span>
		</span>
	</a>
</article>
