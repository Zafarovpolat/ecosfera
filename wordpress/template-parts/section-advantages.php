<?php
/**
 * Преимущества.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cards = ecosfera_get( 'advantages' );
if ( ! is_array( $cards ) ) {
	$cards = ecosfera_defaults()['advantages'];
}
?>
<section class="advantages advantages--svg advantages--animate" aria-labelledby="advantages-heading" id="advantages">

	<img src="<?php echo esc_url( ecosfera_asset( 'images/image-33-0362237225.webp' ) ); ?>" alt="" aria-hidden="true" width="460" height="150" loading="lazy" decoding="async" class="advantages__branch-left" />

	<div class="advantages__tag-wrap">
		<img src="<?php echo esc_url( ecosfera_asset( 'icons/icon.svg' ) ); ?>" alt="" aria-hidden="true" width="12" height="13" class="advantages__tag-icon" />
		<h2 id="advantages-heading" class="advantages__section-title"><?php esc_html_e( 'Наши преимущества', 'ecosfera' ); ?></h2>
	</div>

	<img src="<?php echo esc_url( ecosfera_asset( 'images/image-34-6e68fd7e22.webp' ) ); ?>" alt="" aria-hidden="true" width="593" height="193" loading="lazy" decoding="async" class="advantages__branch-right" />

	<ul class="advantages__card-list" role="list">
		<?php foreach ( $cards as $index => $card ) : ?>
			<li class="advantages__card-item">
				<article class="advantages__card">
					<div class="advantages__card-inner">
						<div class="advantages__card-num-wrap">
							<span class="advantages__card-num"><?php echo esc_html( $card['num'] ?? '' ); ?></span>
							<span class="advantages__card-label"><?php echo esc_html( $card['label'] ?? '' ); ?></span>
						</div>
						<p class="advantages__card-desc"><?php echo esc_html( $card['desc'] ?? '' ); ?></p>
					</div>
					<?php if ( $index === 0 ) : ?>
						<span class="advantages__card-mark advantages__card-mark--top" aria-hidden="true"></span>
					<?php elseif ( $index === 1 ) : ?>
						<svg class="advantages__outline" aria-hidden="true" focusable="false"><path class="advantages__outline-path" /></svg>
						<span class="advantages__card-mark advantages__card-mark--top" aria-hidden="true"></span>
						<span class="advantages__card-mark advantages__card-mark--right" aria-hidden="true"></span>
						<span class="advantages__card-mark advantages__card-mark--bottom advantages__card-mark--white" aria-hidden="true"></span>
					<?php else : ?>
						<svg class="advantages__outline" aria-hidden="true" focusable="false"><path class="advantages__outline-path" /></svg>
						<span class="advantages__card-mark advantages__card-mark--top" aria-hidden="true"></span>
						<span class="advantages__card-mark advantages__card-mark--left" aria-hidden="true"></span>
						<span class="advantages__card-mark advantages__card-mark--right" aria-hidden="true"></span>
						<span class="advantages__card-mark advantages__card-mark--bottom" aria-hidden="true"></span>
					<?php endif; ?>
				</article>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="comparison__pagination advantages__pagination">
		<div class="comparison__nav">
			<img src="<?php echo esc_url( ecosfera_asset( 'icons/arrows.svg' ) ); ?>" alt="" class="comparison__pagination-arrows" aria-hidden="true" />
			<button type="button" class="comparison__nav-btn comparison__nav-btn--prev" aria-label="<?php esc_attr_e( 'Предыдущее преимущество', 'ecosfera' ); ?>"></button>
			<button type="button" class="comparison__nav-btn comparison__nav-btn--next" aria-label="<?php esc_attr_e( 'Следующее преимущество', 'ecosfera' ); ?>"></button>
		</div>
	</div>

	<?php ecosfera_divider( 'advantages__divider' ); ?>

</section>
