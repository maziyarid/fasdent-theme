<?php
/**
 * Lightweight SEO and structured-data foundation.
 *
 * @package Alipasandi_Clinic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Detect common SEO plugins so the theme does not emit duplicate metadata. */
function alipasandi_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/** Return the current service key when viewing one of the dedicated landing pages. */
function alipasandi_current_service_key() {
	foreach ( array( 'implant', 'crown', 'surgery', 'general' ) as $key ) {
		if ( is_page( $key ) ) {
			return $key;
		}
	}
	return '';
}

/** Add per-page SEO controls without requiring a heavy page builder. */
function alipasandi_register_seo_meta_box() {
	add_meta_box(
		'alipasandi-seo',
		__( 'تنظیمات SEO و شبکه‌های اجتماعی', 'alipasandi-clinic' ),
		'alipasandi_render_seo_meta_box',
		array( 'page', 'post' ),
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'alipasandi_register_seo_meta_box' );

/** Render the SEO fields. */
function alipasandi_render_seo_meta_box( $post ) {
	wp_nonce_field( 'alipasandi_save_seo', 'alipasandi_seo_nonce' );
	$fields = array(
		'_alipasandi_seo_title'       => array( 'SEO Title', 'text' ),
		'_alipasandi_seo_description' => array( 'Meta Description', 'textarea' ),
		'_alipasandi_canonical_url'   => array( 'Canonical URL', 'url' ),
		'_alipasandi_og_title'        => array( 'Open Graph Title', 'text' ),
		'_alipasandi_og_description'  => array( 'Open Graph Description', 'textarea' ),
		'_alipasandi_og_image'        => array( 'Open Graph Image URL', 'url' ),
	);
	?>
	<p><?php esc_html_e( 'در صورت خالی‌بودن هر فیلد، مقدار استاندارد وردپرس استفاده می‌شود. با فعال‌بودن افزونه SEO، خروجی متای افزونه اولویت دارد تا تگ تکراری تولید نشود.', 'alipasandi-clinic' ); ?></p>
	<table class="form-table" role="presentation">
		<?php foreach ( $fields as $key => $field ) : $value = get_post_meta( $post->ID, $key, true ); ?>
			<tr>
				<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
				<td>
					<?php if ( 'textarea' === $field[1] ) : ?>
						<textarea class="large-text" rows="3" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
					<?php else : ?>
						<input class="large-text" type="<?php echo esc_attr( $field[1] ); ?>" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

/** Save per-page SEO fields safely. */
function alipasandi_save_seo_fields( $post_id ) {
	if ( ! isset( $_POST['alipasandi_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alipasandi_seo_nonce'] ) ), 'alipasandi_save_seo' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_fields = array( '_alipasandi_seo_title', '_alipasandi_og_title' );
	$url_fields  = array( '_alipasandi_canonical_url', '_alipasandi_og_image' );
	$area_fields = array( '_alipasandi_seo_description', '_alipasandi_og_description' );

	foreach ( array_merge( $text_fields, $url_fields, $area_fields ) as $key ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$value = wp_unslash( $_POST[ $key ] );
		if ( in_array( $key, $url_fields, true ) ) {
			$value = esc_url_raw( $value );
		} elseif ( in_array( $key, $area_fields, true ) ) {
			$value = sanitize_textarea_field( $value );
		} else {
			$value = sanitize_text_field( $value );
		}

		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post', 'alipasandi_save_seo_fields' );

/** Use a page-specific SEO title when supplied. */
function alipasandi_filter_document_title( $title ) {
	if ( is_singular() ) {
		$custom = get_post_meta( get_queried_object_id(), '_alipasandi_seo_title', true );
		if ( $custom ) {
			return $custom;
		}
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'alipasandi_filter_document_title' );

/** Use a page-specific canonical URL while preserving WordPress defaults. */
function alipasandi_filter_canonical_url( $canonical, $post ) {
	$custom = get_post_meta( $post->ID, '_alipasandi_canonical_url', true );
	return $custom ? $custom : $canonical;
}
add_filter( 'get_canonical_url', 'alipasandi_filter_canonical_url', 10, 2 );

/**
 * Keep the template-owned H1 unique by downgrading accidental H1 tags inside editor content.
 * Editors should use H2/H3 for the body hierarchy.
 */
function alipasandi_enforce_single_content_h1( $content ) {
	if ( is_admin() || ! is_singular() || false === stripos( $content, '<h1' ) ) {
		return $content;
	}
	return preg_replace( '/<(\/?)h1(\b[^>]*)>/i', '<$1h2$2>', $content );
}
add_filter( 'the_content', 'alipasandi_enforce_single_content_h1', 20 );

/** Build a plain-text description for metadata. */
function alipasandi_meta_description() {
	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$custom  = get_post_meta( $post_id, '_alipasandi_seo_description', true );
		if ( $custom ) {
			return $custom;
		}

		$service_key = alipasandi_current_service_key();
		if ( $service_key ) {
			$service = alipasandi_get_service( $service_key );
			return isset( $service['intro'] ) ? $service['intro'] : '';
		}

		$excerpt = get_the_excerpt( $post_id );
		if ( $excerpt ) {
			return wp_strip_all_tags( $excerpt );
		}
	}
	return get_bloginfo( 'description' );
}

/** Resolve a relevant Open Graph image without inventing one. */
function alipasandi_open_graph_image() {
	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$custom  = get_post_meta( $post_id, '_alipasandi_og_image', true );
		if ( $custom ) {
			return $custom;
		}
		if ( has_post_thumbnail( $post_id ) ) {
			return get_the_post_thumbnail_url( $post_id, 'full' );
		}
	}

	$service_key = alipasandi_current_service_key();
	if ( $service_key ) {
		$service = alipasandi_get_service( $service_key );
		if ( ! empty( $service['image'] ) ) {
			return get_template_directory_uri() . '/assets/images/' . $service['image'];
		}
	}

	return get_template_directory_uri() . '/assets/images/doctor-keyvan-alipasandi.jpg';
}

/** Output lightweight meta tags only when an SEO plugin is not taking ownership. */
function alipasandi_output_social_meta() {
	if ( alipasandi_seo_plugin_active() || ! is_singular() ) {
		return;
	}
	$post_id     = get_queried_object_id();
	$title       = get_post_meta( $post_id, '_alipasandi_og_title', true );
	$description = get_post_meta( $post_id, '_alipasandi_og_description', true );
	$title       = $title ? $title : wp_get_document_title();
	$description = $description ? $description : alipasandi_meta_description();
	$canonical   = get_canonical_url( $post_id );
	$image       = alipasandi_open_graph_image();

	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	echo '<meta property="og:locale" content="fa_IR">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $description ) {
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( $canonical ) {
		echo '<meta property="og:url" content="' . esc_url( $canonical ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'alipasandi_output_social_meta', 4 );

/** Return breadcrumb items for visible navigation and BreadcrumbList schema. */
function alipasandi_breadcrumb_items() {
	$items = array(
		array( 'name' => 'صفحه اصلی', 'url' => home_url( '/' ) ),
	);

	if ( is_home() ) {
		$items[] = array( 'name' => 'مقالات آموزشی', 'url' => alipasandi_articles_url() );
		return $items;
	}

	if ( is_singular( 'post' ) ) {
		$items[] = array( 'name' => 'مقالات آموزشی', 'url' => alipasandi_articles_url() );
		$items[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
		return $items;
	}

	if ( is_page() ) {
		$post_id   = get_queried_object_id();
		$ancestors = array_reverse( get_post_ancestors( $post_id ) );
		foreach ( $ancestors as $ancestor_id ) {
			$items[] = array( 'name' => get_the_title( $ancestor_id ), 'url' => get_permalink( $ancestor_id ) );
		}
		$items[] = array( 'name' => get_the_title( $post_id ), 'url' => get_permalink( $post_id ) );
		return $items;
	}

	if ( is_archive() ) {
		$items[] = array( 'name' => wp_strip_all_tags( get_the_archive_title() ), 'url' => '' );
	}

	return $items;
}

/** Render accessible, crawlable breadcrumbs on internal pages. */
function alipasandi_breadcrumbs() {
	$items = alipasandi_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'alipasandi-clinic' ); ?>">
		<ol class="site-container">
			<?php foreach ( $items as $index => $item ) : $current = $index === count( $items ) - 1; ?>
				<li>
					<?php if ( ! $current && ! empty( $item['url'] ) ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
					<?php else : ?>
						<span<?php echo $current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item['name'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/** Build truthful schema from existing clinic and page data only. */
function alipasandi_output_schema() {
	if ( alipasandi_seo_plugin_active() ) {
		return;
	}

	$site_url  = home_url( '/' );
	$clinic_id = trailingslashit( $site_url ) . '#clinic';
	$graph     = array(
		array(
			'@type'     => 'Dentist',
			'@id'       => $clinic_id,
			'name'      => 'کلینیک دندانپزشکی دکتر کیوان علی‌پسندی',
			'url'       => $site_url,
			'telephone' => alipasandi_phone_href(),
			'image'     => alipasandi_open_graph_image(),
			'hasMap'    => alipasandi_clinic_option( 'clinic_maps' ),
			'address'   => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => alipasandi_clinic_option( 'clinic_street' ) ?: 'ستارخان، امیراد ۱، طبقه ۵',
				'addressLocality' => alipasandi_clinic_option( 'clinic_city' ) ?: 'نوشهر',
				'addressRegion'   => 'مازندران',
				'addressCountry'  => 'IR',
			),
		),
	);

	$items = alipasandi_breadcrumb_items();
	if ( count( $items ) > 1 ) {
		$list = array();
		foreach ( $items as $index => $item ) {
			$entry = array(
				'@type'    => 'ListItem',
				'position' => $index + 1,
				'name'     => $item['name'],
			);
			if ( ! empty( $item['url'] ) ) {
				$entry['item'] = $item['url'];
			}
			$list[] = $entry;
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $list,
		);
	}

	$service_key = alipasandi_current_service_key();
	if ( $service_key ) {
		$service = alipasandi_get_service( $service_key );
		$graph[]  = array(
			'@type'       => 'Service',
			'@id'         => trailingslashit( get_permalink() ) . '#service',
			'name'        => get_the_title(),
			'description' => isset( $service['intro'] ) ? $service['intro'] : '',
			'url'         => get_permalink(),
			'provider'    => array( '@id' => $clinic_id ),
		);
	}

	if ( is_singular( 'post' ) ) {
		$post_id   = get_queried_object_id();
		$author_id = (int) get_post_field( 'post_author', $post_id );
		$graph[] = array(
			'@type'         => 'Article',
			'headline'      => get_the_title(),
			'url'           => get_permalink(),
			'datePublished' => get_the_date( DATE_W3C, $post_id ),
			'dateModified'  => get_the_modified_date( DATE_W3C, $post_id ),
			'author'        => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', $author_id ),
			),
			'publisher'     => array( '@id' => $clinic_id ),
			'image'         => alipasandi_open_graph_image(),
		);
	}

	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'alipasandi_output_schema', 30 );

/**
 * Safe extension point for reviewed legacy URL redirects.
 * Add exact path => URL pairs through the alipasandi_redirect_map filter.
 */
function alipasandi_handle_reviewed_redirects() {
	if ( is_admin() ) {
		return;
	}
	$map  = apply_filters( 'alipasandi_redirect_map', array() );
	$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	if ( $path && isset( $map[ $path ] ) ) {
		wp_safe_redirect( $map[ $path ], 301, 'Alipasandi Clinic' );
		exit;
	}
}
add_action( 'template_redirect', 'alipasandi_handle_reviewed_redirects', 1 );
