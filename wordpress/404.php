<?php
/**
 * 404.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="ecosfera-inner">
	<div class="container ecosfera-inner__wrap">
		<h1 class="section-heading"><?php esc_html_e( 'Страница не найдена', 'ecosfera' ); ?></h1>
		<p><?php esc_html_e( 'Такой страницы нет. Вернитесь на главную и выберите раздел лендинга.', 'ecosfera' ); ?></p>
		<p><a class="header__cta" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span><?php esc_html_e( 'На главную', 'ecosfera' ); ?></span></a></p>
	</div>
</main>
<?php
get_footer();
