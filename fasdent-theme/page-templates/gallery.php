<?php
/**
 * Template Name: گالری قبل و بعد
 *
 * @package Fasdent
 */

get_header();
?>
<section class="section ba-gallery-page">
	<div class="container">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<header class="ba-gallery-header">
				<h1 class="section-title"><?php the_title(); ?></h1>
				<?php if ( get_the_content() ) : ?>
					<div class="section-desc prose"><?php the_content(); ?></div>
				<?php else : ?>
					<p class="section-desc"><?php esc_html_e( 'نمونه‌های واقعی درمان در کلینیک فس‌دنت — بکشید تا قبل و بعد را مقایسه کنید.', 'fasdent' ); ?></p>
				<?php endif; ?>
			</header>
		<?php endwhile; endif; ?>

		<?php
		$terms = get_terms( array( 'taxonomy' => 'ba_category', 'hide_empty' => true ) );
		?>

		<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
		<nav class="ba-filter" aria-label="<?php esc_attr_e( 'فیلتر دسته‌بندی گالری', 'fasdent' ); ?>">
			<button type="button" class="ba-filter__btn is-active" data-ba-filter="all"><?php esc_html_e( 'همه', 'fasdent' ); ?></button>
			<?php foreach ( $terms as $term ) : ?>
				<button type="button" class="ba-filter__btn" data-ba-filter="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></button>
			<?php endforeach; ?>
		</nav>
		<?php endif; ?>

		<?php
		$cases = new WP_Query( array(
			'post_type'      => 'before_after',
			'posts_per_page' => 24,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );
		?>

		<?php if ( $cases->have_posts() ) : ?>
			<div class="ba-grid" data-ba-grid>
				<?php
				while ( $cases->have_posts() ) :
					$cases->the_post();
					$slugs = array();
					$cats  = get_the_terms( get_the_ID(), 'ba_category' );
					if ( $cats && ! is_wp_error( $cats ) ) {
						$slugs = wp_list_pluck( $cats, 'slug' );
					}
					?>
					<div class="ba-grid__item" data-ba-cats="<?php echo esc_attr( implode( ' ', $slugs ) ); ?>">
						<?php get_template_part( 'template-parts/before-after-slider', null, array( 'post_id' => get_the_ID() ) ); ?>
					</div>
				<?php endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p class="ba-empty"><?php esc_html_e( 'هنوز نمونه‌ای ثبت نشده. از پیشخوان → قبل و بعد، نمونه جدید اضافه کنید.', 'fasdent' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
