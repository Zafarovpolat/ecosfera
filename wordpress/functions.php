<?php
/**
 * Экосфера — точка входа темы.
 *
 * @package Ecosfera
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ECOSFERA_VERSION', '1.0.0' );
define( 'ECOSFERA_DIR', get_template_directory() );
define( 'ECOSFERA_URI', get_template_directory_uri() );

require_once ECOSFERA_DIR . '/inc/helpers.php';
require_once ECOSFERA_DIR . '/inc/defaults.php';
require_once ECOSFERA_DIR . '/inc/setup.php';
require_once ECOSFERA_DIR . '/inc/assets.php';
require_once ECOSFERA_DIR . '/inc/cpt.php';
require_once ECOSFERA_DIR . '/inc/meta-boxes.php';
require_once ECOSFERA_DIR . '/inc/customizer.php';
require_once ECOSFERA_DIR . '/inc/settings.php';
require_once ECOSFERA_DIR . '/inc/booking.php';
require_once ECOSFERA_DIR . '/inc/seed.php';
