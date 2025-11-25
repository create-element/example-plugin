<?php
/**
 * Admin Hooks Class
 *
 * @package ExamplePlugin
 * @since 1.0.0
 */

namespace ExamplePlugin;

use PowerPlugins\Core\Component;

defined( 'ABSPATH' ) || die();

/**
 * Admin hooks manager.
 *
 * This class handles all WordPress admin-area hooks, actions, and filters.
 * Extends PowerPlugins\Core\Component to inherit plugin initialization.
 */
class AdminHooks extends Component {

	/**
	 * Enqueue admin assets
	 *
	 * Registers and enqueues admin-area scripts and styles.
	 * Called via WordPress admin_enqueue_scripts action.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// Enqueue admin-area scripts and styles here.
		error_log( 'AdminHooks: enqueue_assets called' );
	}
}
