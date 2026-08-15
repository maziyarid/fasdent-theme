<?php
/**
 * Validators Module
 * 
 * @package FasDent_Plugin
 * @version 1.3.4
 * @change  Added booking/hours interoperability, date availability validation
 */

declare(strict_types=1);

namespace Alipasandi\FasDent;

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Validators Class
 * 
 * Validates operational data and cross-component contracts
 */
class Validators {
    
    /**
     * Issue 4 & 11: Validate booking slots ↔ opening hours interoperability
     * 
     * This is the core cross-validator that ensures:
     * - At least one configured booking slot overlaps with at least one open range
     * - Zero usable slots across weekly schedule = Critical
     * - Open day without slot = Warning (unless business rule says otherwise)
     * 
     * This same contract is reflected in Health (Issue 11) to prevent UI
     * from reaching a state where no Date is usable.
     */
    public function validate_booking_hours_interoperability(): array {
        $slots = get_option('alipasandi_booking_slots', []);
        $hours = get_option('alipasandi_opening_hours', []);
        
        // Handle empty cases
        if (empty($slots)) {
            return [
                'valid' => false,
                'status' => 'warning',
                'code' => 'no_booking_slots',
                'message' => __('هیچ نوبتی تنظیم نشده است', 'fasdent'),
                'usable_slots' => 0,
                'details' => []
            ];
        }
        
        if (empty($hours)) {
            return [
                'valid' => false,
                'status' => 'warning',
                'code' => 'no_opening_hours',
                'message' => __('ساعات کاری تنظیم نشده است', 'fasdent'),
                'usable_slots' => 0,
                'details' => []
            ];
        }
        
        $usable_slots = 0;
        $slot_details = [];
        $unusable_slots = [];
        
        // Day mapping (English to Persian display)
        $day_names = [
            'SA' => 'شنبه',
            'SU' => 'یکشنبه',
            'MO' => 'دوشنبه',
            'TU' => 'سه‌شنبه',
            'WE' => 'چهارشنبه',
            'TH' => 'پنج‌شنبه',
            'FR' => 'جمعه'
        ];
        
        foreach ($slots as $index => $slot) {
            $slot_time = isset($slot['time']) ? strtotime($slot['time']) : false;
            $slot_day = $slot['day'] ?? '';
            $slot_time_str = $slot['time'] ?? '';
            
            if (!$slot_time || empty($slot_day)) {
                $unusable_slots[] = [
                    'index' => $index,
                    'reason' => 'invalid_time_or_day',
                    'slot' => $slot
                ];
                continue;
            }
            
            $is_usable = false;
            $matched_range = null;
            
            // Check if this slot falls within any open range for that day
            if (isset($hours[$slot_day]) && is_array($hours[$slot_day])) {
                foreach ($hours[$slot_day] as $range_index => $range) {
                    $open = isset($range['open']) ? strtotime($range['open']) : false;
                    $close = isset($range['close']) ? strtotime($range['close']) : false;
                    
                    if ($open && $close && $slot_time >= $open && $slot_time <= $close) {
                        $is_usable = true;
                        $matched_range = [
                            'range_index' => $range_index,
                            'open' => $range['open'],
                            'close' => $range['close']
                        ];
                        break;
                    }
                }
            }
            
            if ($is_usable) {
                $usable_slots++;
            } else {
                $unusable_slots[] = [
                    'index' => $index,
                    'reason' => 'outside_opening_hours',
                    'slot' => $slot,
                    'day_name' => $day_names[$slot_day] ?? $slot_day
                ];
            }
            
            $slot_details[] = [
                'index' => $index,
                'day' => $slot_day,
                'day_name' => $day_names[$slot_day] ?? $slot_day,
                'time' => $slot_time_str,
                'usable' => $is_usable,
                'matched_range' => $matched_range
            ];
        }
        
        // Critical: Zero usable slots
        if ($usable_slots === 0) {
            return [
                'valid' => false,
                'status' => 'critical',
                'code' => 'zero_usable_slots',
                'message' => __('هیچ نوبتی در ساعات کاری موجود نیست', 'fasdent'),
                'impact' => __('فرم رزرو عملاً غیرقابل استفاده است', 'fasdent'),
                'usable_slots' => 0,
                'total_slots' => count($slots),
                'details' => $slot_details,
                'unusable_slots' => $unusable_slots,
                'recommendation' => __(
                    'تنظیم نوبت‌ها در محدوده ساعات کاری. مثال: اگر ساعات کاری دوشنبه ۰۹:۰۰-۱۷:۰۰ است، نوبت‌ها باید در این بازه باشند.',
                    'fasdent'
                )
            ];
        }
        
        // Check for open days without any slots (Warning)
        $days_without_slots = [];
        $day_names_fa = [];
        
        foreach ($hours as $day => $ranges) {
            if (!empty($ranges) && is_array($ranges)) {
                $has_slot = false;
                
                foreach ($slot_details as $detail) {
                    if ($detail['day'] === $day && $detail['usable']) {
                        $has_slot = true;
                        break;
                    }
                }
                
                if (!$has_slot) {
                    $days_without_slots[] = $day;
                    $day_names_fa[] = $day_names[$day] ?? $day;
                }
            }
        }
        
        $result = [
            'valid' => true,
            'status' => 'pass',
            'code' => 'booking_hours_aligned',
            'message' => __('نوبت‌ها و ساعات کاری هماهنگ هستند', 'fasdent'),
            'usable_slots' => $usable_slots,
            'total_slots' => count($slots),
            'details' => $slot_details
        ];
        
        // Warning: Open days without slots
        if (!empty($days_without_slots)) {
            $result['status'] = 'warning';
            $result['code'] = 'open_days_without_slots';
            $result['message'] = sprintf(
                __('برخی روزهای باز بدون نوبت هستند: %s', 'fasdent'),
                implode('، ', $day_names_fa)
            );
            $result['days_without_slots'] = $days_without_slots;
            $result['days_without_slots_names'] = $day_names_fa;
        }
        
        return $result;
    }
    
