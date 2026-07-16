<?php
/**
 * Fasdent Demo — Theme Options / Customizer Mods
 */
if ( ! defined( 'FASDENT_DEMO_IMPORT' ) ) {
	exit;
}

// Contact & Identity
set_theme_mod( 'fasdent_phone', '09201441469' );
set_theme_mod( 'fasdent_phone_intl', '+989201441469' );
set_theme_mod( 'fasdent_address', 'تهران، خیابان ولیعصر، پلاک ۱۲۳، کلینیک دندانپزشکی فس‌دنت' );
set_theme_mod( 'fasdent_email', 'info@fasdent.ir' );
set_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' );

// Working Hours
set_theme_mod( 'fasdent_hours_sat', '۹:۰۰ – ۲۱:۰۰' );
set_theme_mod( 'fasdent_hours_sun', '۹:۰۰ – ۲۱:۰۰' );
set_theme_mod( 'fasdent_hours_mon', '۹:۰۰ – ۲۱:۰۰' );
set_theme_mod( 'fasdent_hours_tue', '۹:۰۰ – ۲۱:۰۰' );
set_theme_mod( 'fasdent_hours_wed', '۹:۰۰ – ۲۱:۰۰' );
set_theme_mod( 'fasdent_hours_thu', '۱۰:۰۰ – ۱۸:۰۰' );
set_theme_mod( 'fasdent_hours_fri', 'تعطیل' );

// Social Media
set_theme_mod( 'fasdent_instagram', 'https://instagram.com/fasdent' );
set_theme_mod( 'fasdent_whatsapp', 'https://wa.me/989201441469' );
set_theme_mod( 'fasdent_telegram', 'https://t.me/fasdent' );

// SEO / Schema
set_theme_mod( 'fasdent_latitude', '35.7219' );
set_theme_mod( 'fasdent_longitude', '51.3347' );
set_theme_mod( 'fasdent_schema_type', 'Dentist' );

// Core WordPress options
update_option( 'blogname', 'کلینیک دندانپزشکی فس‌دنت' );
update_option( 'blogdescription', 'دندانپزشکی پیشرفته — لبخند زیباتر، زندگی بهتر' );
update_option( 'timezone_string', 'Asia/Tehran' );
update_option( 'date_format', 'Y/m/d' );
update_option( 'time_format', 'H:i' );
update_option( 'permalink_structure', '/%postname%/' );
