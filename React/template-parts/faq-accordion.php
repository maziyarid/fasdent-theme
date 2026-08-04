<?php
/**
 * Template part: faq accordion
 *
 * @package Fasdent
 */
?>
<div class="faq-item">
	<button type="button"><?php echo esc_html( $faq['question'] ?? '' ); ?></button>
	<div class="faq-answer"><?php echo wp_kses_post( $faq['answer'] ?? '' ); ?></div>
</div>
