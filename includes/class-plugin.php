<?php

/**
 * Main Plugin Class
 *
 * @package ExamplePlugin
 * @since 1.0.0
 */

namespace ExamplePlugin;

use PowerPlugins\Core\Component;

defined( 'ABSPATH' ) || die();

/**
 * Main plugin class that initializes all components.
 *
 * This class extends PowerPlugins\Core\Component and serves as the entry point
 * for the Example Plugin, coordinating all other plugin components.
 */
class Plugin extends Component {

	/**
	 * Single instance of the plugin
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Admin hooks manager
	 *
	 * @var AdminHooks|null
	 */
	private $admin_hooks = null;

	/**
	 * Public hooks manager
	 *
	 * @var PublicHooks|null
	 */
	private $public_hooks = null;

	/**
	 * Settings controller
	 *
	 * @var Settings|null
	 */
	private $settings = null;

	/**
	 * Initialize the plugin
	 *
	 * @param string $name    Plugin name.
	 * @param string $version Plugin version.
	 */
	public function __construct( string $name, string $version ) {
		parent::__construct( $name, $version );
	}

	/**
	 * Get singleton instance
	 *
	 * @param string $name    Plugin name.
	 * @param string $version Plugin version.
	 * @return Plugin
	 */
	public static function get_instance( string $name, string $version ): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self( $name, $version );
		}

		return self::$instance;
	}

	/**
	 * Run the plugin
	 *
	 * Initialize all components and hook into WordPress.
	 */
	public function run(): void {
		// Register activation/deactivation hooks.
		register_activation_hook( PP_EXP_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( PP_EXP_FILE, array( $this, 'deactivate' ) );

		// Initialize settings.
		$this->settings = new Settings( $this->name, $this->version );

		// Load plugin text domain for translations.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp', array( $this, 'late_init' ) );
	}

	/**
	 * Plugin activation callback
	 *
	 * Runs when the plugin is activated.
	 */
	public function activate(): void {
		// Flush rewrite rules to ensure custom post types work.
		flush_rewrite_rules();

		// Set default options if not already set.
		if ( false === get_option( 'example_plugin_default_capacity' ) ) {
			update_option( 'example_plugin_default_capacity', 50 );
		}

		if ( false === get_option( 'example_plugin_date_format' ) ) {
			update_option( 'example_plugin_date_format', 'Y-m-d' );
		}

		if ( false === get_option( 'example_plugin_show_virtual_badge' ) ) {
			update_option( 'example_plugin_show_virtual_badge', 'true' );
		}

		// Log activation.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Example Plugin activated' );
		}
	}

	/**
	 * Plugin deactivation callback
	 *
	 * Runs when the plugin is deactivated.
	 */
	public function deactivate(): void {
		// Flush rewrite rules on deactivation.
		flush_rewrite_rules();

		// Log deactivation.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Example Plugin deactivated' );
		}

		// Note: We don't delete options on deactivation.
		// Users may want to keep their settings if they reactivate.
	}

	/**
	 * Load plugin text domain for translations
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain( 'example-plugin', false, dirname( plugin_basename( PP_EXP_FILE ) ) . '/languages/' );
	}

	/**
	 * Get settings controller
	 *
	 * @return Settings
	 */
	public function get_settings(): Settings {
		return $this->settings;
	}

	/**
	 * Get admin hooks manager
	 *
	 * @return AdminHooks
	 */
	public function get_admin_hooks(): AdminHooks {
		if ( is_null( $this->admin_hooks ) ) {
			$this->admin_hooks = new AdminHooks( $this->name, $this->version );
		}
		return $this->admin_hooks;
	}

	/**
	 * Get public hooks manager
	 *
	 * @return PublicHooks
	 */
	public function get_public_hooks(): PublicHooks {
		if ( is_null( $this->public_hooks ) ) {
			$this->public_hooks = new PublicHooks( $this->name, $this->version );
		}
		return $this->public_hooks;
	}

	/**
	 * WordPress init hook callback
	 *
	 * Runs during the init action. Register post types, taxonomies, and
	 * initialize public-facing hooks when not in admin area.
	 *
	 * @return void
	 */
	public function init() {
		// Init code here.

		if ( ! is_admin() ) {
			$public_hooks = $this->get_public_hooks();
			add_action( 'wp_enqueue_scripts', array( $public_hooks, 'enqueue_assets' ) );
		}
	}

	/**
	 * WordPress admin_init hook callback
	 *
	 * Runs during the admin_init action. Initialize admin-area hooks,
	 * register settings, and enqueue admin assets.
	 *
	 * @return void
	 */
	public function admin_init() {
		// Admin init code here.
		$admin_hooks = $this->get_admin_hooks();
			add_action( 'admin_enqueue_scripts', array( $admin_hooks, 'enqueue_assets' ) );
	}

	/**
	 * WordPress wp hook callback (late init)
	 *
	 * Runs during the wp action, after query parsing but before template loading.
	 * Use this for any initialization that requires query context.
	 *
	 * @return void
	 */
	public function late_init() {
		// Late init code here.
	}
}
