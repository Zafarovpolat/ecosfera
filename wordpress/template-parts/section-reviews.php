<?php
/**
 * Отзывы.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reviews   = ecosfera_get_reviews();
$total     = count( $reviews );
$instagram = ecosfera_get( 'instagram' );
?>
<section class="reviews" id="reviews" aria-labelledby="reviews-heading">
	<div class="container">

		<header class="reviews__section-header">
			<h2 id="reviews-heading" class="section-heading">
				<?php esc_html_e( 'Что говорят те', 'ecosfera' ); ?>
				<span class="section-heading__accent"><?php esc_html_e( 'кто уже побывал', 'ecosfera' ); ?></span>
			</h2>
		</header>

		<div class="reviews__carousel-wrapper" role="region" aria-label="<?php esc_attr_e( 'Карусель отзывов', 'ecosfera' ); ?>">
			<div class="reviews__track-outer">
				<ul class="reviews__card-list" role="list" aria-label="<?php esc_attr_e( 'Отзывы гостей', 'ecosfera' ); ?>">
					<?php foreach ( $reviews as $review ) : ?>
						<?php
						$datetime = $review['date'] ?? '';
						$display  = ecosfera_format_review_date( $datetime );
						?>
						<li class="reviews__card-item">
							<article class="reviews__card">
								<div class="reviews__rating">
									<img class="reviews__star" src="<?php echo esc_url( ecosfera_asset( 'icons/star-1.svg' ) ); ?>" alt="" aria-hidden="true" width="28" height="26" />
									<span class="reviews__rating-value">
										<span class="sr-only"><?php esc_html_e( 'Рейтинг:', 'ecosfera' ); ?> </span>
										<?php echo esc_html( ( $review['rating'] ?? '5.0' ) . ' ' . __( 'из 5', 'ecosfera' ) ); ?>
									</span>
								</div>
								<blockquote class="reviews__blockquote">
									<p class="reviews__text"><?php echo esc_html( $review['text'] ?? '' ); ?></p>
									<footer class="reviews__profile">
										<img
											class="reviews__avatar"
											src="<?php echo esc_url( ecosfera_src( $review['avatar'] ?? '' ) ); ?>"
											alt=""
											aria-hidden="true"
											width="63"
											height="63"
											loading="lazy"
											decoding="async"
										/>
										<div class="reviews__meta">
											<cite class="reviews__author-name"><?php echo esc_html( $review['author'] ?? '' ); ?></cite>
											<time class="reviews__date" datetime="<?php echo esc_attr( $datetime ); ?>"><?php echo esc_html( $display ); ?></time>
										</div>
									</footer>
								</blockquote>
							</article>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="reviews__controls">
				<div class="reviews__nav">
					<img src="<?php echo esc_url( ecosfera_asset( 'icons/arrows.svg' ) ); ?>" alt="" class="reviews__nav-img" aria-hidden="true" />
					<button type="button" class="reviews__arrow-btn reviews__arrow-btn--prev" aria-label="<?php esc_attr_e( 'Предыдущий отзыв', 'ecosfera' ); ?>"></button>
					<button type="button" class="reviews__arrow-btn reviews__arrow-btn--next" aria-label="<?php esc_attr_e( 'Следующий отзыв', 'ecosfera' ); ?>"></button>
				</div>
				<div class="reviews__counter" aria-live="polite" aria-atomic="true" aria-label="<?php echo esc_attr( sprintf( __( 'Отзыв 1 из %d', 'ecosfera' ), $total ) ); ?>">
					<span>01</span><span class="reviews__counter-sep">/<?php echo esc_html( str_pad( (string) $total, 2, '0', STR_PAD_LEFT ) ); ?></span>
				</div>
			</div>
		</div>

		<?php if ( $instagram ) : ?>
			<a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" class="reviews__instagram-btn" aria-label="<?php esc_attr_e( 'Смотреть все отзывы в Instagram (откроется в новой вкладке)', 'ecosfera' ); ?>">
				<span class="reviews__instagram-btn-text"><?php esc_html_e( 'Смотреть все отзывы в Instagram', 'ecosfera' ); ?></span>
				<span class="reviews__corner-top-left" aria-hidden="true">
					<img src="<?php echo esc_url( ecosfera_asset( 'icons/vector-1-2.svg' ) ); ?>" alt="" width="9" height="8" />
				</span>
				<span class="reviews__corner-bottom-right" aria-hidden="true">
					<img src="<?php echo esc_url( ecosfera_asset( 'icons/vector-1-2.svg' ) ); ?>" alt="" width="9" height="8" />
				</span>
			</a>
		<?php endif; ?>
	</div>

	<?php ecosfera_divider( 'reviews__divider' ); ?>

</section>
