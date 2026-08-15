<?php
/**
 * Health Checks Module
 * 
 * @package FasDent_Plugin
 * @version 1.3.4
 * @change  Added street validation, booking/hours cross-validation, NAP helper integration
 */

declare(strict_types=1);

namespace Alipasandi\FasDent;

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Health Checks Class
 * 
 * Monitors operational readiness and SEO health
 */
class Health_Checks {
    
    /**
     * Run all health checks with explicit observation probe
     * 
     * Issue 7: Health deployment must not depend on random traffic
     * This method explicitly probes Front/Contact pages before running checks
     */
    public function run_health_with_probe(): array {
        // Step 1: Explicit observation probe (Issue 7)
        $this->probe_front_page();
        $this->probe_contact_page();
        
        // Step 2: Brief wait for observation refresh
        usleep(500000); // 500ms
        
        // Step 3: Run all health checks
        return $this->run_all_checks();
    }
    
    /**
     * Probe front page to trigger Rank Math observation
     */
    private function probe_front_page(): void {
        wp_remote_get(home_url('/'), [
            'timeout' => 5,
            'blocking' => false,
            'headers' => [
                'User-Agent' => 'FasDent-Health-Probe/1.3.4'
            ]
        ]);
    }
    
    /**
     * Probe contact page to trigger Rank Math observation
     */
    private function probe_contact_page(): void {
        wp_remote_get(home_url('/contact/'), [
            'timeout' => 5,
            'blocking' => false,
            'headers' => [
                'User-Agent' => 'FasDent-Health-Probe/1.3.4'
            ]
        ]);
    }
    
    /**
     * Run all health checks
     */
    public function run_all_checks(): array {
        $checks = [
            'street_missing' => $this->check_street_missing(),
            'address_missing' => $this->check_address_missing(),
            'phone_missing' => $this->check_phone_missing(),
            'booking_hours_interoperability' => $this->check_booking_hours_interoperability(),
            'form_readiness' => $this->check_form_readiness(),
            'nap_completeness' => $this->check_nap_completeness(),
            'rank_math_observation' => $this->check_rank_math_observation(),
            'loopback_status' => $this->check_loopback_status(),
            'wp_cron_status' => $this->check_wp_cron_status(),
            'debug_display' => $this->check_debug_display()
        ];
        
        // Determine overall status
        $critical_count = 0;
        $warning_count = 0;
        
        foreach ($checks as $check) {
            if ($check['status'] === 'critical') {
                $critical_count++;
            } elseif ($check['status'] === 'warning') {
                $warning_count++;
            }
        }
        
        return [
            'timestamp' => current_time('mysql'),
            'overall_status' => $critical_count > 0 ? 'critical' : ($warning_count > 0 ? 'warning' : 'pass'),
            'critical_count' => $critical_count,
            'warning_count' => $warning_count,
            'checks' => $checks
        ];
    }
    
    /**
     * Issue 2: Check if clinic_street is missing (HIGH SEO)
     * 
     * Health currently checks clinic_address but Rank Math streetAddress
     * is built from clinic_street. This gap allows Health to Pass while
     * Schema has empty streetAddress.
     */
    public function check_street_missing(): array {
        $street = get_option('alipasandi_clinic_street', '');
        
        if (empty($street)) {
            return [
                'status' => 'critical',
                'code' => 'street_missing',
                'message' => __('آدرس خیابان تنظیم نشده است', 'fasdent'),
                'impact' => __('Rank Math streetAddress خالی خواهد بود', 'fasdent'),
                'recommendation' => __('تنظیم آدرس کامل در تنظیمات کلینیک', 'fasdent')
            ];
        }
        
        return [
            'status' => 'pass',
            'code' => 'street_present',
            'message' => __('آدرس خیابان موجود است', 'fasdent')
        ];
    }
    
