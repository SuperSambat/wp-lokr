<?php
namespace WP_Lokr\Models;

use WP_Lokr\Abstracts\Abstract_Main_Plugin_Class;
use WP_Lokr\Helpers\Helper_Functions;
use WP_Lokr\Helpers\Plugin_Constants;
use WP_Lokr\Interfaces\Activatable_Interface;
use WP_Lokr\Interfaces\Deactivatable_Interface;
use WP_Lokr\Interfaces\Initiable_Interface;
use WP_Lokr\Interfaces\Model_Interface;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Model that houses the logic of 'Bootstraping' the plugin.
 *
 * @since 1.0.0
 */
class Bootstrap implements Model_Interface {
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of Bootstrap.
     *
     * @since 1.0.0
     * @access private
     * @var Bootstrap
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.0.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.0.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Array of models implementing the WP_Lokr\Interfaces\Activatable_Interface.
     *
     * @since 1.0.0
     * @access private
     * @var array
     */
    private $_activatables;

    /**
     * Array of models implementing the WP_Lokr\Interfaces\Initiable_Interface.
     *
     * @since 1.0.0
     * @access private
     * @var array
     */
    private $_initiables;

    /**
     * Array of models implementing the WP_Lokr\Interfaces\Deactivatable_Interface.
     *
     * @since 1.0.0
     * @access private
     * @var array
     */
    private $_deactivatables;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 1.0.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @param array                      $activatables     Array of models implementing WP_Lokr\Interfaces\Activatable_Interface.
     * @param array                      $initiables       Array of models implementing WP_Lokr\Interfaces\Initiable_Interface.
     * @param array                      $deactivatables   Array of models implementing WP_Lokr\Interfaces\Deactivatable_Interface.
     */
    public function __construct( Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions, array $activatables = array(), array $initiables = array(), array $deactivatables = array() ) {
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;
        $this->_activatables     = $activatables;
        $this->_initiables       = $initiables;
        $this->_deactivatables   = $deactivatables;

        $main_plugin->add_to_all_plugin_models( $this );
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 1.0.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @param array                      $activatables     Array of models implementing WP_Lokr\Interfaces\Activatable_Interface.
     * @param array                      $initiables       Array of models implementing WP_Lokr\Interfaces\Initiable_Interface.
     * @param array                      $deactivatables   Array of models implementing WP_Lokr\Interfaces\Deactivatable_Interface.
     * @return Bootstrap
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions, array $activatables = array(), array $initiables = array(), array $deactivatables = array() ) {
        if ( ! self::$_instance instanceof self ) {
            self::$_instance = new self( $main_plugin, $constants, $helper_functions, $activatables, $initiables, $deactivatables );
        }

        return self::$_instance;
    }

