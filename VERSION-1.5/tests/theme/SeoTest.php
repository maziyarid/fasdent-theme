<?php
/** Unit tests for Theme/inc/seo.php (titles, canonicals, metadata, breadcrumbs, schema). */

final class SeoTest extends Alipasandi_TestCase {
	/** Put the request on a published page and return its ID. */
	private function visit_page( string $slug, array $fields = array() ): int {
		$id = 100;
		WP_Test_State::add_post( $id, 'page', array_merge( array( 'post_name' => $slug, 'post_title' => $slug ), $fields ) );
		WP_Test_State::$query['queried_id'] = $id;
		return $id;
	}

	public function test_no_seo_plugin_is_detected_in_a_bare_install(): void {
		$this->assertFalse( alipasandi_seo_plugin_active() );
	}

	public function test_current_service_key_only_matches_service_pages(): void {
		$this->visit_page( 'implant' );
		$this->assertSame( 'implant', alipasandi_current_service_key() );

		$this->visit_page( 'about-us' );
		$this->assertSame( '', alipasandi_current_service_key() );

		WP_Test_State::$query['queried_id'] = 0;
		$this->assertSame( '', alipasandi_current_service_key() );
	}

	public function test_document_title_prefers_the_page_specific_seo_title(): void {
		$id = $this->visit_page( 'about-us' );
		$this->assertSame( 'Fallback', alipasandi_filter_document_title( 'Fallback' ) );

		update_post_meta( $id, '_alipasandi_seo_title', 'ایمپلنت در نوشهر' );
		$this->assertSame( 'ایمپلنت در نوشهر', alipasandi_filter_document_title( 'Fallback' ) );
	}

	public function test_document_title_is_untouched_outside_singular_views(): void {
		update_post_meta( 0, '_alipasandi_seo_title', 'Ignored' );
		$this->assertSame( 'Archive', alipasandi_filter_document_title( 'Archive' ) );
	}

	public function test_canonical_url_override_requires_a_published_post(): void {
		$id   = $this->visit_page( 'about-us' );
		$post = get_post( $id );
		update_post_meta( $id, '_alipasandi_canonical_url', 'https://fasdent.ir/canonical/' );

		$this->assertSame( 'https://fasdent.ir/canonical/', alipasandi_filter_canonical_url( 'https://fasdent.ir/about-us/', $post ) );
		$this->assertSame( 'https://fasdent.ir/about-us/', alipasandi_filter_canonical_url( 'https://fasdent.ir/about-us/', null ) );

		WP_Test_State::$posts[ $id ]['post_status'] = 'draft';
		$this->assertSame(
			'https://fasdent.ir/about-us/',
			alipasandi_filter_canonical_url( 'https://fasdent.ir/about-us/', get_post( $id ) ),
			'Unpublished posts keep the WordPress canonical.'
		);
	}

	public function test_editor_h1_tags_are_downgraded_to_h2_on_the_front_end(): void {
		$this->visit_page( 'about-us' );
		$content = '<h1 class="lead">عنوان</h1><p>متن</p><H1>دوم</H1>';

		$this->assertSame( '<h2 class="lead">عنوان</h2><p>متن</p><h2>دوم</h2>', alipasandi_enforce_single_content_h1( $content ) );
	}

	public function test_content_without_an_h1_and_admin_requests_are_left_alone(): void {
		$this->visit_page( 'about-us' );
		$this->assertSame( '<p>متن</p>', alipasandi_enforce_single_content_h1( '<p>متن</p>' ) );

		WP_Test_State::$query['is_admin'] = true;
		$this->assertSame( '<h1>عنوان</h1>', alipasandi_enforce_single_content_h1( '<h1>عنوان</h1>' ) );
	}

