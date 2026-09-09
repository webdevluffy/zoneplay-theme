<?php
/**
 * WooCommerce integration — Checkout, Cart & My Account skin.
 *
 * The theme ships no WooCommerce templates. Checkout / Cart / My Account are
 * ordinary Pages holding the [woocommerce_checkout] / [woocommerce_cart] /
 * [woocommerce_my_account] shortcodes, rendered through page.php. They pick
 * up the site-wide Tailwind Preflight reset from assets/css/main.css but
 * none of the theme's component styling, so out of the box they don't match
 * the rest of the site.
 *
 * This file:
 *   1. Declares `woocommerce` theme support — clears the "theme does not
 *      declare WooCommerce support" admin notice and lets WooCommerce manage
 *      its own template hooks.
 *   2. Enqueues assets/css/woocommerce.css (the ZonePlay skin for those
 *      screens) ONLY on is_checkout() / is_cart() / is_account_page(),
 *      loaded after `zoneplay-style` and WooCommerce's own stylesheets so it
 *      wins the cascade.
 *
 * Front-end CSS only — no template overrides, no checkout field changes.
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'woocommerce' );
	}
);

/**
 * Register the checkout / account stylesheet and enqueue it only on the
 * WooCommerce Checkout, Cart and My Account screens.
 *
 * Priority 20: WooCommerce enqueues its own sheets on `wp_enqueue_scripts`
 * at the default priority 10, so by the time this runs `woocommerce-general`
 * / `woocommerce-layout` are registered and can be listed as dependencies —
 * together with the theme's `zoneplay-style` that guarantees this skin loads
 * last and overrides both the Tailwind reset and WooCommerce's defaults.
 */
add_action( 'wp_enqueue_scripts', 'zp_wc_enqueue_skin', 20 );

function zp_wc_enqueue_skin() {
	// Bail cleanly if WooCommerce is not active.
	if ( ! function_exists( 'is_checkout' ) ) {
		return;
	}

	$deps = array_values(
		array_filter(
			array( 'zoneplay-fonts', 'zoneplay-style', 'woocommerce-general', 'woocommerce-layout' ),
			static function ( $handle ) {
				return wp_style_is( $handle, 'registered' ) || wp_style_is( $handle, 'enqueued' );
			}
		)
	);

	wp_register_style(
		'zoneplay-woocommerce',
		ZP_THEME_URI . '/assets/css/woocommerce.css',
		$deps,
		zp_asset_ver( 'assets/css/woocommerce.css' )
	);

	if ( is_checkout() || is_cart() || is_account_page() ) {
		wp_enqueue_style( 'zoneplay-woocommerce' );
	}
}
