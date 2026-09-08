<?php
/**
 * Front-end assets — only what the theme actually uses.
 *
 *   zoneplay-fonts   assets/fonts.css         self-hosted Fredoka + Nunito @font-face (no Google Fonts).
 *   zoneplay-style   assets/css/main.css      compiled Tailwind for the header / footer / templates.
 *   zoneplay-nav     assets/js/navigation.js  mobile menu toggle (defer).
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filemtime-based version so cache-busting is automatic in dev, falling
 * back to the theme version if the file is missing.
 */
function zp_asset_ver( $rel ) {
	$path = ZP_THEME_DIR . '/' . ltrim( $rel, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : ZP_THEME_VERSION;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'zoneplay-fonts',
			ZP_THEME_URI . '/assets/fonts.css',
			array(),
			zp_asset_ver( 'assets/fonts.css' )
		);

		wp_enqueue_style(
			'zoneplay-style',
			ZP_THEME_URI . '/assets/css/main.css',
			array( 'zoneplay-fonts' ),
			zp_asset_ver( 'assets/css/main.css' )
		);

		wp_enqueue_script(
			'zoneplay-nav',
			ZP_THEME_URI . '/assets/js/navigation.js',
			array(),
			zp_asset_ver( 'assets/js/navigation.js' ),
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
	}
);

/**
 * Preload the two above-the-fold Latin font files to cut the swap flash.
 */
add_action(
	'wp_head',
	function () {
		$base = ZP_THEME_URI . '/assets/fonts/';
		foreach ( array( 'nunito-latin.woff2', 'fredoka-latin.woff2' ) as $file ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( $base . $file )
			);
		}
	},
	2
);
