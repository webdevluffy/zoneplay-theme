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
require_once ZP_THEME_DIR . '/inc/analytics.php';
require_once ZP_THEME_DIR . '/inc/navigation.php';
require_once ZP_THEME_DIR . '/inc/customizer.php';

// WooCommerce Checkout / Cart / My Account skin (front-end CSS only).
if ( class_exists( 'WooCommerce' ) ) {
	require_once ZP_THEME_DIR . '/inc/woocommerce.php';
}

// Standalone feature — the [zoneplay_contact_form] shortcode (not theme chrome).
require_once ZP_THEME_DIR . '/inc/contact-form.php';