    /**
     * Check if clinic_address is missing (legacy check)
     */
    public function check_address_missing(): array {
        $address = get_option('alipasandi_clinic_address', '');
        
        if (empty($address)) {
            return [
                'status' => 'warning',
                'code' => 'address_missing',
                'message' => __('آدرس کلینیک تنظیم نشده است', 'fasdent'),
                'impact' => __('نمایش آدرس در صفحات ممکن است خالی باشد', 'fasdent')
            ];
        }
        
        return [
            'status' => 'pass',
            'code' => 'address_present',
            'message' => __('آدرس کلینیک موجود است', 'fasdent')
        ];
    }
    
    /**
     * Check if phone number is missing
     */
    public function check_phone_missing(): array {
        $phone = get_option('alipasandi_clinic_phone', '');
        
        if (empty($phone)) {
            return [
                'status' => 'critical',
                'code' => 'phone_missing',
                'message' => __('شماره تلفن تنظیم نشده است', 'fasdent'),
                'impact' => __('فرم تماس و CTA تلفنی کار نخواهند کرد', 'fasdent')
            ];
        }
        
        return [
            'status' => 'pass',
            'code' => 'phone_present',
            'message' => __('شماره تلفن موجود است', 'fasdent')
        ];
    }
    
    /**
     * Issue 4: Check booking slots ↔ opening hours interoperability (HIGH)
     * 
     * Booking slots and opening hours are validated independently, but
     * their interoperability is not checked. Example failure:
     * - Slots: 20:00
     * - Opening: MO=09:00-17:00
     * Both valid individually, but no Monday slot is actually bookable.
     */
    public function check_booking_hours_interoperability(): array {
        $slots = get_option('alipasandi_booking_slots', []);
        $hours = get_option('alipasandi_opening_hours', []);
        
        if (empty($slots)) {
            return [
                'status' => 'warning',
                'code' => 'no_booking_slots',
                'message' => __('هیچ نوبتی تنظیم نشده است', 'fasdent')
            ];
        }
        
        if (empty($hours)) {
            return [
                'status' => 'warning',
                'code' => 'no_opening_hours',
                'message' => __('ساعات کاری تنظیم نشده است', 'fasdent')
            ];
        }
        
        $usable_slots = 0;
        $slot_details = [];
        
        foreach ($slots as $slot) {
            $slot_time = isset($slot['time']) ? strtotime($slot['time']) : false;
            $slot_day = $slot['day'] ?? '';
            
            if (!$slot_time || empty($slot_day)) {
                continue;
            }
            
            $is_usable = false;
            
            // Check if this slot falls within any open range for that day
            if (isset($hours[$slot_day])) {
                foreach ($hours[$slot_day] as $range) {
                    $open = isset($range['open']) ? strtotime($range['open']) : false;
                    $close = isset($range['close']) ? strtotime($range['close']) : false;
                    
                    if ($open && $close && $slot_time >= $open && $slot_time <= $close) {
                        $is_usable = true;
                        break;
                    }
                }
            }
            
            if ($is_usable) {
                $usable_slots++;
            }
            
            $slot_details[] = [
                'day' => $slot_day,
                'time' => $slot['time'] ?? '',
                'usable' => $is_usable
            ];
        }
        
        if ($usable_slots === 0) {
            return [
                'status' => 'critical',
                'code' => 'zero_usable_slots',
                'message' => __('هیچ نوبتی در ساعات کاری موجود نیست', 'fasdent'),
                'impact' => __('فرم رزرو عملاً غیرقابل استفاده است', 'fasdent'),
                'details' => $slot_details,
                'recommendation' => __('تنظیم نوبت‌ها در محدوده ساعات کاری', 'fasdent')
            ];
        }
        
        // Check for open days without any slots (Warning, not Critical)
        $days_without_slots = [];
        foreach ($hours as $day => $ranges) {
            if (!empty($ranges)) {
                $has_slot = false;
                foreach ($slot_details as $detail) {
                    if ($detail['day'] === $day && $detail['usable']) {
                        $has_slot = true;
                        break;
                    }
                }
                if (!$has_slot) {
                    $days_without_slots[] = $day;
                }
            }
        }
        
        $result = [
            'status' => 'pass',
            'code' => 'booking_hours_aligned',
            'message' => __('نوبت‌ها و ساعات کاری هماهنگ هستند', 'fasdent'),
            'usable_slots' => $usable_slots,
            'total_slots' => count($slots)
        ];
        
        if (!empty($days_without_slots)) {
            $result['status'] = 'warning';
            $result['code'] = 'open_days_without_slots';
            $result['message'] = __('برخی روزهای باز بدون نوبت هستند', 'fasdent');
            $result['days_without_slots'] = $days_without_slots;
        }
        
        return $result;
    }
    