	public function test_meta_description_precedence_custom_then_service_then_excerpt(): void {
		$id = $this->visit_page( 'implant', array( 'post_excerpt' => 'خلاصه <b>صفحه</b>' ) );

		update_post_meta( $id, '_alipasandi_seo_description', 'توضیح دستی' );
		$this->assertSame( 'توضیح دستی', alipasandi_meta_description() );

		delete_post_meta( $id, '_alipasandi_seo_description' );
		update_post_meta( $id, ALIPASANDI_SERVICE_META_KEY, array( 'intro' => 'معرفی ایمپلنت' ) );
		$this->assertSame( 'معرفی ایمپلنت', alipasandi_meta_description() );

		$excerpt_id = $this->visit_page( 'about-us', array( 'post_excerpt' => 'خلاصه <b>صفحه</b>' ) );
		$this->assertSame( $excerpt_id, get_queried_object_id() );
		$this->assertSame( 'خلاصه صفحه', alipasandi_meta_description(), 'Excerpts are used as plain text.' );
	}

	public function test_meta_description_falls_back_to_the_site_description(): void {
		$this->assertSame( get_bloginfo( 'description' ), alipasandi_meta_description() );
	}

	public function test_open_graph_image_precedence(): void {
		$id = $this->visit_page( 'implant', array( 'thumbnail_url' => 'https://fasdent.ir/thumb.jpg' ) );

		update_post_meta( $id, '_alipasandi_og_image', 'https://fasdent.ir/custom.jpg' );
		$this->assertSame( 'https://fasdent.ir/custom.jpg', alipasandi_open_graph_image() );

		delete_post_meta( $id, '_alipasandi_og_image' );
		$this->assertSame( 'https://fasdent.ir/thumb.jpg', alipasandi_open_graph_image() );

		WP_Test_State::$posts[ $id ]['thumbnail_url'] = '';
		$this->assertSame(
			get_template_directory_uri() . '/assets/images/' . alipasandi_get_service( 'implant' )['image'],
			alipasandi_open_graph_image(),
			'Service pages fall back to their own hero image.'
		);

		WP_Test_State::$query['queried_id'] = 0;
		$this->assertSame( get_template_directory_uri() . '/assets/images/doctor-keyvan-alipasandi.jpg', alipasandi_open_graph_image() );
	}

	public function test_social_meta_is_emitted_for_published_singular_views(): void {
		$id = $this->visit_page( 'about-us' );
		update_post_meta( $id, '_alipasandi_og_title', 'عنوان اشتراک' );
		update_post_meta( $id, '_alipasandi_og_description', 'توضیح اشتراک' );

		ob_start();
		alipasandi_output_social_meta();
		$head = ob_get_clean();

		$this->assertStringContainsString( '<meta name="description" content="توضیح اشتراک">', $head );
		$this->assertStringContainsString( '<meta property="og:title" content="عنوان اشتراک">', $head );
		$this->assertStringContainsString( '<meta property="og:type" content="website">', $head );
		$this->assertStringContainsString( '<meta property="og:url" content="https://fasdent.ir/about-us/">', $head );
		$this->assertStringContainsString( '<meta property="og:locale" content="fa_IR">', $head );
		$this->assertStringContainsString( 'og:image', $head );
	}

	public function test_social_meta_marks_posts_as_articles_and_defaults_the_title(): void {
		WP_Test_State::add_post( 200, 'post', array( 'post_name' => 'guide', 'post_title' => 'راهنما' ) );
		WP_Test_State::$query['queried_id'] = 200;

		ob_start();
		alipasandi_output_social_meta();
		$head = ob_get_clean();

		$this->assertStringContainsString( '<meta property="og:type" content="article">', $head );
		$this->assertStringContainsString( '<meta property="og:title" content="راهنما – Fasdent">', $head );
	}

	public function test_social_meta_is_suppressed_for_drafts_and_non_singular_views(): void {
		$id = $this->visit_page( 'about-us' );
		WP_Test_State::$posts[ $id ]['post_status'] = 'draft';

		ob_start();
		alipasandi_output_social_meta();
		$this->assertSame( '', ob_get_clean(), 'Drafts must not emit social metadata.' );

		WP_Test_State::$query['queried_id'] = 0;
		ob_start();
		alipasandi_output_social_meta();
		$this->assertSame( '', ob_get_clean() );
	}

