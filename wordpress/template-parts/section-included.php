<?php
/**
 * Включено / за доплату.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$included = ecosfera_get( 'included' );
$extras   = ecosfera_get( 'extras' );
if ( ! is_array( $included ) ) {
	$included = ecosfera_defaults()['included'];
}
if ( ! is_array( $extras ) ) {
	$extras = ecosfera_defaults()['extras'];
}
$last = count( $included ) - 1;
?>
<section class="included" id="uslugi" aria-labelledby="pricing-heading">

	<h2 id="pricing-heading" class="sr-only"><?php esc_html_e( 'Стоимость', 'ecosfera' ); ?></h2>

	<div class="container">
		<div class="included__inner">

			<div class="included__block included__block--lime">
				<div class="included__label included__label--lime">
					<h3 id="included-heading" class="included__label-heading"><?php esc_html_e( 'Включено в стоимость', 'ecosfera' ); ?></h3>
					<div class="included__label-decor" aria-hidden="true">
						<div class="included__label-decor-inner"></div>
					</div>
				</div>

				<div class="included__table-wrap">
					<table class="included__table" aria-labelledby="included-heading">
						<caption><span class="sr-only"><?php esc_html_e( 'Услуги, включённые в стоимость проживания', 'ecosfera' ); ?></span></caption>
						<thead class="included__thead">
							<tr>
								<th scope="col"><span class="sr-only"><?php esc_html_e( 'Номер', 'ecosfera' ); ?></span></th>
								<th scope="col"><span class="sr-only"><?php esc_html_e( 'Наименование', 'ecosfera' ); ?></span></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $included as $i => $label ) : ?>
								<tr class="included__row<?php echo $i === $last ? ' included__row--span' : ''; ?>">
									<td class="included__cell-num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></td>
									<td class="included__cell-label"><?php echo esc_html( $label ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

			<div class="included__block included__block--black">
				<div class="included__label included__label--black">
					<h3 id="extra-heading" class="included__label-heading"><?php esc_html_e( 'По желанию, за доплату', 'ecosfera' ); ?></h3>
					<div class="included__label-decor" aria-hidden="true"></div>
				</div>

				<div class="included__table-wrap">
					<table class="included__table" aria-labelledby="extra-heading">
						<caption><span class="sr-only"><?php esc_html_e( 'Дополнительные услуги за отдельную плату', 'ecosfera' ); ?></span></caption>
						<thead class="included__thead">
							<tr>
								<th scope="col"><span class="sr-only"><?php esc_html_e( 'Номер', 'ecosfera' ); ?></span></th>
								<th scope="col"><span class="sr-only"><?php esc_html_e( 'Наименование', 'ecosfera' ); ?></span></th>
								<th scope="col"><span class="sr-only"><?php esc_html_e( 'Стоимость', 'ecosfera' ); ?></span></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $extras as $i => $row ) : ?>
								<tr class="included__row">
									<td class="included__cell-num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></td>
									<td class="included__cell-label"><?php echo esc_html( $row['label'] ?? '' ); ?></td>
									<td class="included__cell-price"><?php echo esc_html( $row['price'] ?? '' ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>

	<?php ecosfera_divider( 'included__divider' ); ?>

</section>
