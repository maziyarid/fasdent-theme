<?php
/**
 * Template part: Accessible breadcrumb trail.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$crumbs = array(
	array(
		'label' => __( 'خانه', 'fasdent' ),
		'url'   => home_url( '/' ),
		'icon'  => 'fa-duotone fa-solid fa-house',
	),
);

if ( is_singular( 'post' ) ) {
	$categories = get_the_category();
	if ( ! empty( $categories ) ) {
		$primary_category = $categories[0];
		$crumbs[] = array(
			'label' => $primary_category->name,
			'url'   => get_category_link( $primary_category ),
			'icon'  => 'fa-duotone fa-solid fa-folder-open',
		);
	}
} elseif ( is_page() ) {
	$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
	foreach ( $ancestors as $ancestor_id ) {
		$crumbs[] = array(
			'label' => get_the_title( $ancestor_id ),
			'url'   => get_permalink( $ancestor_id ),
			'icon'  => 'fa-duotone fa-solid fa-file-lines',
		);
	}
}

$current_label = is_singular() ? get_the_title() : wp_get_document_title();
$crumbs[] = array(
	'label' => $current_label,
	'url'   => '',
	'icon'  => 'fa-duotone fa-solid fa-location-dot',
);
?>
<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'fasdent' ); ?>">
	<div class="container">
		<ol itemscope itemtype="https://schema.org/BreadcrumbList">
			<?php foreach ( $crumbs as $index => $crumb ) : ?>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<?php if ( $index > 0 ) : ?>
						<i class="fa-solid fa-chevron-left breadcrumb__separator" aria-hidden="true"></i>
					<?php endif; ?>
					<?php if ( $crumb['url'] ) : ?>
						<a itemprop="item" href="<?php echo esc_url( $crumb['url'] ); ?>">
							<i class="<?php echo esc_attr( $crumb['icon'] ); ?>" aria-hidden="true"></i>
							<span itemprop="name"><?php echo esc_html( $crumb['label'] ); ?></span>
						</a>
					<?php else : ?>
						<span aria-current="page" itemprop="name"><?php echo esc_html( $crumb['label'] ); ?></span>
					<?php endif; ?>
					<meta itemprop="position" content="<?php echo esc_attr( (string) ( $index + 1 ) ); ?>">
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</nav>
