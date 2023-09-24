<?php
/**
 * Plugin Name: WP Lokr
 * Plugin URI: https://wp-lokr.com/
 * Description: Simple job board for WordPress. No frills, no bloat, just a simple way to get the job done.
 * Version: 1.0.0
 * Author: Super Sambat
 * Author URI: https://github.com/SuperSambat/
 * Requires at least: 6.2
 * Requires PHP: 8.1
 *
 * Text Domain: wp-lokr
 * Domain Path: /languages/
 *
 * @package WP_Lokr
 * @category Core
 * @author Digidea
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_Lokr\Abstracts\Abstract_Main_Plugin_Class;
use WP_Lokr\Helpers\Helper_Functions;
use WP_Lokr\Helpers\Plugin_Constants;
use WP_Lokr\Interfaces\Model_Interface;
use WP_Lokr\Models\Bootstrap;
use WP_Lokr\Models\Script_Loader;
use WP_Lokr\Models\Admin;
use WP_Lokr\Models\Jobs_Post_Type;
use WP_Lokr\Models\Jobs_Meta;

/**
 * Register plugin autoloader.
 *
 * @since 1.0.0
 *
 * @param string $class_name Name of the class to load.
 */
spl_autoload_register(
    function ( $class_name ) {
        if ( strpos( $class_name, 'WP_Lokr\\' ) === 0 ) { // Only do autoload for our plugin files.
            $class_file = str_replace( array( '\\', 'WP_Lokr' . DIRECTORY_SEPARATOR ), array( DIRECTORY_SEPARATOR, '' ), $class_name ) . '.php';
            require_once plugin_dir_path( __FILE__ ) . $class_file;
        }
    }
);

/**
 * The main plugin class.
 */
class WP_Lokr extends Abstract_Main_Plugin_Class { // phpcs:ignore 

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Single main instance of Plugin WP_Lokr plugin.
     *
     * @since 1.0.0
     * @access private
     * @var WP_Lokr
     */
    private static $_instance;

    /**
     * Array of missing external plugins/or plugins with invalid version that this plugin is depends on.
     *
     * @since 1.0.0
     * @access private
     * @var array
     */
    private $_failed_dependencies;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * WP_Lokr constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct() {
        register_deactivation_hook( __FILE__, array( $this, 'general_deactivation_code' ) );

		// Lock 'n Load.
		$this->_initialize_plugin_components();
		$this->_run_plugin();
    }

    /**
     * Ensure that only one instance of Plugin Boilerplate is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.0.0
     * @access public
     *
     * @return WP_Lokr
     */
    public static function get_instance() {
        if ( ! self::$_instance instanceof self ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * The purpose of this function is to have a "general/global" deactivation function callback that is
     * guaranteed to execute when a plugin is deactivated.
     *
     * We have experienced in the past that WordPress does not require "activation" and "deactivation" callbacks,
     * regardless if its present or not, it just activates/deactivates the plugin.
     *
     * In our past experience, a plugin can be activated/deactivated without triggering its "activation" and/or
     * "deactivation" callback on cases where plugin dependency requirements failed or plugin dependency version
     * requirement failed.
     *
     * By registering this "deactivation" callback on constructor, we ensure this "deactivation" callback
     * is always triggered on plugin deactivation.
     *
     * We put inside the function body just the "general" deactivation codebase.
     * Model specific activation/deactivation code base should still reside inside its individual models.
     *
     * We do not need to register a general/global "activation" callback coz we do need all plugin requirements
     * passed before activating the plugin.
     *
     * @since 1.0.0
     * @access public
     *
     * @global object $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param boolean $network_wide Flag that determines whether the plugin has been activated network wid ( on multi site environment ) or not.
     */
    public function general_deactivation_code( $network_wide ) {
        // Delete the flag that determines if plugin activation code is triggered.
        global $wpdb;

        // check if it is a multisite network.
        if ( is_multisite() ) {

            // check if the plugin has been activated on the network or on a single site.
            if ( $network_wide ) {

                // get ids of all sites.
                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                foreach ( $blog_ids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    delete_option( $this->Plugin_Constants->OPTION_WP_LOKR_ACTIVATION_CODE_TRIGGERED );
                    delete_option( $this->Plugin_Constants->INSTALLED_VERSION );
                }

                restore_current_blog();

            } else {
                // activated on a single site, in a multi-site.
                delete_option( $this->Plugin_Constants->OPTION_WP_LOKR_ACTIVATION_CODE_TRIGGERED );
                delete_option( $this->Plugin_Constants->INSTALLED_VERSION );

            }
        } else {
            // activated on a single site.
            delete_option( $this->Plugin_Constants->OPTION_WP_LOKR_ACTIVATION_CODE_TRIGGERED );
            delete_option( $this->Plugin_Constants->INSTALLED_VERSION );
        }
    }

    /**
     * Initialize plugin components.
     *
     * @since 1.0.0
     * @access private
     */
    private function _initialize_plugin_components() {
        Plugin_Constants::get_instance( $this );
        Helper_Functions::get_instance( $this, $this->Plugin_Constants );

        $activatables   = array();
        $initiables     = array(
            Admin::get_instance( $this, $this->Plugin_Constants, $this->Helper_Functions ),
            Jobs_Post_Type::get_instance( $this, $this->Plugin_Constants, $this->Helper_Functions ),
            Jobs_Meta::get_instance( $this, $this->Plugin_Constants, $this->Helper_Functions ),
        );
        $deactivatables = array();

        Bootstrap::get_instance( $this, $this->Plugin_Constants, $this->Helper_Functions, $activatables, $initiables, $deactivatables );
        Script_Loader::get_instance( $this, $this->Plugin_Constants, $this->Helper_Functions );
    }

    /**
     * Run the plugin. ( Runs the various plugin components ).
     *
     * @since 1.0.0
     * @access private
     */
    private function _run_plugin() {
        foreach ( $this->_all_models as $model ) {
            if ( $model instanceof Model_Interface ) {
                $model->run();
            }
        }
    }
}

/**
 * Returns the main instance of WP_Lokr to prevent the need to use globals.
 *
 * @since 1.0.0
 * @return WP_Lokr Main instance of the plugin.
 */
function WP_Lokr() { // phpcs:ignore
    return WP_Lokr::get_instance();
}

// Autobots! Let's Roll!
$GLOBALS['WP_Lokr'] = WP_Lokr::get_instance();
