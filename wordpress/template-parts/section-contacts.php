<?php
/**
 * Контакты и карта.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$map     = ecosfera_get( 'map_iframe' );
$yandex  = ecosfera_get( 'map_yandex' );
$gis     = ecosfera_get( 'map_2gis' );
$heading = ecosfera_get( 'contacts_heading' );
?>
<section id="contacts" class="contacts" aria-labelledby="contacts-heading">

	<figure class="contacts__map-wrap">
		<div class="contacts__map-embed">
			<?php if ( $map ) : ?>
				<iframe
					src="<?php echo esc_url( $map ); ?>"
					class="contacts__map-frame"
					title="<?php esc_attr_e( 'Карта проезда к Экосфере', 'ecosfera' ); ?>"
					loading="lazy"
					allowfullscreen
				></iframe>
			<?php endif; ?>
		</div>

		<figcaption class="contacts__panel">
			<h2 id="contacts-heading" class="contacts__heading"><?php echo esc_html( $heading ); ?></h2>

			<ul class="contacts__block-list" role="list">
				<li class="contacts__block">
					<span class="contacts__icon-wrap contacts__icon-wrap--amber" aria-hidden="true">
						<img src="<?php echo esc_url( ecosfera_asset( 'icons/frame-387-2.svg' ) ); ?>" alt="" aria-hidden="true" width="66" height="66" loading="lazy" decoding="async" />
					</span>
					<address class="contacts__block-text">
						<p class="contacts__block-title"><?php esc_html_e( 'Адрес', 'ecosfera' ); ?></p>
						<p class="contacts__block-desc"><?php echo esc_html( ecosfera_get( 'address_full' ) ); ?></p>
					</address>
				</li>

				<li class="contacts__block">
					<span class="contacts__icon-wrap contacts__icon-wrap--black" aria-hidden="true">
						<img src="<?php echo esc_url( ecosfera_asset( 'icons/frame-387-3.svg' ) ); ?>" alt="" aria-hidden="true" width="66" height="66" loading="lazy" decoding="async" />
					</span>
					<div class="contacts__block-text">
						<p class="contacts__block-title"><?php esc_html_e( 'На машине', 'ecosfera' ); ?></p>
						<p class="contacts__block-desc"><?php echo esc_html( ecosfera_get( 'directions' ) ); ?></p>
					</div>
				</li>

				<li class="contacts__block">
					<span class="contacts__icon-wrap contacts__icon-wrap--amber" aria-hidden="true">
						<img src="<?php echo esc_url( ecosfera_asset( 'icons/frame-387-4.svg' ) ); ?>" alt="" aria-hidden="true" width="66" height="66" loading="lazy" decoding="async" />
					</span>
					<div class="contacts__block-text">
						<p class="contacts__block-title"><?php esc_html_e( 'Нет машины?', 'ecosfera' ); ?></p>
						<p class="contacts__block-desc"><?php echo esc_html( ecosfera_get( 'no_car' ) ); ?></p>
					</div>
				</li>
			</ul>

			<ul class="contacts__map-links" role="list">
				<?php if ( $yandex ) : ?>
					<li>
						<a href="<?php echo esc_url( $yandex ); ?>" class="contacts__map-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Открыть маршрут в Яндекс Карты', 'ecosfera' ); ?>">
							<img src="<?php echo esc_url( ecosfera_asset( 'icons/frame-419.svg' ) ); ?>" alt="<?php esc_attr_e( 'Яндекс Карты', 'ecosfera' ); ?>" width="150" height="69" loading="lazy" decoding="async" />
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $gis ) : ?>
					<li>
						<a href="<?php echo esc_url( $gis ); ?>" class="contacts__map-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Открыть маршрут в 2ГИС', 'ecosfera' ); ?>">
							<img src="<?php echo esc_url( ecosfera_asset( 'icons/frame-420.svg' ) ); ?>" alt="<?php esc_attr_e( '2ГИС', 'ecosfera' ); ?>" width="150" height="70" loading="lazy" decoding="async" />
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</figcaption>
	</figure>

</section>
