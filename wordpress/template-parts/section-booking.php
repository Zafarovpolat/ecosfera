<?php
/**
 * Форма бронирования.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$houses  = ecosfera_get_houses();
$guests  = ecosfera_get( 'guest_options' );
if ( ! is_array( $guests ) ) {
	$guests = ecosfera_defaults()['guest_options'];
}
$status  = isset( $_GET['booking'] ) ? sanitize_key( wp_unslash( $_GET['booking'] ) ) : '';
$privacy = ecosfera_page_url( 'privacy', home_url( '/privacy/' ) );
?>
<section class="booking" id="booking" aria-labelledby="booking-heading">
	<div class="container">
		<div class="booking__glass">

			<div class="booking__top-row">
				<h2 id="booking-heading" class="section-heading">
					<?php esc_html_e( 'Забронировать', 'ecosfera' ); ?> <span class="section-heading__accent section-heading__accent--inline"><?php esc_html_e( 'дом', 'ecosfera' ); ?></span>
				</h2>

				<div class="booking__payment-badge">
					<span class="booking__payment-icon" aria-hidden="true">
						<img src="<?php echo esc_url( ecosfera_asset( 'icons/frame-387.svg' ) ); ?>" alt="" width="24" height="24" loading="lazy" decoding="async" />
					</span>
					<div class="booking__payment-text">
						<strong class="booking__payment-title"><?php echo esc_html( ecosfera_get( 'booking_prepay' ) ); ?></strong>
						<p class="booking__payment-detail"><?php echo esc_html( ecosfera_get( 'booking_prepay_d' ) ); ?></p>
					</div>
				</div>
			</div>

			<div class="booking__form-panel">
				<p class="booking__form-title" aria-hidden="true"><?php esc_html_e( 'Проверить наличие', 'ecosfera' ); ?></p>

				<?php if ( $status === 'ok' ) : ?>
					<p class="booking__notice booking__notice--ok" role="status"><?php esc_html_e( 'Заявка отправлена. Мы свяжемся с вами, чтобы подтвердить бронь.', 'ecosfera' ); ?></p>
				<?php elseif ( $status === 'err' ) : ?>
					<p class="booking__notice booking__notice--err" role="alert"><?php esc_html_e( 'Не удалось отправить заявку. Проверьте поля или напишите нам напрямую.', 'ecosfera' ); ?></p>
				<?php endif; ?>

				<form class="booking__form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate>
					<input type="hidden" name="action" value="ecosfera_booking">
					<?php wp_nonce_field( 'ecosfera_booking', 'ecosfera_booking_nonce' ); ?>

					<div class="booking__row-wide">
						<div class="booking__field">
							<label id="booking-house-label" class="booking__field-label" for="booking-house"><?php esc_html_e( 'Выберите дом', 'ecosfera' ); ?></label>
							<div class="booking__select-wrapper">
								<select id="booking-house" name="house" required aria-invalid="false" aria-describedby="booking-house-error" class="booking__select">
									<option value="" disabled selected hidden><?php esc_html_e( 'Выберите дом', 'ecosfera' ); ?></option>
									<?php foreach ( $houses as $house ) : ?>
										<option value="<?php echo esc_attr( $house['slug'] ); ?>"><?php echo esc_html( $house['title'] ); ?></option>
									<?php endforeach; ?>
								</select>
								<span class="booking__select-arrow" aria-hidden="true">
									<img src="<?php echo esc_url( ecosfera_asset( 'icons/vector.svg' ) ); ?>" alt="" width="6" height="12" />
								</span>
								<span id="booking-house-error" class="booking__field-error" role="alert" hidden></span>
							</div>
						</div>
					</div>

					<div class="booking__row-dates">
						<div class="booking__field">
							<label id="booking-check-in-label" class="booking__field-label" for="booking-check-in"><?php esc_html_e( 'Дата заезда', 'ecosfera' ); ?></label>
							<div class="booking__input-wrapper">
								<input id="booking-check-in" name="checkIn" type="text" data-date inputmode="numeric" placeholder="дд.мм.гггг" required aria-invalid="false" aria-describedby="booking-check-in-error" class="booking__input" />
								<span class="booking__input-icon" aria-hidden="true">
									<img src="<?php echo esc_url( ecosfera_asset( 'icons/vuesax-bulk-calendar.svg' ) ); ?>" alt="" width="24" height="24" loading="lazy" decoding="async" />
								</span>
								<span id="booking-check-in-error" class="booking__field-error" role="alert" hidden></span>
							</div>
						</div>

						<div class="booking__field">
							<label id="booking-check-out-label" class="booking__field-label" for="booking-check-out"><?php esc_html_e( 'Дата выезда', 'ecosfera' ); ?></label>
							<div class="booking__input-wrapper">
								<input id="booking-check-out" name="checkOut" type="text" data-date inputmode="numeric" placeholder="дд.мм.гггг" required aria-invalid="false" aria-describedby="booking-check-out-error" class="booking__input" />
								<span class="booking__input-icon" aria-hidden="true">
									<img src="<?php echo esc_url( ecosfera_asset( 'icons/vuesax-bulk-calendar.svg' ) ); ?>" alt="" width="24" height="24" loading="lazy" decoding="async" />
								</span>
								<span id="booking-check-out-error" class="booking__field-error" role="alert" hidden></span>
							</div>
						</div>
					</div>

					<div class="booking__row-wide">
						<div class="booking__field">
							<label id="booking-guests-label" class="booking__field-label" for="booking-guests"><?php esc_html_e( 'Количество гостей', 'ecosfera' ); ?></label>
							<div class="booking__select-wrapper">
								<select id="booking-guests" name="guests" required aria-invalid="false" aria-describedby="booking-guests-error" class="booking__select">
									<option value="" disabled selected hidden><?php esc_html_e( 'Количество гостей', 'ecosfera' ); ?></option>
									<?php foreach ( $guests as $value => $label ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
								<span class="booking__select-arrow" aria-hidden="true">
									<img src="<?php echo esc_url( ecosfera_asset( 'icons/vector.svg' ) ); ?>" alt="" width="6" height="12" />
								</span>
								<span id="booking-guests-error" class="booking__field-error" role="alert" hidden></span>
							</div>
						</div>
					</div>

					<div class="booking__row-dates booking__row-contacts">
						<div class="booking__field">
							<label id="booking-name-label" class="booking__field-label" for="booking-name"><?php esc_html_e( 'Ваше имя', 'ecosfera' ); ?></label>
							<div class="booking__input-wrapper">
								<input id="booking-name" name="name" type="text" placeholder=" " autocomplete="given-name" required aria-invalid="false" aria-describedby="booking-name-error" class="booking__input" />
								<span id="booking-name-error" class="booking__field-error" role="alert" hidden></span>
							</div>
						</div>

						<div class="booking__field">
							<label id="booking-phone-label" class="booking__field-label" for="booking-phone"><?php esc_html_e( 'Ваш телефон', 'ecosfera' ); ?></label>
							<div class="booking__input-wrapper">
								<input id="booking-phone" name="phone" type="tel" placeholder="+7 (___) ___-__-__" autocomplete="tel" inputmode="tel" required aria-invalid="false" aria-describedby="booking-phone-error" class="booking__input" />
								<span id="booking-phone-error" class="booking__field-error" role="alert" hidden></span>
							</div>
						</div>
					</div>

					<div class="booking__footer-row">
						<button type="submit" class="booking__submit-btn">
							<img src="<?php echo esc_url( ecosfera_asset( 'icons/vector-1-2.svg' ) ); ?>" alt="" aria-hidden="true" width="9" height="8" class="booking__btn-mark booking__btn-mark--tl" />
							<span class="booking__btn-text"><?php esc_html_e( 'Забронировать дом', 'ecosfera' ); ?></span>
							<img src="<?php echo esc_url( ecosfera_asset( 'icons/vector-1-2.svg' ) ); ?>" alt="" aria-hidden="true" width="9" height="8" class="booking__btn-mark booking__btn-mark--br" />
						</button>

						<div class="booking__consent-row">
							<input id="booking-consent" type="checkbox" name="consent" required aria-invalid="false" aria-describedby="booking-consent-error" class="booking__checkbox" />
							<label for="booking-consent" class="booking__consent-label">
								<?php
								echo wp_kses(
									sprintf(
										/* translators: %s: privacy policy url */
										__( 'Я даю свое согласие на обработку персональных данных в соответствии с ФЗ №152-ФЗ «О персональных данных» на условиях и для целей, определенных <a href="%s" class="booking__consent-link" target="_blank" rel="noopener noreferrer">Политикой Конфиденциальности</a>.', 'ecosfera' ),
										esc_url( $privacy )
									),
									array(
										'a' => array(
											'href'   => array(),
											'class'  => array(),
											'target' => array(),
											'rel'    => array(),
										),
									)
								);
								?>
							</label>
							<span id="booking-consent-error" class="booking__consent-error" role="alert" hidden></span>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php ecosfera_divider( 'booking__divider' ); ?>

</section>
