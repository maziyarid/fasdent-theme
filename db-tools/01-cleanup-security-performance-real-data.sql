-- ============================================================================
-- Fasdent Database Cleanup + Real Data + Security + Performance
-- Database: fasdenti_rd  |  Table prefix: fd_
-- Date: 2026-07-23
-- Run this AFTER importing the original dump (or on the live DB).
-- Recommended: take a full backup first, then run in phpMyAdmin / Adminer / WP-CLI.
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- ============================================================================
-- 1. SECURITY HARDENING
-- ============================================================================

-- Ensure registration is closed
UPDATE `fd_options` SET `option_value` = '0' WHERE `option_name` = 'users_can_register';

-- Close comments & pings by default (prevent spam)
UPDATE `fd_options` SET `option_value` = 'closed' WHERE `option_name` IN ('default_comment_status', 'default_ping_status');
UPDATE `fd_options` SET `option_value` = '0' WHERE `option_name` IN ('default_pingback_flag', 'comment_moderation');

-- Remove the default "Hello World" style comment (already post-trashed)
DELETE FROM `fd_comments` WHERE `comment_ID` = 1;
DELETE FROM `fd_commentmeta` WHERE `comment_id` = 1;

-- Permanently delete the two fictional/trashed demo doctors (keep only real Dr. Keyvan)
DELETE FROM `fd_posts` WHERE `ID` IN (28, 29) AND `post_type` = 'doctor';
DELETE FROM `fd_postmeta` WHERE `post_id` IN (28, 29);
DELETE FROM `fd_term_relationships` WHERE `object_id` IN (28, 29);

-- Clean any orphaned postmeta for non-existent posts (safety)
DELETE pm FROM `fd_postmeta` pm
LEFT JOIN `fd_posts` p ON p.ID = pm.post_id
WHERE p.ID IS NULL;

-- Remove unused / potentially sensitive default mailserver leftovers
UPDATE `fd_options` SET `option_value` = '' WHERE `option_name` IN ('mailserver_url', 'mailserver_login', 'mailserver_pass');

-- ============================================================================
-- 2. PERFORMANCE CLEANUP
-- ============================================================================

-- Clean completed / failed Action Scheduler rows older than needed
DELETE FROM `fd_actionscheduler_actions`
WHERE `status` IN ('complete', 'failed', 'canceled')
  AND `scheduled_date_gmt` < DATE_SUB(NOW(), INTERVAL 7 DAY);

DELETE FROM `fd_actionscheduler_logs`
WHERE `action_id` NOT IN (SELECT `action_id` FROM `fd_actionscheduler_actions`);

-- Clean Rank Math 404 logs (if any accumulated)
TRUNCATE TABLE `fd_rank_math_404_logs`;

-- Remove expired transients (both timeout and the value itself)
DELETE FROM `fd_options`
WHERE `option_name` LIKE '\_transient\_timeout\_%'
  AND CAST(`option_value` AS UNSIGNED) < UNIX_TIMESTAMP();

DELETE a FROM `fd_options` a
LEFT JOIN `fd_options` b ON b.option_name = CONCAT('_transient_timeout_', SUBSTRING(a.option_name, 12))
WHERE a.option_name LIKE '\_transient\_%'
  AND a.option_name NOT LIKE '\_transient\_timeout\_%'
  AND b.option_id IS NULL;

-- Same for site transients
DELETE FROM `fd_options`
WHERE `option_name` LIKE '\_site\_transient\_timeout\_%'
  AND CAST(`option_value` AS UNSIGNED) < UNIX_TIMESTAMP();

DELETE a FROM `fd_options` a
LEFT JOIN `fd_options` b ON b.option_name = CONCAT('_site_transient_timeout_', SUBSTRING(a.option_name, 17))
WHERE a.option_name LIKE '\_site\_transient\_%'
  AND a.option_name NOT LIKE '\_site\_transient\_timeout\_%'
  AND b.option_id IS NULL;

-- Remove auto-drafts older than 7 days
DELETE FROM `fd_posts`
WHERE `post_status` = 'auto-draft'
  AND `post_date` < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- ============================================================================
-- 3. REAL DATA INJECTION – Doctor (ID 27)
-- ============================================================================

UPDATE `fd_posts` SET
  `post_title`   = 'دکتر کیوان علی‌پسندی',
  `post_name`    = 'dr-keyvan-alipasandi',
  `post_excerpt` = 'دکتری حرفه‌ای (ایمپلنتولوژیست) با بیش از ۱۰ سال سابقه تخصصی. شماره نظام پزشکی ۱۹۱۷۴۰. متخصص ایمپلنت با برندهای معتبر Bego، Megagen، Straumann، Sic و 3zahn.',
  `post_content` = '<p>دکتر کیوان علی‌پسندی، دکتری حرفه‌ای دندانپزشکی و ایمپلنتولوژیست با بیش از ۱۰ سال سابقه بالینی، مسئول کلینیک دندانپزشکی فس‌دنت است.</p>
