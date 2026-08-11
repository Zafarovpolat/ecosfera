<?php
/**
 * Главная — одностраничный лендинг.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main">
	<?php
	get_template_part( 'template-parts/section', 'hero' );
	?>
	<div class="page-body">
		<div class="page-backdrop" aria-hidden="true">
			<img class="page-backdrop__img" src="<?php echo esc_url( ecosfera_src( ecosfera_get( 'backdrop' ) ) ); ?>" alt="" loading="lazy" decoding="async">
			<div class="page-backdrop__veil"></div>
		</div>
		<?php
		get_template_part( 'template-parts/section', 'advantages' );
		get_template_part( 'template-parts/section', 'nearby' );
		get_template_part( 'template-parts/section', 'houses' );
		get_template_part( 'template-parts/section', 'included' );
		get_template_part( 'template-parts/section', 'gallery' );
		get_template_part( 'template-parts/section', 'comparison' );
		get_template_part( 'template-parts/section', 'audience' );
		get_template_part( 'template-parts/section', 'booking' );
		get_template_part( 'template-parts/section', 'reviews' );
		get_template_part( 'template-parts/section', 'faq' );
		get_template_part( 'template-parts/section', 'contacts' );
		?>
	</div>
</main>
<?php
get_footer();
