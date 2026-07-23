<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!-- دسترسی‌پذیری: پرش به محتوا (WCAG 2.1 AA) -->
<a class="skip-link" href="#content"><?php esc_html_e( 'رفتن به محتوا', 'fasdent' ); ?></a>

<?php get_template_part( 'template-parts/site-navigation' ); ?>
<?php fasdent_breadcrumb(); ?>
<main id="content" role="main">
