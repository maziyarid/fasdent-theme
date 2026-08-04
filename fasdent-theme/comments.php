<?php
/**
 * Comments Template
 * 
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}

if ( have_comments() ) {
	?>
	<div id="comments" class="comments-area">
		<h2 class="comments-title">
			<?php
			$comments_number = get_comments_number();
			printf(
				esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $comments_number, 'comments title', 'fasdent' ) ),
				number_format_i18n( $comments_number ),
				'<span>' . get_the_title() . '</span>'
			);
			?>
		</h2>
		
		<ol class="comment-list">
			<?php wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 50,
			) ); ?>
		</ol>
		
		<?php the_comments_navigation(); ?>
	</div>
	<?php
}

comment_form( array(
	'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
	'title_reply_after'  => '</h2>',
) );
