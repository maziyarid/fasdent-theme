<?php
/**
 * Homepage doctor intro card.
 * @package Fasdent
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$doctor = get_posts( array( 'post_type' => 'doctor', 'numberposts' => 1, 'post_status' => 'publish' ) );
$doctor = $doctor ? $doctor[0] : null;
if ( ! $doctor ) {
	return;
}

$d_title   = function_exists( 'fasdent_field' ) ? ( fasdent_field( 'doctor_title', $doctor->ID ) ?: 'دکتری حرفه‌ای (ایمپلنتولوژیست)' ) : 'دکتری حرفه‌ای (ایمپلنتولوژیست)';
$d_license = function_exists( 'fasdent_field' ) ? fasdent_field( 'doctor_license', $doctor->ID ) : '';
$d_years   = function_exists( 'fasdent_field' ) ? fasdent_field( 'doctor_years', $doctor->ID ) : '';
$photo_url = '';
if ( has_post_thumbnail( $doctor->ID ) ) {
	$photo_url = get_the_post_thumbnail_url( $doctor->ID, 'large' );
}
if ( ! $photo_url ) {
	$photo_url = content_url( '/uploads/2026/07/Dr.keyvan_alipasandi.webp' );
}
?>
<section class="doctor-intro-section section" aria-labelledby="doctor-intro-title">
	<div class="container">
		<div class="doctor-intro-card">
			<div class="doctor-intro-card__photo">
				<img
					src="<?php echo esc_url( $photo_url ); ?>"
					alt="<?php echo esc_attr( $doctor->post_title ); ?>"
					class="doctor-photo"
					width="220"
					height="220"
					loading="lazy"
					decoding="async"
				>
			</div>
			<div class="doctor-intro-card__content">
				<p class="eyebrow"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i> <?php esc_html_e( 'متخصص کلینیک', 'fasdent' ); ?></p>
				<h2 id="doctor-intro-title">
					<a href="<?php echo esc_url( get_permalink( $doctor ) ); ?>"><?php echo esc_html( $doctor->post_title ); ?></a>
				</h2>
				<p class="doctor-speciality"><?php echo esc_html( $d_title ); ?></p>
				<div class="doctor-meta">
					<?php if ( $d_license ) : ?>
						<span><i class="fa-solid fa-certificate" aria-hidden="true"></i> <?php esc_html_e( 'نظام پزشکی:', 'fasdent' ); ?> <?php echo esc_html( $d_license ); ?></span>
					<?php endif; ?>
					<?php if ( $d_years ) : ?>
						<span><i class="fa-solid fa-star" aria-hidden="true"></i> <?php echo esc_html( $d_years ); ?> <?php esc_html_e( 'سال تجربه', 'fasdent' ); ?></span>
					<?php else : ?>
						<span><i class="fa-solid fa-star" aria-hidden="true"></i> <?php esc_html_e( 'بیش از ۱۰ سال سابقه', 'fasdent' ); ?></span>
					<?php endif; ?>
				</div>
				<div class="prose"><?php echo wp_kses_post( wp_trim_words( get_post_field( 'post_content', $doctor->ID ), 45 ) ); ?></div>
				<a href="<?php echo esc_url( get_permalink( $doctor ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'پروفایل کامل پزشک', 'fasdent' ); ?></a>
			</div>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