    /**
     * Load plugin text domain.
     *
     * @since 1.0.0
     * @access public
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( $this->_constants->TEXT_DOMAIN, false, $this->_constants->PLUGIN_DIRNAME . '/languages' );
    }

    /**
     * Method that houses the logic relating to activating the plugin.
     *
     * @since 1.0.0
     * @access public
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param boolean $network_wide Flag that determines whether the plugin has been activated network wid ( on multi site environment ) or not.
     */
    public function activate_plugin( $network_wide ) {
        global $wpdb;

        if ( is_multisite() ) {

            if ( $network_wide ) {

                // get ids of all sites.
                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    $this->_activate_plugin( $blog_id );

                }

                restore_current_blog();

            } else {
                $this->_activate_plugin( $wpdb->blogid );
            }
            // activated on a single site, in a multi-site.

        } else {
            $this->_activate_plugin( $wpdb->blogid );
        }
        // activated on a single site.
    }

    /**
     * Method to initialize the plugin in a newly created site in a multi site set up.
     *
     * @since 1.0.0
     * @access public
     *
     * @param int    $blog_id Blog ID of the created blog.
     * @param int    $user_id User ID of the user creating the blog.
     * @param string $domain Domain used for the new blog.
     * @param string $path Path to the new blog.
     * @param int    $site_id Site ID. Only relevant on multi-network installs.
     * @param array  $meta Meta data. Used to set initial site options.
     */
    public function new_mu_site_init( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        if ( $this->_helper_functions->is_plugin_active_for_network( 'wp-lokr/wp-lokr.php' ) ) {

            switch_to_blog( $blog_id );
            $this->_activate_plugin( $blog_id );
            restore_current_blog();

        }
    }

    /**
     * Initialize plugin settings options.
     * This is a compromise to my idea of 'Modularity'. Ideally, bootstrap should not take care of plugin settings stuff.
     * However due to how WooCommerce do its thing, we need to do it this way. We can't separate settings on its own.
     *
     * @since 1.0.0
     * @access private
     */
    private function _initialize_plugin_settings_options() {
        // Help settings section options.

        // Set initial value of 'no' for the option that sets the option that specify whether to delete the options on plugin uninstall. Optionception.
        if ( ! get_option( $this->_constants->OPTION_CLEAN_UP_PLUGIN_OPTIONS, false ) ) {
            update_option( $this->_constants->OPTION_CLEAN_UP_PLUGIN_OPTIONS, 'no' );
        }
    }

    /**
     * Actual function that houses the code to execute on plugin activation.
     *
     * @since 1.0.0
     * @access private
     *
     * @param int $blogid Blog ID of the created blog.
     */
    private function _activate_plugin( $blogid ) {
        // Initialize settings options.
        $this->_initialize_plugin_settings_options();

        // Execute 'activate' contract of models implementing WP_Lokr\Interfaces\Activatable_Interface.
        foreach ( $this->_activatables as $activatable ) {
            if ( $activatable instanceof Activatable_Interface ) {
                $activatable->activate();
            }
        }

        flush_rewrite_rules();

        update_option( $this->_constants->INSTALLED_VERSION, $this->_constants->VERSION );
        update_option( $this->_constants->OPTION_WP_LOKR_ACTIVATION_CODE_TRIGGERED, 'yes' );
    }

    /**
     * Method that houses the logic relating to deactivating the plugin.
     *
     * @since 1.0.0
     * @access public
     *
     * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
     *
     * @param boolean $network_wide Flag that determines whether the plugin has been activated network wid ( on multi site environment ) or not.
     */
    public function deactivate_plugin( $network_wide ) {
        global $wpdb;

        // check if it is a multisite network.
        if ( is_multisite() ) {

            // check if the plugin has been activated on the network or on a single site.
            if ( $network_wide ) {

                // get ids of all sites.
                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    $this->_deactivate_plugin( $wpdb->blogid );

                }

                restore_current_blog();

            } else {
                $this->_deactivate_plugin( $wpdb->blogid );
            }
            // activated on a single site, in a multi-site.

        } else {
            $this->_deactivate_plugin( $wpdb->blogid );
        }
        // activated on a single site.
    }

    /**
     * Actual method that houses the code to execute on plugin deactivation.
     *
     * @since 1.0.0
     * @access private
     *
     * @param int $blogid Blog ID of the created blog.
     */
    private function _deactivate_plugin( $blogid ) {
        // Execute 'deactivate' contract of models implementing WP_Lokr\Interfaces\Deactivatable_Interface.
        foreach ( $this->_deactivatables as $deactivatable ) {
            if ( $deactivatable instanceof Deactivatable_Interface ) {
                $deactivatable->deactivate();
            }
        }

        flush_rewrite_rules();
    }

    /**
     * Method that houses codes to be executed on init hook.
     *
     * @since 1.0.0
     * @access public
     */
    public function initialize() {
        /**
         * Execute activation codebase if not yet executed.
         * This occurs when plugin is activated and plugin requirements fails.
         * It just enables the plugin but won't execute activation code.
         * When you meet the plugin requirements, since the plugin is already enabled, it won't execute the
         * activation codebase anymore.
         *
         * Also there are cases where activation codebase isn't executed after update.
         * This ensures the activation codebase is executed after plugin update.
         */
        if (
            version_compare( get_option( $this->_constants->INSTALLED_VERSION, false ), $this->_constants->VERSION, '!=' ) ||
            get_option( $this->_constants->OPTION_WP_LOKR_ACTIVATION_CODE_TRIGGERED, false ) !== 'yes'
        ) {

            $network_wide = $this->_helper_functions->is_plugin_active_for_network( 'block-scumbags-wc/block-scumbags-wc.php' );
            $this->activate_plugin( $network_wide );

        }

        // Execute 'initialize' contract of models implementing WP_Lokr\Interfaces\Initiable_Interface.
        foreach ( $this->_initiables as $initiable ) {
            if ( $initiable instanceof Initiable_Interface ) {
                $initiable->initialize();
            }
        }
    }

    /**
     * Add settings link to plugin action links.
     *
     * @since 1.0.0
     * @access public
     *
     * @param array $links Array of plugin action links.
     * @return array Array of modified plugin action links.
     */
    public function plugin_settings_action_link( $links ) {
        // Important Note: Replace the link "href" with the proper location of the settings page
        // The example below assumes you are using a settings page integrated within WooCommerce settings.
        $settings_link = '<a href="edit.php?post_type=' . $this->_constants->POST_TYPE_WP_LOKR_JOB_LISTINGS . '">' . __( 'Settings', 'wp-lokr' ) . '</a>';
        array_unshift( $links, $settings_link );

        return $links;
    }

    /*
    |--------------------------------------------------------------------------
    | Interface contract functions
    |--------------------------------------------------------------------------
     */

    /**
     * Execute plugin bootstrap code.
     *
     * @since 1.0.0
     * @access public
     */
    public function run() {
        // Internationalization.
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

        // Execute plugin activation/deactivation.
        register_activation_hook( $this->_constants->MAIN_PLUGIN_FILE_PATH, array( $this, 'activate_plugin' ) );
        register_deactivation_hook( $this->_constants->MAIN_PLUGIN_FILE_PATH, array( $this, 'deactivate_plugin' ) );

        // Execute plugin initialization ( plugin activation ) on every newly created site in a multi site set up.
        add_action( 'wpmu_new_blog', array( $this, 'new_mu_site_init' ), 10, 6 );

        // Execute codes that need to run on 'init' hook.
        add_action( 'init', array( $this, 'initialize' ) );

        // Add settings link to plugin action links.
        add_filter( 'plugin_action_links_' . $this->_constants->PLUGIN_BASENAME, array( $this, 'plugin_settings_action_link' ) );
    }
}
