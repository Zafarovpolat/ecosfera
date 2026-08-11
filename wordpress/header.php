<?php
/**
 * Шапка документа.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<script>
		(function () {
			var d = document.documentElement;
			d.classList.remove('no-js');
			d.classList.add('rv-boot');
			window.setTimeout(function () { d.classList.add('rv-ready'); }, 4000);
		})();
	</script>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Перейти к содержимому', 'ecosfera' ); ?></a>
