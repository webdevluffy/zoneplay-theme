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
 *   3. Dequeues WooCommerce's (and WooCommerce Subscriptions') global
 *      front-end CSS/JS on every non-WooCommerce page — see
 *      zp_wc_dequeue_frontend_assets().
 *
 * Front-end CSS only — no template overrides, no checkout field changes.
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * True on pages that legitimately need WooCommerce's front-end assets:
 * Shop / product / product-taxonomy archives, Cart, Checkout, My Account and
 * the account/checkout endpoints (order-received, view-order, …).
 *
 * @return bool
 */
function zp_is_woocommerce_page() {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return false;
	}

	return is_woocommerce()
		|| is_cart()
		|| is_checkout()
		|| is_account_page()
		|| is_wc_endpoint_url();
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'woocommerce' );
	}
);

/**
 * Reword the login heading on the logged-out My Account screen.
 *
 * WooCommerce's myaccount/form-login.php outputs just "Login" as its <h2>.
 * Filtering the WooCommerce text domain (rather than overriding the whole
 * template) keeps this to the single string.
 */
add_filter(
	'gettext_woocommerce',
	function ( $translation, $text ) {
		if ( 'Login' === $text && function_exists( 'is_account_page' ) && is_account_page() && ! is_user_logged_in() ) {
			return __( 'Log in to your account', 'zoneplay' );
		}
		return $translation;
	},
	10,
	2
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

/**
 * Strip WooCommerce's global front-end assets from non-WooCommerce pages.
 *
 * WooCommerce (and WooCommerce Subscriptions / All Products for Subscriptions)
 * enqueue their layout/general CSS, blockUI, js-cookie, the main woocommerce
 * bundle, country-select / address-i18n, order-attribution + sourcebuster and
 * the wcsatt bundle on *every* front-end page — none of which the content
 * pages here use. Only the Shop / Cart / Checkout / My Account screens keep
 * them (see zp_is_woocommerce_page()).
 *
 * Runs at priority 99 so it sees everything WooCommerce enqueued at the
 * default priority 10. Any handle starting with one of the WooCommerce
 * prefixes is dropped; the theme's own `zoneplay-woocommerce` skin is
 * explicitly spared (it is never enqueued on these pages anyway).
 */
add_action( 'wp_enqueue_scripts', 'zp_wc_dequeue_frontend_assets', 99 );

function zp_wc_dequeue_frontend_assets() {
	if ( is_admin() || zp_is_woocommerce_page() ) {
		return;
	}

	$prefixes = array( 'woocommerce', 'wc-', 'wcsatt', 'sourcebuster', 'selectWoo', 'select2', 'flexslider', 'photoswipe', 'zoom' );

	$is_wc_handle = static function ( $handle ) use ( $prefixes ) {
		if ( 'zoneplay-woocommerce' === $handle ) {
			return false;
		}
		foreach ( $prefixes as $prefix ) {
			if ( 0 === strpos( $handle, $prefix ) ) {
				return true;
			}
		}
		return false;
	};

	foreach ( (array) wp_styles()->queue as $handle ) {
		if ( $is_wc_handle( $handle ) ) {
			wp_dequeue_style( $handle );
		}
	}

	foreach ( (array) wp_scripts()->queue as $handle ) {
		if ( $is_wc_handle( $handle ) ) {
			wp_dequeue_script( $handle );
		}
	}

	// Drops the `woocommerce-no-js` <body> class and the tiny <script> that
	// swaps it, both added by WooCommerce's wc_body_class() filter.
	remove_filter( 'body_class', 'wc_body_class' );

	// The <noscript> product-gallery style is only meaningful on product pages.
	remove_action( 'wp_head', 'wc_gallery_noscript' );
}
