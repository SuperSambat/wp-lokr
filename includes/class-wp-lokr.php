<?php
/**
 * WP_Lokr setup
 *
 * @package WP_Lokr
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main WP_Lokr Class.
 *
 * @class WP_Lokr
 */
final class WP_Lokr {

	/**
	 * WP_Lokr version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var WP_Lokr
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WP_Lokr Instance.
	 *
	 * Ensures only one instance of WP_Lokr is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @see WP_Lokr()
	 * @return WP_Lokr - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WP_Lokr Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define WP_Lokr Constants.
	 */
	private function define_constants() {
		define( 'WP_LOKR_ABSPATH', dirname( WP_LOKR_PLUGIN_FILE ) . '/' );
		define( 'WP_LOKR_PLUGIN_BASENAME', plugin_basename( WP_LOKR_PLUGIN_FILE ) );
		define( 'WP_LOKR_VERSION', $this->version );
		define( 'WP_LOKR_NOTICE_MIN_PHP_VERSION', '7.2' );
		define( 'WP_LOKR_NOTICE_MIN_WP_VERSION', '5.2' );
		define( 'WP_LOKR_PHP_MIN_REQUIREMENTS_NOTICE', 'wp_php_min_requirements_' . WP_LOKR_NOTICE_MIN_PHP_VERSION . '_' . WP_LOKR_NOTICE_MIN_WP_VERSION );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		/**
		 * Core classes.
		 */
		include_once WP_LOKR_ABSPATH . 'includes/class-wp-lokr-install.php';
		include_once WP_LOKR_ABSPATH . 'includes/class-wp-lokr-post-types.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		register_activation_hook( WP_LOKR_PLUGIN_FILE, array( 'WP_Lokr_Install', 'install' ) );
	}
}
