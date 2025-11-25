<?php

/**
 * Settings Class
 *
 * @package ExamplePlugin
 * @since 1.0.0
 */

namespace ExamplePlugin;

use PowerPlugins\Core\Settings\SettingsCore;

defined( 'ABSPATH' ) || die();

/**
 * Plugin settings controller.
 *
 * This class extends PowerPlugins\Core\Settings\SettingsCore and handles
 * all plugin settings, including rendering the settings page and saving options.
 */
class Settings extends SettingsCore {

	/**
	 * Render the settings page
	 *
	 * Displays the plugin settings page in the WordPress admin.
	 * Checks user capabilities before rendering.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( $this->settings_cap ) ) {
			printf( '<p>%s</p>', esc_html__( 'Not authorized', 'example-plugin' ) );
		} else {
			$this->open_wrap();

			$this->render_page_title();

			$this->open_form();

			$settings = $this;

			include PP_EXP_ADMIN_TEMPLATES_DIR . 'general-settings.php';

			submit_button( esc_html__( 'Save Changes', 'example-plugin' ) );

			$this->close_form();

			$this->close_wrap();
		}
	}

	/**
	 * Save settings
	 *
	 * Process and save plugin settings from POST data.
	 * Should be called when the settings form is submitted.
	 */
	public function save_settings() {
		// $this->set_bool(
		// OPT_ENABLE_SPAM_CHECK_FOR_LOGGED_IN_USERS,
		// array_key_exists(OPT_ENABLE_SPAM_CHECK_FOR_LOGGED_IN_USERS, $_POST)
		// );
	}

	/**
	 * Get default value for an option
	 *
	 * Returns the default value for a specific plugin option.
	 * Override this method to provide custom defaults.
	 *
	 * @param string $option_name The option name to get the default value for.
	 * @return mixed The default value, or null if no default is defined.
	 */
	public function get_default_value( $option_name ) {
		$value = null;

		// switch ($option_name) {
		// case OPT_USED_QUOTA_EMAIL_ADDRESS:
		// $value = get_option('admin_email');
		// break;

		// default:
		// ...
		// break;
		// }

		return $value;
	}
}
