<?php
/**
 * Single Knowledge Base article.
 *
 * @package Fasdent
 */

get_header();

while ( have_posts() ) :
	the_post();

	$icon    = function_exists( 'fasdent_kb_meta' ) ? ( fasdent_kb_meta( 'kb_icon' ) ?: 'fa-solid fa-book-open' ) : 'fa-solid fa-book-open';
	$reading = function_exists( 'fasdent_kb_meta' ) ? (int) fasdent_kb_meta( 'kb_reading_time' ) : 0;
	$quick   = function_exists( 'fasdent_kb_meta' ) ? (string) fasdent_kb_meta( 'kb_quick_answer' ) : '';
	$points  = function_exists( 'fasdent_kb_key_points' ) ? fasdent_kb_key_points() : array();
	$svc_id  = function_exists( 'fasdent_kb_meta' ) ? absint( fasdent_kb_meta( 'kb_related_service' ) ) : 0;
	$topics  = get_the_terms( get_the_ID(), 'kb_topic' );
	?>
	<article <?php post_class( 'section kb-single' ); ?>>
		<div class="container kb-single__layout">
			<header class="kb-single__header">
				<?php if ( $topics && ! is_wp_error( $topics ) ) : ?>
					<p class="kb-single__topics">
						<?php foreach ( $topics as $t ) : ?>
							<a href="<?php echo esc_url( get_term_link( $t ) ); ?>" class="kb-tag"><?php echo esc_html( $t->name ); ?></a>
						<?php endforeach; ?>
					</p>
				<?php endif; ?>
				<h1 class="kb-single__title">
					<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
					<?php the_title(); ?>
				</h1>
				<div class="kb-single__meta">
					<?php if ( $reading ) : ?>
						<span><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo esc_html( (string) $reading ); ?> <?php esc_html_e( 'دقیقه مطالعه', 'fasdent' ); ?></span>
					<?php endif; ?>
					<span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( get_the_date() ); ?></span>
					<span><i class="fa-regular fa-user" aria-hidden="true"></i> <?php the_author(); ?></span>
				</div>
			</header>

			<?php if ( $quick ) : ?>
				<div class="kb-quick-answer" role="note">
					<strong class="kb-quick-answer__label"><i class="fa-solid fa-lightbulb" aria-hidden="true"></i> <?php esc_html_e( 'پاسخ سریع', 'fasdent' ); ?></strong>
					<p><?php echo esc_html( $quick ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( $points ) : ?>
				<aside class="kb-key-points card">
					<h2 class="kb-key-points__title"><?php esc_html_e( 'نکات کلیدی', 'fasdent' ); ?></h2>
					<ul>
						<?php foreach ( $points as $p ) : ?>
							<li><i class="fa-solid fa-check-circle" aria-hidden="true"></i> <?php echo esc_html( $p ); ?></li>
						<?php endforeach; ?>
					</ul>
				</aside>
			<?php endif; ?>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="kb-single__featured">
					<?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="kb-single__content prose">
				<?php the_content(); ?>
			</div>

			<?php if ( $svc_id ) : ?>
				<div class="kb-related-service card">
					<p><?php esc_html_e( 'خدمت مرتبط در کلینیک:', 'fasdent' ); ?></p>
					<a href="<?php echo esc_url( get_permalink( $svc_id ) ); ?>" class="btn btn-primary">
						<?php echo esc_html( get_the_title( $svc_id ) ); ?>
					</a>
					<?php if ( function_exists( 'fasdent_booking_button' ) ) : ?>
						<?php fasdent_booking_button( __( 'رزرو نوبت', 'fasdent' ), 'btn-secondary' ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<footer class="kb-single__footer">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'kb_article' ) ); ?>" class="btn btn-secondary">
					<i class="fa-solid fa-arrow-right" aria-hidden="true"></i> <?php esc_html_e( 'بازگشت به مرکز آموزش', 'fasdent' ); ?>
				</a>
			</footer>
		</div>
	</article>
	<?php
endwhile;

get_footer();
