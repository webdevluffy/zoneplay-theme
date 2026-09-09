<?php
/**
 * Customizer settings.
 *
 * - "Navigation Button" (inside the core Menus panel): the header
 *   call-to-action. Clearing the label hides it; see zp_nav_button().
 * - "Footer Content": the editable footer text — brand blurb, social
 *   links, opening times and Find Us details. The footer logo reuses the
 *   Site Identity logo; the legal row uses the "legal" menu location.
 * - "Google Tag Manager": the container ID (see zp_gtm_id() /
 *   zp_load_gtm() in inc/analytics.php for how it is used and gated).
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Default label for the header button, shared by the setting and the fallback. */
function zp_nav_button_default_label() {
	return __( 'Book Soft Play', 'zoneplay' );
}

/**
 * Default values for every "Footer Content" setting.
 *
 * Shared between the Customizer registration and zp_footer_mod() so the
 * front end shows the original copy until the owner overrides it.
 *
 * @return array<string,string>
 */
function zp_footer_defaults() {
	return array(
		'zp_footer_heading'    => __( 'Indoor Soft Play in Cardiff', 'zoneplay' ),
		'zp_footer_blurb'      => __( 'Zone Play Cardiff is an indoor soft play centre at Unit 5, Stadium Close (off Penarth Road), perfect for babies, toddlers, and young children. With free parking, a café, birthday parties, and plenty of seating for parents, we’re one of Cardiff’s favourite family-friendly rainy-day activities.', 'zoneplay' ),
		'zp_footer_facebook'   => 'https://www.facebook.com/zoneplaycardiff',
		'zp_footer_instagram'  => 'https://www.instagram.com/zoneplaycardiff/',
		'zp_footer_hours'      => "Monday | Closed\nTuesday – Saturday | 10am – 5:30pm\nSunday | 11am – 4pm",
		'zp_footer_hours_note' => __( 'Open daily during school holidays & Bank Holidays', 'zoneplay' ),
		'zp_footer_address'    => "Unit 5, Stadium Cl\nCardiff, CF11 8TS",
		'zp_footer_email'      => 'info@zoneplaycardiff.co.uk',
		'zp_footer_phone'      => '02920 239777',
	);
}

/**
 * Read a "Footer Content" theme mod, falling back to its packaged default.
 *
 * @param string $key Setting id from zp_footer_defaults().
 * @return string
 */
