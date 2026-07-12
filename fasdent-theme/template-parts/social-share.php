<?php
/**
 * Template part: Social Share Buttons
 * @package Fasdent
 */
$url   = rawurlencode( (string) get_permalink() );
$title = rawurlencode( (string) get_the_title() );
$text  = rawurlencode( get_the_title() . ' — ' . get_theme_mod( 'fasdent_clinic_name', 'کلینیک فس‌دنت' ) );
?>
<div class="social-share" aria-label="اشتراک‌گذاری">
  <span class="social-share__label"><i class="fa-solid fa-share-nodes" aria-hidden="true"></i> اشتراک‌گذاری:</span>
  <a href="<?php echo esc_url( 'https://t.me/share/url?url=' . $url . '&text=' . $text ); ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--telegram" aria-label="اشتراک در تلگرام">
    <i class="fa-brands fa-telegram" aria-hidden="true"></i>
  </a>
  <a href="<?php echo esc_url( 'https://wa.me/?text=' . $text . '%20' . $url ); ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--whatsapp" aria-label="اشتراک در واتس‌اپ">
    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
  </a>
  <a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?text=' . $text . '&url=' . $url ); ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--twitter" aria-label="اشتراک در توییتر">
    <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
  </a>
  <a href="<?php echo esc_url( 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title ); ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--linkedin" aria-label="اشتراک در لینکدین">
    <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
  </a>
  <button class="social-btn social-btn--copy" data-copy-url="<?php echo esc_attr( (string) get_permalink() ); ?>" aria-label="کپی لینک" type="button">
    <i class="fa-solid fa-link" aria-hidden="true"></i>
  </button>
</div>