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
class Jobs_Meta extends Base_Model implements Model_Interface, Initiable_Interface {

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

    /**
	 * Returns configuration for custom fields on Job Listing posts.
	 *
	 * @return array See `job_manager_job_listing_data_fields` filter for more documentation.
	 */
	public static function get_job_listing_data_fields() {
		$default_field = array(
			'label'              => null,
			'placeholder'        => null,
			'description'        => null,
			'priority'           => 10,
			'value'              => null,
			'default'            => null,
			'classes'            => array(),
			'type'               => 'text',
			'data_type'          => 'string',
			'show_in_admin'      => true,
			'show_in_rest'       => false,
			'auth_edit_callback' => array( __CLASS__, 'auth_check_can_edit_job_listings' ),
			'auth_view_callback' => null,
			'sanitize_callback'  => array( __CLASS__, 'sanitize_meta_field_based_on_input_type' ),
		);

		$fields = array(
			'wp_lokr_job_location'    => array(
				'label'         => __( 'Location', 'wp-lokr' ),
				'placeholder'   => __( 'e.g. "London"', 'wp-lokr' ),
				'description'   => __( 'Leave this blank if the location is not important.', 'wp-lokr' ),
				'priority'      => 1,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
            ),
            'wp_lokr_application'     => array(
				'label'             => __( 'Application email/URL', 'wp-lokr' ),
				'placeholder'       => __( 'Enter an email address or website URL', 'wp-lokr' ),
				'description'       => __( 'This field is required for the "application" area to appear beneath the listing.', 'wp-lokr' ),
				'priority'          => 2,
				'data_type'         => 'string',
				'show_in_admin'     => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_meta_field_application' ),
			),
			'wp_lokr_company_name'    => array(
				'label'         => __( 'Company Name', 'wp-lokr' ),
				'placeholder'   => '',
				'priority'      => 3,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
			),
			'wp_lokr_company_website' => array(
				'label'             => __( 'Company Website', 'wp-lokr' ),
				'placeholder'       => '',
				'priority'          => 4,
				'data_type'         => 'string',
				'show_in_admin'     => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_meta_field_url' ),
			),
			'wp_lokr_company_tagline' => array(
				'label'         => __( 'Company Tagline', 'wp-lokr' ),
				'placeholder'   => __( 'Brief description about the company', 'wp-lokr' ),
				'priority'      => 5,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
			),
        );

		/**
		 * Filters job listing data fields.
		 *
		 * @since 1.0.0
		 */
		$fields = apply_filters( 'job_manager_job_listing_data_fields', $fields );

		// Ensure all fields have the correct structure.
        foreach ( $fields as $key => $field ) {
            $fields[ $key ] = wp_parse_args( $field, $default_field );
        }

		return $fields;
	}

    /**
     * Add custom meta boxes.
     *
     * @since 1.0.0
     * @access public
     */
    public function add_meta_boxes() {
        add_meta_box(
            'wp_lokr_job_details',
            __( 'Job listing data', 'wp-lokr' ),
            array( $this, 'job_listing_data_meta_box' ),
            $this->_post_type
        );
    }

    /**
     * Job details meta box.
     *
     * @since 1.0.0
     * @access public
     *
     * @param object $post Post object.
     */
    public function job_listing_data_meta_box( $post ) {
        $job_listing_data_fields = $this->get_job_listing_data_fields();

        include $this->_constants->VIEWS_ROOT_PATH . 'admin' . DIRECTORY_SEPARATOR . 'meta' . DIRECTORY_SEPARATOR . 'view-job-listing-data.php';
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
        // Add custom meta boxes.
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
    }
}
