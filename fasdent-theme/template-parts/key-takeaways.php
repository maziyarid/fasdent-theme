<?php
/**
 * Template part: Key Takeaways
 * @package Fasdent
 */
$takeaways = fasdent_field( 'key_takeaways' );
if ( ! $takeaways || ! is_array( $takeaways ) ) { return; }
?>
<div class="key-takeaways card" role="complementary" aria-label="نکات کلیدی">
  <h3 class="key-takeaways__title">
    <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>
    <?php esc_html_e( 'نکات کلیدی این مطلب', 'fasdent' ); ?>
  </h3>
  <ul class="key-takeaways__list">
    <?php foreach ( $takeaways as $item ) :
      $icon = $item['icon'] ?? 'fa-solid fa-check-circle';
      $text = $item['text'] ?? '';
      if ( ! $text ) continue;
    ?>
    <li>
      <i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
      <?php echo esc_html( $text ); ?>
    </li>
    <?php endforeach; ?>
  </ul>
</div>