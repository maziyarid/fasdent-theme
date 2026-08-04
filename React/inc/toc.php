<?php
/**
 * Table of Contents (ToC) — Fasdent
 * استخراج H2/H3 از محتوا، افزودن ID، ساخت HTML ناوبری
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * استخراج عناوین H2/H3 از محتوا و افزودن ID اگر وجود نداشته باشد.
 *
 * @param string $content محتوا.
 * @return array{id:string,text:string,level:int}[]
 */
function fasdent_toc_extract( string $content ): array {
	$items = array();
	if ( preg_match_all( '/<h([23])(?:[^>]*)>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER ) ) {
		$used = array();
		foreach ( $matches as $m ) {
			$level = (int) $m[1];
			$text  = wp_strip_all_tags( $m[2] );
			// ساخت ID: تبدیل فارسی به اسلاگ ASCII با حفظ کاراکترهای فارسی.
			$id = preg_replace( '/[^\p{L}\p{N}_\-]/u', '-', mb_strtolower( $text ) );
			$id = preg_replace( '/-+/', '-', trim( $id, '-' ) );
			$id = $id ?: 'section-' . count( $items );
			// جلوگیری از تکرار.
			$original_id = $id;
			$suffix      = 2;
			while ( in_array( $id, $used, true ) ) {
				$id = $original_id . '-' . $suffix++;
			}
			$used[]  = $id;
			$items[] = array( 'id' => $id, 'text' => $text, 'level' => $level );
		}
	}
	return $items;
}

/**
 * افزودن ID به عناوین H2/H3 در محتوا.
 *
 * @param string $content محتوا.
 * @return string
 */
function fasdent_toc_add_ids( string $content ): string {
	$items = fasdent_toc_extract( $content );
	if ( ! $items ) {
		return $content;
	}
	$idx = 0;
	$content = preg_replace_callback(
		'/<h([23])((?:[^>]*)?)>(.*?)<\/h\1>/is',
		function ( $m ) use ( &$idx, $items ) {
			if ( ! isset( $items[ $idx ] ) ) {
				return $m[0];
			}
			$id    = $items[ $idx++ ]['id'];
			$attrs = $m[2];
			// اگر از قبل id داشت تغییر نده.
			if ( preg_match( '/\bid=["\']/', $attrs ) ) {
				return $m[0];
			}
			return "<h{$m[1]}{$attrs} id=\"" . esc_attr( $id ) . "\">{$m[3]}</h{$m[1]}>";
		},
		$content
	);
	return $content ?? $content;
}
add_filter( 'the_content', 'fasdent_toc_add_ids', 5 );

/**
 * ساخت HTML بلوک ToC.
 *
 * @param array  $items  آیتم‌های ToC.
 * @param string $title  عنوان ToC.
 * @return string
 */
function fasdent_toc_render( array $items, string $title = 'فهرست مطالب' ): string {
	if ( count( $items ) < 3 ) {
		return '';
	}
	$html  = '<nav class="toc-nav" aria-label="' . esc_attr( $title ) . '">';
	$html .= '<button class="toc-toggle" aria-expanded="true" aria-controls="toc-list">';
	$html .= '<i class="fa-solid fa-list" aria-hidden="true"></i> ' . esc_html( $title ) . ' ';
	$html .= '<i class="fa-solid fa-chevron-down toc-chevron" aria-hidden="true"></i>';
	$html .= '</button>';
	$html .= '<ol id="toc-list" class="toc-list">';
	foreach ( $items as $item ) {
		$class = 'toc-item toc-h' . $item['level'];
		$html .= '<li class="' . esc_attr( $class ) . '">';
		$html .= '<a href="#' . esc_attr( $item['id'] ) . '">' . esc_html( $item['text'] ) . '</a>';
		$html .= '</li>';
	}
	$html .= '</ol>';
	$html .= '<a href="#content" class="toc-back-top">'
		. '<i class="fa-solid fa-arrow-up" aria-hidden="true"></i> '
		. esc_html__( 'بازگشت به بالا', 'fasdent' )
		. '</a>';
	$html .= '</nav>';
	return $html;
}

/**
 * ToC را به ابتدای محتوا (پس از اولین پاراگراف) اضافه می‌کند.
 *
 * @param string $content محتوا.
 * @return string
 */
function fasdent_toc_inject_inline( string $content ): string {
	if ( ! is_singular( array( 'post', 'service' ) ) ) {
		return $content;
	}
	// توابع کمکی هنوز تعریف نشده ← فقط در زمینه اجرای پست.
	$items = fasdent_toc_extract( $content );
	if ( count( $items ) < 3 ) {
		return $content;
	}
	$toc_html = fasdent_toc_render( $items );
	// درج بعد از اولین </p>.
	$pos = strpos( $content, '</p>' );
	if ( $pos !== false ) {
		$content = substr_replace( $content, '</p>' . $toc_html, $pos, 4 );
	} else {
		$content = $toc_html . $content;
	}
	return $content;
}
// فقط در صورتی که sidebar ToC استفاده نشود از inline استفاده می‌کنیم.
// این فیلتر در template-parts/toc-sidebar.php غیرفعال می‌شود.
if ( ! defined( 'FASDENT_TOC_SIDEBAR' ) ) {
	add_filter( 'the_content', 'fasdent_toc_inject_inline', 15 );
}
