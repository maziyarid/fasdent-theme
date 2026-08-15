<?php
/**
 * Forms Module
 * 
 * @package FasDent_Plugin
 * @version 1.3.4
 * @change  Added phone route validation (Issue 5), form fail-safe logic
 */

declare(strict_types=1);

namespace Alipasandi\FasDent;

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Forms Class
 * 
 * Handles appointment form readiness and validation
 */
class Forms {
    
    /**
     * Issue 5: Check form readiness including phone route
     * 
     * Form readiness currently enforces mail configuration, but if mail
     * fails, UI directs users to call. Therefore, Production should ideally
     * have a valid Direct Phone route as well.
     * 
     * Contract A selected: Valid operational Phone is part of Form readiness.
     * 
     * Error path must NOT say "call us" without providing a number.
     */
    public function check_form_readiness(): array {
        $mail_configured = $this->check_mail_configuration();
        $phone_valid = $this->check_phone_valid();
        
        // Critical: Neither mail nor phone available
        if (!$mail_configured && !$phone_valid) {
            return [
                'ready' => false,
                'status' => 'critical',
                'code' => 'no_contact_method',
                'error' => __('نه ایمیل و نه تلفن معتبر موجود نیست', 'fasdent'),
                'fallback' => null, // No valid fallback to show
                'user_message' => __('سیتم تماس در حال حاضر در دسترس نیست. لطفاً بعداً تلاش کنید.', 'fasdent'),
                'admin_message' => __(
                    'تنظیم حداقل یکی از: SMTP/ایمیل یا شماره تلفن معتبر در تنظیمات کلینیک',
                    'fasdent'
                )
            ];
        }
        
        // Warning: Phone only mode (mail not configured)
        if (!$mail_configured && $phone_valid) {
            $phone = get_option('alipasandi_clinic_phone', '');
            
            return [
                'ready' => true,
                'status' => 'warning',
                'code' => 'phone_only_mode',
                'mode' => 'phone_only',
                'mail_configured' => false,
                'phone_valid' => true,
                'instruction' => sprintf(
                    __('تماس مستقیم: %s', 'fasdent'),
                    $phone
                ),
                'user_message' => sprintf(
                    __('برای رزرو نوبت با شماره %s تماس بگیرید', 'fasdent'),
                    $phone
                ),
                'form_state' => 'disabled', // Show phone CTA, disable form
                'cta_text' => __('تماس تلفنی', 'fasdent'),
                'cta_link' => 'tel:' . preg_replace('/[\s\-\(\)]/', '', $phone)
            ];
        }
        
        // Warning: Mail only mode (phone not configured)
        if ($mail_configured && !$phone_valid) {
            return [
                'ready' => true,
                'status' => 'warning',
                'code' => 'mail_only_mode',
                'mode' => 'mail_only',
                'mail_configured' => true,
                'phone_valid' => false,
                'user_message' => __('فرم زیر را پر کنید تا با شما تماس بگیریم', 'fasdent'),
                'form_state' => 'enabled',
                'fallback_instruction' => null // No phone fallback to show
            ];
        }
        
        // Full mode: Both mail and phone available
        $phone = get_option('alipasandi_clinic_phone', '');
        
        return [
            'ready' => true,
            'status' => 'pass',
            'code' => 'full_contact_ready',
            'mode' => 'full',
            'mail_configured' => true,
            'phone_valid' => true,
            'user_message' => __('فرم زیر را پر کنید یا با ما تماس بگیرید', 'fasdent'),
            'form_state' => 'enabled',
            'phone_cta' => sprintf(
                __('یا تماس: %s', 'fasdent'),
                $phone
            ),
            'cta_link' => 'tel:' . preg_replace('/[\s\-\(\)]/', '', $phone)
        ];
    }
    
    /**
     * Check mail configuration
     */
    private function check_mail_configuration(): bool {
        $smtp_host = get_option('alipasandi_smtp_host', '');
        $mail_from = get_option('alipasandi_mail_from', '');
        $mail_from_name = get_option('alipasandi_mail_from_name', '');
        
        // Basic check: SMTP host and from address configured
        if (empty($smtp_host) || empty($mail_from)) {
            return false;
        }
        
        // Additional check: Can we send a test email?
        // (This would be done in a more comprehensive check)
        
        return true;
    }
    
    /**
     * Check if phone is valid
     */
    private function check_phone_valid(): bool {
        $phone = get_option('alipasandi_clinic_phone', '');
        
        if (empty($phone)) {
            return false;
        }
        
        // Basic validation: at least 10 digits
        $clean = preg_replace('/[\s\-\(\)]/', '', $phone);
        
        return strlen(preg_replace('/[^\d]/', '', $clean)) >= 10;
    }
    
    /**
     * Get form configuration for frontend
     */
    public function get_form_config(): array {
        $readiness = $this->check_form_readiness();
        
        $config = [
            'ready' => $readiness['ready'],
            'mode' => $readiness['mode'] ?? 'unknown',
            'user_message' => $readiness['user_message'] ?? '',
            'form_state' => $readiness['form_state'] ?? 'disabled'
        ];
        
        // Add phone CTA if available
        if (isset($readiness['cta_text']) && isset($readiness['cta_link'])) {
            $config['phone_cta'] = [
                'text' => $readiness['cta_text'],
                'link' => $readiness['cta_link']
            ];
        }
        
        // Add phone inline CTA if in full mode
        if (isset($readiness['phone_cta']) && isset($readiness['cta_link'])) {
            $config['inline_phone'] = [
                'text' => $readiness['phone_cta'],
                'link' => $readiness['cta_link']
            ];
        }
        
        return $config;
    }
    
