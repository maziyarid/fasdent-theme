<?php
/**
 * Knowledge base article card.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$icon    = function_exists( 'fasdent_kb_meta' ) ? ( fasdent_kb_meta( 'kb_icon' ) ?: 'fa-solid fa-book-open' ) : 'fa-solid fa-book-open';
$reading = function_exists( 'fasdent_kb_meta' ) ? (int) fasdent_kb_meta( 'kb_reading_time' ) : 0;
$topics  = get_the_terms( get_the_ID(), 'kb_topic' );
?>
<article <?php post_class( 'kb-card card' ); ?>>
	<a class="kb-card__link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="kb-card__media">
				<?php the_post_thumbnail( 'fasdent-card', array( 'loading' => 'lazy', 'class' => 'kb-card__img' ) ); ?>
			</div>
		<?php else : ?>
			<div class="kb-card__media kb-card__media--icon" aria-hidden="true">
				<i class="<?php echo esc_attr( $icon ); ?>"></i>
			</div>
		<?php endif; ?>
		<div class="kb-card__body">
			<?php if ( $topics && ! is_wp_error( $topics ) ) : ?>
				<span class="kb-card__topic"><?php echo esc_html( $topics[0]->name ); ?></span>
			<?php endif; ?>
			<h3 class="kb-card__title"><?php the_title(); ?></h3>
			<p class="kb-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt() ?: get_the_content(), 16 ) ); ?></p>
			<div class="kb-card__meta">
				<?php if ( $reading ) : ?>
					<span><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo esc_html( (string) $reading ); ?> <?php esc_html_e( 'دقیقه', 'fasdent' ); ?></span>
				<?php endif; ?>
				<span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( get_the_date() ); ?></span>
			</div>
		</div>
	</a>
</article>
