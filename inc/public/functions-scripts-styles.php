<?php

/**
 * Frontend-related functions and filters.
 *
 */

# Register scripts and styles.
add_action( 'wp_enqueue_scripts', 'smnps_register_scripts', 0 );

# Enqueue scripts and styles.
add_action( 'wp_enqueue_scripts', 'smnps_enqueue_scripts', 10 );

/**
 * Registers scripts.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function smnps_register_scripts() {

	$current_ID = get_the_ID();
	$pages      = smnps_get_pages();
	$script     = smnps_get_script();

	$script = stripslashes( $script );

	/**
	 * If post type is not page, just baill
	 * OR
	 * If pages are not set on settings page, just baill
	 * OR
	 * If script is not set on settings page, just baill
	 */
	if ( ! is_page() || ! $pages || ! $script ) {
		return;
	}

	# If script should not be loaded on current page, just baill
	if ( ! in_array( $current_ID, $pages ) ) {
		return;
	}

 	wp_add_inline_script( 'jquery-migrate', $script, 'after' );
}

/**
 * Enqueue scripts.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function smnps_enqueue_scripts() {
	if ( ! wp_script_is( 'jquery', 'done' ) ) {
		wp_enqueue_script( 'jquery' );
	}
}
