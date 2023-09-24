<?php
namespace WP_Lokr\Models;

use WP_Lokr\Abstracts\Abstract_Main_Plugin_Class;
use WP_Lokr\Helpers\Helper_Functions;
use WP_Lokr\Helpers\Plugin_Constants;
use WP_Lokr\Interfaces\Initiable_Interface;
use WP_Lokr\Interfaces\Model_Interface;
use WP_Lokr\Abstracts\Base_Model;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Model that houses the Editor module logic.
 * Public Model.
 *
 * @since 1.0
 */
class Admin extends Base_Model implements Model_Interface, Initiable_Interface {

    /**
     * Class property that houses the error message.
     *
     * @since 1.0
     * @access private
     * @var string
     */
    private $_admin_notice = array(
        'type'    => '',
        'message' => '',
    );

    /**
     * Class property that holds the post type name.
     *
     * @since 1.0
     * @access private
     * @var string
     */
    private $_post_type;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 1.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct( Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions ) {
        parent::__construct( $main_plugin, $constants, $helper_functions );

        $main_plugin->add_to_all_plugin_models( $this );
        $main_plugin->add_to_public_models( $this );

        $this->_post_type = $this->_constants->POST_TYPE_WP_LOKR_JOB_LISTINGS;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 1.0
     * @access public
     * @implements WP_Lokr\Interfaces\Initializable_Interface
     */
    public function initialize() {
    }

    /**
     * Execute Editor class.
     *
     * @since 1.0
     * @access public
     * @inherit WP_Lokr\Interfaces\Model_Interface
     */
    public function run() {
    }
}
