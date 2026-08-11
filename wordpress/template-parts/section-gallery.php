<?php
/**
 * Живые фото.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$row1   = ecosfera_get( 'gallery_row_1' );
$row2   = ecosfera_get( 'gallery_row_2' );
$mobile = ecosfera_get( 'gallery_mobile' );
if ( ! is_array( $row1 ) ) {
	$row1 = ecosfera_defaults()['gallery_row_1'];
}
if ( ! is_array( $row2 ) ) {
	$row2 = ecosfera_defaults()['gallery_row_2'];
}
if ( ! is_array( $mobile ) ) {
	$mobile = ecosfera_defaults()['gallery_mobile'];
}
?>
<section class="photo-gallery" id="gallery" aria-labelledby="photo-gallery-heading">

	<div class="container">
		<h2 id="photo-gallery-heading" class="section-heading section-heading--center">
			<?php esc_html_e( 'Живые фото', 'ecosfera' ); ?>
			<span class="section-heading__accent">
				<img class="photo-gallery__video-icon" src="<?php echo esc_url( ecosfera_asset( 'icons/vuesax-bulk-video.svg' ) ); ?>" alt="" aria-hidden="true" width="49" height="49" />
				<?php esc_html_e( 'без фильтров', 'ecosfera' ); ?>
			</span>
		</h2>
	</div>

	<div class="photo-gallery__desktop" aria-label="<?php esc_attr_e( 'Галерея фотографий', 'ecosfera' ); ?>">
		<div class="photo-gallery__row photo-gallery__row--right">
			<div class="photo-gallery__track">
				<?php
				foreach ( $row1 as $item ) {
					ecosfera_gallery_figure( $item );
				}
				foreach ( $row1 as $item ) {
					ecosfera_gallery_figure( $item, true );
				}
				?>
			</div>
		</div>
		<div class="photo-gallery__row photo-gallery__row--left">
			<div class="photo-gallery__track">
				<?php
				foreach ( $row2 as $item ) {
					ecosfera_gallery_figure( $item );
				}
				foreach ( $row2 as $item ) {
					ecosfera_gallery_figure( $item, true );
				}
				?>
			</div>
		</div>
	</div>

	<div class="photo-gallery__mobile" aria-label="<?php esc_attr_e( 'Галерея фотографий', 'ecosfera' ); ?>">
		<?php foreach ( $mobile as $item ) : ?>
			<figure class="photo-gallery__mobile-figure">
				<img
					class="photo-gallery__photo"
					src="<?php echo esc_url( ecosfera_src( $item['src'] ?? '' ) ); ?>"
					alt="<?php echo esc_attr( $item['alt'] ?? '' ); ?>"
					width="219"
					height="149"
					loading="lazy"
					decoding="async"
				/>
			</figure>
		<?php endforeach; ?>
	</div>

	<?php ecosfera_divider( 'photo-gallery__divider' ); ?>

</section>