	public function test_breadcrumbs_include_ancestors_for_nested_pages(): void {
		WP_Test_State::add_post( 300, 'page', array( 'post_name' => 'services', 'post_title' => 'خدمات' ) );
		WP_Test_State::add_post( 301, 'page', array( 'post_name' => 'implant', 'post_title' => 'ایمپلنت', 'post_parent' => 300 ) );
		WP_Test_State::$query['queried_id'] = 301;

		$items = alipasandi_breadcrumb_items();

		$this->assertSame( array( 'صفحه اصلی', 'خدمات', 'ایمپلنت' ), array_column( $items, 'name' ) );
		$this->assertSame( 'https://fasdent.ir/', $items[0]['url'] );
		$this->assertSame( 'https://fasdent.ir/implant/', $items[2]['url'] );
	}

	public function test_breadcrumbs_for_posts_and_archives(): void {
		WP_Test_State::add_post( 310, 'post', array( 'post_name' => 'guide', 'post_title' => 'راهنما' ) );
		WP_Test_State::$query['queried_id'] = 310;
		$this->assertSame( array( 'صفحه اصلی', 'مقالات آموزشی', 'راهنما' ), array_column( alipasandi_breadcrumb_items(), 'name' ) );

		WP_Test_State::$query['queried_id']    = 0;
		WP_Test_State::$query['archive_title'] = 'آرشیو <span>۱۴۰۴</span>';
		$archive = alipasandi_breadcrumb_items();
		$this->assertSame( array( 'صفحه اصلی', 'آرشیو ۱۴۰۴' ), array_column( $archive, 'name' ) );
		$this->assertSame( '', $archive[1]['url'], 'Archive crumbs are not linked.' );
	}

	public function test_breadcrumb_markup_marks_the_last_crumb_as_current(): void {
		$this->visit_page( 'about-us', array( 'post_title' => 'درباره ما' ) );

		ob_start();
		alipasandi_breadcrumbs();
		$html = ob_get_clean();

		$this->assertStringContainsString( '<nav class="breadcrumbs"', $html );
		$this->assertStringContainsString( '<a href="https://fasdent.ir/">صفحه اصلی</a>', $html );
		$this->assertStringContainsString( '<span aria-current="page">درباره ما</span>', $html );
	}

	public function test_breadcrumb_markup_is_skipped_on_the_front_page(): void {
		ob_start();
		alipasandi_breadcrumbs();
		$this->assertSame( '', ob_get_clean(), 'A single home crumb renders nothing.' );
	}

	public function test_schema_uses_clinic_settings_and_omits_unknown_values(): void {
		WP_Test_State::$options['alipasandi_clinic_city']  = 'نوشهر';
		WP_Test_State::$options['alipasandi_clinic_phone'] = '۰۱۱۵۲۳۴۵۶۷';
		WP_Test_State::$options['alipasandi_clinic_geo_lat'] = 'not-a-number';

		ob_start();
		alipasandi_output_schema();
		$json = ob_get_clean();

		$this->assertStringStartsWith( '<script type="application/ld+json">', $json );
		$graph = json_decode( strip_tags( $json ), true )['@graph'];

		$this->assertSame( 'Dentist', $graph[0]['@type'] );
		$this->assertSame( 'https://fasdent.ir/#clinic', $graph[0]['@id'] );
		$this->assertSame( 'نوشهر', $graph[0]['address']['addressLocality'] );
		$this->assertArrayNotHasKey( 'geo', $graph[0], 'Coordinates are never guessed from partial data.' );
		$this->assertArrayNotHasKey( 'postalCode', $graph[0]['address'] );
	}

	public function test_schema_includes_geo_coordinates_and_a_service_node(): void {
		WP_Test_State::$options['alipasandi_clinic_geo_lat']     = '36.6486';
		WP_Test_State::$options['alipasandi_clinic_geo_lng']     = '51.4967';
		WP_Test_State::$options['alipasandi_clinic_postal_code'] = '4651234567';
		$this->visit_page( 'implant', array( 'post_title' => 'ایمپلنت تک‌دندان' ) );

		ob_start();
		alipasandi_output_schema();
		$graph = json_decode( strip_tags( ob_get_clean() ), true )['@graph'];

		$this->assertSame( 36.6486, $graph[0]['geo']['latitude'] );
		$this->assertSame( '4651234567', $graph[0]['address']['postalCode'] );

		$types = array_column( $graph, '@type' );
		$this->assertContains( 'BreadcrumbList', $types );
		$this->assertContains( 'Service', $types );

		$service = $graph[ array_search( 'Service', $types, true ) ];
		$this->assertSame( 'https://fasdent.ir/implant/#service', $service['@id'] );
		$this->assertSame( 'ایمپلنت تک‌دندان', $service['name'] );
		$this->assertSame( array( '@id' => 'https://fasdent.ir/#clinic' ), $service['provider'] );
	}

