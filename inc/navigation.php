<?php
/**
 * Header logo + primary navigation.
 *
 * The markup these produce is intentionally identical to the old static
 * header: the logo is still an <a> wrapping an <img> with the same classes,
 * and each menu entry is still a bare <a> (no <ul>/<li> chrome) carrying the
 * same base / active / inactive Tailwind class sets. Only the data source
 * changed — the logo now comes from the Customizer "Site Identity" logo and
 * the links from the "primary" menu location ("Main Menu").
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print just the site logo <img>, preferring the Customizer "Site Identity"
 * logo and falling back to the bundled asset so it is never empty.
 *
 * Used for both the linked header logo (via zp_the_logo()) and the plain,
 * unlinked footer logo.
 *
 * @param string $img_class Classes for the <img>.
 * @param array  $attrs     Extra <img> attributes (e.g. loading, fetchpriority, sizes).
 */
function zp_logo_img( $img_class = '', $attrs = array() ) {
	$name    = get_bloginfo( 'name' );
	$logo_id = (int) get_theme_mod( 'custom_logo' );

	$attrs = array_merge(
		array(
			'class'    => $img_class,
			'decoding' => 'async',
		),
		$attrs
	);

	if ( $logo_id && wp_attachment_is_image( $logo_id ) ) {
		$alt          = trim( (string) get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) );
		$attrs['alt'] = '' !== $alt ? $alt : $name . ' logo';
		echo wp_get_attachment_image( $logo_id, 'medium_large', false, $attrs );
		return;
	}

	if ( empty( $attrs['alt'] ) ) {
		$attrs['alt'] = $name . ' logo';
	}
	$html = '<img src="' . esc_url( get_theme_file_uri( 'assets/images/logo.webp' ) ) . '" width="200" height="133"';
	foreach ( $attrs as $key => $value ) {
		$html .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
	}
	echo $html . ' />'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_attr() above.
}

/**
 * Print the header site logo: the <img> wrapped in a home link.
 *
 * @param string $img_class  Classes for the <img>.
 * @param string $link_class Classes for the wrapping <a>.
 */
function zp_the_logo( $img_class = 'h-24 md:h-28 w-auto drop-shadow-md', $link_class = 'hover:scale-105 transition-transform' ) {
	printf(
		'<a href="%s" class="%s" aria-label="%s">',
		esc_url( home_url( '/' ) ),
		esc_attr( $link_class ),
		esc_attr( get_bloginfo( 'name' ) . ' home' )
	);

	zp_logo_img(
		$img_class,
		array(
			'sizes'         => '(min-width: 768px) 168px, 144px',
			'fetchpriority' => 'high',
		)
	);

	echo '</a>';
}

/**
 * Render a menu location as a flat list of bare <a> tags.
 *
 * @param string $location Registered theme menu location ('primary', 'legal').
 * @param array  $args {
 *     @type string $aria_label     aria-label for the wrapping <nav>, or '' to skip the <nav>.
 *     @type string $nav_class      Classes for the wrapping <nav> (only used when aria_label is set).
 *     @type string $base_class     Classes applied to every link.
 *     @type string $active_class   Extra classes for the current page's link.
 *     @type string $inactive_class Extra classes for every other link.
 * }
 */
function zp_menu( $location, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'aria_label'     => '',
			'nav_class'      => '',
			'base_class'     => '',
			'active_class'   => '',
			'inactive_class' => '',
		)
	);

	if ( ! has_nav_menu( $location ) ) {
		return;
	}

	if ( '' !== $args['aria_label'] ) {
		printf( '<nav class="%s" aria-label="%s">', esc_attr( $args['nav_class'] ), esc_attr( $args['aria_label'] ) );
	}

	wp_nav_menu(
		array(
			'theme_location'    => $location,
			'container'         => false,
			'items_wrap'        => '%3$s',
			'fallback_cb'       => false,
			'depth'             => 1,
			'walker'            => new ZP_Walker_Nav_Menu(),
			// Custom, read by the walker.
			'zp_base_class'     => $args['base_class'],
			'zp_active_class'   => $args['active_class'],
			'zp_inactive_class' => $args['inactive_class'],
		)
	);

	if ( '' !== $args['aria_label'] ) {
		echo '</nav>';
	}
}

/**
 * Print the header call-to-action button, unless its label has been cleared
 * in the Customizer (Menus → Navigation Button).
 *
 * @param string $link_class    Classes for the <a>.
 * @param string $wrapper_class Optional classes for a wrapping <div>; the
 *                              wrapper (and everything) is skipped when the
 *                              label is empty.
 */
function zp_nav_button( $link_class, $wrapper_class = '' ) {
	$label = trim( (string) get_theme_mod( 'zp_nav_button_label', zp_nav_button_default_label() ) );
	if ( '' === $label ) {
		return;
	}

	$url = trim( (string) get_theme_mod( 'zp_nav_button_url', '' ) );
	if ( '' === $url ) {
		$url = home_url( '/contact/' );
	}

	if ( '' !== $wrapper_class ) {
		printf( '<div class="%s">', esc_attr( $wrapper_class ) );
	}

	printf(
		'<a href="%s" class="%s">%s</a>',
		esc_url( $url ),
		esc_attr( $link_class ),
		esc_html( $label )
	);

	if ( '' !== $wrapper_class ) {
		echo '</div>';
	}
}

/**
 * Nav walker that emits bare <a> tags with theme class sets — no <ul>/<li>.
 */
class ZP_Walker_Nav_Menu extends Walker_Nav_Menu {

	/** Flat menu: no submenu wrappers. */
	public function start_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_el( &$output, $data_object = null, $depth = 0, $args = null ) {}

	/**
	 * @param string   $output Passed by reference; appended to.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth (always 0 here).
	 * @param stdClass $args   wp_nav_menu args, incl. our zp_* class sets.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes = (array) $item->classes;
		$active  = in_array( 'current-menu-item', $classes, true )
			|| in_array( 'current-menu-parent', $classes, true )
			|| in_array( 'current-menu-ancestor', $classes, true )
			|| in_array( 'current_page_item', $classes, true )
			|| in_array( 'current_page_parent', $classes, true );

		$class = trim(
			$args->zp_base_class . ' ' . ( $active ? $args->zp_active_class : $args->zp_inactive_class )
		);

		$url    = ! empty( $item->url ) ? $item->url : '#';
		$target = ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$rel    = ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$title  = apply_filters( 'the_title', $item->title, $item->ID );

		$output .= sprintf(
			'<a href="%1$s" class="%2$s"%3$s%4$s%5$s>%6$s</a>',
			esc_url( $url ),
			esc_attr( $class ),
			$active ? ' aria-current="page"' : '',
			$target,
			$rel,
			esc_html( $title )
		);
	}
}
