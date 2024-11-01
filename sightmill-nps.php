<?php
/**
 * Plugin Name:       SightMill NPS
 * Plugin URI:        https://sightmill.com/plugin
 * Description:       Add a SightMill NPS customer feedback survey to your website.
 * Version:           1.0.3
 * Author:            Sightmill
 * Author URI:        https://sightmill.com/
 * Requires at least: 4.6
 * Tested up to:      5.2.2
 * license:           MIT
 *
 * @license           https://opensource.org/licenses/MIT
 */

namespace SightmillNps;

/**
 * Copyright 2017 SightMill Ltd
 */

/**
 * Singleton class for setting up the plugin.
 *
 * @since  1.0.0
 * @access public
 */
final class Plugin {

    /**
     * Tracks current plugin version throughout codebase.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $version = '1.0.3';

    /**
     * Plugin version.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $db_version = '';

    /**
     * Plugin min.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $min = '';

    /**
     * Minimum required PHP version.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $php_version = '5.6';

    /**
     * Plugin file path.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $plugin_file = '';

    /**
     * Plugin directory path.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $dir = '';

    /**
     * Plugin directory URI.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $uri = '';

    /**
     * Returns the instance.
     *
     * @since  1.0.0
     * @access public
     * @return object
     */
    public static function get_instance() {

        static $instance = null;

        if ( is_null( $instance ) ) {
            $instance = new self;
            $instance->setup();
            $instance->includes();
            $instance->setup_actions();
        }

        return $instance;
    }

    /**
     * Constructor method.
     *
     * @since  1.0.0
     * @access private
     * @return void
     */
    private function __construct() {
    }

    /**
     * Magic method to output a string if trying to use the object as a string.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public function __toString() {
        return 'sightmillnps';
    }

    /**
     * Magic method to keep the object from being cloned.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'sightmill-nps' ), '1.0.0' );
    }

    /**
     * Magic method to keep the object from being unserialized.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'sightmill-nps' ), '1.0.0' );
    }

    /**
     * Magic method to prevent a fatal error when calling a method that doesn't exist.
     *
     * @since  1.0.0
     * @access public
     * @return null
     */
    public function __call( $method = '', $args = array() ) {
        _doing_it_wrong( "SightmillNps_Plugin::{$method}", esc_html__( 'Method does not exist.', 'sightmill-nps' ), '1.0.0' );
        unset( $method, $args );
        return null;
    }

    /**
     * Sets up globals.
     *
     * @since  1.0.0
     * @access private
     * @return void
     */
    private function setup() {

        # Define plugin vars
        $this->plugin_file = __FILE__;

        # Main plugin directory path and URI.
        $this->dir = trailingslashit( plugin_dir_path( $this->plugin_file ) );
        $this->uri = trailingslashit( plugin_dir_url( $this->plugin_file ) );

        $this->min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    }

    /**
     * Loads files needed by the plugin.
     *
     * @since  1.0.0
     * @access private
     * @return void
     */
    private function includes() {

        # Check if we meet the minimum PHP version.
        if ( version_compare( PHP_VERSION, $this->php_version, '<' ) ) {

            # Add admin notice.
            add_action( 'admin_notices', array( $this, 'php_admin_notice' ) );

            # Bail.
            return;
        }

        require_once( $this->dir . 'inc/public/functions-options.php' );
        require_once( $this->dir . 'inc/public/functions-scripts-styles.php' );

        # Load admin files.
        if ( is_admin() ) {

            require_once( $this->dir . 'inc/admin/class-settings.php' );
        }
    }

    /**
     * Sets up main plugin actions and filters.
     *
     * @since  1.0.0
     * @access private
     * @return void
     */
    private function setup_actions() {

        # Internationalize the text strings used.
        add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );

        # Register activation hook.
        register_activation_hook( $this->plugin_file, array( $this, 'activation' ) );
    }

    /**
     * Loads the translation files.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public function i18n() {

        load_plugin_textdomain( 'sightmill-nps', false, trailingslashit( dirname( plugin_basename( $this->plugin_file ) ) ) . 'lang' );
    }

    /**
     * Method that runs only when the plugin is activated.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public function activation() {

        # Check PHP version requirements.
        if ( version_compare( PHP_VERSION, $this->php_version, '<' ) ) {

            # Make sure the plugin is deactivated.
            deactivate_plugins( plugin_basename( $this->plugin_file ) );

            # Add an error message and die.
            wp_die( $this->get_min_php_message() );
        }

    }

    /**
     * Returns a message noting the minimum version of PHP required.
     *
     * @since  1.0.0
     * @access private
     * @return void
     */
    private function get_min_php_message() {

        return sprintf(
            __( 'SightMill NPS requires PHP version %1$s. You are running version %2$s. Please upgrade and try again.', 'sightmill-nps' ),
            $this->php_version,
            PHP_VERSION
        );
    }

    /**
     * Outputs the admin notice that the user needs to upgrade their PHP version. It also
     * auto-deactivates the plugin.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public function php_admin_notice() {

        # Output notice.
        printf(
            '<div class="notice notice-error is-dismissible"><p><strong>%s</strong></p></div>',
            esc_html( $this->get_min_php_message() )
        );

        # Make sure the plugin is deactivated.
        deactivate_plugins( plugin_basename( $this->plugin_file ) );
    }
}

/**
 * Gets the instance of the `SightmillNps\Plugin` class.  This function is useful for quickly grabbing data
 * used throughout the plugin.
 *
 * @since  1.0.0
 * @access public
 * @return object
 */
function plugin() {
    return Plugin::get_instance();
}

// Let's do this thang!
plugin();
