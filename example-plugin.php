<?php
/**
 * Plugin Name:       Example Plugin
 * Plugin URI:        https://power-plugins.com/plugins/example-plugin
 * Description:       An example Power Plugin demonstrating modern development practices with Composer, PSR-4 autoloading, and the Power Plugins Core library.
 * Version:           1.0.6
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            Power Plugins
 * Author URI:        https://power-plugins.com/
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       example-plugin
 * Domain Path:       /languages
 * Update URI:        https://power-plugins.com/plugins/example-plugin
 *
 * @package ExamplePlugin
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die();

const PP_EXP_NAME    = 'example-plugin';
const PP_EXP_VERSION = '1.0.6';

define( 'PP_EXP_FILE', __FILE__ );
define( 'PP_EXP_DIR', plugin_dir_path( __FILE__ ) );
define( 'PP_EXP_URL', plugin_dir_url( __FILE__ ) );
define( 'PP_EXP_ADMIN_TEMPLATES_DIR', trailingslashit( PP_EXP_DIR . 'admin-templates' ) );
define( 'PP_EXP_PUBLIC_TEMPLATES_DIR', trailingslashit( PP_EXP_DIR . 'public-templates' ) );
define( 'PP_EXP_EMAIL_TEMPLATES_DIR', trailingslashit( PP_EXP_DIR . 'email-templates' ) );
define( 'PP_EXP_EMAIL_TEMPLATES', trailingslashit( PP_EXP_DIR . 'email-templates' ) );
define( 'PP_EXP_ASSETS_DIR', trailingslashit( PP_EXP_DIR . 'assets' ) );
define( 'PP_EXP_ASSETS_URL', trailingslashit( PP_EXP_URL . 'assets' ) );

/**
 * ============================================================================
 * COMPOSER AUTOLOADER
 * ============================================================================
 *
 * This plugin now uses Composer to manage dependencies.
 * The old pp-core.php and pwpl/ directories are kept for reference only.
 *
 * To set up for development:
 * 1. Clone pp-core and pp-updater repos to /var/www/devx.headwall.tech/web/wp-content/libraries/
 * 2. Run `composer install` in this plugin directory
 * 3. Composer will symlink to the local library copies for development
 *
 * See docs/04-development-workflow.md for complete setup instructions.
 */

// Composer autoloader (will be available after running `composer install`).
if ( file_exists( PP_EXP_DIR . 'vendor/autoload.php' ) ) {
	require_once PP_EXP_DIR . 'vendor/autoload.php';
} else {
	// Development mode: If Composer isn't set up yet, show admin notice.
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-error"><p>';
			echo '<strong>Example Plugin:</strong> Please run <code>composer install</code> in the plugin directory. ';
			echo 'See <code>docs/04-development-workflow.md</code> for setup instructions.';
			echo '</p></div>';
		}
	);
	return; // Don't initialize plugin without dependencies.
}

/**
 * ============================================================================
 * LEGACY FILES (REFERENCE ONLY - DO NOT REQUIRE)
 * ============================================================================
 *
 * These files are kept as reference during the migration to Composer:
 * - pp-core.php (old monolithic core library)
 * - pp-assets/ (old asset directory)
 * - pwpl/ (old updater library)
 *
 * NEW COMPOSER PACKAGES:
 * - power-plugins/core (replaces pp-core.php)
 * - power-plugins/updater (replaces pwpl/)
 */

// Load utility files (not namespaced, so not autoloaded).
require_once PP_EXP_DIR . 'constants.php';
require_once PP_EXP_DIR . 'functions.php';

// NOTE: includes/class-*.php files are autoloaded via Composer PSR-4
// No need to require them manually - the autoloader handles it.

/**
 * ============================================================================
 * PLUGIN INITIALIZATION
 * ============================================================================
 */

use ExamplePlugin\Plugin;

// Initialize and run the plugin.
$example_plugin = Plugin::get_instance( PP_EXP_NAME, PP_EXP_VERSION );
$example_plugin->run();
