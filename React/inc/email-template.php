<?php
/**
 * HTML email template — Fasdent brand
 * Uses Vazirmatn (Google Fonts CDN) for Persian text.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fasdent_email_html( string $title, string $content ): string {
	$clinic = esc_html( get_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' ) );
	$phone  = esc_html( function_exists( 'fasdent_phone' ) ? fasdent_phone() : '09201441469' );
	$site   = esc_url( home_url( '/' ) );
	$year   = esc_html( wp_date( 'Y' ) );

	return '<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>' . esc_html( $title ) . '</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background:#f7fafc;font-family:\'Vazirmatn\',Tahoma,Arial,sans-serif;direction:rtl;text-align:right;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f7fafc;padding:24px 12px;">
<tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 12px 32px rgba(7,31,63,0.1);">
  <tr>
    <td style="background:linear-gradient(135deg,#071f3f,#0e55b1);padding:28px 24px;text-align:center;">
      <div style="font-size:22px;font-weight:700;color:#ffffff;letter-spacing:-0.02em;">' . $clinic . '</div>
      <div style="font-size:13px;color:#09d4d6;margin-top:6px;">fasdent.ir</div>
    </td>
  </tr>
  <tr>
    <td style="padding:28px 24px;">
      <h1 style="margin:0 0 16px;font-size:18px;font-weight:700;color:#071f3f;">' . esc_html( $title ) . '</h1>
      <div style="font-size:14px;line-height:1.85;color:#334155;">' . $content . '</div>
    </td>
  </tr>
  <tr>
    <td style="background:#f1f5f9;padding:18px 24px;border-top:1px solid #e2e8f0;">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="font-size:12px;color:#64748b;line-height:1.7;">
            تلفن: <a href="tel:' . esc_attr( function_exists( 'fasdent_phone_link' ) ? fasdent_phone_link() : '+989201441469' ) . '" style="color:#0e55b1;text-decoration:none;direction:ltr;unicode-bidi:embed;">' . $phone . '</a><br>
            <a href="' . $site . '" style="color:#0e55b1;text-decoration:none;">fasdent.ir</a>
          </td>
        </tr>
      </table>
      <p style="margin:12px 0 0;font-size:11px;color:#94a3b8;text-align:center;">&copy; ' . $year . ' ' . $clinic . ' — تمام حقوق محفوظ است.</p>
    </td>
  </tr>
</table>
</td></tr>
</table>
</body>
</html>';
}

function fasdent_mail_html( string $to, string $subject, string $title, string $body_html ): bool {
	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . get_theme_mod( 'fasdent_clinic_name', 'فس‌دنت' ) . ' <' . ( get_theme_mod( 'fasdent_email' ) ?: get_option( 'admin_email' ) ) . '>',
	);
	$html = fasdent_email_html( $title, $body_html );
	return (bool) wp_mail( $to, $subject, $html, $headers );
}
