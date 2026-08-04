<?php
/**
 * Template Name: سوالات متداول
 * @package Fasdent
 */
get_header(); ?>
<section class="section">
<div class="container" style="max-width:820px;">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<h1><?php the_title(); ?></h1>
<?php the_content(); endwhile; endif; ?>

<!-- جستجو در FAQ -->
<div style="max-width:480px;margin:1.5rem 0;">
  <label for="faq-search" class="sr-only">جستجو در سوالات</label>
  <div style="position:relative;">
    <input type="search" id="faq-search" placeholder="جستجو در سوالات..." style="padding-right:2.5rem;">
    <i class="fa-solid fa-magnifying-glass" aria-hidden="true" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);color:var(--color-muted);pointer-events:none;"></i>
  </div>
</div>

<?php
$cats = get_terms( [ 'taxonomy' => 'service_category', 'hide_empty' => false, 'parent' => 0, 'number' => 15 ] );
$general_faqs = get_posts( [ 'post_type' => 'faq', 'numberposts' => 60, 'post_status' => 'publish', 'orderby' => 'menu_order title' ] );
?>

<!-- FAQ عمومی -->
<?php if ( $general_faqs ) : ?>
<div class="faq-list card faq-searchable" style="margin-bottom:2rem;">
  <?php foreach ( $general_faqs as $faq ) : ?>
  <div class="faq-item" data-faq-text="<?php echo esc_attr( strtolower( $faq->post_title . ' ' . wp_strip_all_tags( $faq->post_content ) ) ); ?>">
    <button type="button" aria-expanded="false"><?php echo esc_html( $faq->post_title ); ?></button>
    <div class="faq-answer"><?php echo wp_kses_post( $faq->post_content ); ?></div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- FAQ بر اساس دسته خدمات -->
<?php
if ( $cats && ! is_wp_error( $cats ) ) :
  foreach ( $cats as $cat ) :
    $faqs = fasdent_category_faqs( $cat->slug );
    if ( ! $faqs ) continue;
?>
<h2 class="section-title" style="text-align:right;font-size:1.1rem;margin-top:2rem;">
  <i class="<?php echo esc_attr( fasdent_category_icon( $cat ) ); ?>" aria-hidden="true"></i> <?php echo esc_html( $cat->name ); ?>
</h2>
<div class="faq-list card faq-searchable" style="margin-bottom:1.5rem;">
  <?php foreach ( $faqs as $faq ) : ?>
  <div class="faq-item" data-faq-text="<?php echo esc_attr( strtolower( ( $faq['question'] ?? '' ) . ' ' . wp_strip_all_tags( $faq['answer'] ?? '' ) ) ); ?>">
    <button type="button" aria-expanded="false"><?php echo esc_html( $faq['question'] ?? '' ); ?></button>
    <div class="faq-answer"><?php echo wp_kses_post( $faq['answer'] ?? '' ); ?></div>
  </div>
  <?php endforeach; ?>
</div>
<?php endforeach; endif; ?>

<div style="text-align:center;margin-top:2rem;">
  <p>سوال خود را پیدا نکردید؟</p>
  <?php fasdent_call_button(); ?> <?php fasdent_booking_button(); ?>
</div>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var input = document.getElementById('faq-search');
  if (!input) return;
  input.addEventListener('input', function() {
    var q = this.value.toLowerCase().trim();
    document.querySelectorAll('.faq-searchable .faq-item').forEach(function(item) {
      item.style.display = (!q || (item.dataset.faqText || '').includes(q)) ? '' : 'none';
    });
  });
});
</script>
<?php get_footer(); ?>