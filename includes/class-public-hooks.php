<?php
/**
 * Public Hooks Class
 *
 * @package ExamplePlugin
 * @since 1.0.0
 */

namespace ExamplePlugin;

use PowerPlugins\Core\Component;

defined( 'ABSPATH' ) || die();

/**
 * Public hooks manager.
 *
 * This class handles all WordPress public-facing hooks, actions, and filters.
 * Extends PowerPlugins\Core\Component to inherit plugin initialization.
 */
class PublicHooks extends Component {

	/**
	 * Enqueue public assets
	 *
	 * Registers and enqueues public-facing scripts and styles.
	 * Called via WordPress wp_enqueue_scripts action.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// Enqueue public-facing scripts and styles here.
		error_log( 'PublicHooks: enqueue_assets called' );
	}
}
