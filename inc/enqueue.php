<?php
/**
 * Front-end assets — only what the theme actually uses.
 *
 *   zoneplay-fonts   assets/fonts.css         self-hosted Fredoka + Nunito @font-face (no Google Fonts).
 *   zoneplay-style   assets/css/main.css      compiled Tailwind for the header / footer / templates.
 *   zoneplay-nav     assets/js/navigation.js  mobile menu toggle (defer).
 *
 * Block editor canvas (enqueue_block_assets, admin only):
 *   zoneplay-fonts        the same @font-face file — makes the faces available.
 *   zoneplay-editor       assets/css/editor.css — canvas typography, so plain
 *                         blocks aren't left on Gutenberg's `serif` reset.
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
 * Block editor canvas — load the @font-face file and the canvas typography
 * so plain blocks don't fall through to Gutenberg's `serif` reset in
 * wp-includes/css/dist/block-library/reset.min.css. enqueue_block_assets
 * loads into the WP 6.3+ editor-canvas iframe; the is_admin() guard keeps
 * it out of the front end (already covered by main.css there).
 */
add_action(
	'enqueue_block_assets',
	function () {
		if ( ! is_admin() ) {
			return;
		}

		wp_enqueue_style(
			'zoneplay-fonts',
			ZP_THEME_URI . '/assets/fonts.css',
			array(),
			zp_asset_ver( 'assets/fonts.css' )
		);

		wp_enqueue_style(
			'zoneplay-editor',
			ZP_THEME_URI . '/assets/css/editor.css',
			array( 'zoneplay-fonts' ),
			zp_asset_ver( 'assets/css/editor.css' )
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
