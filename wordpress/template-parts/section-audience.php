<?php
/**
 * Для кого.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cards = ecosfera_get( 'audience' );
if ( ! is_array( $cards ) ) {
	$cards = ecosfera_defaults()['audience'];
}
?>
<section class="audience" id="audience" aria-labelledby="audience-heading">
	<div class="container">

		<header class="audience__section-header">
			<h2 id="audience-heading" class="section-heading">
				<?php esc_html_e( 'Подходит для', 'ecosfera' ); ?> <span class="section-heading__accent"><?php esc_html_e( 'любого повода', 'ecosfera' ); ?></span>
			</h2>
			<p class="audience__subtitle">
				<img class="audience__subtitle-icon" src="<?php echo esc_url( ecosfera_asset( 'icons/icon-2.svg' ) ); ?>" alt="" aria-hidden="true" width="12" height="13" />
				<?php esc_html_e( 'Расскажите о своих планах — поможем выбрать лучший вариант', 'ecosfera' ); ?>
			</p>
		</header>

		<ul class="audience__grid" role="list">
			<?php foreach ( $cards as $i => $card ) : ?>
				<li class="audience__item" style="--card-index: <?php echo (int) $i; ?>">
					<article class="audience__card">
						<figure class="audience__figure">
							<img
								class="audience__photo"
								src="<?php echo esc_url( ecosfera_src( $card['image'] ?? '' ) ); ?>"
								alt="<?php echo esc_attr( $card['title'] ?? '' ); ?>"
								width="401"
								height="379"
								loading="lazy"
								decoding="async"
							/>
						</figure>
						<div class="audience__card-body">
							<h3 class="audience__card-title"><?php echo esc_html( $card['title'] ?? '' ); ?></h3>
							<p class="audience__card-desc"><?php echo esc_html( $card['desc'] ?? '' ); ?></p>
						</div>
					</article>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="comparison__pagination audience__pagination">
			<div class="comparison__nav">
				<img src="<?php echo esc_url( ecosfera_asset( 'icons/arrows.svg' ) ); ?>" alt="" class="comparison__pagination-arrows" aria-hidden="true" />
				<button type="button" class="comparison__nav-btn comparison__nav-btn--prev" aria-label="<?php esc_attr_e( 'Предыдущий вариант', 'ecosfera' ); ?>"></button>
				<button type="button" class="comparison__nav-btn comparison__nav-btn--next" aria-label="<?php esc_attr_e( 'Следующий вариант', 'ecosfera' ); ?>"></button>
			</div>
		</div>
	</div>

	<?php ecosfera_divider( 'audience__divider' ); ?>

</section>
