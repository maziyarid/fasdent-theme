<?php
/**
 * قالب نظرات — Fasdent
 * بدون فیلد ایمیل | Honeypot | هایلایت پاسخ دکتر | نظرات تو در تو
 *
 * @package Fasdent
 */

if ( post_password_required() ) {
	return;
}
?>

<?php if ( have_comments() ) : ?>
<section class="comments-section" id="comments" aria-labelledby="comments-title">
	<div class="container">
		<h2 id="comments-title" class="comments-title">
			<?php
			$count = get_comments_number();
			if ( $count ) {
				printf( esc_html( '%s نظر' ), number_format_i18n( $count ) );
			} else {
				esc_html_e( 'نظرات', 'fasdent' );
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 56,
				'callback'    => 'fasdent_comment_callback',
			) );
			?>
		</ol>

		<?php the_comments_pagination( array(
			'prev_text' => '<i class="fa-solid fa-angle-right" aria-hidden="true"></i> قبلی',
			'next_text' => 'بعدی <i class="fa-solid fa-angle-left" aria-hidden="true"></i>',
		) ); ?>

	</div>
</section>
<?php endif; ?>

<?php if ( comments_open() ) : ?>
<section class="comment-form-section">
	<div class="container">
		<?php
		$notes_after = '<p class="comment-form-hp" aria-hidden="true" style="position:absolute;left:-9999px;height:0;overflow:hidden;">'
			. '<label for="comment_hp_email">این فیلد را خالی بگذارید</label>'
			. '<input type="text" id="comment_hp_email" name="comment_hp_email" tabindex="-1" autocomplete="off">'
			. '</p>';

		comment_form( array(
			'title_reply'          => '<span id="leave-a-comment">' . __( 'ثبت نظر', 'fasdent' ) . '</span>',
			'title_reply_before'   => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'    => '</h2>',
			'cancel_reply_link'    => __( 'انصراف', 'fasdent' ),
			'label_submit'         => __( 'ثبت نظر', 'fasdent' ),
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s btn btn-primary">'
				. '<i class="fa-solid fa-paper-plane" aria-hidden="true"></i> %4$s</button>',
			'comment_notes_before' => '<p class="comment-notes">' . __( 'نظر شما پس از بررسی منتشر می‌شود.', 'fasdent' ) . '</p>',
			'comment_notes_after'  => $notes_after,
			'fields'               => array(
				'author' => '<p class="comment-form-author"><label for="author">'
					. __( 'نام', 'fasdent' )
					. ' <span class="required" aria-hidden="true">*</span></label>'
					. '<input id="author" name="author" type="text" maxlength="245" required autocomplete="name"></p>',
				'phone'  => '<p class="comment-form-phone"><label for="comment_phone">'
					. __( 'شماره تماس (اختیاری)', 'fasdent' )
					. '</label><input id="comment_phone" name="comment_phone" type="tel" maxlength="15" autocomplete="tel"></p>',
			),
			'comment_field' => '<p class="comment-form-comment"><label for="comment">'
				. __( 'نظر شما', 'fasdent' )
				. ' <span class="required" aria-hidden="true">*</span></label>'
				. '<textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required spellcheck="false"></textarea></p>',
		) );
		?>
	</div>
</section>
<?php endif; ?>

<?php
// fasdent_comment_callback is defined in inc/forms.php to avoid redeclaration.