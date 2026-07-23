<?php
/**
 * Template Name: نقشه سایت
 * @package Fasdent
 */
get_header(); ?>
<section class="section">
  <div class="container">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>
    <?php endwhile; endif; ?>

    <div class="sitemap-tree">
      <!-- صفحه اصلی -->
      <ul class="sitemap-level-1">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="fa-solid fa-house" aria-hidden="true"></i> <?php esc_html_e( 'خانه', 'fasdent' ); ?></a>

          <!-- خدمات -->
          <ul class="sitemap-level-2">
            <li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><i class="fa-solid fa-tooth" aria-hidden="true"></i> <?php esc_html_e( 'خدمات دندانپزشکی', 'fasdent' ); ?></a>
              <ul class="sitemap-level-3">
                <?php
                $cats = get_terms( array( 'taxonomy' => 'service_category', 'hide_empty' => true, 'parent' => 0 ) );
                if ( $cats && ! is_wp_error( $cats ) ) :
                  foreach ( $cats as $cat ) :
                ?>
                <li>
                  <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
                    <i class="<?php echo esc_attr( fasdent_category_icon( $cat ) ); ?>" aria-hidden="true"></i>
                    <?php echo esc_html( $cat->name ); ?>
                  </a>
                  <?php
                  $services = get_posts( array( 'post_type' => 'service', 'numberposts' => -1, 'post_status' => 'publish', 'tax_query' => array( array( 'taxonomy' => 'service_category', 'field' => 'term_id', 'terms' => $cat->term_id, 'include_children' => false ) ) ) );
                  if ( $services ) :
                  ?>
                  <ul class="sitemap-level-4">
                    <?php foreach ( $services as $svc ) : ?>
                    <li><a href="<?php echo esc_url( get_permalink( $svc ) ); ?>"><?php echo esc_html( $svc->post_title ); ?></a></li>
                    <?php endforeach; wp_reset_postdata(); ?>
                  </ul>
                  <?php endif; ?>
                </li>
                <?php endforeach; endif; ?>
              </ul>
            </li>
          </ul>

          <!-- مقالات -->
          <ul class="sitemap-level-2">
            <li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i> <?php esc_html_e( 'مقالات', 'fasdent' ); ?></a>
              <ul class="sitemap-level-3">
                <?php $blog_cats = get_categories( array( 'hide_empty' => true ) );
                foreach ( $blog_cats as $bc ) : ?>
                <li><a href="<?php echo esc_url( get_category_link( $bc ) ); ?>"><?php echo esc_html( $bc->name ); ?></a></li>
                <?php endforeach; ?>
              </ul>
            </li>
          </ul>

          <!-- صفحات ثابت -->
          <ul class="sitemap-level-2">
            <?php
            $pages = get_pages( array( 'parent' => 0, 'post_status' => 'publish', 'exclude' => array( get_option( 'page_on_front' ), get_option( 'page_for_posts' ) ) ) );
            foreach ( $pages as $p ) : ?>
            <li><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( $p->post_title ); ?></a></li>
            <?php endforeach; ?>
          </ul>

        </li>
      </ul>
    </div>
  </div>
</section>
<?php get_footer(); ?>