    /**
     * Validate form submission
     */
    public function validate_submission(array $data): array {
        $errors = [];
        
        // Required fields
        $required = ['name', 'phone', 'service', 'date', 'time'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = sprintf(
                    __('فیلد %s الزامی است', 'fasdent'),
                    $this->get_field_label($field)
                );
            }
        }
        
        // Validate phone
        if (!empty($data['phone'])) {
            $phone_validator = new Validators();
            $phone_result = $phone_validator->validate_phone($data['phone']);
            
            if (!$phone_result['valid']) {
                $errors[] = $phone_result['error'];
            }
        }
        
        // Validate email (if provided)
        if (!empty($data['email'])) {
            $phone_validator = new Validators();
            $email_result = $phone_validator->validate_email($data['email']);
            
            if (!$email_result['valid']) {
                $errors[] = $email_result['error'];
            }
        }
        
        // Validate date is in future
        if (!empty($data['date'])) {
            $date_timestamp = strtotime($data['date']);
            $now = current_time('timestamp');
            
            if ($date_timestamp < $now) {
                $errors[] = __('تاریخ باید در آینده باشد', 'fasdent');
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Get field label for error messages
     */
    private function get_field_label(string $field): string {
        $labels = [
            'name' => __('نام', 'fasdent'),
            'phone' => __('شماره تلفن', 'fasdent'),
            'email' => __('ایمیل', 'fasdent'),
            'service' => __('خدمت', 'fasdent'),
            'date' => __('تاریخ', 'fasdent'),
            'time' => __('زمان', 'fasdent'),
            'message' => __('پیام', 'fasdent')
        ];
        
        return $labels[$field] ?? $field;
    }
    
    /**
     * Send appointment email
     */
    public function send_appointment_email(array $data): array {
        $readiness = $this->check_form_readiness();
        
        if (!$readiness['mail_configured']) {
            return [
                'success' => false,
                'error' => __('سیستم ایمیل پیکربندی نشده است', 'fasdent'),
                'fallback' => $readiness['instruction'] ?? null
            ];
        }
        
        $to = get_option('alipasandi_clinic_notify_email', get_option('alipasandi_mail_from', ''));
        $subject = sprintf(
            __('درخواست نوبت جدید - %s', 'fasdent'),
            $data['name']
        );
        
        $message = $this->build_appointment_email($data);
        
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_option('alipasandi_mail_from_name', 'کلینیک فاسدنت') . ' <' . get_option('alipasandi_mail_from', '') . '>'
        ];
        
        $sent = wp_mail($to, $subject, $message, $headers);
        
        if (!$sent) {
            return [
                'success' => false,
                'error' => __('ارسال ایمیل ناموفق بود', 'fasdent'),
                'fallback' => $readiness['instruction'] ?? null
            ];
        }
        
        return [
            'success' => true,
            'message' => __('درخواست شما با موفقیت ارسال شد. با شما تماس خواهیم گرفت.', 'fasdent')
        ];
    }
    
    /**
     * Build appointment email body
     */
    private function build_appointment_email(array $data): string {
        ob_start();
        ?>
        <div style="font-family: Tahoma, Arial, sans-serif; direction: rtl; text-align: right;">
            <h2><?php esc_html_e('درخواست نوبت جدید', 'fasdent'); ?></h2>
            
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong><?php esc_html_e('نام', 'fasdent'); ?>:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo esc_html($data['name']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong><?php esc_html_e('شماره تلفن', 'fasdent'); ?>:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo esc_html($data['phone']); ?></td>
                </tr>
                <?php if (!empty($data['email'])) : ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong><?php esc_html_e('ایمیل', 'fasdent'); ?>:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo esc_html($data['email']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong><?php esc_html_e('خدمت', 'fasdent'); ?>:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo esc_html($data['service']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong><?php esc_html_e('تاریخ درخواستی', 'fasdent'); ?>:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo esc_html($data['date']); ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong><?php esc_html_e('زمان درخواستی', 'fasdent'); ?>:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo esc_html($data['time']); ?></td>
                </tr>
                <?php if (!empty($data['message'])) : ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong><?php esc_html_e('پیام', 'fasdent'); ?>:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo esc_html($data['message']); ?></td>
                </tr>
                <?php endif; ?>
            </table>
            
            <p style="color: #666; font-size: 12px;">
                <?php
                printf(
                    esc_html__('این ایمیل به صورت خودکار در %s ارسال شده است', 'fasdent'),
                    current_time('mysql')
                );
                ?>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }
}

/**
 * Helper function to check form readiness
 */
function check_appointment_form_readiness(): array {
    $forms = new Forms();
    return $forms->check_form_readiness();
}

/**
 * Helper function to get form config
 */
function get_appointment_form_config(): array {
    $forms = new Forms();
    return $forms->get_form_config();
}