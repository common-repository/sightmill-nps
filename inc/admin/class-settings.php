<?php
/**
 * Plugin settings screen.
 *
 */

namespace SightmillNps;

/**
 * Sets up and handles the plugin settings screen.
 *
 * @since  1.0.0
 * @access public
 */
final class Settings_Page {

	/**
	 * Settings page name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $settings_page = '';

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
		}

		return $instance;
	}

	/**
	 * Sets up the needed actions for adding and saving the meta boxes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	private function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_filter( 'plugin_action_links', array( $this, 'add_settings_link' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'add_settings_link' ), 10, 2 );
	}

	/**
	 * Sets up custom admin menus.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_menu() {

		// Create the settings page.
		$this->settings_page = add_menu_page(
			esc_html__( 'SightMill', 'sightmill-nps' ),
			esc_html__( 'SightMill NPS', 'sightmill-nps' ),
		   'manage_options',
		   'sightmillnps-settings',
			array( $this, 'settings_page' ),
		   '',
		   24
		);

		if ( $this->settings_page ) {

			// Register settings.
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}
	}

	/**
	 * Add a link to the settings on the Plugins screen.
	 */
	public function add_settings_link( $links, $file ) {

		if ( $file === 'sightmill-nps/sightmill-nps.php' && current_user_can( 'manage_options' ) ) {
			if ( current_filter() === 'plugin_action_links' ) {
				$url = admin_url( 'admin.php?page=sightmillnps-settings' );
			} else {
				$url = admin_url( '/network/admin.php?page=sightmillnps-settings' );
			}

			// Prevent warnings in PHP 7.0+ when a plugin uses this filter incorrectly.
			$links = (array) $links;
			$links[] = sprintf( '<a href="%s">%s</a>', $url, __( 'Settings', 'sightmill-nps' ) );
		}

		return $links;
	}

	/**
	 * Registers the plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function register_settings() {

		// Register the setting.
		register_setting( 'sightmill_nps_settings', 'sightmill-nps', array( $this, 'validate_settings' ) );

		/* === Settings Sections === */

		add_settings_section( 'general', esc_html__( 'General Settings', 'sightmill-nps' ), array( $this, 'section_general' ), $this->settings_page );

		/* === Settings Fields === */

		// General section fields
		add_settings_field( 'pages', esc_html__( 'Pages (required)', 'sightmill-nps' ), array( $this, 'field_pages' ), $this->settings_page, 'general' );
		add_settings_field( 'script', esc_html__( 'Script (required)', 'sightmill-nps' ), array( $this, 'field_script' ), $this->settings_page, 'general' );
	}

	/**
	 * Validates the plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $input
	 * @return array
	 */
	function validate_settings( $settings ) {

		// Text boxes.
		$settings['pages'] = isset( $settings['pages'] ) ? $settings['pages'] : array();

		// Kill evil scripts.
		$settings['script'] = stripslashes( wp_filter_post_kses( addslashes( $settings['script'] ) ) );


		// Return the validated/sanitized settings.
		return $settings;
	}

	/**
	 * General section callback.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function section_general() { ?>

		<!--<p class="description">
			<?php esc_html_e( 'General SightMill NPS settings for your site.', 'sightmill-nps' ); ?>
		</p>-->
	<?php }

	/**
	 * Portfolio title field callback.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function field_pages() {
		$selected_pages = smnps_get_pages();

		?>

		<label>
			<select name="sightmill-nps[pages][]" id="sightmill-nps_pages" multiple="multiple">
				<?php
					$pages = get_pages();

					foreach ( $pages as $page ) {
						$selected = false;

						if ( in_array( $page->ID, $selected_pages ) ) {
							$selected = true;
						}

						$option = '<option value="' . $page->ID . '" ' . selected( $selected, true, false ) . ' >';
						$option .= $page->post_title;
						$option .= '</option>';

						echo $option;
					}
				?>
			</select>
			<br />
			<span class="description"><?php esc_html_e( 'Select page(s) to display the NPS survey.', 'sightmill-nps' ); ?></span>
		</label>
	<?php }

	/**
	 * Portfolio description field callback.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function field_script() {
		$current_script = smnps_get_script();
		//esc_textarea
		?>
		<textarea class="widefat" rows="16" cols="20" id="sightmill-nps_script" name="sightmill-nps[script]"><?php echo ( isset( $current_script ) ? stripslashes( $current_script ) : '' ); ?></textarea>
		<p>
			<span class="description"><?php esc_html_e( 'Paste your unique SightMill tracker script.', 'sightmill-nps' ); ?></span>
		</p>
	<?php }

	/**
	 * Renders the settings page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function settings_page() {

		?>
		<style>
		#poststuff h2 {
			display: none;
		}
		</style>
		<div class="wrap">
			<h1><?php esc_html_e( 'SightMill NPS » Settings', 'sightmill-nps' ); ?></h1>

			<?php settings_errors(); ?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? 1 : 2; ?>">
					<div id="post-body-content">
						<form method="post" action="options.php">
							<?php settings_fields( 'sightmill_nps_settings' ); ?>
							<?php do_settings_sections( $this->settings_page ); ?>
							<?php submit_button( esc_attr__( 'Save Settings', 'sightmill-nps' ), 'primary' ); ?>
						</form>
						</div><!-- #post-body-content -->
						<div id="postbox-container-1" class="postbox-container side">
			                <?php

			                require_once( plugin()->dir . '/views/sidebar.php' );
			                ?>

						</div><!-- .post-box-container -->
				</div><!-- #post-body -->
			</div><!-- #poststuff -->

		</div><!-- wrap -->
	<?php }
}

Settings_Page::get_instance();
