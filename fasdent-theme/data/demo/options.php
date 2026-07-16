<?php
/**
 * Fasdent Demo — Theme Options / Customizer Mods
 *
 * Keys must match those registered in inc/customizer.php.
 */
if ( ! defined( 'FASDENT_DEMO_IMPORT' ) ) {
	exit;
}

// Contact & Identity
set_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' );
set_theme_mod( 'fasdent_doctor_name', 'دکتر کیوان علی‌پسندی' );
set_theme_mod( 'fasdent_phone', '09201441469' );
set_theme_mod( 'fasdent_phone_intl', '+989201441469' );
set_theme_mod( 'fasdent_address', 'تهران، خیابان ولیعصر، پلاک ۱۲۳، کلینیک دندانپزشکی فس‌دنت' );
set_theme_mod( 'fasdent_email', 'info@fasdent.ir' );
set_theme_mod( 'fasdent_hours', 'شنبه تا پنجشنبه ۹ تا ۲۱' );

// Geo (Schema / map)
set_theme_mod( 'fasdent_geo_lat', '35.7219' );
set_theme_mod( 'fasdent_geo_lng', '51.3347' );

// Social Media
set_theme_mod( 'fasdent_instagram', 'https://instagram.com/fasdent' );
set_theme_mod( 'fasdent_whatsapp', 'https://wa.me/989201441469' );
set_theme_mod( 'fasdent_telegram', 'https://t.me/fasdent' );

// Homepage stats
set_theme_mod( 'fasdent_stat_patients', '12000' );
set_theme_mod( 'fasdent_stat_years', '15' );
set_theme_mod( 'fasdent_stat_implants', '3500' );
set_theme_mod( 'fasdent_stat_rating', '4.9' );

// Emergency bar
set_theme_mod( 'fasdent_emergency_text', 'اورژانس دندانپزشکی ۲۴ ساعته — تماس:' );
set_theme_mod( 'fasdent_emergency_phone', '09201441469' );

// Core WordPress options
update_option( 'blogname', 'کلینیک دندانپزشکی فس‌دنت' );
update_option( 'blogdescription', 'دندانپزشکی پیشرفته — لبخند زیباتر، زندگی بهتر' );
update_option( 'timezone_string', 'Asia/Tehran' );
update_option( 'date_format', 'Y/m/d' );
update_option( 'time_format', 'H:i' );
update_option( 'permalink_structure', '/%category%/%postname%/' );
