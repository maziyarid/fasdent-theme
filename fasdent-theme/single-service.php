<?php get_header(); ?>
<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article class="card">
				<h1><?php the_title(); ?></h1>
				<p><strong>قیمت پایه:</strong> <?php echo esc_html( fasdent_field( 'service_price' ) ?: 'تماس بگیرید' ); ?></p>
				<p><strong>مدت درمان:</strong> <?php echo esc_html( fasdent_field( 'service_duration' ) ?: 'درخواست مشاوره' ); ?></p>
				<?php the_content(); ?>
				<?php if ( $steps = fasdent_get_service_steps() ) : ?>
					<h2>مراحل درمان</h2>
					<ol>
						<?php foreach ( $steps as $step ) : ?>
							<li><strong><?php echo esc_html( $step['title'] ?? '' ); ?>:</strong> <?php echo esc_html( $step['description'] ?? '' ); ?></li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>
				<?php if ( $faqs = fasdent_get_service_faqs() ) : ?>
					<h2>سوالات متداول</h2>
					<div class="faq-list">
						<?php foreach ( $faqs as $faq ) : ?>
							<div class="faq-item">
								<button type="button"><?php echo esc_html( $faq['question'] ?? '' ); ?></button>
								<div class="faq-answer"><?php echo wp_kses_post( $faq['answer'] ?? '' ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</article>
		<?php endwhile; endif; ?>
	</div>
</section>
<?php get_footer(); ?>