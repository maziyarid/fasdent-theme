<?php
/**
 * Site Settings Module
 * 
 * @package FasDent_Plugin
 * @version 1.3.4
 * @change  Added NAP helper (Issue 6), NAP freeze (Issue 12)
 */

declare(strict_types=1);

namespace Alipasandi\FasDent;

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * NAP Helper Class
 * 
 * Issue 6: Shared contract for Required Local NAP
 * Used by Operational Health and Rank Math integration
 * 
 * Required fields for Local Entity Production:
 * - business_name
 * - clinic_street
 * - clinic_city
 * - clinic_region
 * - clinic_country
 */
class NAP_Helper {
    
    /**
     * Required NAP fields (must all be non-empty)
     */
    const REQUIRED_FIELDS = [
        'business_name',
        'clinic_street',
        'clinic_city',
        'clinic_region',
        'clinic_country'
    ];
    
    /**
     * Optional NAP fields (enhance but not required)
     */
    const OPTIONAL_FIELDS = [
        'clinic_postal',
        'clinic_phone',
        'clinic_email',
        'clinic_geo_lat',
        'clinic_geo_lng',
        'clinic_map_embed'
    ];
    
    /**
     * Get required NAP data from options
     */
    public static function get_required_nap(): array {
        $nap = [];
        
        foreach (self::REQUIRED_FIELDS as $field) {
            $nap[$field] = get_option('alipasandi_' . $field, '');
        }
        
        return $nap;
    }
    
    /**
     * Get all NAP data (required + optional)
     */
    public static function get_complete_nap(): array {
        $nap = self::get_required_nap();
        
        foreach (self::OPTIONAL_FIELDS as $field) {
            $nap[$field] = get_option('alipasandi_' . $field, '');
        }
        
        return $nap;
    }
    
