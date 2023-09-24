<?php
namespace WP_Lokr\Models;

use WP_Lokr\Abstracts\Abstract_Main_Plugin_Class;
use WP_Lokr\Helpers\Helper_Functions;
use WP_Lokr\Helpers\Plugin_Constants;
use WP_Lokr\Interfaces\Model_Interface;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Model that houses the Script_Loader module logic.
 * Public Model.
 *
 * @since 1.0
 */
class Script_Loader implements Model_Interface {
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
     * @var Script_Loader
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
     */
    public function __construct( Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions ) {
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

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
     * @return Script_Loader
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions ) {
        if ( ! self::$_instance instanceof self ) {
            self::$_instance = new self( $main_plugin, $constants, $helper_functions );
        }

        return self::$_instance;
    }

    /**
     * Load backend js and css scripts.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $handle Unique identifier of the current backend page.
     */
    public function load_backend_scripts( $handle ) {
        $screen = get_current_screen();

        $post_type = get_post_type();
        if ( ! $post_type && isset( $_GET['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $post_type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        }

        // if ( 'woocommerce_page_block-scumbags' === $screen->id ) {
        // $settings = wp_enqueue_code_editor( array( 'type' => 'application/json' ) );
        // wp_enqueue_style( 'json-help-content-editor', $this->_constants->DIST_ROOT_URL . 'block-scumbags.css', array(), $this->_constants->VERSION, 'all' );
        // wp_enqueue_script( 'block-scumbags-admin-js', $this->_constants->DIST_ROOT_URL . 'block-scumbags.js', array( 'jquery', 'code-editor' ), $this->_constants->VERSION, true );

        // wp_localize_script(
        // 'block-scumbags-admin-js',
        // 'bswc',
        // array(
		// 'codeMirrorSettings' => $settings,
        // 'editorErrorMessage' => __( 'Please fix the errors in the JSON data before saving.', 'block-scumbags-wc' ),
        // )
        // );
        // }
    }

    /**
     * Load frontend js and css scripts.
     *
     * @since 1.0.0
     * @access public
     */
    public function load_frontend_scripts() {
        global $post, $wp;
    }

    /**
     * Execute plugin script loader.
     *
     * @since 1.0.0
     * @access public
     */
    public function run() {
        add_action( 'admin_enqueue_scripts', array( $this, 'load_backend_scripts' ), 10, 1 );
        add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_scripts' ) );
    }
}
