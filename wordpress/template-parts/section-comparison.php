<?php
/**
 * Чем Экосфера отличается.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cards = ecosfera_get( 'comparison' );
if ( ! is_array( $cards ) ) {
	$cards = ecosfera_defaults()['comparison'];
}
$total = count( $cards );
?>
<section class="comparison" aria-labelledby="comparison-heading">

	<div class="comparison__heading-wrap">
		<h2 id="comparison-heading" class="comparison__heading">
			<?php esc_html_e( 'Чем', 'ecosfera' ); ?>&nbsp;<?php esc_html_e( 'ЭКОСФЕРА', 'ecosfera' ); ?> <span class="comparison__heading-nl"><?php esc_html_e( 'отличается', 'ecosfera' ); ?></span>
			<span class="sr-only">&nbsp;<?php esc_html_e( 'от других', 'ecosfera' ); ?></span>
		</h2>
		<div class="comparison__heading-accent" aria-hidden="true">
			<span class="comparison__heading-accent-text"><?php esc_html_e( 'от других', 'ecosfera' ); ?></span>
		</div>
	</div>

	<div class="comparison__photos" aria-hidden="true">
		<div class="comparison__photo-frame comparison__photo-frame--left">
			<img src="<?php echo esc_url( ecosfera_asset( 'images/image-33-fc624bc5a6.webp' ) ); ?>" alt="" class="comparison__photo-img" loading="lazy" decoding="async" aria-hidden="true" />
		</div>
		<div class="comparison__photo-frame comparison__photo-frame--right">
			<img src="<?php echo esc_url( ecosfera_asset( 'images/image-34-c11962e4b5.webp' ) ); ?>" alt="" class="comparison__photo-img" loading="lazy" decoding="async" aria-hidden="true" />
		</div>
	</div>

	<div class="comparison__cards">
		<ul class="comparison__card-list">
			<?php foreach ( $cards as $i => $card ) : ?>
				<li class="comparison__card">
					<div class="comparison__badge" aria-hidden="true">
						<span class="comparison__badge-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					</div>
					<div class="comparison__card-body">
						<h3 class="comparison__card-title"><?php echo esc_html( $card['title'] ?? '' ); ?></h3>
						<p class="comparison__card-desc"><?php echo esc_html( $card['desc'] ?? '' ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="comparison__pagination">
			<p class="comparison__pagination-counter" aria-live="polite">
				<span class="comparison__pagination-current">03</span>
				<span class="comparison__pagination-total">/<?php echo esc_html( str_pad( (string) $total, 2, '0', STR_PAD_LEFT ) ); ?></span>
			</p>
			<div class="comparison__nav">
				<img src="<?php echo esc_url( ecosfera_asset( 'icons/arrows.svg' ) ); ?>" alt="" class="comparison__pagination-arrows" aria-hidden="true" />
				<button type="button" class="comparison__nav-btn comparison__nav-btn--prev" aria-label="<?php esc_attr_e( 'Предыдущее преимущество', 'ecosfera' ); ?>"></button>
				<button type="button" class="comparison__nav-btn comparison__nav-btn--next" aria-label="<?php esc_attr_e( 'Следующее преимущество', 'ecosfera' ); ?>"></button>
			</div>
		</div>
	</div>

	<?php ecosfera_divider( 'comparison__divider' ); ?>

</section>
