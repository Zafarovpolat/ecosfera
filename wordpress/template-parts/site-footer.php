<?php
/**
 * Подвал сайта.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$phone   = ecosfera_get( 'phone' );
$email   = ecosfera_get( 'email' );
$tel     = ecosfera_tel_href( $phone );
$wa      = ecosfera_whatsapp_href();
$ig      = ecosfera_get( 'instagram' );
$max     = ecosfera_get( 'max' );
$address = ecosfera_get( 'address_short' );
$lines   = preg_split( '/\r\n|\r|\n/', (string) $address );
$privacy = ecosfera_page_url( 'privacy', home_url( '/privacy/' ) );
$oferta  = ecosfera_page_url( 'oferta', home_url( '/oferta/' ) );
$terms   = ecosfera_page_url( 'terms', home_url( '/terms/' ) );
?>
<footer class="site-footer">
	<div class="site-footer__inner">

		<div class="site-footer__top">
			<div class="site-footer__contacts">

				<div class="site-footer__contact-item">
					<span class="site-footer__contact-label"><?php esc_html_e( 'Номер телефона', 'ecosfera' ); ?></span>
					<address class="site-footer__contact-value">
						<a href="<?php echo esc_url( $tel ); ?>" class="site-footer__phone-link"><?php echo esc_html( $phone ); ?></a>
					</address>
				</div>

				<div class="site-footer__contact-item">
					<span class="site-footer__contact-label"><?php esc_html_e( 'Адрес', 'ecosfera' ); ?></span>
					<address class="site-footer__contact-value">
						<?php foreach ( $lines as $i => $line ) : ?>
							<?php if ( $i > 0 ) : ?><br /><?php endif; ?>
							<span><?php echo esc_html( $line ); ?></span>
						<?php endforeach; ?>
					</address>
				</div>

				<div class="site-footer__contact-item">
					<span class="site-footer__contact-label"><?php esc_html_e( 'Электронный адрес', 'ecosfera' ); ?></span>
					<address class="site-footer__contact-value">
						<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="site-footer__email-link"><?php echo esc_html( $email ); ?></a>
					</address>
				</div>

				<ul class="site-footer__social-desktop" role="list">
					<?php if ( $wa ) : ?>
						<li>
							<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener noreferrer" class="site-footer__social-btn site-footer__social-btn--whatsapp" aria-label="WhatsApp"></a>
						</li>
					<?php endif; ?>
					<?php if ( $ig ) : ?>
						<li>
							<a href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener noreferrer" class="site-footer__social-btn site-footer__social-btn--instagram" aria-label="Instagram"></a>
						</li>
					<?php endif; ?>
					<?php if ( $max ) : ?>
						<li>
							<a href="<?php echo esc_url( $max ); ?>" target="_blank" rel="noopener noreferrer" class="site-footer__social-btn site-footer__social-btn--telegram" aria-label="Max"></a>
						</li>
					<?php endif; ?>
				</ul>
			</div>

			<div class="site-footer__nav-columns">
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<nav aria-label="<?php esc_attr_e( 'Основные разделы', 'ecosfera' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container'      => false,
								'menu_class'     => 'site-footer__nav-list',
								'items_wrap'     => '<ul class="%2$s" role="list">%3$s</ul>',
								'depth'          => 1,
							)
						);
						?>
					</nav>
				<?php else : ?>
					<nav aria-label="<?php esc_attr_e( 'Основные разделы', 'ecosfera' ); ?>">
						<ul class="site-footer__nav-list" role="list">
							<li><a href="<?php echo esc_url( ecosfera_home_hash( 'doma' ) ); ?>" class="site-footer__nav-link"><?php esc_html_e( 'Дома', 'ecosfera' ); ?></a></li>
							<li><a href="<?php echo esc_url( ecosfera_home_hash( 'uslugi' ) ); ?>" class="site-footer__nav-link"><?php esc_html_e( 'Услуги', 'ecosfera' ); ?></a></li>
							<li><a href="<?php echo esc_url( ecosfera_home_hash( 'gallery' ) ); ?>" class="site-footer__nav-link"><?php esc_html_e( 'Галерея', 'ecosfera' ); ?></a></li>
							<li><a href="<?php echo esc_url( ecosfera_home_hash( 'faq' ) ); ?>" class="site-footer__nav-link"><?php esc_html_e( 'Вопросы', 'ecosfera' ); ?></a></li>
							<li><a href="<?php echo esc_url( ecosfera_home_hash( 'reviews' ) ); ?>" class="site-footer__nav-link"><?php esc_html_e( 'Отзывы', 'ecosfera' ); ?></a></li>
						</ul>
					</nav>
				<?php endif; ?>

				<?php if ( has_nav_menu( 'footer2' ) ) : ?>
					<nav aria-label="<?php esc_attr_e( 'Дополнительные разделы', 'ecosfera' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer2',
								'container'      => false,
								'menu_class'     => 'site-footer__nav-list',
								'items_wrap'     => '<ul class="%2$s" role="list">%3$s</ul>',
								'depth'          => 1,
							)
						);
						?>
					</nav>
				<?php else : ?>
					<nav aria-label="<?php esc_attr_e( 'Дополнительные разделы', 'ecosfera' ); ?>">
						<ul class="site-footer__nav-list" role="list">
							<li><a href="<?php echo esc_url( ecosfera_home_hash( 'audience' ) ); ?>" class="site-footer__nav-link"><?php esc_html_e( 'Для кого', 'ecosfera' ); ?></a></li>
							<li><a href="<?php echo esc_url( ecosfera_home_hash( 'booking' ) ); ?>" class="site-footer__nav-link"><?php esc_html_e( 'Забронировать', 'ecosfera' ); ?></a></li>
						</ul>
					</nav>
				<?php endif; ?>
			</div>
		</div>

		<hr class="site-footer__divider" />

		<div class="site-footer__bottom">
			<p class="site-footer__copyright"><?php echo esc_html( ecosfera_get( 'copyright' ) ); ?></p>

			<ul class="site-footer__legal-links" role="list">
				<li><a href="<?php echo esc_url( $oferta ); ?>" class="site-footer__legal-link"><?php esc_html_e( 'Оферта', 'ecosfera' ); ?></a></li>
				<li><a href="<?php echo esc_url( $privacy ); ?>" class="site-footer__legal-link"><?php esc_html_e( 'Политика конфиденциальности', 'ecosfera' ); ?></a></li>
				<li><a href="<?php echo esc_url( $terms ); ?>" class="site-footer__legal-link"><?php esc_html_e( 'Пользовательское соглашение', 'ecosfera' ); ?></a></li>
			</ul>

			<p class="site-footer__made-by">
				<span class="site-footer__made-by-text"><?php esc_html_e( 'Сделано в', 'ecosfera' ); ?></span>
				<a href="<?php echo esc_url( ecosfera_get( 'made_by_url' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Студия RUSO', 'ecosfera' ); ?>" class="site-footer__ruso-link">
					<img src="<?php echo esc_url( ecosfera_asset( 'icons/873de29f39d705685f8db2b2916e0d4b-ruso-1.svg' ) ); ?>" alt="RUSO" width="58" height="14" loading="lazy" decoding="async" />
				</a>
			</p>
		</div>
	</div>
</footer>
