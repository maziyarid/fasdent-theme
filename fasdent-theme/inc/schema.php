<?php
/**
 * Schema Markup (JSON-LD) — Fasdent
 * Dentist/LocalBusiness (سراسری) | MedicalProcedure | FAQPage | Physician |
 * Review/AggregateRating | OpeningHoursSpecification (اورژانس)
 * BreadcrumbList در inc/breadcrumb.php تولید می‌شود.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * چاپ امن JSON-LD.
 *
 * @param array $schema داده اسکیما.
 */
function fasdent_print_schema( array $schema ): void {
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * اسکیمای سراسری Dentist (LocalBusiness).
 */
function fasdent_schema_dentist(): void {
	$schema = array(
		'@context'  => 'https://schema.org',
		'@type'     => 'Dentist',
		'@id'       => home_url( '/#dentist' ),
		'name'      => get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' ),
		'url'       => home_url( '/' ),
		'telephone' => fasdent_phone_link(),
		'image'     => get_custom_logo() ? wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) : FASDENT_URI . '/assets/images/logo.png',
		'address'   => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => get_theme_mod( 'fasdent_address', 'تهران' ),
			'addressLocality' => 'تهران',
			'addressCountry'  => 'IR',
		),
		'geo'       => array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => get_theme_mod( 'fasdent_geo_lat', '35.7219' ),
			'longitude' => get_theme_mod( 'fasdent_geo_lng', '51.3347' ),
		),
		'openingHoursSpecification' => array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday' ),
				'opens'     => '09:00',
				'closes'    => '21:00',
			),
		),
		'priceRange'  => '$$',
		'founder'     => array(
			'@type' => 'Person',
			'name'  => get_theme_mod( 'fasdent_doctor_name', 'دکتر کیوان علی‌پسندی' ),
		),
		'sameAs'      => array_values( array_filter( array(
			get_theme_mod( 'fasdent_instagram', '' ),
			get_theme_mod( 'fasdent_telegram', '' ),
		) ) ),
	);
	fasdent_print_schema( $schema );
}

/**
 * اسکیمای MedicalProcedure برای صفحات خدمت.
 */
function fasdent_schema_medical_procedure(): void {
	if ( ! is_singular( 'service' ) ) {
		return;
	}
	$post_id = get_the_ID();
	$steps   = fasdent_get_service_steps( $post_id );

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'MedicalProcedure',
		'name'        => get_the_title(),
		'url'         => get_permalink(),
		'description' => wp_strip_all_tags( get_the_excerpt() ),
		'procedureType' => 'http://schema.org/NoninvasiveProcedure',
		'performer'   => array(
			'@type' => 'Dentist',
			'@id'   => home_url( '/#dentist' ),
		),
	);
	if ( $steps ) {
		$schema['howPerformed'] = implode( ' ', array_map(
			static fn( $s ) => ( $s['title'] ?? '' ) . ': ' . ( $s['description'] ?? '' ),
			$steps
		) );
	}
	fasdent_print_schema( $schema );

	// HowTo برای مراحل انجام.
	if ( $steps ) {
		$howto_steps = array();
		foreach ( $steps as $i => $step ) {
			$howto_steps[] = array(
				'@type'    => 'HowToStep',
				'position' => $i + 1,
				'name'     => $step['title'] ?? '',
				'text'     => $step['description'] ?? '',
			);
		}
		fasdent_print_schema( array(
			'@context' => 'https://schema.org',
			'@type'    => 'HowTo',
			'name'     => 'مراحل انجام ' . get_the_title(),
			'step'     => $howto_steps,
		) );
	}
}

/**
 * اسکیمای FAQPage.
 */
function fasdent_schema_faq(): void {
	$faqs = array();

	if ( is_singular( 'service' ) ) {
		$faqs = fasdent_get_service_faqs();
	} elseif ( is_page( 'faq' ) ) {
		$faq_posts = get_posts( array( 'post_type' => 'faq', 'numberposts' => 50, 'post_status' => 'publish' ) );
		foreach ( $faq_posts as $fp ) {
			$faqs[] = array(
				'question' => $fp->post_title,
				'answer'   => wp_strip_all_tags( $fp->post_content ),
			);
		}
	} elseif ( is_tax( 'service_category' ) ) {
		$faqs = fasdent_category_faqs( get_queried_object()->slug );
	}

	if ( ! $faqs ) {
		return;
	}

	$entities = array();
	foreach ( $faqs as $faq ) {
		$q = $faq['question'] ?? '';
		$a = $faq['answer'] ?? '';
		if ( ! $q || ! $a ) {
			continue;
		}
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $q ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $a ),
			),
		);
	}
	if ( $entities ) {
		fasdent_print_schema( array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $entities,
		) );
	}
}

/**
 * سوالات متداول پیش‌فرض برای صفحات دسته (Pillar).
 * از متای ترم faqs_json خوانده می‌شود.
 *
 * @param string $slug اسلاگ دسته.
 * @return array
 */
function fasdent_category_faqs( string $slug ): array {
	$term = get_term_by( 'slug', $slug, 'service_category' );
	if ( ! $term ) {
		return array();
	}
	$json = get_term_meta( $term->term_id, 'fasdent_faqs_json', true );
	if ( $json ) {
		$decoded = json_decode( $json, true );
		if ( is_array( $decoded ) ) {
			return $decoded;
		}
	}
	return array();
}

/**
 * اسکیمای Physician برای صفحه پزشک.
 */
