<?php
/**
 * Регистрация возможностей темы.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'ecosfera_setup' );

function ecosfera_setup() {
	load_theme_textdomain( 'ecosfera', ECOSFERA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 97,
		'width'       => 114,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title' ),
	) );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus(
		array(
			'primary' => __( 'Шапка', 'ecosfera' ),
			'footer'  => __( 'Подвал — основные', 'ecosfera' ),
			'footer2' => __( 'Подвал — дополнительные', 'ecosfera' ),
		)
	);

	add_image_size( 'ecosfera-house', 909, 750, true );
	add_image_size( 'ecosfera-gallery', 934, 646, true );
	add_image_size( 'ecosfera-card', 401, 379, true );
	add_image_size( 'ecosfera-avatar', 126, 126, true );
}

add_filter( 'document_title_parts', 'ecosfera_document_title' );

function ecosfera_document_title( $parts ) {
	if ( is_front_page() ) {
		$parts['title'] = __( 'Экосфера — отдых вне города', 'ecosfera' );
		unset( $parts['site'] );
	}
	return $parts;
}

add_action( 'wp_head', 'ecosfera_meta_head', 1 );

function ecosfera_meta_head() {
	if ( ! is_front_page() ) {
		return;
	}

	$desc = ecosfera_get( 'hero_lead' );
	echo '<meta name="theme-color" content="#123206">' . "\n";
	echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $desc ) ) . '">' . "\n";
}

/**
 * Ссылка на якорь лендинга: с внутренних страниц ведёт на главную + якорь.
 */
function ecosfera_home_hash( $hash ) {
	$hash = ltrim( $hash, '#' );
	return home_url( '/#' . $hash );
}
