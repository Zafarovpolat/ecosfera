<?php
/**
 * Уезжать далеко не нужно.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cards = ecosfera_get( 'nearby' );
if ( ! is_array( $cards ) ) {
	$cards = ecosfera_defaults()['nearby'];
}
?>
<section class="nearby" aria-labelledby="nearby-benefits-heading">

	<img
		class="nearby__bg"
		src="<?php echo esc_url( ecosfera_asset( 'images/image-33-4415fc2162.webp' ) ); ?>"
		alt=""
		aria-hidden="true"
		width="2050"
		height="913"
		loading="lazy"
		decoding="async"
	/>

	<div class="container">
		<h2 id="nearby-benefits-heading" class="section-heading section-heading--center">
			<?php esc_html_e( 'Уезжать далеко', 'ecosfera' ); ?>&#x202F;&#x2014;&#x202F;<span class="section-heading__accent section-heading__accent--lg"><?php esc_html_e( 'не нужно', 'ecosfera' ); ?></span>
		</h2>

		<ol class="nearby__list">
			<?php foreach ( $cards as $card ) : ?>
				<li class="nearby__card">
					<span class="nearby__badge" aria-hidden="true"><?php echo esc_html( $card['num'] ?? '' ); ?></span>
					<article class="nearby__content">
						<h3 class="nearby__card-title"><?php echo esc_html( $card['title'] ?? '' ); ?></h3>
						<p class="nearby__card-desc"><?php echo esc_html( $card['desc'] ?? '' ); ?></p>
					</article>
				</li>
			<?php endforeach; ?>
		</ol>

		<div class="comparison__pagination nearby__pagination">
			<div class="comparison__nav">
				<img src="<?php echo esc_url( ecosfera_asset( 'icons/arrows.svg' ) ); ?>" alt="" class="comparison__pagination-arrows" aria-hidden="true" />
				<button type="button" class="comparison__nav-btn comparison__nav-btn--prev" aria-label="<?php esc_attr_e( 'Предыдущее жильё рядом', 'ecosfera' ); ?>"></button>
				<button type="button" class="comparison__nav-btn comparison__nav-btn--next" aria-label="<?php esc_attr_e( 'Следующее жильё рядом', 'ecosfera' ); ?>"></button>
			</div>
		</div>
	</div>

	<?php ecosfera_divider( 'nearby__divider' ); ?>

</section>
