<?php
/**
 * تک مطلب بلاگ — Fasdent
 * @package Fasdent
 */
get_header();
if ( ! have_posts() ) { get_footer(); return; }
while ( have_posts() ) : the_post();
$reading_time = fasdent_reading_time();
$post_id      = get_the_ID();
?>

<div class="single-post" data-post-id="<?php echo esc_attr( (string) $post_id ); ?>">

  <!-- نوار پیشرفت مطالعه -->
  <div class="reading-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>

  <div class="container single-post__layout">
    <article class="single-post__main">

      <!-- هدر مطلب -->
      <header class="post-header">
        <?php $cats = get_the_category();
        if ( $cats ) : ?>
        <div class="post-cats">
          <?php foreach ( $cats as $cat ) : ?>
          <a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="post-cat-badge"><?php echo esc_html( $cat->name ); ?></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h1><?php the_title(); ?></h1>

        <div class="post-meta-bar">
          <span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( get_the_date() ); ?></span>
          <?php if ( get_the_modified_date() !== get_the_date() ) : ?>
          <span><i class="fa-solid fa-pen" aria-hidden="true"></i> <?php esc_html_e( 'بروزرسانی:', 'fasdent' ); ?> <?php echo esc_html( get_the_modified_date() ); ?></span>
          <?php endif; ?>
          <?php if ( $reading_time ) : ?>
          <span><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo esc_html( $reading_time . ' دقیقه مطالعه' ); ?></span>
          <?php endif; ?>
          <span><i class="fa-regular fa-user" aria-hidden="true"></i> <?php the_author(); ?></span>
        </div>

        <?php if ( has_post_thumbnail() ) : ?>
          <div class="post-featured-image"><?php the_post_thumbnail( 'fasdent-hero', array( 'loading' => 'eager' ) ); ?></div>
        <?php endif; ?>
        </header>
  
        <!-- نکات کلیدی -->
        <?php get_template_part( 'template-parts/key-takeaways' ); ?>
  
        <!-- محتوا -->
        <div class="post-content prose">
          <?php the_content(); ?>
        </div>

      <!-- برچسب‌ها -->
      <?php $tags = get_the_tags();
      if ( $tags ) : ?>
      <div class="post-tags" style="margin-top:2rem;">
        <i class="fa-solid fa-tags" aria-hidden="true"></i>
        <?php foreach ( $tags as $tag ) : ?>
        <a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>" class="tag-pill"><?php echo esc_html( $tag->name ); ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- واکنش‌های بیماران -->
      <div class="post-reactions" style="margin:1.5rem 0;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        <span style="font-size:.85rem;color:var(--color-muted);">این مطلب چقدر مفید بود؟</span>
        <?php
        $reactions = array(
          'helpful'  => array( 'fa-solid fa-thumbs-up', 'مفید' ),
          'thanks'   => array( 'fa-solid fa-heart', 'ممنون' ),
          'accurate' => array( 'fa-solid fa-bullseye', 'دقیق' ),
        );
        foreach ( $reactions as $key => $data ) :
          $count = (int) get_post_meta( $post_id, '_reaction_' . $key, true );
        ?>
        <button type="button" class="reaction-btn btn btn-secondary" data-reaction="<?php echo esc_attr( $key ); ?>" style="padding:.5rem 1rem;font-size:.85rem;">
          <i class="<?php echo esc_attr( $data[0] ); ?>" aria-hidden="true"></i>
          <?php echo esc_html( $data[1] ); ?>
          <?php if ( $count ) : ?><span class="reaction-count" style="margin-right:.3rem;opacity:.7;">(<?php echo esc_html( number_format_i18n( $count ) ); ?>)</span><?php endif; ?>
        </button>
        <?php endforeach; ?>
      </div>

      <!-- اشتراک‌گذاری -->
      <?php get_template_part( 'template-parts/social-share' ); ?>

      <!-- ناوبری مطالب -->
      <nav class="post-navigation" aria-label="ناوبری مطالب">
        <?php
        $prev = get_previous_post();
        $next = get_next_post();
        ?>
        <?php if ( $prev ) : ?>
        <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" class="card post-nav-item post-nav-prev">
          <span class="nav-label"><i class="fa-solid fa-angle-right" aria-hidden="true"></i> مطلب قبلی</span>
          <span class="nav-title"><?php echo esc_html( $prev->post_title ); ?></span>
        </a>
        <?php endif; ?>
        <?php if ( $next ) : ?>
        <a href="<?php echo esc_url( get_permalink( $next ) ); ?>" class="card post-nav-item post-nav-next">
          <span class="nav-label">مطلب بعدی <i class="fa-solid fa-angle-left" aria-hidden="true"></i></span>
          <span class="nav-title"><?php echo esc_html( $next->post_title ); ?></span>
        </a>
        <?php endif; ?>
      </nav>

      <!-- بیوگرافی نویسنده -->
      <div class="author-bio card" style="margin-top:2rem;">
        <?php echo get_avatar( get_the_author_meta( 'ID' ), 64 ); ?>
        <div>
          <strong><?php the_author(); ?></strong>
          <p><?php echo esc_html( get_the_author_meta( 'description' ) ?: 'نویسنده کلینیک فس‌دنت' ); ?></p>
        </div>
      </div>

      <!-- نظرات -->
      <?php comments_template(); ?>

    </article>

    <!-- سایدبار -->
    <aside class="single-post__sidebar">
      <?php get_template_part( 'template-parts/toc-sidebar' ); ?>
      <?php get_template_part( 'template-parts/cta-banner' ); ?>
    </aside>
  </div>

</div>

<?php endwhile; ?>
<?php get_footer(); ?>