<p>ایشان با شماره نظام پزشکی <strong>۱۹۱۷۴۰</strong> فعالیت می‌کنند و تمرکز اصلی‌شان بر ایمپلنتولوژی پیشرفته، جراحی‌های دهان و فک و بازسازی لبخند بیماران است. در کلینیک فس‌دنت از معتبرترین برندهای جهانی ایمپلنت شامل <strong>Bego</strong>، <strong>Megagen</strong>، <strong>Straumann</strong>، <strong>Sic</strong> و <strong>3zahn</strong> استفاده می‌شود تا نتایج ماندگار، ایمن و طبیعی برای بیماران فراهم شود.</p>
<p>رویکرد دکتر علی‌پسندی مبتنی بر برنامه‌ریزی دقیق، استفاده از مواد باکیفیت، تکنیک‌های کم‌تهاجمی و پیگیری منظم پس از درمان است. ایشان زمان کافی برای مشاوره اختصاص می‌دهند تا هر بیمار به طور کامل از مراحل درمان، مراقبت‌های بعدی و انتظارات واقعی آگاه شود.</p>
<p>کلینیک فس‌دنت با فضایی مدرن، آرام و مجهز (اتاق‌های درمان پیشرفته، پذیرش شیک و فضای انتظار راحت) تجربه‌ای حرفه‌ای و بدون استرس برای بیماران فراهم می‌کند. ساعات کاری کلینیک از <strong>ساعت ۱۱ صبح تا ۱۹ شب</strong> است.</p>
<p>برای ارتباط مستقیم:</p>
<ul>
<li>تلفن / واتس‌اپ: <a href="tel:+989201441469">+98 920 144 1469</a></li>
<li>ایمیل: <a href="mailto:Dr.keyvan.alipasandii@gmail.com">Dr.keyvan.alipasandii@gmail.com</a></li>
<li>اینستاگرام: <a href="https://instagram.com/Dr.keyvan_alipasandi" target="_blank" rel="noopener">@Dr.keyvan_alipasandi</a> و <a href="https://instagram.com/Fasdent.clinic" target="_blank" rel="noopener">@Fasdent.clinic</a></li>
</ul>
<p>هدف دکتر علی‌پسندی بازگرداندن عملکرد و زیبایی لبخند بیماران با بالاترین استانداردهای علمی و کمترین دوره نقاهت ممکن است.</p>',
  `post_status`  = 'publish',
  `post_modified` = NOW(),
  `post_modified_gmt` = UTC_TIMESTAMP()
WHERE `ID` = 27 AND `post_type` = 'doctor';

UPDATE `fd_postmeta` SET `meta_value` = 'دکتری حرفه‌ای (ایمپلنتولوژیست)' WHERE `post_id` = 27 AND `meta_key` = 'doctor_title';
UPDATE `fd_postmeta` SET `meta_value` = 'دکتری حرفه‌ای دندانپزشکی\nتخصص ایمپلنتولوژی' WHERE `post_id` = 27 AND `meta_key` = 'doctor_education';
UPDATE `fd_postmeta` SET `meta_value` = '۱۹۱۷۴۰' WHERE `post_id` = 27 AND `meta_key` = 'doctor_license';
UPDATE `fd_postmeta` SET `meta_value` = '۱۰+' WHERE `post_id` = 27 AND `meta_key` = 'doctor_years';

INSERT INTO `fd_postmeta` (`post_id`, `meta_key`, `meta_value`)
SELECT 27, 'doctor_brands', 'Bego, Megagen, Straumann, Sic, 3zahn'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `fd_postmeta` WHERE `post_id` = 27 AND `meta_key` = 'doctor_brands'
);
UPDATE `fd_postmeta` SET `meta_value` = 'Bego, Megagen, Straumann, Sic, 3zahn'
WHERE `post_id` = 27 AND `meta_key` = 'doctor_brands';

-- ============================================================================
-- 4. REAL DATA – Theme Mods (serialized option)
-- ============================================================================

