<?php
/**
 * Template Name: گالری
 * گالری قبل/بعد — تصاویر از فیلد ACF service_gallery خدمات.
 *
 * @package Fasdent
 */

get_header(); ?>
<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
		<?php the_content(); ?>
		<?php endwhile; endif; ?>

		<?php
		// دریافت همه دسته‌های خدمات (سطح اول).
		$categories = get_terms( array(
			'taxonomy'   => 'service_category',
			'hide_empty' => true,
			'parent'     => 0,
		) );
		if ( $categories && ! is_wp_error( $categories ) ) :
			foreach ( $categories as $cat ) :
				// دریافت خدمات این دسته.
				$services = get_posts( array(
					'post_type'   => 'service',
					'numberposts' => -1,
					'post_status' => 'publish',
					'tax_query'   => array( array(
						'taxonomy' => 'service_category',
						'field'    => 'term_id',
						'terms'    => $cat->term_id,
					) ),
				) );
				// بررسی وجود گالری در حداقل یک خدمت.
				$has_gallery = false;
				foreach ( $services as $service ) {
					$gallery = fasdent_field( 'service_gallery', $service->ID );
					if ( $gallery ) { $has_gallery = true; break; }
				}
				if ( ! $has_gallery ) continue;
		?>
		<section class="gallery-category" id="cat-<?php echo esc_attr( $cat->slug ); ?>">
			<h2 class="gallery-category__title">
				<i class="<?php echo esc_attr( fasdent_category_icon( $cat ) ); ?>" aria-hidden="true"></i>
				<?php echo esc_html( $cat->name ); ?>
			</h2>
			<?php foreach ( $services as $service ) :
				$gallery = fasdent_field( 'service_gallery', $service->ID );
				if ( ! $gallery || ! is_array( $gallery ) ) continue;
			?>
			<div class="gallery-service">
				<h3 class="gallery-service__title"><?php echo esc_html( $service->post_title ); ?></h3>
				<div class="gallery-grid grid-4">
					<?php foreach ( $gallery as $img ) :
						if ( is_array( $img ) ) {
							$src     = $img['url']   ?? '';
							$alt     = $img['alt']   ?? $service->post_title;
							$thumb   = $img['sizes']['fasdent-gallery'] ?? $src;
						} elseif ( is_numeric( $img ) ) {
							$src   = wp_get_attachment_url( $img ) ?: '';
							$alt   = get_post_meta( $img, '_wp_attachment_image_alt', true ) ?: $service->post_title;
							$thumb = wp_get_attachment_image_src( $img, 'fasdent-gallery' )[0] ?? $src;
						} else {
							continue;
						}
						if ( ! $src ) continue;
					?>
					<figure class="gallery-item">
						<img
							src="<?php echo esc_url( $thumb ); ?>"
							alt="<?php echo esc_attr( $alt ); ?>"
							loading="lazy"
							decoding="async"
							data-lightbox
							data-full="<?php echo esc_url( $src ); ?>"
						>
					</figure>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</section>
		<?php
			endforeach;
		endif;
		?>
	</div>
</section>
<?php get_footer(); ?>