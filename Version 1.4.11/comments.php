<?php
/** Comments template for native WordPress posts. */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title"><?php echo esc_html( sprintf( _n( 'یک دیدگاه', '%s دیدگاه', get_comments_number(), 'alipasandi-clinic' ), number_format_i18n( get_comments_number() ) ) ); ?></h2>
		<ol class="comment-list">
			<?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 42 ) ); ?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>
