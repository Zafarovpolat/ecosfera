<?php
/**
 * Подключение CSS и JS.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'ecosfera_enqueue_assets' );

function ecosfera_enqueue_assets() {
	$css = array(
		'tokens'      => 'css/tokens.css',
		'base'        => 'css/base.css',
		'header'      => 'css/01-header.css',
		'hero'        => 'css/02-hero.css',
		'advantages'  => 'css/03-advantages.css',
		'nearby'      => 'css/04-nearby.css',
		'houses'      => 'css/05-houses.css',
		'included'    => 'css/06-included.css',
		'gallery'     => 'css/07-gallery.css',
		'comparison'  => 'css/08-comparison.css',
		'audience'    => 'css/09-audience.css',
		'booking'     => 'css/10-booking.css',
		'reviews'     => 'css/11-reviews.css',
		'faq'         => 'css/12-faq.css',
		'contacts'    => 'css/13-contacts.css',
		'footer'      => 'css/14-footer.css',
		'reveal'      => 'css/15-reveal.css',
		'wp'          => 'css/16-wp.css',
	);

	$prev = 'ecosfera-style';
	wp_enqueue_style( 'ecosfera-style', get_stylesheet_uri(), array(), ECOSFERA_VERSION );

	foreach ( $css as $handle => $file ) {
		$path = ECOSFERA_DIR . '/assets/' . $file;
		$ver  = file_exists( $path ) ? (string) filemtime( $path ) : ECOSFERA_VERSION;
		wp_enqueue_style(
			'ecosfera-' . $handle,
			ecosfera_asset( $file ),
			array( $prev ),
			$ver
		);
		$prev = 'ecosfera-' . $handle;
	}

	$js = array(
		'reveal'      => 'js/reveal.js',
		'header'      => 'js/header.js',
		'advantages'  => 'js/advantages.js',
		'comparison'  => 'js/comparison.js',
		'booking'     => 'js/booking.js',
		'gallery'     => 'js/gallery.js',
		'reviews'     => 'js/reviews.js',
		'faq'         => 'js/faq.js',
		'reveal-in'   => 'js/reveal-in.js',
		'nearby'      => 'js/nearby.js',
		'audience'    => 'js/audience.js',
		'houses'      => 'js/houses.js',
		'wp'          => 'js/ecosfera-wp.js',
	);

	foreach ( $js as $handle => $file ) {
		$path = ECOSFERA_DIR . '/assets/' . $file;
		$ver  = file_exists( $path ) ? (string) filemtime( $path ) : ECOSFERA_VERSION;
		wp_enqueue_script(
			'ecosfera-' . $handle,
			ecosfera_asset( $file ),
			array(),
			$ver,
			true
		);
	}

	wp_localize_script(
		'ecosfera-wp',
		'ecosferaWp',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ecosfera_booking' ),
			'homeUrl'  => home_url( '/' ),
			'success'  => __( 'Заявка отправлена. Мы свяжемся с вами, чтобы подтвердить бронь.', 'ecosfera' ),
			'error'    => __( 'Не удалось отправить заявку. Позвоните нам или напишите в WhatsApp.', 'ecosfera' ),
			'sending'  => __( 'Отправляем…', 'ecosfera' ),
		)
	);

	wp_enqueue_style(
		'ecosfera-fonts-preload-hint',
		false,
		array(),
		null
	);
}

add_action( 'wp_head', 'ecosfera_preload_fonts', 2 );

function ecosfera_preload_fonts() {
	$fonts = array(
		'fonts/FormaDJRCyrillicDisplay-Regular-Testing.woff2',
		'fonts/CoFoSans-Regular.woff2',
	);

	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( ecosfera_asset( $font ) )
		);
	}
}

add_filter( 'script_loader_tag', 'ecosfera_script_defer', 10, 3 );

function ecosfera_script_defer( $tag, $handle, $src ) {
	if ( str_starts_with( $handle, 'ecosfera-' ) && ! str_contains( $tag, ' defer' ) ) {
		$tag = str_replace( ' src', ' defer src', $tag );
	}
	return $tag;
}

add_action( 'admin_enqueue_scripts', 'ecosfera_admin_assets' );

function ecosfera_admin_assets( $hook ) {
	$need = in_array( $hook, array( 'toplevel_page_ecosfera', 'post.php', 'post-new.php' ), true );
	if ( ! $need ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style(
		'ecosfera-admin',
		ecosfera_asset( 'css/ecosfera-admin.css' ),
		array(),
		ECOSFERA_VERSION
	);
	wp_enqueue_script(
		'ecosfera-admin',
		ecosfera_asset( 'js/ecosfera-admin.js' ),
		array( 'jquery' ),
		ECOSFERA_VERSION,
		true
	);
}
