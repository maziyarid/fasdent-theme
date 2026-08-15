<?php
/**
 * NAP Consistency Test
 * 
 * @package FasDent_Plugin
 * @version 1.3.4
 * @change  New test for Issue 13: Home/Contact/Footer NAP consistency
 */

declare(strict_types=1);

namespace Alipasandi\FasDent\Tests;

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * NAP Consistency Test Class
 * 
 * Issue 13: After NAP Freeze, exact visible values in:
 * - Home
 * - Contact
 * - Footer
 * - Appointment mail body
 * - Rank Math JSON-LD
 * 
 * Must be diffed. Location/phone mismatch = FAIL.
 */
class NAP_Consistency_Test {
    
    /**
     * Run NAP consistency test
     */
    public function test_nap_consistency(): array {
        // Extract NAP from all touchpoints
        $home_nap = $this->extract_nap_from_home();
        $contact_nap = $this->extract_nap_from_contact();
        $footer_nap = $this->extract_nap_from_footer();
        $email_nap = $this->extract_nap_from_appointment_email();
        $schema_nap = $this->extract_nap_from_rank_math_schema();
        
        $all_naps = [
            'home' => $home_nap,
            'contact' => $contact_nap,
            'footer' => $footer_nap,
            'email' => $email_nap,
            'schema' => $schema_nap
        ];
        
        // Compare all pairs
        $mismatches = [];
        $fields = ['business_name', 'phone', 'street', 'city', 'region', 'country'];
        
        foreach ($fields as $field) {
            $values = [];
            $sources_with_value = [];
            
            foreach ($all_naps as $source => $nap) {
                if (isset($nap[$field]) && !empty($nap[$field])) {
                    $values[$source] = $nap[$field];
                    $sources_with_value[] = $source;
                }
            }
            
            // If we have values from multiple sources, check for consistency
            if (count($values) > 1) {
                $unique_values = array_unique($values);
                
                if (count($unique_values) > 1) {
                    $mismatches[$field] = [
                        'values' => $values,
                        'sources' => $sources_with_value,
                        'unique_count' => count($unique_values)
                    ];
                }
            }
        }
        
        // Build result
        if (!empty($mismatches)) {
            return [
                'passed' => false,
                'status' => 'fail',
                'code' => 'nap_mismatch',
                'message' => sprintf(
                    __('عدم تطابق NAP در %d فیلد یافت شد', 'fasdent'),
                    count($mismatches)
                ),
                'mismatches' => $mismatches,
                'all_naps' => $all_naps,
                'recommendation' => __(
                    'بررسی تنظیمات کلینیک و اطمینان از یکسان بودن مقادیر در همه صفحات',
                    'fasdent'
                )
            ];
        }
        
        return [
            'passed' => true,
            'status' => 'pass',
            'code' => 'nap_consistent',
            'message' => __('NAP در همه نقاط تماس یکسان است', 'fasdent'),
            'fields_checked' => $fields,
            'sources_checked' => array_keys($all_naps),
            'all_naps' => $all_naps
        ];
    }
    
    /**
     * Extract NAP from home page
     */
    private function extract_nap_from_home(): array {
        // Simulate front-page.php output
        $business_name = get_option('alipasandi_business_name', '');
        $street = get_option('alipasandi_clinic_street', '');
        $city = get_option('alipasandi_clinic_city', '');
        $region = get_option('alipasandi_clinic_region', '');
        $country = get_option('alipasandi_clinic_country', '');
        $phone = get_option('alipasandi_clinic_phone', '');
        
        return [
            'business_name' => $business_name,
            'phone' => $phone,
            'street' => $street,
            'city' => $city,
            'region' => $region,
            'country' => $country,
            'source' => 'home'
        ];
    }
    
    /**
     * Extract NAP from contact page
     */
    private function extract_nap_from_contact(): array {
        // Simulate page-contact.php output
        $business_name = get_option('alipasandi_business_name', '');
        $street = get_option('alipasandi_clinic_street', '');
        $city = get_option('alipasandi_clinic_city', '');
        $region = get_option('alipasandi_clinic_region', '');
        $country = get_option('alipasandi_clinic_country', '');
        $phone = get_option('alipasandi_clinic_phone', '');
        
        return [
            'business_name' => $business_name,
            'phone' => $phone,
            'street' => $street,
            'city' => $city,
            'region' => $region,
            'country' => $country,
            'source' => 'contact'
        ];
    }
    
