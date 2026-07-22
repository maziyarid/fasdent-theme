<?php
/**
 * Template part: Native accessible FAQ item.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$question = isset( $faq['question'] ) ? trim( (string) $faq['question'] ) : '';
$answer   = isset( $faq['answer'] ) ? (string) $faq['answer'] : '';

if ( '' === $question || '' === trim( wp_strip_all_tags( $answer ) ) ) {
	return;
}
?>
<details class="faq-item">
	<summary class="faq-item__question">
		<span class="faq-item__icon" aria-hidden="true"><i class="fa-duotone fa-solid fa-circle-question"></i></span>
		<span><?php echo esc_html( $question ); ?></span>
		<i class="fa-solid fa-chevron-down faq-item__caret" aria-hidden="true"></i>
	</summary>
	<div class="faq-item__answer faq-answer"><?php echo wp_kses_post( wpautop( $answer ) ); ?></div>
</details>
