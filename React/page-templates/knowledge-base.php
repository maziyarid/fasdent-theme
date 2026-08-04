<?php
/**
 * Template Name: مرکز آموزش
 *
 * @package Fasdent
 */

get_header();
?>
<section class="section kb-hub">
	<div class="container">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<header class="kb-hub__header">
				<h1 class="section-title"><?php the_title(); ?></h1>
				<?php if ( get_the_content() ) : ?>
					<div class="section-desc prose"><?php the_content(); ?></div>
				<?php else : ?>
					<p class="section-desc"><?php esc_html_e( 'راهنماهای علمی و پاسخ‌های شفاف درباره ایمپلنت، لمینت، ارتودنسی و مراقبت‌های دندانپزشکی.', 'fasdent' ); ?></p>
				<?php endif; ?>
			</header>
		<?php endwhile; endif; ?>

		<div class="kb-search">
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="kb-search__form">
				<label class="screen-reader-text" for="kb-s"><?php esc_html_e( 'جستجو در مرکز آموزش', 'fasdent' ); ?></label>
				<input type="search" id="kb-s" name="s" placeholder="<?php esc_attr_e( 'جستجو… مثال: ایمپلنت درد دارد؟', 'fasdent' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
				<input type="hidden" name="post_type" value="kb_article">
				<button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> <?php esc_html_e( 'جستجو', 'fasdent' ); ?></button>
			</form>
		</div>

		<?php
		$topics = get_terms( array( 'taxonomy' => 'kb_topic', 'hide_empty' => true, 'parent' => 0 ) );
		if ( $topics && ! is_wp_error( $topics ) ) :
			?>
			<section class="kb-topics" aria-labelledby="kb-topics-title">
				<h2 id="kb-topics-title" class="kb-section-title"><?php esc_html_e( 'موضوعات', 'fasdent' ); ?></h2>
				<div class="kb-topics__grid">
					<?php foreach ( $topics as $topic ) :
						$icon  = function_exists( 'fasdent_kb_topic_icon' ) ? fasdent_kb_topic_icon( $topic ) : 'fa-solid fa-book-medical';
						$count = (int) $topic->count;
						?>
						<a href="<?php echo esc_url( get_term_link( $topic ) ); ?>" class="kb-topic-card card">
							<span class="kb-topic-card__icon" aria-hidden="true"><i class="<?php echo esc_attr( $icon ); ?>"></i></span>
							<strong class="kb-topic-card__name"><?php echo esc_html( $topic->name ); ?></strong>
							<span class="kb-topic-card__count"><?php echo esc_html( sprintf( _n( '%d مقاله', '%d مقاله', $count, 'fasdent' ), $count ) ); ?></span>
							<?php if ( $topic->description ) : ?>
								<p class="kb-topic-card__desc"><?php echo esc_html( wp_trim_words( $topic->description, 12 ) ); ?></p>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php
		$latest = new WP_Query( array(
			'post_type' => 'kb_article',
			'posts_per_page' => 9,
			'post_status' => 'publish',
			'orderby' => 'date',
			'order' => 'DESC',
		) );
		if ( $latest->have_posts() ) :
			?>
			<section class="kb-latest" aria-labelledby="kb-latest-title">
				<h2 id="kb-latest-title" class="kb-section-title"><?php esc_html_e( 'آخرین مقالات', 'fasdent' ); ?></h2>
				<div class="kb-articles-grid">
					<?php
					while ( $latest->have_posts() ) :
						$latest->the_post();
						get_template_part( 'template-parts/kb-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
				<p class="kb-hub__more">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'kb_article' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'مشاهده همه مقالات', 'fasdent' ); ?></a>
				</p>
			</section>
		<?php else : ?>
			<p class="kb-empty"><?php esc_html_e( 'هنوز مقاله‌ای منتشر نشده. از پیشخوان → مرکز آموزش، مقاله جدید اضافه کنید.', 'fasdent' ); ?></p>
		<?php endif; ?>

		<?php
		$faqs = get_posts( array( 'post_type' => 'faq', 'numberposts' => 8, 'post_status' => 'publish' ) );
		if ( $faqs ) :
			?>
			<section class="kb-faq-bridge" aria-labelledby="kb-faq-title">
				<h2 id="kb-faq-title" class="kb-section-title"><?php esc_html_e( 'سوالات متداول', 'fasdent' ); ?></h2>
				<div class="faq-list card">
					<?php foreach ( $faqs as $faq ) : ?>
						<div class="faq-item">
							<button type="button" aria-expanded="false"><?php echo esc_html( $faq->post_title ); ?></button>
							<div class="faq-answer"><?php echo wp_kses_post( $faq->post_content ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
