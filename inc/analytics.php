<?php
/**
 * Google Tag Manager gate.
 *
 * The GTM markup itself lives in header.php (in <head> and right after the
 * opening <body>); this file only decides whether it should be printed.
 *
 * GTM is emitted on live sites only. It is suppressed while developing so
 * local traffic never reaches the container. "Local" is detected from, in
 * order: an explicit WP environment type, WP_DEBUG, then the request host.
 *
 * Force it on locally (e.g. to test the container) with:
 *   define( 'ZP_GTM_FORCE', true );  // wp-config.php
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Container ID. Empty string disables GTM everywhere.
if ( ! defined( 'ZP_GTM_ID' ) ) {
	define( 'ZP_GTM_ID', 'GTM-5Q5RGBPL' );
}

/**
 * Whether the GTM snippet should be output for this request.
 *
 * @return bool
 */
function zp_load_gtm() {
	if ( '' === (string) ZP_GTM_ID ) {
		return false;
	}

	if ( defined( 'ZP_GTM_FORCE' ) && ZP_GTM_FORCE ) {
		return true;
	}

	// Never track wp-admin, AJAX, REST, CLI or logged-in editors' previews.
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return false;
	}

	// Explicit environment wins.
	if ( function_exists( 'wp_get_environment_type' ) ) {
		$env = wp_get_environment_type();
		if ( in_array( $env, array( 'local', 'development' ), true ) ) {
			return false;
		}
		if ( in_array( $env, array( 'staging', 'production' ), true ) ) {
			return true;
		}
	}

	// Fallbacks for when the environment type is unset (defaults to
	// "production" even on a dev box).
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		return false;
	}

	$host = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( (string) wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
	$host = preg_replace( '/:\d+$/', '', $host ); // strip port

	if ( '' === $host || 'localhost' === $host || '127.0.0.1' === $host || '::1' === $host ) {
		return false;
	}
	foreach ( array( '.local', '.test', '.localhost', '.dev', '.mamp' ) as $tld ) {
		if ( substr( $host, -strlen( $tld ) ) === $tld ) {
			return false;
		}
	}

	return true;
}