function zp_footer_mod( $key ) {
	$defaults = zp_footer_defaults();
	return (string) get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {

		/* -----------------------------------------------------------------
		 * Menus panel → Navigation Button
		 * -------------------------------------------------------------- */
		$wp_customize->add_section(
			'zp_nav_button',
			array(
				'title'       => __( 'Navigation Button', 'zoneplay' ),
				'description' => __( 'Call-to-action button shown in the header menu. Leave the label empty to hide it.', 'zoneplay' ),
				'panel'       => 'nav_menus',
				'priority'    => 100,
			)
		);

		$wp_customize->add_setting(
			'zp_nav_button_label',
			array(
				'default'           => zp_nav_button_default_label(),
				'type'              => 'theme_mod',
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'zp_nav_button_label',
			array(
				'label'       => __( 'Button label', 'zoneplay' ),
				'description' => __( 'Empty = button hidden.', 'zoneplay' ),
				'section'     => 'zp_nav_button',
				'type'        => 'text',
			)
		);

		$wp_customize->add_setting(
			'zp_nav_button_url',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'zp_nav_button_url',
			array(
				'label'       => __( 'Button link', 'zoneplay' ),
				'description' => __( 'Leave empty to point at the Contact page.', 'zoneplay' ),
				'section'     => 'zp_nav_button',
				'type'        => 'url',
			)
		);

		/* -----------------------------------------------------------------
		 * Footer Content
		 * -------------------------------------------------------------- */
		$wp_customize->add_section(
			'zp_footer',
			array(
				'title'       => __( 'Footer Content', 'zoneplay' ),
				'description' => __( 'Text shown in the site footer. The footer logo uses the Site Identity logo; the legal links use the "Footer Legal Links" menu.', 'zoneplay' ),
				'priority'    => 160,
			)
		);

		$defaults = zp_footer_defaults();

		$footer_controls = array(
			'zp_footer_heading'    => array(
				'label'       => __( 'Brand column heading', 'zoneplay' ),
				'type'        => 'text',
				'sanitize'    => 'sanitize_text_field',
				'description' => __( 'Leave empty to hide.', 'zoneplay' ),
			),
			'zp_footer_blurb'      => array(
				'label'       => __( 'Brand blurb', 'zoneplay' ),
				'type'        => 'textarea',
				'sanitize'    => 'sanitize_textarea_field',
				'description' => __( 'Leave empty to hide.', 'zoneplay' ),
			),
			'zp_footer_facebook'   => array(
				'label'       => __( 'Facebook URL', 'zoneplay' ),
				'type'        => 'url',
				'sanitize'    => 'esc_url_raw',
				'description' => __( 'Leave empty to hide the icon.', 'zoneplay' ),
			),
			'zp_footer_instagram'  => array(
				'label'       => __( 'Instagram URL', 'zoneplay' ),
				'type'        => 'url',
				'sanitize'    => 'esc_url_raw',
				'description' => __( 'Leave empty to hide the icon.', 'zoneplay' ),
			),
			'zp_footer_hours'      => array(
				'label'       => __( 'Opening times', 'zoneplay' ),
				'type'        => 'textarea',
				'sanitize'    => 'sanitize_textarea_field',
				'description' => __( 'One row per line as "Day | Hours". A row whose hours say "Closed" shows in red.', 'zoneplay' ),
			),
			'zp_footer_hours_note' => array(
				'label'       => __( 'Opening times note', 'zoneplay' ),
				'type'        => 'text',
				'sanitize'    => 'sanitize_text_field',
				'description' => __( 'Small line under the times. Leave empty to hide.', 'zoneplay' ),
			),
			'zp_footer_address'    => array(
				'label'       => __( 'Address', 'zoneplay' ),
				'type'        => 'textarea',
				'sanitize'    => 'sanitize_textarea_field',
				'description' => __( 'One line per row. Leave empty to hide.', 'zoneplay' ),
			),
			'zp_footer_email'      => array(
				'label'       => __( 'Email address', 'zoneplay' ),
				'type'        => 'text',
				'sanitize'    => 'sanitize_email',
				'description' => __( 'Leave empty to hide.', 'zoneplay' ),
			),
			'zp_footer_phone'      => array(
				'label'       => __( 'Phone number', 'zoneplay' ),
				'type'        => 'text',
				'sanitize'    => 'sanitize_text_field',
				'description' => __( 'Shown as typed; the tel: link strips spaces. Leave empty to hide.', 'zoneplay' ),
			),
		);

		foreach ( $footer_controls as $id => $control ) {
			$wp_customize->add_setting(
				$id,
				array(
					'default'           => isset( $defaults[ $id ] ) ? $defaults[ $id ] : '',
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'transport'         => 'refresh',
					'sanitize_callback' => $control['sanitize'],
				)
			);
			$wp_customize->add_control(
				$id,
				array(
					'label'       => $control['label'],
					'description' => $control['description'],
					'section'     => 'zp_footer',
					'type'        => $control['type'],
				)
			);
		}

		/* -----------------------------------------------------------------
		 * Google Tag Manager
		 * -------------------------------------------------------------- */
		$wp_customize->add_section(
			'zp_analytics',
			array(
				'title'       => __( 'Google Tag Manager', 'zoneplay' ),
				'description' => __( 'The container loads on the live site only — never on local, WP_DEBUG or *.local hosts. Leave empty to disable GTM. Ignored if a ZP_GTM_ID constant is set in wp-config.php.', 'zoneplay' ),
				'priority'    => 165,
			)
		);

		$wp_customize->add_setting(
			'zp_gtm_id',
			array(
				'default'           => ZP_GTM_ID_DEFAULT,
				'type'              => 'theme_mod',
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'sanitize_callback' => 'zp_sanitize_gtm_id',
			)
		);
		$wp_customize->add_control(
			'zp_gtm_id',
			array(
				'label'       => __( 'Container ID', 'zoneplay' ),
				'description' => __( 'e.g. GTM-XXXXXXX', 'zoneplay' ),
				'section'     => 'zp_analytics',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'GTM-XXXXXXX' ),
			)
		);

		$zp_gtm_control = $wp_customize->get_control( 'zp_gtm_id' );
		if ( defined( 'ZP_GTM_ID' ) && $zp_gtm_control ) {
			$zp_gtm_control->description = sprintf(
				/* translators: %s: the container ID currently forced by the constant. */
				__( 'Overridden by the ZP_GTM_ID constant in wp-config.php (currently %s). Edit that to change it.', 'zoneplay' ),
				'' !== zp_gtm_id() ? zp_gtm_id() : __( '(empty — GTM disabled)', 'zoneplay' )
			);
		}
	}
);