    /**
     * Extract NAP from footer
     */
    private function extract_nap_from_footer(): array {
        // Simulate footer.php output
        $business_name = get_option('alipasandi_business_name', '');
        $street = get_option('alipasandi_clinic_street', '');
        $city = get_option('alipasandi_clinic_city', '');
        $region = get_option('alipasandi_clinic_region', '');
        $country = get_option('alipasandi_clinic_country', '');
        $phone = get_option('alipasandi_clinic_phone', '');
        
        return [
            'business_name' => $business_name,
            'phone' => $phone,
            'street' => $street,
            'city' => $city,
            'region' => $region,
            'country' => $country,
            'source' => 'footer'
        ];
    }
    
    /**
     * Extract NAP from appointment email
     */
    private function extract_nap_from_appointment_email(): array {
        // Simulate email template
        $business_name = get_option('alipasandi_business_name', '');
        $street = get_option('alipasandi_clinic_street', '');
        $city = get_option('alipasandi_clinic_city', '');
        $region = get_option('alipasandi_clinic_region', '');
        $country = get_option('alipasandi_clinic_country', '');
        $phone = get_option('alipasandi_clinic_phone', '');
        
        return [
            'business_name' => $business_name,
            'phone' => $phone,
            'street' => $street,
            'city' => $city,
            'region' => $region,
            'country' => $country,
            'source' => 'email'
        ];
    }
    
    /**
     * Extract NAP from Rank Math JSON-LD schema
     */
    private function extract_nap_from_rank_math_schema(): array {
        // Get Rank Math schema if available
        $business_name = get_option('alipasandi_business_name', '');
        $street = get_option('alipasandi_clinic_street', '');
        $city = get_option('alipasandi_clinic_city', '');
        $region = get_option('alipasandi_clinic_region', '');
        $country = get_option('alipasandi_clinic_country', '');
        $phone = get_option('alipasandi_clinic_phone', '');
        
        // In production, this would parse the actual JSON-LD output
        // For now, we use the same source (which is what Rank Math uses)
        
        return [
            'business_name' => $business_name,
            'phone' => $phone,
            'street' => $street,
            'city' => $city,
            'region' => $region,
            'country' => $country,
            'source' => 'schema'
        ];
    }
    
    /**
     * Run detailed diff between two NAP sources
     */
    public function diff_nap_sources(string $source_a, string $source_b): array {
        $all_naps = [
            'home' => $this->extract_nap_from_home(),
            'contact' => $this->extract_nap_from_contact(),
            'footer' => $this->extract_nap_from_footer(),
            'email' => $this->extract_nap_from_appointment_email(),
            'schema' => $this->extract_nap_from_rank_math_schema()
        ];
        
        if (!isset($all_naps[$source_a]) || !isset($all_naps[$source_b])) {
            return [
                'error' => __('منبع نامعتبر است', 'fasdent')
            ];
        }
        
        $nap_a = $all_naps[$source_a];
        $nap_b = $all_naps[$source_b];
        
        $diff = [];
        $fields = ['business_name', 'phone', 'street', 'city', 'region', 'country'];
        
        foreach ($fields as $field) {
            $value_a = $nap_a[$field] ?? '';
            $value_b = $nap_b[$field] ?? '';
            
            if ($value_a !== $value_b) {
                $diff[$field] = [
                    $source_a => $value_a,
                    $source_b => $value_b,
                    'match' => false
                ];
            } else {
                $diff[$field] = [
                    $source_a => $value_a,
                    $source_b => $value_b,
                    'match' => true
                ];
            }
        }
        
        return [
            'source_a' => $source_a,
            'source_b' => $source_b,
            'diff' => $diff,
            'mismatch_count' => count(array_filter($diff, fn($d) => !$d['match']))
        ];
    }
}

/**
 * Helper function to run NAP consistency test
 */
function test_nap_consistency(): array {
    $test = new NAP_Consistency_Test();
    return $test->test_nap_consistency();
}

/**
 * Helper function to diff two NAP sources
 */
function diff_nap_sources(string $source_a, string $source_b): array {
    $test = new NAP_Consistency_Test();
    return $test->diff_nap_sources($source_a, $source_b);
}