<?php
/**
 * Front-end weight trimming — strip WordPress defaults this theme doesn't
 * use so pages ship only the CSS/JS they need.
 *
 * Page content comes from the Editable HTML Block plugin, which renders raw
 * HTML with its own compiled Tailwind. None of the core block-library /
 * global-styles CSS is required on the front end; re-enable individual
 * pieces here if a page later needs a core block's default styling.
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * <head> cleanup
 * ---------------------------------------------------------------------- */
add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
		remove_action( 'wp_head', 'rest_output_link_wp_head' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'template_redirect', 'rest_output_link_header', 11 );

		// Emoji.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}
);

/**
 * Resource-hint trimming.
 *
 *  - dns-prefetch: drop the s.w.org emoji hint.
 *  - prefetch / prerender: drop WooCommerce Blocks' Cart & Checkout script
 *    prefetch. Once the cart has items, WooCommerce prefetches the whole
 *    @wordpress/* dependency tree for those blocks (~25 wp-includes/js/dist
 *    files) on every front-end page. It's idle-time + cached, so harmless,
 *    but this site's checkout isn't block-based — remove this block to get
 *    it back.
 */
add_filter(
	'wp_resource_hints',
	function ( $hints, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			return array_values( array_filter( $hints, function ( $h ) {
				return false === strpos( is_array( $h ) ? ( $h['href'] ?? '' ) : $h, 's.w.org' );
			} ) );
		}

		if ( 'prefetch' === $relation_type || 'prerender' === $relation_type ) {
			return array_values( array_filter( $hints, function ( $h ) {
				$href = is_array( $h ) ? ( $h['href'] ?? '' ) : $h;
				return false === strpos( $href, '/wp-includes/js/dist/' )
					&& false === strpos( $href, '/plugins/woocommerce/' );
			} ) );
		}

		return $hints;
	},
	20,
	2
);

/* -------------------------------------------------------------------------
 * Stylesheet / script trimming (front end only)
 * ---------------------------------------------------------------------- */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_admin() ) {
			return;
		}

		// Core block CSS + theme.json presets — not used; Tailwind covers the
		// chrome and the Editable HTML Block plugin styles its own content.
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'wp-emoji-styles' );

		// jQuery Migrate — nothing here needs the legacy shims.
		$scripts = wp_scripts();
		if ( isset( $scripts->registered['jquery'] ) ) {
			$scripts->registered['jquery']->deps = array_diff(
				$scripts->registered['jquery']->deps,
				array( 'jquery-migrate' )
			);
		}
	},
	100
);

// Stop WP from injecting the separate SVG duotone / global-styles <svg> and
// the "skip-link-focus-fix" inline script that this theme doesn't rely on.
add_action(
	'wp_footer',
	function () {
		remove_action( 'wp_footer', 'the_block_template_skip_link' );
	},
	0
);

/* -------------------------------------------------------------------------
 * Comments — the site doesn't use them
 * ---------------------------------------------------------------------- */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 20 );

add_action(
	'init',
	function () {
		// Drop the per-post comment-feed <link> and the admin-bar Comments node.
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}
);

add_action(
	'wp_before_admin_bar_render',
	function () {
		global $wp_admin_bar;
		$wp_admin_bar->remove_node( 'comments' );
	}
);

add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'edit-comments.php' );
	}
);

/* -------------------------------------------------------------------------
 * Misc
 * ---------------------------------------------------------------------- */

// No XML-RPC surface.
add_filter( 'xmlrpc_enabled', '__return_false' );

// Disable wptexturize. Page bodies are authored as raw HTML in the Editable
// HTML Block, mirroring astro-build-site character-for-character (straight
// quotes/apostrophes, literal -- and ...). Texturize would silently swap in
// curly quotes / en–em dashes / ellipses and break that parity. The theme's
// header/footer are hardcoded PHP and never ran through it anyway.
add_filter( 'run_wptexturize', '__return_false' );

// Keep the REST API for logged-in editors (block editor needs it) but drop
// the public link header noise handled above. Nothing else to do here.
