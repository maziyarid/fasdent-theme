<?php
/**
 * Template part: breadcrumb
 *
 * @package Fasdent
 */
?>
<nav class="breadcrumb" aria-label="مسیر صفحه">
	<div class="container">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">خانه</a></li>
			<li><span><?php the_title(); ?></span></li>
		</ol>
	</div>
</nav>
