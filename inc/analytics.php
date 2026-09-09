<?php
/**
 * Google Tag Manager gate.
 *
 * The GTM markup itself lives in header.php (in <head> and right after the
 * opening <body>); this file only decides whether it should be printed and
 * with which container ID.
 *
 * Container ID resolution (first hit wins):
 *   1. the ZP_GTM_ID constant, if defined in wp-config.php
 *   2. the Customizer setting (Appearance → Customize → Google Tag Manager)
 *   3. the packaged default, GTM-5Q5RGBPL
 * An empty result disables GTM everywhere.
 *
 * GTM is emitted on live sites only and suppressed while developing so local
 * traffic never reaches the container. A request is treated as "developing"
 * when ANY of these is true:
 *   - the WP environment type is 'local' or 'development'
 *   - WP_DEBUG is on
 *   - WP_LOCAL_DEV is defined and truthy
 *   - the host is localhost / 127.0.0.1 / ::1, or ends in .local / .test /
 *     .localhost / .dev / .mamp
 *
 * Note: an unset WP_ENVIRONMENT_TYPE reports as 'production', so 'production'
 * is NOT treated as a positive signal on its own — one of the checks above
 * must fail to clear for GTM to load. On a real staging box with WP_DEBUG
 * left on, use ZP_GTM_FORCE to load it anyway:
 *   define( 'ZP_GTM_FORCE', true );  // wp-config.php
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Packaged default container ID, used until one is set in the Customizer. */
if ( ! defined( 'ZP_GTM_ID_DEFAULT' ) ) {
	define( 'ZP_GTM_ID_DEFAULT', 'GTM-5Q5RGBPL' );
}

/**
 * The GTM container ID for this site.
 *
 * @return string e.g. "GTM-XXXXXXX", or '' when GTM is disabled.
 */
function zp_gtm_id() {
	if ( defined( 'ZP_GTM_ID' ) ) {
		return trim( (string) ZP_GTM_ID );
	}
	return trim( (string) get_theme_mod( 'zp_gtm_id', ZP_GTM_ID_DEFAULT ) );
}

/**
 * Whether the GTM snippet should be output for this request.
 *
 * @return bool
 */
function zp_load_gtm() {
	if ( '' === zp_gtm_id() ) {
		return false;
	}

	// Explicit override — wins over every check below.
	if ( defined( 'ZP_GTM_FORCE' ) ) {
		return (bool) ZP_GTM_FORCE;
	}

	// Never track wp-admin, AJAX, cron, REST or CLI.
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return false;
	}

	// Explicit "this is a dev environment" markers.
	if ( function_exists( 'wp_get_environment_type' ) && in_array( wp_get_environment_type(), array( 'local', 'development' ), true ) ) {
		return false;
	}
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		return false;
	}
	if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) {
		return false;
	}

	// Host-based fallback for a dev box that sets none of the above.
	$host = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( (string) wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
	$host = preg_replace( '/:\d+$/', '', $host ); // strip port

	if ( '' === $host || 'localhost' === $host || '127.0.0.1' === $host || '::1' === $host ) {
		return false;
	}
	foreach ( array( '.local', '.test', '.localhost', '.dev', '.mamp' ) as $suffix ) {
		if ( substr( $host, -strlen( $suffix ) ) === $suffix ) {
			return false;
		}
	}

	return true;
}

/**
 * Sanitize a GTM container ID from the Customizer.
 *
 * Accepts "GTM-XXXXXXX" or a bare "XXXXXXX" (the GTM- prefix is added),
 * upper-cases it, and drops anything that isn't a valid container ID.
 *
 * @param string $value Raw setting value.
 * @return string
 */
function zp_sanitize_gtm_id( $value ) {
	$value = strtoupper( preg_replace( '/[^A-Za-z0-9\-]/', '', (string) $value ) );

	if ( '' === $value ) {
		return '';
	}
	if ( 0 !== strpos( $value, 'GTM-' ) ) {
		$value = 'GTM-' . ltrim( $value, '-' );
	}

	return preg_match( '/^GTM-[A-Z0-9]{4,}$/', $value ) ? $value : '';
}
