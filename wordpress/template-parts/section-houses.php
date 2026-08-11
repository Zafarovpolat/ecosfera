<?php
/**
 * Дома.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$houses = ecosfera_get_houses();
$wa     = ecosfera_whatsapp_href();
?>
<section class="houses" aria-labelledby="houses-heading" id="doma">
	<div class="container">

		<h2 id="houses-heading" class="sr-only"><?php esc_html_e( 'Наши дома', 'ecosfera' ); ?></h2>

		<ul class="houses__list">
			<?php foreach ( $houses as $house ) : ?>
				<?php
				$slug     = $house['slug'] ?? 'house';
				$gallery  = $house['gallery'] ?? array();
				$total    = max( 1, count( $gallery ) );
				$reversed = ! empty( $house['reversed'] );
				$article  = 'houses__card' . ( $reversed ? ' houses__card--reversed' : '' );
				$amenities = $house['amenities'] ?? array();
				?>
				<li>
					<article class="<?php echo esc_attr( $article ); ?>" id="<?php echo esc_attr( $slug ); ?>">

						<div class="houses__gallery-panel">
							<div class="houses__gallery" aria-roledescription="carousel" aria-label="<?php echo esc_attr( sprintf( __( 'Фотографии — %s', 'ecosfera' ), $house['title'] ) ); ?>">
								<div id="gallery-<?php echo esc_attr( $slug ); ?>-live" aria-live="polite" aria-atomic="true" class="houses__gallery-live">
									<?php echo esc_html( sprintf( __( 'Слайд 1 из %d', 'ecosfera' ), $total ) ); ?>
								</div>

								<div class="houses__gallery-track">
									<?php foreach ( $gallery as $i => $src ) : ?>
										<div
											class="houses__slide"
											role="group"
											aria-roledescription="slide"
											aria-label="<?php echo esc_attr( sprintf( __( 'Слайд %1$d из %2$d', 'ecosfera' ), $i + 1, $total ) ); ?>"
											<?php echo $i === 0 ? 'data-active' : 'aria-hidden="true"'; ?>
										>
											<img
												src="<?php echo esc_url( ecosfera_src( $src ) ); ?>"
												alt="<?php echo $i === 0 ? esc_attr( $house['title'] ) : ''; ?>"
												width="909"
												height="750"
												loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>"
												decoding="async"
												class="houses__slide-img"
											/>
										</div>
									<?php endforeach; ?>
								</div>

								<div class="houses__gallery-arrows">
									<img src="<?php echo esc_url( ecosfera_asset( 'icons/arrows.svg' ) ); ?>" alt="" class="houses__gallery-arrows-img" aria-hidden="true">
									<button type="button" class="houses__gallery-arrow-btn houses__gallery-arrow-btn--prev" aria-label="<?php esc_attr_e( 'Предыдущий слайд', 'ecosfera' ); ?>"></button>
									<button type="button" class="houses__gallery-arrow-btn houses__gallery-arrow-btn--next" aria-label="<?php esc_attr_e( 'Следующий слайд', 'ecosfera' ); ?>"></button>
								</div>

								<div class="houses__gallery-dots" role="tablist" aria-label="<?php esc_attr_e( 'Слайды', 'ecosfera' ); ?>">
									<?php for ( $i = 0; $i < $total; $i++ ) : ?>
										<button
											type="button"
											role="tab"
											aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
											<?php echo $i === 0 ? 'aria-current="true" data-active' : ''; ?>
											aria-label="<?php echo esc_attr( sprintf( __( 'Перейти к слайду %d', 'ecosfera' ), $i + 1 ) ); ?>"
											class="houses__gallery-dot"
										></button>
									<?php endfor; ?>
								</div>
							</div>
						</div>

						<div class="houses__content">
							<header class="houses__title-block">
								<h3 class="houses__title"><?php echo esc_html( $house['title'] ); ?></h3>
								<ul class="houses__specs" aria-label="<?php esc_attr_e( 'Характеристики дома', 'ecosfera' ); ?>">
									<li class="houses__spec-item">
										<?php echo esc_html( $house['rooms'] ); ?>
										<span class="houses__spec-divider" aria-hidden="true"></span>
									</li>
									<li class="houses__spec-item">
										<?php echo esc_html( $house['adults'] ); ?>
										<span class="houses__spec-divider" aria-hidden="true"></span>
									</li>
									<li class="houses__spec-item"><?php echo esc_html( $house['children'] ); ?></li>
								</ul>
							</header>

							<section class="houses__prices-section" aria-label="<?php esc_attr_e( 'Стоимость аренды', 'ecosfera' ); ?>">
								<ul class="houses__price-list">
									<?php
									$prices = array(
										array( 'period' => __( 'Вс–Чт', 'ecosfera' ), 'amount' => $house['price_weekday'] ),
										array( 'period' => __( 'Пт–Сб', 'ecosfera' ), 'amount' => $house['price_weekend'] ),
									);
									foreach ( $prices as $price ) :
										?>
										<li class="houses__price-item">
											<article class="houses__price-card">
												<p class="houses__price-period"><?php echo esc_html( $price['period'] ); ?></p>
												<dl class="houses__price-block">
													<div class="houses__price-row">
														<dt class="sr-only"><?php esc_html_e( 'Стоимость', 'ecosfera' ); ?></dt>
														<dd class="houses__price-amount">
															<span class="houses__price-number"><?php echo esc_html( $price['amount'] ); ?></span>
															<span class="houses__price-currency"><?php esc_html_e( 'руб.', 'ecosfera' ); ?></span>
														</dd>
													</div>
													<div class="houses__price-unit-row">
														<dt class="sr-only"><?php esc_html_e( 'Период', 'ecosfera' ); ?></dt>
														<dd class="houses__price-unit"><?php esc_html_e( 'за сутки', 'ecosfera' ); ?></dd>
													</div>
												</dl>
												<img src="<?php echo esc_url( ecosfera_asset( 'icons/frame-1000005436.svg' ) ); ?>" alt="" aria-hidden="true" width="54" height="118" loading="lazy" decoding="async" class="houses__price-arrow" />
											</article>
										</li>
									<?php endforeach; ?>
								</ul>

								<ul class="houses__amenities" aria-label="<?php esc_attr_e( 'Удобства', 'ecosfera' ); ?>">
									<?php foreach ( $amenities as $amenity ) : ?>
										<li class="houses__amenity-tile">
											<img src="<?php echo esc_url( ecosfera_src( $amenity['icon'] ?? '' ) ); ?>" alt="" aria-hidden="true" width="68" height="48" loading="lazy" decoding="async" class="houses__amenity-icon" />
											<span class="houses__amenity-label"><?php echo esc_html( $amenity['label'] ?? '' ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							</section>

							<div class="houses__buttons">
								<a
									href="<?php echo esc_url( ecosfera_home_hash( 'booking' ) ); ?>"
									class="houses__book-btn"
									data-house="<?php echo esc_attr( $slug ); ?>"
									aria-label="<?php echo esc_attr( sprintf( __( 'Забронировать %s', 'ecosfera' ), $house['title'] ) ); ?>"
								>
									<svg class="houses__corner-tl" width="9" height="8" viewBox="0 0 9 8" fill="none" aria-hidden="true">
										<path d="M1 8V1H9" stroke="currentColor" stroke-width="1" fill="none"/>
									</svg>
									<span class="houses__book-label"><?php esc_html_e( 'Забронировать дом', 'ecosfera' ); ?></span>
									<svg class="houses__corner-br" width="9" height="8" viewBox="0 0 9 8" fill="none" aria-hidden="true">
										<path d="M8 0V7H0" stroke="currentColor" stroke-width="1" fill="none"/>
									</svg>
								</a>

								<?php if ( $wa ) : ?>
									<a
										href="<?php echo esc_url( $wa ); ?>"
										target="_blank"
										rel="noopener noreferrer"
										class="houses__whatsapp-btn"
										aria-label="<?php echo esc_attr( sprintf( __( 'Написать в WhatsApp по поводу %s', 'ecosfera' ), $house['title'] ) ); ?>"
									>
										<img src="<?php echo esc_url( ecosfera_asset( 'icons/frame-1000005480.svg' ) ); ?>" alt="" aria-hidden="true" width="93" height="98" decoding="async" class="houses__whatsapp-icon" />
									</a>
								<?php endif; ?>
							</div>

							<?php if ( ! empty( $house['footnote'] ) ) : ?>
								<p class="houses__footnote">
									<img src="<?php echo esc_url( ecosfera_asset( 'icons/icon-2.svg' ) ); ?>" alt="*" width="12" height="13" aria-hidden="true" class="houses__footnote-icon" />
									<?php echo esc_html( $house['footnote'] ); ?>
								</p>
							<?php endif; ?>
						</div>
					</article>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