UPDATE `fd_options`
SET `option_value` = 'a:30:{i:0;b:0;s:18:"nav_menu_locations";a:3:{s:9:"main-menu";i:32;s:11:"footer-menu";i:33;s:10:"legal-menu";i:34;}s:18:"custom_css_post_id";i:-1;s:19:"fasdent_clinic_name";s:47:"کلینیک دندانپزشکی فس‌دنت";s:19:"fasdent_doctor_name";s:37:"دکتر کیوان علی‌پسندی";s:13:"fasdent_phone";s:11:"09201441469";s:18:"fasdent_phone_intl";s:13:"+989201441469";s:15:"fasdent_address";s:10:"تهران";s:13:"fasdent_email";s:33:"Dr.keyvan.alipasandii@gmail.com";s:13:"fasdent_hours";s:49:"از ساعت ۱۱ صبح الی ۱۹ شب";s:15:"fasdent_geo_lat";s:7:"35.7219";s:15:"fasdent_geo_lng";s:7:"51.3347";s:17:"fasdent_instagram";s:48:"https://instagram.com/Dr.keyvan_alipasandi";s:16:"fasdent_whatsapp";s:26:"https://wa.me/989201441469";s:16:"fasdent_telegram";s:0:"";s:21:"fasdent_stat_patients";s:5:"5000+";s:18:"fasdent_stat_years";s:5:"۱۰+";s:21:"fasdent_stat_implants";s:5:"2000+";s:19:"fasdent_stat_rating";s:3:"4.9";s:22:"fasdent_emergency_text";s:55:"اورژانس دندانپزشکی — تماس فوری:";s:23:"fasdent_emergency_phone";s:11:"09201441469";s:19:"fasdent_booking_url";s:13:"/appointment/";s:20:"fasdent_chat_enabled";b:1;s:21:"fasdent_chat_position";s:5:"right";s:18:"fasdent_chat_label";s:23:"ارتباط سریع";s:18:"fasdent_chat_title";s:49:"چطور می‌توانیم کمک کنیم؟";s:18:"fasdent_chat_intro";s:85:"یکی از روش‌های زیر را انتخاب کنید یا در واتس‌اپ پیام دهید.";s:21:"fasdent_chat_whatsapp";s:12:"989201441469";s:29:"fasdent_chat_whatsapp_message";s:91:"سلام، برای دریافت مشاوره از کلینیک فس‌دنت پیام می‌دهم.";s:18:"fasdent_chat_phone";s:13:"+989201441469";s:21:"fasdent_chat_telegram";s:0:"";s:18:"fasdent_chat_email";s:33:"Dr.keyvan.alipasandii@gmail.com";s:11:"custom_logo";i:148;}'
WHERE `option_name` = 'theme_mods_fasdent-theme';

UPDATE `fd_options` SET `option_value` = 'کلینیک دندانپزشکی فس‌دنت | دکتر کیوان علی‌پسندی' WHERE `option_name` = 'blogname';
UPDATE `fd_options` SET `option_value` = 'ایمپلنتولوژی تخصصی — بیش از ۱۰ سال سابقه — برندهای Bego، Megagen، Straumann، Sic، 3zahn' WHERE `option_name` = 'blogdescription';
UPDATE `fd_options` SET `option_value` = 'Dr.keyvan.alipasandii@gmail.com' WHERE `option_name` = 'admin_email';

UPDATE `fd_options` SET `option_value` = 'Asia/Tehran' WHERE `option_name` = 'timezone_string';
UPDATE `fd_options` SET `option_value` = 'Y/m/d' WHERE `option_name` = 'date_format';
UPDATE `fd_options` SET `option_value` = 'H:i' WHERE `option_name` = 'time_format';

-- ============================================================================
-- 5. FINAL OPTIMIZATION
-- ============================================================================

OPTIMIZE TABLE
  `fd_options`,
  `fd_posts`,
  `fd_postmeta`,
  `fd_comments`,
  `fd_commentmeta`,
  `fd_terms`,
  `fd_term_taxonomy`,
  `fd_term_relationships`,
  `fd_termmeta`,
  `fd_users`,
  `fd_usermeta`,
  `fd_actionscheduler_actions`,
  `fd_actionscheduler_logs`,
  `fd_fasdent_bookings`,
  `fd_fasdent_polls`,
  `fd_fasdent_poll_votes`;

ANALYZE TABLE
  `fd_options`,
  `fd_posts`,
  `fd_postmeta`,
  `fd_users`,
  `fd_usermeta`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- DONE
-- After running:
-- 1. Clear all caches (Rank Math, any page cache, object cache, CDN)
-- 2. Log out and log back in (session tokens)
-- 3. Go to Appearance → Customize and verify floating-chat + contact fields
-- 4. Flush permalinks (Settings → Permalinks → Save)
-- 5. Test homepage, doctor page, floating button, forms
-- ============================================================================
