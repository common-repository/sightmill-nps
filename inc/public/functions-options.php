<?php
/**
 * Functions for handling plugin options.
 *
 */

/**
 * Conditional check to see if SightMill NPS script should be loaded on current page or not
 * in turn should show survey or not.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function smnps_get_pages() {

	return apply_filters( 'smnps_get_pages', smnps_get_setting( 'pages' ) );
}

/**
 * Conditional check to see if SightMill NPS script is added or not
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function smnps_get_script() {

    return apply_filters( 'smnps_get_script', smnps_get_setting( 'script' ) );
}

/**
 * Gets a setting from from the plugin settings in the database.
 *
 * @since  1.0.0
 * @access public
 * @return mixed
 */
function smnps_get_setting( $option = '' ) {

	$defaults = smnps_get_default_settings();

	$settings = wp_parse_args( get_option( 'sightmill-nps', $defaults ), $defaults );

	return isset( $settings[ $option ] ) ? $settings[ $option ] : false;
}

/**
 * Returns an array of the default plugin settings.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function smnps_get_default_settings() {

	return array(

		// @since 1.0.0
		'pages'  => array(),
		'script' => '',
	);
}
