<?php
/**
 * Zone Play Cardiff theme bootstrap.
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ZP_THEME_VERSION', '1.0.0' );
define( 'ZP_THEME_DIR', get_template_directory() );
define( 'ZP_THEME_URI', get_template_directory_uri() );

require_once ZP_THEME_DIR . '/inc/setup.php';
require_once ZP_THEME_DIR . '/inc/enqueue.php';
require_once ZP_THEME_DIR . '/inc/cleanup.php';
