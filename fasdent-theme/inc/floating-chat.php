<?php
/**
 * Customizer-driven floating contact/chat widget.
 * Customizer-driven floating contact/chat widget + Chaty compatibility.
 * Provides native floating button; CSS also supports the Chaty plugin.
 *
 * @package Fasdent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fasdent_sanitize_checkbox( $checked ) {
	return (bool) $checked;
}

function fasdent_sanitize_chat_position( $value ) {
	return in_array( $value, array( 'right', 'left' ), true ) ? $value : 'right';
}

function fasdent_sanitize_contact_value( $value ) {
	return preg_replace( '/[^0-9+@._\-a-zA-Z]/', '', (string) $value );
}

function fasdent_customize_floating_chat( $wp_customize ) {
	$wp_customize->add_section(
		'fasdent_floating_chat',
		array(
			'title'       => __( 'دکمه تماس شناور', 'fasdent' ),
			'description' => __( 'شماره‌ها و کانال‌های تماس شناور را مدیریت کنید.', 'fasdent' ),
			'title'       => __( 'دکمه تماس شناور (Native + Chaty)', 'fasdent' ),
			'description' => __( 'شماره‌ها و کانال‌های تماس شناور را مدیریت کنید. این ویجت native است و با افزونه Chaty نیز سازگار است (z-index و موقعیت).', 'fasdent' ),
			'priority'    => 145,
		)
	);

	$settings = array(
		'fasdent_chat_enabled' => array( 'default' => true, 'sanitize' => 'fasdent_sanitize_checkbox', 'label' => __( 'فعال باشد', 'fasdent' ), 'type' => 'checkbox' ),
		'fasdent_chat_position' => array( 'default' => 'right', 'sanitize' => 'fasdent_sanitize_chat_position', 'label' => __( 'جایگاه', 'fasdent' ), 'type' => 'select', 'choices' => array( 'right' => __( 'راست', 'fasdent' ), 'left' => __( 'چپ', 'fasdent' ) ) ),
		'fasdent_chat_label' => array( 'default' => __( 'ارتباط سریع', 'fasdent' ), 'sanitize' => 'sanitize_text_field', 'label' => __( 'متن دکمه', 'fasdent' ), 'type' => 'text' ),
		'fasdent_chat_title' => array( 'default' => __( 'چطور می‌توانیم کمک کنیم؟', 'fasdent' ), 'sanitize' => 'sanitize_text_field', 'label' => __( 'عنوان پنجره', 'fasdent' ), 'type' => 'text' ),
		'fasdent_chat_intro' => array( 'default' => __( 'یکی از روش‌های زیر را انتخاب کنید.', 'fasdent' ), 'sanitize' => 'sanitize_text_field', 'label' => __( 'توضیح کوتاه', 'fasdent' ), 'type' => 'text' ),
		'fasdent_chat_whatsapp' => array( 'default' => '', 'sanitize' => 'fasdent_sanitize_contact_value', 'label' => __( 'شماره واتس‌اپ با کد کشور', 'fasdent' ), 'type' => 'text' ),
		'fasdent_chat_whatsapp_message' => array( 'default' => __( 'سلام، برای دریافت مشاوره پیام می‌دهم.', 'fasdent' ), 'sanitize' => 'sanitize_textarea_field', 'label' => __( 'پیام پیش‌فرض واتس‌اپ', 'fasdent' ), 'type' => 'textarea' ),
		'fasdent_chat_phone' => array( 'default' => '', 'sanitize' => 'fasdent_sanitize_contact_value', 'label' => __( 'شماره تماس', 'fasdent' ), 'type' => 'text' ),
		'fasdent_chat_telegram' => array( 'default' => '', 'sanitize' => 'fasdent_sanitize_contact_value', 'label' => __( 'نام کاربری تلگرام', 'fasdent' ), 'type' => 'text' ),
		'fasdent_chat_email' => array( 'default' => '', 'sanitize' => 'sanitize_email', 'label' => __( 'ایمیل', 'fasdent' ), 'type' => 'email' ),
		'fasdent_chat_whatsapp' => array( 'default' => '989201441469', 'sanitize' => 'fasdent_sanitize_contact_value', 'label' => __( 'شماره واتس‌اپ با کد کشور', 'fasdent' ), 'type' => 'text' ),
		'fasdent_chat_whatsapp_message' => array( 'default' => __( 'سلام، برای دریافت مشاوره از کلینیک فس‌دنت پیام می‌دهم.', 'fasdent' ), 'sanitize' => 'sanitize_textarea_field', 'label' => __( 'پیام پیش‌فرض واتس‌اپ', 'fasdent' ), 'type' => 'textarea' ),
		'fasdent_chat_phone' => array( 'default' => '+989201441469', 'sanitize' => 'fasdent_sanitize_contact_value', 'label' => __( 'شماره تماس', 'fasdent' ), 'type' => 'text' ),
		'fasdent_chat_telegram' => array( 'default' => '', 'sanitize' => 'fasdent_sanitize_contact_value', 'label' => __( 'نام کاربری تلگرام', 'fasdent' ), 'type' => 'text' ),
		'fasdent_chat_email' => array( 'default' => 'Dr.keyvan.alipasandii@gmail.com', 'sanitize' => 'sanitize_email', 'label' => __( 'ایمیل', 'fasdent' ), 'type' => 'email' ),
	);

	foreach ( $settings as $id => $config ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => $config['sanitize'],
				'transport'         => 'refresh',
			)
		);

		$control_args = array(
			'label'   => $config['label'],
			'section' => 'fasdent_floating_chat',
			'type'    => $config['type'],
		);
		if ( isset( $config['choices'] ) ) {
			$control_args['choices'] = $config['choices'];
		}
		$wp_customize->add_control( $id, $control_args );
	}
}
add_action( 'customize_register', 'fasdent_customize_floating_chat' );

function fasdent_get_chat_channels() {
	$channels = array();
	$whatsapp = preg_replace( '/\D+/', '', (string) get_theme_mod( 'fasdent_chat_whatsapp', '' ) );
	$phone    = preg_replace( '/[^0-9+]/', '', (string) get_theme_mod( 'fasdent_chat_phone', '' ) );
	$telegram = ltrim( (string) get_theme_mod( 'fasdent_chat_telegram', '' ), '@' );
	$email    = sanitize_email( (string) get_theme_mod( 'fasdent_chat_email', '' ) );
	$whatsapp = preg_replace( '/\D+/', '', (string) get_theme_mod( 'fasdent_chat_whatsapp', '989201441469' ) );
	$phone    = preg_replace( '/[^0-9+]/', '', (string) get_theme_mod( 'fasdent_chat_phone', '+989201441469' ) );
	$telegram = ltrim( (string) get_theme_mod( 'fasdent_chat_telegram', '' ), '@' );
	$email    = sanitize_email( (string) get_theme_mod( 'fasdent_chat_email', 'Dr.keyvan.alipasandii@gmail.com' ) );

	if ( $whatsapp ) {
		$channels['whatsapp'] = array(
			'label' => __( 'واتس‌اپ', 'fasdent' ),
			'note'  => __( 'پاسخ‌گویی سریع', 'fasdent' ),
			'url'   => add_query_arg( 'text', (string) get_theme_mod( 'fasdent_chat_whatsapp_message', '' ), 'https://wa.me/' . $whatsapp ),
			'icon'  => 'fa-brands fa-whatsapp',
		);
	}
	if ( $phone ) {
		$channels['phone'] = array( 'label' => __( 'تماس تلفنی', 'fasdent' ), 'note' => $phone, 'url' => 'tel:' . $phone, 'icon' => 'fa-duotone fa-solid fa-phone-volume' );
	}
	if ( $telegram ) {
		$channels['telegram'] = array( 'label' => __( 'تلگرام', 'fasdent' ), 'note' => '@' . $telegram, 'url' => 'https://t.me/' . rawurlencode( $telegram ), 'icon' => 'fa-brands fa-telegram' );
	}
	if ( $email ) {
		$channels['email'] = array( 'label' => __( 'ایمیل', 'fasdent' ), 'note' => $email, 'url' => 'mailto:' . $email, 'icon' => 'fa-duotone fa-solid fa-envelope' );
	}

	return apply_filters( 'fasdent_floating_chat_channels', $channels );
}

function fasdent_render_floating_chat() {
	if ( ! get_theme_mod( 'fasdent_chat_enabled', true ) ) {
		return;
	}

	$channels = fasdent_get_chat_channels();
	if ( empty( $channels ) ) {
		return;
	}

	$position = fasdent_sanitize_chat_position( get_theme_mod( 'fasdent_chat_position', 'right' ) );
	$panel_id = 'fasdent-chat-panel';
	?>
	<div class="fasdent-chat fasdent-chat--<?php echo esc_attr( $position ); ?>" data-fasdent-chat>
		<div class="fasdent-chat__panel" id="<?php echo esc_attr( $panel_id ); ?>" hidden>
			<div class="fasdent-chat__header">
				<span class="fasdent-chat__avatar" aria-hidden="true"><i class="fa-duotone fa-solid fa-headset"></i></span>
				<div><h2 class="fasdent-chat__title"><?php echo esc_html( get_theme_mod( 'fasdent_chat_title', __( 'چطور می‌توانیم کمک کنیم؟', 'fasdent' ) ) ); ?></h2><p class="fasdent-chat__intro"><?php echo esc_html( get_theme_mod( 'fasdent_chat_intro', __( 'یکی از روش‌های زیر را انتخاب کنید.', 'fasdent' ) ) ); ?></p></div>
				<button class="fasdent-chat__close" type="button" data-chat-close aria-label="<?php esc_attr_e( 'بستن', 'fasdent' ); ?>"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
			</div>
			<ul class="fasdent-chat__channels">
				<?php foreach ( $channels as $key => $channel ) : ?>
					<li><a class="fasdent-chat__channel fasdent-chat__channel--<?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( $channel['url'] ); ?>"<?php echo in_array( $key, array( 'whatsapp', 'telegram' ), true ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
						<span class="fasdent-chat__channel-icon" aria-hidden="true"><i class="<?php echo esc_attr( $channel['icon'] ); ?>"></i></span>
						<span><span class="fasdent-chat__channel-label"><?php echo esc_html( $channel['label'] ); ?></span><span class="fasdent-chat__channel-note"><?php echo esc_html( $channel['note'] ); ?></span></span>
						<i class="fa-solid fa-arrow-left fasdent-chat__channel-arrow" aria-hidden="true"></i>
					</a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<button class="fasdent-chat__launcher" type="button" data-chat-toggle aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
			<span class="fasdent-chat__launcher-icon" aria-hidden="true"><i class="fa-duotone fa-solid fa-comments"></i></span>
			<span class="fasdent-chat__launcher-label"><?php echo esc_html( get_theme_mod( 'fasdent_chat_label', __( 'ارتباط سریع', 'fasdent' ) ) ); ?></span>
		</button>
	</div>
	<?php
}
add_action( 'wp_footer', 'fasdent_render_floating_chat', 30 );