    /**
     * Check if NAP is complete (all required fields non-empty)
     */
    public static function is_complete(): bool {
        $nap = self::get_required_nap();
        
        foreach ($nap as $value) {
            if (empty($value)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get list of missing required fields
     */
    public static function get_missing_fields(): array {
        $missing = [];
        $nap = self::get_required_nap();
        
        foreach ($nap as $field => $value) {
            if (empty($value)) {
                $missing[] = $field;
            }
        }
        
        return $missing;
    }
    
    /**
     * Validate NAP data
     */
    public static function validate(array $nap_data): array {
        $errors = [];
        $warnings = [];
        
        // Validate required fields
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!isset($nap_data[$field]) || empty($nap_data[$field])) {
                $errors[] = sprintf(
                    __('فیلد الزامی %s خالی است', 'fasdent'),
                    $field
                );
            }
        }
        
        // Validate phone format (if provided)
        if (isset($nap_data['clinic_phone']) && !empty($nap_data['clinic_phone'])) {
            if (!self::validate_phone($nap_data['clinic_phone'])) {
                $warnings[] = __('فرمت شماره تلفن ممکن است نادرست باشد', 'fasdent');
            }
        }
        
        // Validate email format (if provided)
        if (isset($nap_data['clinic_email']) && !empty($nap_data['clinic_email'])) {
            if (!is_email($nap_data['clinic_email'])) {
                $errors[] = __('فرمت ایمیل نادرست است', 'fasdent');
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }
    
    /**
     * Validate phone number (basic E.164 check)
     */
    private static function validate_phone(string $phone): bool {
        // Remove common separators
        $clean = preg_replace('/[\s\-\(\)]/', '', $phone);
        
        // Basic check: at least 10 digits, optional + prefix
        return preg_match('/^\+?\d{10,15}$/', $clean);
    }
    
    /**
     * Format NAP for display
     */
    public static function format_address(array $nap = null): string {
        if ($nap === null) {
            $nap = self::get_required_nap();
        }
        
        $parts = [];
        
        if (!empty($nap['clinic_street'])) {
            $parts[] = $nap['clinic_street'];
        }
        
        if (!empty($nap['clinic_city'])) {
            $parts[] = $nap['clinic_city'];
        }
        
        if (!empty($nap['clinic_region'])) {
            $parts[] = $nap['clinic_region'];
        }
        
        if (!empty($nap['clinic_country'])) {
            $parts[] = $nap['clinic_country'];
        }
        
        return implode(', ', $parts);
    }
}

/**
 * NAP Freeze Class
 * 
 * Issue 12: Owner-approved NAP Freeze
 * 
 * Before any Rank Math/Form runtime evidence, freeze and sign-off:
 * - Business name
 * - Display phone (E.164)
 * - Display address
 * - Schema street, city, region, country
 * - Postal, geo, map
 * - Notify email, mail from, mail from name
 * - Booking slots, opening hours
 * - Social URLs
 */
class NAP_Freeze {
    
    /**
     * All fields that must be frozen
     */
    const FREEZE_FIELDS = [
        'business_name',
        'clinic_phone_display',
        'clinic_phone_e164',
        'clinic_street',
        'clinic_city',
        'clinic_region',
        'clinic_country',
        'clinic_postal',
        'clinic_geo_lat',
        'clinic_geo_lng',
        'clinic_map_embed',
        'clinic_notify_email',
        'mail_from',
        'mail_from_name',
        'booking_slots',
        'opening_hours',
        'social_urls'
    ];
    
    /**
     * Freeze NAP and get owner sign-off
     * 
     * @param array $owner_approval Owner approval data
     * @return bool Success
     */
    public static function freeze_and_signoff(array $owner_approval): bool {
        // Validate approval
        if (!isset($owner_approval['approved']) || !$owner_approval['approved']) {
            return false;
        }
        
        if (empty($owner_approval['owner_name'])) {
            return false;
        }
        
        // Capture frozen state
        $frozen_state = [];
        
        foreach (self::FREEZE_FIELDS as $field) {
            $frozen_state[$field] = get_option('alipasandi_' . $field, '');
        }
        
        // Add metadata
        $frozen_state['frozen_at'] = current_time('mysql');
        $frozen_state['frozen_timestamp'] = current_time('timestamp');
        $frozen_state['approved_by'] = $owner_approval['owner_name'];
        $frozen_state['approval_note'] = $owner_approval['note'] ?? '';
        
        // Compute signature hash
        $frozen_state['signature_hash'] = hash('sha256', json_encode($frozen_state, JSON_UNESCAPED_UNICODE));
        
        // Store frozen state
        update_option('alipasandi_nap_freeze', $frozen_state, false);
        
        // Log the freeze
        error_log(sprintf(
            '[FasDent] NAP frozen by %s at %s',
            $owner_approval['owner_name'],
            $frozen_state['frozen_at']
        ));
        
        return true;
    }
    
    /**
     * Check if NAP is frozen
     */
    public static function is_frozen(): bool {
        return get_option('alipasandi_nap_freeze') !== false;
    }
    
    /**
     * Get frozen state
     */
    public static function get_frozen_state(): array {
        return get_option('alipasandi_nap_freeze', []);
    }
    
    /**
     * Verify current state matches frozen state
     */
    public static function verify_integrity(): array {
        $frozen = self::get_frozen_state();
        
        if (empty($frozen)) {
            return [
                'frozen' => false,
                'message' => __('NAP منجمد نشده است', 'fasdent')
            ];
        }
        
        $drifts = [];
        
        foreach (self::FREEZE_FIELDS as $field) {
            $current = get_option('alipasandi_' . $field, '');
            $frozen_value = $frozen[$field] ?? null;
            
            if ($current !== $frozen_value) {
                $drifts[] = [
                    'field' => $field,
                    'frozen' => $frozen_value,
                    'current' => $current
                ];
            }
        }
        
        if (!empty($drifts)) {
            return [
                'frozen' => true,
                'integrity' => 'drifted',
                'drifts' => $drifts,
                'message' => sprintf(
                    __('%d انحراف از حالت منجمد یافت شد', 'fasdent'),
                    count($drifts)
                )
            ];
        }
        
        return [
            'frozen' => true,
            'integrity' => 'intact',
            'message' => __('NAP منجمد دست‌نخورده باقی مانده است', 'fasdent'),
            'signature_hash' => $frozen['signature_hash']
        ];
    }
    
    /**
     * Unfreeze NAP (requires owner approval)
     */
    public static function unfreeze(array $owner_approval): bool {
        if (!isset($owner_approval['approved']) || !$owner_approval['approved']) {
            return false;
        }
        
        delete_option('alipasandi_nap_freeze');
        
        error_log(sprintf(
            '[FasDent] NAP unfrozen by %s at %s',
            $owner_approval['owner_name'],
            current_time('mysql')
        ));
        
        return true;
    }
}

/**
 * Documentation: Difference between clinic_address and clinic_street
 * 
 * Issue 12: Guide must explain the difference and requirement for
 * simultaneous update on location change.
 */

/**
 * @doc
 * 
 * Difference between clinic_address and clinic_street:
 * 
 * - **clinic_street**: The actual street address line
 *   Example: "ستارخان، امیراد ۱، طبقه ۵"
 *   Used by: Rank Math streetAddress, Home page, Contact page
 * 
 * - **clinic_address**: Legacy field, may include city/region
 *   Example: "ستارخان، امیراد ۱، طبقه ۵، تهران"
 *   Used by: Legacy templates, some health checks
 * 
 * CRITICAL: When changing location, BOTH fields must be updated together
 * to prevent NAP drift. Use NAP_Freeze::freeze_and_signoff() for atomic updates.
 * 
 * Best practice:
 * 1. Update clinic_street with exact street address
 * 2. Update clinic_city, clinic_region, clinic_country separately
 * 3. Update clinic_address only if legacy compatibility needed
 * 4. Call NAP_Freeze::freeze_and_signoff() to lock the state
 * 5. Run NAP consistency test to verify no drift
 */

/**
 * Initialize site settings
 */
function init_site_settings(): void {
    // Ensure NAP helper is loaded
    class_exists('Alipasandi\\FasDent\\NAP_Helper') or die('NAP_Helper not loaded');
    class_exists('Alipasandi\\FasDent\\NAP_Freeze') or die('NAP_Freeze not loaded');
}

add_action('plugins_loaded', __NAMESPACE__ . '\\init_site_settings');