function fasdent_schema_physician(): void {
	if ( ! is_singular( 'doctor' ) ) {
		return;
	}
	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Physician',
		'name'             => get_the_title(),
		'url'              => get_permalink(),
		'image'            => get_the_post_thumbnail_url( null, 'large' ) ?: '',
		'description'      => wp_strip_all_tags( get_the_excerpt() ),
		'medicalSpecialty' => 'Dentistry',
		'telephone'        => fasdent_phone_link(),
		'worksFor'         => array(
			'@type' => 'Dentist',
			'@id'   => home_url( '/#dentist' ),
		),
		'address'          => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => get_theme_mod( 'fasdent_address', 'تهران' ),
			'addressLocality' => 'تهران',
			'addressCountry'  => 'IR',
		),
	);
	fasdent_print_schema( $schema );
}

/**
 * اسکیمای AggregateRating + Review (صفحات دارای نظرات بیماران).
 */
function fasdent_schema_reviews(): void {
	if ( ! is_front_page() && ! is_singular( 'service' ) && ! is_tax( 'service_category' ) && ! is_singular( 'doctor' ) ) {
		return;
	}
	$testimonials = get_posts( array( 'post_type' => 'testimonial', 'numberposts' => 20, 'post_status' => 'publish' ) );
	if ( ! $testimonials ) {
		return;
	}

	$reviews = array();
	$sum     = 0;
	foreach ( $testimonials as $t ) {
		$rating = (float) ( get_post_meta( $t->ID, 'rating', true ) ?: 5 );
		$sum   += $rating;
		$reviews[] = array(
			'@type'        => 'Review',
			'author'       => array( '@type' => 'Person', 'name' => $t->post_title ),
			'reviewBody'   => wp_strip_all_tags( $t->post_content ),
			'reviewRating' => array(
				'@type'       => 'Rating',
				'ratingValue' => $rating,
				'bestRating'  => 5,
			),
		);
	}
	$avg = round( $sum / count( $testimonials ), 1 );

	fasdent_print_schema( array(
		'@context'        => 'https://schema.org',
		'@type'           => 'Dentist',
		'@id'             => home_url( '/#dentist' ),
		'name'            => get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' ),
		'aggregateRating' => array(
			'@type'       => 'AggregateRating',
			'ratingValue' => $avg,
			'reviewCount' => count( $testimonials ),
			'bestRating'  => 5,
		),
		'review'          => array_slice( $reviews, 0, 5 ),
	) );
}

/**
 * اسکیمای ساعات اورژانس (قالب C).
 */
function fasdent_schema_emergency_hours(): void {
	if ( ! fasdent_is_emergency_context() ) {
		return;
	}
	fasdent_print_schema( array(
		'@context'  => 'https://schema.org',
		'@type'     => 'EmergencyService',
		'name'      => 'اورژانس دندانپزشکی فس‌دنت',
		'telephone' => fasdent_phone_link(),
		'url'       => home_url( '/services/dental-emergency/' ),
		'openingHoursSpecification' => array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
			'opens'     => '00:00',
			'closes'    => '23:59',
		),
	) );
}

/**
 * اسکیمای BlogPosting برای پست‌های بلاگ.
 */
function fasdent_schema_blog_posting(): void {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$author_name = get_the_author_meta( 'display_name' );
	$schema      = array(
		'@context'         => 'https://schema.org',
		'@type'            => array( 'BlogPosting', 'Article' ),
		'headline'         => get_the_title(),
		'url'              => get_permalink(),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'description'      => wp_strip_all_tags( get_the_excerpt() ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => $author_name,
			'url'   => get_author_posts_url( get_the_author_meta( 'ID' ) ),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' ),
			'url'   => home_url( '/' ),
		),
		'isPartOf'         => array( '@id' => home_url( '/#website' ) ),
		'inLanguage'       => 'fa-IR',
	);
	if ( has_post_thumbnail() ) {
		$schema['image'] = get_the_post_thumbnail_url( null, 'large' );
	}
	fasdent_print_schema( $schema );
}

/**
 * اسکیمای WebSite با Sitelinks Searchbox.
 */
function fasdent_schema_website(): void {
	if ( ! is_front_page() ) {
		return;
	}
	fasdent_print_schema( array(
		'@context'        => 'https://schema.org',
		'@type'           => 'WebSite',
		'@id'             => home_url( '/#website' ),
		'url'             => home_url( '/' ),
		'name'            => get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' ),
		'inLanguage'      => 'fa-IR',
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		),
	) );
}

/**
 * اسکیمای MedicalWebPage برای صفحات خدمت.
 */
function fasdent_schema_medical_webpage(): void {
	if ( ! is_singular( 'service' ) ) {
		return;
	}
	fasdent_print_schema( array(
		'@context'          => 'https://schema.org',
		'@type'             => 'MedicalWebPage',
		'url'               => get_permalink(),
		'name'              => get_the_title(),
		'description'       => wp_strip_all_tags( get_the_excerpt() ),
		'datePublished'     => get_the_date( 'c' ),
		'dateModified'      => get_the_modified_date( 'c' ),
		'inLanguage'        => 'fa-IR',
		'medicalAudience'   => array(
			'@type'           => 'MedicalAudience',
			'audienceType'    => 'Patient',
		),
		'reviewedBy'        => array(
			'@type' => 'Dentist',
			'@id'   => home_url( '/#dentist' ),
		),
	) );
}

/**
 * خروجی همه اسکیماها در head.
 */
function fasdent_output_schemas(): void {
	fasdent_schema_dentist();
	fasdent_schema_website();
	fasdent_schema_medical_procedure();
	fasdent_schema_medical_webpage();
	fasdent_schema_faq();
	fasdent_schema_physician();
	fasdent_schema_reviews();
	fasdent_schema_emergency_hours();
	fasdent_schema_blog_posting();
}
add_action( 'wp_head', 'fasdent_output_schemas', 5 );
