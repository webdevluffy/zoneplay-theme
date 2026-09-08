<?php
/**
 * Theme supports, menus, image sizes, i18n.
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'zoneplay', ZP_THEME_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'custom-logo', array(
			'height'      => 133,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		) );
		add_theme_support( 'html5', array(
			'search-form',
			'gallery',
			'caption',
			'style',
			'script',
		) );

		// Registered now for the "make the header/footer dynamic" pass.
		register_nav_menus( array(
			'primary' => __( 'Primary Navigation', 'zoneplay' ),
			'legal'   => __( 'Footer Legal Links', 'zoneplay' ),
		) );
	}
);

// Content width used by oEmbeds and wide alignments (max-w-7xl = 80rem).
add_action(
	'after_setup_theme',
	function () {
		$GLOBALS['content_width'] = 1280;
	},
	0
);
