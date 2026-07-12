<?php
/**
 * Template Name: تعرفه‌ها
 * @package Fasdent
 */
get_header(); ?>
<section class="section">
<div class="container">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<h1><?php the_title(); ?></h1>
<?php the_content(); endwhile; endif; ?>

<p class="alert alert--warn"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> قیمت‌ها تقریبی هستند و بسته به شرایط بیمار ممکن است متفاوت باشند. برای قیمت دقیق با ما تماس بگیرید.</p>

<?php
$cats = get_terms( [ 'taxonomy' => 'service_category', 'hide_empty' => true, 'parent' => 0, 'orderby' => 'name' ] );
if ( $cats && ! is_wp_error( $cats ) ) :
  foreach ( $cats as $cat ) :
    $services = get_posts( [
      'post_type'      => 'service',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'tax_query'      => [ [ 'taxonomy' => 'service_category', 'field' => 'term_id', 'terms' => $cat->term_id ] ],
      'orderby'        => 'title',
      'order'          => 'ASC',
    ] );
    if ( ! $services ) continue;
?>
<div class="pricing-category" style="margin-bottom:2.5rem;">
  <h2 class="section-title" style="text-align:right;">
    <i class="<?php echo esc_attr( fasdent_category_icon( $cat ) ); ?>" aria-hidden="true"></i>
    <?php echo esc_html( $cat->name ); ?>
  </h2>
  <div class="card" style="overflow-x:auto;">
    <table class="pricing-table" role="table">
      <thead>
        <tr>
          <th scope="col">خدمت</th>
          <th scope="col">مدت زمان</th>
          <th scope="col">قیمت پایه</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ( $services as $svc ) : ?>
        <tr>
          <td><strong><?php echo esc_html( $svc->post_title ); ?></strong></td>
          <td><?php echo esc_html( fasdent_field( 'duration', $svc->ID ) ?: '—' ); ?></td>
          <td class="service-price"><?php echo esc_html( fasdent_field( 'price', $svc->ID ) ?: 'تماس بگیرید' ); ?></td>
          <td><a href="<?php echo esc_url( get_permalink( $svc ) ); ?>" class="btn" style="padding:.35rem .75rem;font-size:.8rem;">اطلاعات بیشتر</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endforeach; endif; ?>

<div class="section--cta card" style="text-align:center;padding:2rem;margin-top:2rem;">
  <h2>مشاوره رایگان</h2>
  <p>برای دریافت مشاوره و برآورد دقیق هزینه درمان با ما تماس بگیرید.</p>
  <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
    <?php fasdent_call_button(); ?>
    <?php fasdent_booking_button(); ?>
  </div>
</div>
</div>
</section>
<?php get_footer(); ?>