<?php
/**
 * Шапка поверх героя / внутренних страниц.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$phone     = ecosfera_get( 'phone' );
$email     = ecosfera_get( 'email' );
$tel       = ecosfera_tel_href( $phone );
$logo_id   = get_theme_mod( 'custom_logo' );
$logo_src  = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : ecosfera_src( ecosfera_get( 'logo' ) );
$home      = home_url( '/' );
?>
<header class="header">

	<div class="header__logo-wrap">
		<a href="<?php echo esc_url( $home ); ?>" class="header__logo-link">
			<img
				src="<?php echo esc_url( $logo_src ); ?>"
				alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				width="114"
				height="97"
				loading="eager"
				decoding="async"
				class="header__logo"
			/>
		</a>
	</div>

	<nav
		id="mobile-menu"
		aria-label="<?php esc_attr_e( 'Основная навигация', 'ecosfera' ); ?>"
		class="header__nav"
	>
		<?php ecosfera_primary_menu(); ?>

		<div class="header__nav-extra">
			<address class="header__contact-item">
				<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="header__contact-value">
					<?php echo esc_html( $email ); ?>
				</a>
				<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="header__contact-label"><?php esc_html_e( 'Написать нам', 'ecosfera' ); ?></a>
			</address>

			<address class="header__contact-item">
				<a href="<?php echo esc_url( $tel ); ?>" class="header__contact-value">
					<?php echo esc_html( $phone ); ?>
				</a>
				<a href="<?php echo esc_url( $tel ); ?>" class="header__contact-label"><?php esc_html_e( 'Заказать звонок', 'ecosfera' ); ?></a>
			</address>

			<a href="<?php echo esc_url( ecosfera_home_hash( 'booking' ) ); ?>" class="header__cta">
				<svg class="header__cta-corner header__cta-corner--tl" width="9" height="8" viewBox="0 0 9 8" fill="none" aria-hidden="true" focusable="false">
					<path d="M1 8V1H9" stroke="currentColor" stroke-width="1" />
				</svg>
				<span><?php esc_html_e( 'Забронировать дом', 'ecosfera' ); ?></span>
				<svg class="header__cta-corner header__cta-corner--br" width="9" height="8" viewBox="0 0 9 8" fill="none" aria-hidden="true" focusable="false">
					<path d="M8 0V7H0" stroke="currentColor" stroke-width="1" />
				</svg>
			</a>
		</div>
	</nav>

	<div class="header__contacts">
		<div class="header__contacts-group">
			<address class="header__contact-item">
				<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="header__contact-value">
					<?php echo esc_html( $email ); ?>
				</a>
				<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="header__contact-label"><?php esc_html_e( 'Написать нам', 'ecosfera' ); ?></a>
			</address>

			<span class="header__contacts-divider" aria-hidden="true"></span>

			<address class="header__contact-item">
				<a href="<?php echo esc_url( $tel ); ?>" class="header__contact-value">
					<?php echo esc_html( $phone ); ?>
				</a>
				<a href="<?php echo esc_url( $tel ); ?>" class="header__contact-label"><?php esc_html_e( 'Заказать звонок', 'ecosfera' ); ?></a>
			</address>
		</div>

		<a href="<?php echo esc_url( ecosfera_home_hash( 'booking' ) ); ?>" class="header__cta">
			<svg class="header__cta-corner header__cta-corner--tl" width="9" height="8" viewBox="0 0 9 8" fill="none" aria-hidden="true" focusable="false">
				<path d="M1 8V1H9" stroke="currentColor" stroke-width="1" />
			</svg>
			<span><?php esc_html_e( 'Забронировать дом', 'ecosfera' ); ?></span>
			<svg class="header__cta-corner header__cta-corner--br" width="9" height="8" viewBox="0 0 9 8" fill="none" aria-hidden="true" focusable="false">
				<path d="M8 0V7H0" stroke="currentColor" stroke-width="1" />
			</svg>
		</a>
	</div>

	<div class="header__mobile-actions">
		<address class="header__contact-item">
			<a href="<?php echo esc_url( $tel ); ?>" class="header__contact-value">
				<?php echo esc_html( $phone ); ?>
			</a>
			<a href="<?php echo esc_url( $tel ); ?>" class="header__contact-label"><?php esc_html_e( 'Заказать звонок', 'ecosfera' ); ?></a>
		</address>

		<button
			type="button"
			class="header__burger"
			aria-expanded="false"
			aria-controls="mobile-menu"
			aria-label="<?php esc_attr_e( 'Открыть меню', 'ecosfera' ); ?>"
		>
			<span class="header__burger-box" aria-hidden="true">
				<span class="header__burger-bar"></span>
				<span class="header__burger-bar"></span>
			</span>
			<span class="header__burger-label"><?php esc_html_e( 'Меню', 'ecosfera' ); ?></span>
		</button>
	</div>

</header>
