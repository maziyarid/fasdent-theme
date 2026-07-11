<?php
/**
 * Template part: Social Share Buttons
 * @package Fasdent
 */
$url   = rawurlencode( get_permalink() );
$title = rawurlencode( get_the_title() );
$text  = rawurlencode( get_the_title() . ' — ' . get_theme_mod( 'fasdent_clinic_name', 'کلینیک فس‌دنت' ) );
?>
<div class="social-share" aria-label="اشتراک‌گذاری">
  <span class="social-share__label"><i class="fa-solid fa-share-nodes" aria-hidden="true"></i> اشتراک‌گذاری:</span>
  <a href="https://t.me/share/url?url=<?php echo $url; ?>&text=<?php echo $text; ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--telegram" aria-label="اشتراک در تلگرام">
    <i class="fa-brands fa-telegram" aria-hidden="true"></i>
  </a>
  <a href="https://wa.me/?text=<?php echo $text; ?>%20<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--whatsapp" aria-label="اشتراک در واتس‌اپ">
    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
  </a>
  <a href="https://twitter.com/intent/tweet?text=<?php echo $text; ?>&url=<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--twitter" aria-label="اشتراک در توییتر">
    <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
  </a>
  <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $title; ?>" target="_blank" rel="noopener noreferrer" class="social-btn social-btn--linkedin" aria-label="اشتراک در لینکدین">
    <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
  </a>
  <button class="social-btn social-btn--copy" data-copy-url="<?php echo esc_attr( get_permalink() ); ?>" aria-label="کپی لینک">
    <i class="fa-solid fa-link" aria-hidden="true"></i>
  </button>
</div>