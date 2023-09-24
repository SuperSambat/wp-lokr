<?php
namespace WP_Lokr\Helpers;

use WP_Lokr\Abstracts\Abstract_Main_Plugin_Class;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Model that houses all the plugin constants.
 *
 * @since 1.0.0
 */
class Plugin_Constants {
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Single main instance of Plugin_Constants.
     *
     * @since 1.0.0
     * @access private
     * @var Plugin_Constants
     */
    private static $_instance;

    /**
     * Class property that houses all the actual constants data.
     *
     * @since 1.0.0
     * @access private
     * @var array
     */
    private $_data = array();

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
     * @param Abstract_Main_Plugin_Class $main_plugin Main plugin object.
     */
    public function __construct( Abstract_Main_Plugin_Class $main_plugin = null ) {
        $main_plugin_file_path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'wp-lokr' . DIRECTORY_SEPARATOR . 'wp-lokr.php';
        $plugin_dir_path       = plugin_dir_path( $main_plugin_file_path ); // /home/user/var/www/wordpress/wp-content/plugins/block-scumbags-wc/
        $plugin_dir_url        = plugin_dir_url( $main_plugin_file_path ); // http://example.com/wp-content/plugins/block-scumbags-wc/.
        $plugin_basename       = plugin_basename( $main_plugin_file_path ); // block-scumbags-wc/block-scumbags-wc.php.
        $plugin_dirname        = plugin_basename( dirname( $main_plugin_file_path ) ); // block-scumbags-wc.

        $this->_data = array(

            // Configuration Constants.
            'TOKEN'                                    => 'wp_lokr',
            'INSTALLED_VERSION'                        => 'wp_lokr_installed_version',
            'VERSION'                                  => '1.0.0',
            'TEXT_DOMAIN'                              => 'wp-lokr',
            'THEME_TEMPLATE_PATH'                      => 'wp-lokr',

            // Paths.
            'MAIN_PLUGIN_FILE_PATH'                    => $main_plugin_file_path,
            'PLUGIN_DIR_PATH'                          => $plugin_dir_path,
            'PLUGIN_DIR_URL'                           => $plugin_dir_url,
            'PLUGIN_BASENAME'                          => $plugin_basename,
            'PLUGIN_DIRNAME'                           => $plugin_dirname,
            'JS_ROOT_PATH'                             => $plugin_dir_path . 'js/',
            'VIEWS_ROOT_PATH'                          => $plugin_dir_path . 'views/',
            'TEMPLATES_ROOT_PATH'                      => $plugin_dir_path . 'templates/',
            'LOGS_ROOT_PATH'                           => $plugin_dir_path . 'logs/',
            'HELP_DIR_PATH'                            => ABSPATH . 'help/',
            'DIST_ROOT_PATH'                           => $plugin_dir_path . 'dist/',

            // URLs.
            'CSS_ROOT_URL'                             => $plugin_dir_url . 'css/',
            'IMAGES_ROOT_URL'                          => $plugin_dir_url . 'images/',
            'JS_ROOT_URL'                              => $plugin_dir_url . 'js/',
            'DIST_ROOT_URL'                            => $plugin_dir_url . 'dist/',

            // User capabilities.
            'EDIT_HELP_CONTENT_CAP'                    => 'edit_json_help_content',

            // Options.
            'OPTION_WP_LOKR_ACTIVATION_CODE_TRIGGERED' => 'option_wp_lokr_activation_code_triggered',

            // Settings ( Help ).
            'OPTION_CLEAN_UP_PLUGIN_OPTIONS'           => 'wp_lokr_clean_up_plugin_options',

            // REST API.
            'REST_API_NAMESPACE'                       => 'wp-lokr/v1',

        );

        if ( $main_plugin ) {
            $main_plugin->add_to_public_helpers( $this );
        }
    }

    /**
     * Ensure that only one instance of Plugin_Constants is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.0.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin Main plugin object.
     * @return Plugin_Constants
     */
    public static function get_instance( Abstract_Main_Plugin_Class $main_plugin = null ) {
        if ( ! self::$_instance instanceof self && $main_plugin ) {
            self::$_instance = new self( $main_plugin );
        }

        return self::$_instance;
    }

    /**
     * Get constant property.
     * We use this magic method to automatically access data from the _data property so
     * we do not need to create individual methods to expose each of the constant properties.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $prop The name of the data property to access.
     * @return mixed Data property value.
     * @throws \Exception Error message.
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->_data ) ) {
            return $this->_data[ $prop ];
        } else {
            throw new \Exception( 'Trying to access unknown property' );
        }
    }
}

function constants() { // phpcs:ignore
    return Plugin_Constants::get_instance();
}
