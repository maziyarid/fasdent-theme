<?php
/**
 * The front page template file
 *
 * @package FasDent
 * @version 1.4.29
 * @change  Removed hard-coded address fallback, SSOT-only display
 */

declare(strict_types=1);

namespace Alipasandi\FasDent;

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

get_header();

/**
 * Get operational data from SSOT (Plugin settings)
 * CRITICAL: NO FALLBACK VALUES - SSOT contract only
 * If data is missing, show non-operational UI, not template defaults
 */
$clinic_name    = get_option('alipasandi_business_name', '');
$clinic_street  = get_option('alipasandi_clinic_street', '');
$clinic_city    = get_option('alipasandi_clinic_city', '');
$clinic_region  = get_option('alipasandi_clinic_region', '');
$clinic_country = get_option('alipasandi_clinic_country', '');
$clinic_phone   = get_option('alipasandi_clinic_phone', '');

/**
 * Build complete address only if ALL required fields exist (SSOT contract)
 * Required: street, city, region (country optional)
 */
$complete_address = '';
if (!empty($clinic_street) && !empty($clinic_city) && !empty($clinic_region)) {
    $complete_address = trim($clinic_street . ', ' . $clinic_city . ', ' . $clinic_region);
    if (!empty($clinic_country)) {
        $complete_address .= ', ' . $clinic_country;
    }
}

/**
 * IMPORTANT: No hard-coded fallback
 * If address is incomplete, show non-operational state
 * This prevents NAP drift between Home, Contact, Footer, and Schema
 */
$address_display = !empty($complete_address) ? $complete_address : '';
$address_missing = empty($complete_address);
?>

<div class="homepage-hero">
    <div class="container">
        <h1 class="clinic-name">
            <?php echo esc_html(!empty($clinic_name) ? $clinic_name : 'کلینیک دندانپزشکی فاسدنت'); ?>
        </h1>
        
        <?php if (!$address_missing) : ?>
            <div class="clinic-address" itemscope itemtype="https://schema.org/PostalAddress">
                <span class="address-icon" aria-hidden="true">📍</span>
                <span class="address-text" itemprop="streetAddress">
                    <?php echo esc_html($address_display); ?>
                </span>
            </div>
        <?php else : ?>
            <div class="clinic-address-missing" role="alert" aria-live="polite">
                <span class="address-icon" aria-hidden="true">⚠️</span>
                <span class="address-text">
                    <?php esc_html_e('آدرس در حال بارگذاری...', 'fasdent'); ?>
                </span>
                <span class="screen-reader-text">
                    <?php esc_html_e('آدرس کلینیک هنوز تنظیم نشده است', 'fasdent'); ?>
                </span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($clinic_phone)) : ?>
            <div class="clinic-phone">
                <a href="tel:<?php echo esc_attr($clinic_phone); ?>" itemprop="telephone">
                    <span class="phone-icon" aria-hidden="true">📞</span>
                    <span class="phone-text"><?php echo esc_html($clinic_phone); ?></span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <h2><?php esc_html_e('خدمات ما', 'fasdent'); ?></h2>
        <div class="services-grid">
            <!-- Services will be loaded from SSOT -->
            <?php
            // Load services from plugin registry
            if (function_exists('\Alipasandi\FasDent\get_services')) {
                $services = \Alipasandi\FasDent\get_services();
                foreach ($services as $service) {
                    echo '<div class="service-card">';
                    echo '<h3>' . esc_html($service['name']) . '</h3>';
                    echo '<p>' . esc_html($service['description']) . '</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2><?php esc_html_e('همین حالا نوبت بگیرید', 'fasdent'); ?></h2>
        <?php if (!empty($clinic_phone)) : ?>
            <a href="tel:<?php echo esc_attr($clinic_phone); ?>" class="btn btn-primary">
                <?php esc_html_e('تماس تلفنی', 'fasdent'); ?>
            </a>
        <?php endif; ?>
        <?php
        $appointments_page = get_page_by_path('appointments');
        if ($appointments_page) :
        ?>
            <a href="<?php echo get_permalink($appointments_page); ?>" class="btn btn-secondary">
                <?php esc_html_e('رزرو آنلاین', 'fasdent'); ?>
            </a>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();