    /**
     * Issue 5: Check form readiness including phone route
     * 
     * Form readiness currently enforces mail configuration, but if mail
     * fails, UI directs users to call. Therefore, a valid phone should
     * be required for interactive form readiness.
     */
    public function check_form_readiness(): array {
        $mail_configured = $this->check_mail_configuration();
        $phone_valid = !empty(get_option('alipasandi_clinic_phone', ''));
        
        if (!$mail_configured && !$phone_valid) {
            return [
                'status' => 'critical',
                'code' => 'no_contact_method',
                'message' => __('نه ایمیل و نه تلفن معتبر موجود نیست', 'fasdent'),
                'impact' => __('فرم تماس کاملاً غیرعملیاتی است', 'fasdent'),
                'recommendation' => __('تنظیم حداقل یکی: SMTP یا تلفن معتبر', 'fasdent')
            ];
        }
        
        if (!$mail_configured && $phone_valid) {
            return [
                'status' => 'warning',
                'code' => 'phone_only_mode',
                'message' => __('فقط تلفن موجود است (بدون ایمیل)', 'fasdent'),
                'mode' => 'phone_only',
                'instruction' => sprintf(
                    __('تماس مستقیم: %s', 'fasdent'),
                    get_option('alipasandi_clinic_phone', '')
                )
            ];
        }
        
        if ($mail_configured && !$phone_valid) {
            return [
                'status' => 'warning',
                'code' => 'mail_only_mode',
                'message' => __('فقط ایمیل موجود است (بدون تلفن)', 'fasdent'),
                'mode' => 'mail_only'
            ];
        }
        
        return [
            'status' => 'pass',
            'code' => 'full_contact_ready',
            'message' => __('ایمیل و تلفن هر دو موجود هستند', 'fasdent'),
            'mode' => 'full'
        ];
    }
    
    /**
     * Check mail configuration
     */
    private function check_mail_configuration(): bool {
        $smtp_host = get_option('alipasandi_smtp_host', '');
        $mail_from = get_option('alipasandi_mail_from', '');
        
        return !empty($smtp_host) && !empty($mail_from);
    }
    
    /**
     * Issue 6: Check NAP completeness using shared helper
     */
    public function check_nap_completeness(): array {
        $nap = NAP_Helper::get_required_nap();
        $missing = NAP_Helper::get_missing_fields();
        
        if (!empty($missing)) {
            return [
                'status' => 'critical',
                'code' => 'nap_incomplete',
                'message' => __('اطلاعات NAP ناقص است', 'fasdent'),
                'missing_fields' => $missing,
                'impact' => __('Schema LocalBusiness ناقص خواهد بود', 'fasdent')
            ];
        }
        
        return [
            'status' => 'pass',
            'code' => 'nap_complete',
            'message' => __('تمام فیلدهای NAP موجود هستند', 'fasdent')
        ];
    }
    
