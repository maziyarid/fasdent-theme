<?php
/**
 * Template Name: تعرفه‌ها
 *
 * @package Fasdent
 */

get_header(); ?>
<section class="section">
	<div class="container">
		<h1><?php the_title(); ?></h1>
		<div class="card">
			<table style="width:100%;border-collapse:collapse;">
				<thead>
				<tr><th style="text-align:right;padding:0.75rem;border-bottom:1px solid #e2e8f0;">خدمت</th><th style="text-align:right;padding:0.75rem;border-bottom:1px solid #e2e8f0;">قیمت پایه</th></tr>
				</thead>
				<tbody>
				<?php
				$services = get_posts( array( 'post_type' => 'service', 'numberposts' => 10, 'post_status' => 'publish' ) );
				foreach ( $services as $service ) {
					echo '<tr><td style="padding:0.75rem;border-bottom:1px solid #f1f5f9;">' . esc_html( $service->post_title ) . '</td><td style="padding:0.75rem;border-bottom:1px solid #f1f5f9;">' . esc_html( fasdent_field( 'service_price', $service->ID ) ?: 'تماس بگیرید' ) . '</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php get_footer(); ?>