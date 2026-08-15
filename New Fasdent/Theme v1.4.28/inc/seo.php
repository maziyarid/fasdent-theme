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
	$active = defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' );
	/** Test-only/compatibility seam; production defaults to real plugin constants. */
	return (bool) apply_filters( 'alipasandi_seo_plugin_active', $active );
}

/**
 * Return the current DESIGN-LOCKED primary service key. Registry extensibility
 * alone must not silently expand primary Theme UI/SEO surfaces.
 */
function alipasandi_current_service_key() {
	$keys = function_exists( 'alipasandi_primary_service_keys' ) ? alipasandi_primary_service_keys() : array( 'implant', 'crown', 'surgery', 'general' );
	foreach ( $keys as $key ) {
		if ( is_page( $key ) ) {
			return $key;
		}
	}
	return '';
}

/**
 * SEO metadata is owned by Rank Math. The theme does not expose or save SEO
 * admin fields. Legacy _alipasandi_* values remain read-only inputs to the
 * emergency fallback only when no SEO plugin is active.
 */

/** Use a page-specific SEO title when supplied. */
function alipasandi_filter_document_title( $title ) {
	if ( alipasandi_seo_plugin_active() ) {
		return $title;
	}
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
	if ( alipasandi_seo_plugin_active() ) {
		return $canonical;
	}
	if ( ! $post instanceof WP_Post || 'publish' !== get_post_status( $post ) ) {
		return $canonical;
	}
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
	$post         = $post_id ? get_post( $post_id ) : null;
	if ( ! $post instanceof WP_Post || 'publish' !== get_post_status( $post ) ) {
		return;
	}
	$title       = get_post_meta( $post_id, '_alipasandi_og_title', true );
	$description = get_post_meta( $post_id, '_alipasandi_og_description', true );
	$title       = $title ? $title : wp_get_document_title();
	$description = $description ? $description : alipasandi_meta_description();
	$canonical   = function_exists( 'wp_get_canonical_url' ) ? wp_get_canonical_url( $post_id ) : '';
	if ( ! $canonical ) {
		$permalink = get_permalink( $post_id );
		$canonical = is_string( $permalink ) ? $permalink : '';
	}
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
	$clinic = array(
		'@type' => 'Dentist',
		'@id'   => $clinic_id,
		'name'  => (string) alipasandi_clinic_option( 'clinic_business_name' ),
		'url'   => $site_url,
		'image' => alipasandi_open_graph_image(),
	);
	$phone = trim( (string) alipasandi_phone_href() );
	if ( '' !== $phone ) {
		$clinic['telephone'] = $phone;
	}
	$map = trim( (string) alipasandi_clinic_option( 'clinic_maps' ) );
	if ( '' !== $map ) {
		$clinic['hasMap'] = $map;
	}
	$address = array( '@type' => 'PostalAddress' );
	$address_fields = array(
		'streetAddress'   => 'clinic_street',
		'addressLocality' => 'clinic_city',
		'addressRegion'   => 'clinic_region',
		'addressCountry'  => 'clinic_country',
	);
	foreach ( $address_fields as $schema_key => $option_key ) {
		$value = trim( (string) alipasandi_clinic_option( $option_key ) );
		if ( '' !== $value ) { $address[ $schema_key ] = $value; }
	}
	if ( count( $address ) > 1 ) {
		$clinic['address'] = $address;
	}
	$graph = array( $clinic );

	// Optional postalCode — only when an official value is provided (never fabricated).
	$postal = trim( (string) alipasandi_clinic_option( 'clinic_postal_code' ) );
	if ( '' !== $postal ) {
		if ( empty( $graph[0]['address'] ) ) { $graph[0]['address'] = array( '@type' => 'PostalAddress' ); }
		$graph[0]['address']['postalCode'] = $postal;
	}

	// Optional GeoCoordinates — only when both real lat/lng are provided (never guessed).
	$lat = trim( (string) alipasandi_clinic_option( 'clinic_geo_lat' ) );
	$lng = trim( (string) alipasandi_clinic_option( 'clinic_geo_lng' ) );
	if ( '' !== $lat && '' !== $lng && is_numeric( $lat ) && is_numeric( $lng ) && (float) $lat >= -90 && (float) $lat <= 90 && (float) $lng >= -180 && (float) $lng <= 180 ) {
		$graph[0]['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) $lat,
			'longitude' => (float) $lng,
		);
	}

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
 * Emergency indexability policy used ONLY when the production SEO owner is
 * absent. This keeps critical utility/failure contexts predictable without
 * competing with Rank Math during normal operation.
 */
function alipasandi_emergency_robots_policy( $robots ) {
	if ( alipasandi_seo_plugin_active() ) {
		return $robots;
	}
	if ( is_404() ) {
		$robots['noindex'] = true;
		$robots['nofollow'] = true;
		unset( $robots['index'], $robots['follow'] );
		return $robots;
	}
	if ( is_page( 'appointments' ) || is_search() || is_home() || is_archive() || is_paged() ) {
		$robots['noindex'] = true;
		$robots['follow'] = true;
		unset( $robots['index'], $robots['nofollow'] );
	}
	return $robots;
}
add_filter( 'wp_robots', 'alipasandi_emergency_robots_policy', 90 );

/**
 * Failure-mode sitemap guard. Rank Math is the Production sitemap owner; if it
 * is unavailable, disable WordPress Core sitemaps instead of exposing URLs
 * that conflict with the launch Indexability Matrix.
 */
function alipasandi_emergency_core_sitemaps_enabled( $enabled ) {
	return alipasandi_seo_plugin_active() ? $enabled : false;
}
add_filter( 'wp_sitemaps_enabled', 'alipasandi_emergency_core_sitemaps_enabled', 90 );

/** Feed indexability contract: functional feed, explicitly noindex. */
function alipasandi_feed_robots_header_value() {
	return 'noindex, follow';
}

function alipasandi_feed_robots_header() {
	if ( is_feed() && ! headers_sent() ) {
		header( 'X-Robots-Tag: ' . alipasandi_feed_robots_header_value(), true );
	}
}
add_action( 'template_redirect', 'alipasandi_feed_robots_header', 0 );

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