    /**
     * Issue 7: Check Rank Math observation freshness
     */
    public function check_rank_math_observation(): array {
        $last_observation = get_option('alipasandi_last_observation', []);
        
        if (empty($last_observation)) {
            return [
                'status' => 'warning',
                'code' => 'no_observation',
                'message' => __('هیچ مشاهده‌ای از Rank Math ثبت نشده است', 'fasdent'),
                'recommendation' => __('درخواست از Front Page و Contact، سپس بررسی مجدد', 'fasdent')
            ];
        }
        
        $observed_at = isset($last_observation['observed_at']) ? strtotime($last_observation['observed_at']) : 0;
        $now = current_time('timestamp');
        $age_minutes = ($now - $observed_at) / 60;
        
        // Observation becomes stale after 15 minutes
        if ($age_minutes > 15) {
            return [
                'status' => 'warning',
                'code' => 'observation_stale',
                'message' => sprintf(
                    __('مشاهده Rank Math قدیمی است (%d دقیقه پیش)', 'fasdent'),
                    (int)$age_minutes
                ),
                'recommendation' => __('درخواست از Front Page و Contact برای refresh', 'fasdent')
            ];
        }
        
        return [
            'status' => 'pass',
            'code' => 'observation_fresh',
            'message' => __('مشاهده Rank Math به‌روز است', 'fasdent'),
            'age_minutes' => round($age_minutes, 1),
            'rank_math_version' => $last_observation['rank_math_version'] ?? 'unknown'
        ];
    }
    
    /**
     * Check loopback status (Issue 14/18)
     */
    public function check_loopback_status(): array {
        $loopback = wp_remote_get(home_url('/wp-cron.php'), [
            'timeout' => 10,
            'blocking' => true
        ]);
        
        if (is_wp_error($loopback)) {
            return [
                'status' => 'critical',
                'code' => 'loopback_error',
                'message' => sprintf(
                    __('خطای loopback: %s', 'fasdent'),
                    $loopback->get_error_message()
                ),
                'impact' => __('WP-Cron و health checks ممکن است کار نکنند', 'fasdent')
            ];
        }
        
        $status_code = wp_remote_retrieve_response_code($loopback);
        
        if ($status_code === 503) {
            return [
                'status' => 'critical',
                'code' => 'loopback_503',
                'message' => __('Loopback HTTP 503返回', 'fasdent'),
                'impact' => __('میزبانی باید قبل از تولید حل شود', 'fasdent')
            ];
        }
        
        if ($status_code !== 200) {
            return [
                'status' => 'warning',
                'code' => 'loopback_unexpected',
                'message' => sprintf(__('Loopback HTTP %d', 'fasdent'), $status_code)
            ];
        }
        
        return [
            'status' => 'pass',
            'code' => 'loopback_ok',
            'message' => __('Loopback سالم است', 'fasdent')
        ];
    }
    
    /**
     * Check WP-Cron status
     */
    public function check_wp_cron_status(): array {
        $crons = _get_cron_array();
        
        if (empty($crons)) {
            return [
                'status' => 'warning',
                'code' => 'no_cron_events',
                'message' => __('هیچ رویداد WP-Cron یافت نشد', 'fasdent')
            ];
        }
        
        return [
            'status' => 'pass',
            'code' => 'cron_active',
            'message' => __('WP-Cron فعال است', 'fasdent'),
            'event_count' => count($crons, COUNT_RECURSIVE)
        ];
    }
    
    /**
     * Issue 15: Check WP_DEBUG_DISPLAY is false in production
     */
    public function check_debug_display(): array {
        $is_debug_display = defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY;
        
        if ($is_debug_display) {
            return [
                'status' => 'critical',
                'code' => 'debug_display_enabled',
                'message' => __('WP_DEBUG_DISPLAY فعال است', 'fasdent'),
                'impact' => __('خطاها برای کاربران نمایش داده می‌شود', 'fasdent'),
                'recommendation' => __('تنظیم WP_DEBUG_DISPLAY=false قبل از تولید', 'fasdent')
            ];
        }
        
        return [
            'status' => 'pass',
            'code' => 'debug_display_disabled',
            'message' => __('WP_DEBUG_DISPLAY غیرفعال است', 'fasdent')
        ];
    }
}

/**
 * Initialize health checks
 */
function run_health_checks(): array {
    $health = new Health_Checks();
    return $health->run_health_with_probe();
}