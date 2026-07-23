<?php
/**
 * Fasdent Demo — Theme Options / Customizer Mods
 *
 * Real clinic data for Dr. Keyvan Alipasandi / Fasdent Clinic.
 * Keys must match those registered in inc/customizer.php and floating-chat.
 */
if ( ! defined( 'FASDENT_DEMO_IMPORT' ) ) {
	exit;
}

// Contact & Identity — real data
set_theme_mod( 'fasdent_clinic_name', 'کلینیک دندانپزشکی فس‌دنت' );
set_theme_mod( 'fasdent_doctor_name', 'دکتر کیوان علی‌پسندی' );
set_theme_mod( 'fasdent_phone', '09201441469' );
set_theme_mod( 'fasdent_phone_intl', '+989201441469' );
set_theme_mod( 'fasdent_address', 'تهران' ); // Update with exact address when available
set_theme_mod( 'fasdent_email', 'Dr.keyvan.alipasandii@gmail.com' );
set_theme_mod( 'fasdent_hours', 'از ساعت ۱۱ صبح الی ۱۹ شب' );

// Geo (Schema / map) — keep previous or update later
set_theme_mod( 'fasdent_geo_lat', '35.7219' );
set_theme_mod( 'fasdent_geo_lng', '51.3347' );

// Social Media — real Instagram handles
set_theme_mod( 'fasdent_instagram', 'https://instagram.com/Dr.keyvan_alipasandi' );
set_theme_mod( 'fasdent_whatsapp', 'https://wa.me/989201441469' );
set_theme_mod( 'fasdent_telegram', '' ); // Add if available

// Homepage stats — adjusted to real experience (>10 years)
set_theme_mod( 'fasdent_stat_patients', '5000+' );
set_theme_mod( 'fasdent_stat_years', '۱۰+' );
set_theme_mod( 'fasdent_stat_implants', '2000+' );
set_theme_mod( 'fasdent_stat_rating', '4.9' );

// Emergency bar
set_theme_mod( 'fasdent_emergency_text', 'اورژانس دندانپزشکی — تماس فوری:' );
set_theme_mod( 'fasdent_emergency_phone', '09201441469' );

// Floating chat defaults (for native widget + Chaty compatibility)
set_theme_mod( 'fasdent_chat_enabled', true );
set_theme_mod( 'fasdent_chat_position', 'right' );
set_theme_mod( 'fasdent_chat_label', 'ارتباط سریع' );
set_theme_mod( 'fasdent_chat_title', 'چطور می‌توانیم کمک کنیم؟' );
set_theme_mod( 'fasdent_chat_intro', 'یکی از روش‌های زیر را انتخاب کنید یا در واتس‌اپ پیام دهید.' );
set_theme_mod( 'fasdent_chat_whatsapp', '989201441469' );
set_theme_mod( 'fasdent_chat_whatsapp_message', 'سلام، برای دریافت مشاوره از کلینیک فس‌دنت پیام می‌دهم.' );
set_theme_mod( 'fasdent_chat_phone', '+989201441469' );
set_theme_mod( 'fasdent_chat_telegram', '' );
set_theme_mod( 'fasdent_chat_email', 'Dr.keyvan.alipasandii@gmail.com' );

// Booking
set_theme_mod( 'fasdent_booking_url', '/appointment/' );

// Core WordPress options
update_option( 'blogname', 'کلینیک دندانپزشکی فس‌دنت | دکتر کیوان علی‌پسندی' );
update_option( 'blogdescription', 'ایمپلنتولوژی تخصصی — بیش از ۱۰ سال سابقه — برندهای Bego، Megagen، Straumann، Sic، 3zahn' );
update_option( 'timezone_string', 'Asia/Tehran' );
update_option( 'date_format', 'Y/m/d' );
update_option( 'time_format', 'H:i' );
update_option( 'permalink_structure', '/%category%/%postname%/' );