	public function test_schema_adds_an_article_node_for_posts(): void {
		WP_Test_State::add_post(
			400,
			'post',
			array(
				'post_name'     => 'guide',
				'post_title'    => 'راهنما',
				'post_date'     => '2026-01-02 10:00:00',
				'post_modified' => '2026-02-03 10:00:00',
			)
		);
		WP_Test_State::$query['queried_id'] = 400;

		ob_start();
		alipasandi_output_schema();
		$graph = json_decode( strip_tags( ob_get_clean() ), true )['@graph'];

		$types   = array_column( $graph, '@type' );
		$article = $graph[ array_search( 'Article', $types, true ) ];

		$this->assertSame( 'راهنما', $article['headline'] );
		$this->assertStringStartsWith( '2026-01-02', $article['datePublished'] );
		$this->assertStringStartsWith( '2026-02-03', $article['dateModified'] );
		$this->assertSame( array( '@id' => 'https://fasdent.ir/#clinic' ), $article['publisher'] );
	}

	public function test_seo_fields_are_saved_sanitized_and_cleared_when_emptied(): void {
		$id = $this->visit_page( 'about-us' );
		$_POST = array(
			'alipasandi_seo_nonce'        => wp_create_nonce( 'alipasandi_save_seo' ),
			'_alipasandi_seo_title'       => "  عنوان <b>سئو</b>\n",
			'_alipasandi_seo_description' => "خط اول\nخط دوم<script>x</script>",
			'_alipasandi_canonical_url'   => 'javascript:alert(1)',
			'_alipasandi_og_image'        => 'https://fasdent.ir/og.jpg',
		);

		alipasandi_save_seo_fields( $id );

		$this->assertSame( 'عنوان سئو', get_post_meta( $id, '_alipasandi_seo_title', true ) );
		$this->assertStringNotContainsString( '<script>', get_post_meta( $id, '_alipasandi_seo_description', true ) );
		$this->assertSame( 'https://fasdent.ir/og.jpg', get_post_meta( $id, '_alipasandi_og_image', true ) );
		$this->assertFalse( metadata_exists( 'post', $id, '_alipasandi_canonical_url' ), 'Unsafe URL schemes are dropped, not stored.' );

		$_POST['_alipasandi_seo_title'] = '   ';
		alipasandi_save_seo_fields( $id );
		$this->assertFalse( metadata_exists( 'post', $id, '_alipasandi_seo_title' ), 'Blank values delete the meta row.' );
	}

	public function test_seo_fields_are_not_saved_without_a_valid_nonce(): void {
		$id    = $this->visit_page( 'about-us' );
		$_POST = array(
			'alipasandi_seo_nonce'  => 'forged',
			'_alipasandi_seo_title' => 'عنوان',
		);

		alipasandi_save_seo_fields( $id );

		$this->assertFalse( metadata_exists( 'post', $id, '_alipasandi_seo_title' ) );
	}

	public function test_meta_box_renders_current_values_with_a_nonce(): void {
		$id = $this->visit_page( 'about-us' );
		update_post_meta( $id, '_alipasandi_seo_description', 'توضیح "نقل‌قول"' );

		ob_start();
		alipasandi_render_seo_meta_box( get_post( $id ) );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'name="alipasandi_seo_nonce"', $html );
		$this->assertStringContainsString( 'name="_alipasandi_seo_title"', $html );
		$this->assertStringContainsString( 'type="url"', $html );
		$this->assertStringContainsString( 'توضیح &quot;نقل‌قول&quot;', $html );
	}
}