    /**
     * Issue 11: Generate available dates based on interoperable slots
     * 
     * This is the date-aware UI validator that ensures the UI never
     * reaches a state where no Date is usable.
     */
    public function generate_available_dates(int $days_ahead = 30): array {
        // First, validate interoperability
        $interop = $this->validate_booking_hours_interoperability();
        
        if ($interop['status'] === 'critical') {
            return [
                'available_dates' => [],
                'error' => [
                    'code' => $interop['code'],
                    'message' => $interop['message']
                ],
                'ui_state' => 'non_operational',
                'user_message' => __('در حال حاضر نوبتی در ساعات کاری موجود نیست. لطفاً با کلینیک تماس بگیرید.', 'fasdent')
            ];
        }
        
        if ($interop['status'] === 'warning' && $interop['usable_slots'] === 0) {
            return [
                'available_dates' => [],
                'error' => [
                    'code' => 'no_usable_slots',
                    'message' => __('هیچ نوبت معتبری یافت نشد', 'fasdent')
                ],
                'ui_state' => 'limited',
                'user_message' => __('نوبت‌دهی محدود است. لطفاً با کلینیک تماس بگیرید.', 'fasdent')
            ];
        }
        
        // Generate available dates
        $available_dates = [];
        $slots = get_option('alipasandi_booking_slots', []);
        $base_date = current_time('timestamp');
        
        for ($i = 0; $i < $days_ahead; $i++) {
            $date_timestamp = strtotime("+{$i} days", $base_date);
            $date_str = date('Y-m-d', $date_timestamp);
            $day_of_week = date('D', $date_timestamp);
            
            // Map PHP day to our day code
            $day_map = [
                'Sat' => 'SA',
                'Sun' => 'SU',
                'Mon' => 'MO',
                'Tue' => 'TU',
                'Wed' => 'WE',
                'Thu' => 'TH',
                'Fri' => 'FR'
            ];
            
            $day_code = $day_map[$day_of_week] ?? '';
            
            if (empty($day_code)) {
                continue;
            }
            
            // Find usable slots for this day
            $usable_slots_for_day = [];
            
            foreach ($interop['details'] as $slot_detail) {
                if ($slot_detail['day'] === $day_code && $slot_detail['usable']) {
                    $usable_slots_for_day[] = [
                        'time' => $slot_detail['time'],
                        'date' => $date_str,
                        'timestamp' => strtotime($date_str . ' ' . $slot_detail['time'])
                    ];
                }
            }
            
            if (!empty($usable_slots_for_day)) {
                $available_dates[] = [
                    'date' => $date_str,
                    'day_name' => date('l', $date_timestamp),
                    'slots' => $usable_slots_for_day,
                    'slot_count' => count($usable_slots_for_day)
                ];
            }
        }
        
        if (empty($available_dates)) {
            return [
                'available_dates' => [],
                'error' => [
                    'code' => 'no_future_dates',
                    'message' => __('هیچ تاریخی در %d روز آینده با نوبت‌های موجود هماهنگ نیست', 'fasdent'),
                    'days_checked' => $days_ahead
                ],
                'ui_state' => 'non_operational',
                'user_message' => __('در حال حاضر نوبتی موجود نیست. لطفاً با کلینیک تماس بگیرید.', 'fasdent')
            ];
        }
        
        return [
            'available_dates' => $available_dates,
            'total_dates' => count($available_dates),
            'total_slots' => array_sum(array_column($available_dates, 'slot_count')),
            'ui_state' => 'operational',
            'generated_at' => current_time('mysql')
        ];
    }
    
    /**
     * Validate NAP data completeness
     */
    public function validate_nap_completeness(): array {
        return [
            'complete' => NAP_Helper::is_complete(),
            'missing_fields' => NAP_Helper::get_missing_fields(),
            'required_fields' => NAP_Helper::REQUIRED_FIELDS
        ];
    }
    
    /**
     * Validate phone number format
     */
    public function validate_phone(string $phone): array {
        $clean = preg_replace('/[\s\-\(\)]/', '', $phone);
        
        if (empty($clean)) {
            return [
                'valid' => false,
                'error' => __('شماره تلفن خالی است', 'fasdent')
            ];
        }
        
        if (!preg_match('/^\+?\d{10,15}$/', $clean)) {
            return [
                'valid' => false,
                'error' => __('فرمت شماره تلفن نادرست است. فرمت E.164 پیشنهاد می‌شود (مثال: +982112345678)', 'fasdent'),
                'suggested_format' => '+' . ltrim($clean, '+')
            ];
        }
        
        return [
            'valid' => true,
            'formatted' => '+' . ltrim($clean, '+'),
            'original' => $phone
        ];
    }
    
    /**
     * Validate email format
     */
    public function validate_email(string $email): array {
        if (empty($email)) {
            return [
                'valid' => false,
                'error' => __('ایمیل خالی است', 'fasdent')
            ];
        }
        
        if (!is_email($email)) {
            return [
                'valid' => false,
                'error' => __('فرمت ایمیل نادرست است', 'fasdent')
            ];
        }
        
        return [
            'valid' => true,
            'email' => $email
        ];
    }
}

/**
 * Helper function to run interoperability validation
 */
function validate_booking_hours(): array {
    $validator = new Validators();
    return $validator->validate_booking_hours_interoperability();
}

/**
 * Helper function to generate available dates
 */
function get_available_dates(int $days_ahead = 30): array {
    $validator = new Validators();
    return $validator->generate_available_dates($days_ahead);
}