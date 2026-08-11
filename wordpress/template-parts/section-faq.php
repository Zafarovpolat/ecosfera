<?php
/**
 * FAQ.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = ecosfera_get_faq();
$wa    = ecosfera_whatsapp_href();
?>
<section class="faq" id="faq" aria-labelledby="faq-heading">
	<div class="container">
		<div class="faq__inner">

			<div class="faq__top-row">
				<h2 id="faq-heading" class="section-heading">
					<span class="section-heading__lead"><?php esc_html_e( 'Часто', 'ecosfera' ); ?></span>
					<span class="section-heading__accent"><?php esc_html_e( 'спрашивают', 'ecosfera' ); ?></span>
				</h2>

				<div class="faq__cta">
					<p class="faq__cta-text"><?php echo esc_html( ecosfera_get( 'faq_cta' ) ); ?></p>
					<?php if ( $wa ) : ?>
						<a href="<?php echo esc_url( $wa ); ?>" class="faq__cta-btn" target="_blank" rel="noopener noreferrer">
							<span>Whatsapp</span>
							<img src="<?php echo esc_url( ecosfera_asset( 'icons/vector-1-2.svg' ) ); ?>" alt="" aria-hidden="true" width="9" height="8" class="faq__cta-mark faq__cta-mark--tl" />
							<img src="<?php echo esc_url( ecosfera_asset( 'icons/vector-1-2.svg' ) ); ?>" alt="" aria-hidden="true" width="9" height="8" class="faq__cta-mark faq__cta-mark--br" />
						</a>
					<?php endif; ?>
				</div>
			</div>

			<ul class="faq__list" role="list">
				<?php foreach ( $items as $i => $item ) : ?>
					<?php
					$open = $i === 0;
					$hid  = $open ? '' : ' hidden';
					?>
					<li class="faq__item<?php echo $open ? ' faq__item--open' : ''; ?>">
						<h3 class="faq__question">
							<button
								type="button"
								id="faq-heading-<?php echo (int) $i; ?>"
								aria-expanded="<?php echo $open ? 'true' : 'false'; ?>"
								aria-controls="faq-panel-<?php echo (int) $i; ?>"
								class="faq__toggle"
							>
								<span class="faq__number" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								<span class="faq__question-text"><?php echo esc_html( $item['question'] ); ?></span>
								<span class="faq__icon" aria-hidden="true"></span>
							</button>
						</h3>
						<div
							id="faq-panel-<?php echo (int) $i; ?>"
							role="region"
							aria-labelledby="faq-heading-<?php echo (int) $i; ?>"
							class="faq__panel"
							<?php echo $hid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						>
							<div class="faq__panel-inner">
								<?php
								$answer = $item['answer'] ?? '';
								if ( str_contains( $answer, 'faq__answer' ) ) {
									echo wp_kses_post( $answer );
								} else {
									echo '<p class="faq__answer">' . wp_kses_post( $answer ) . '</p>';
								}
								?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<?php ecosfera_divider( 'faq__divider' ); ?>

</section>
