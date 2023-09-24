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
class Jobs_Post_Type extends Base_Model implements Model_Interface, Initiable_Interface {

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
     * Register custom post type.
     *
     * @since 1.0
     * @access public
     */
    public function register_custom_post_type() {

        if ( post_type_exists( $this->_post_type ) ) {
			return;
		}

        $has_archive            = get_option( 'wp_lokr_disable_archive_page' ) !== 'disable' ? true : false;
		$with_front             = get_option( 'wp_lokr_remove_permalink_front_base' ) !== 'remove' ? true : false;
        $supports               = array( 'title', 'editor', 'excerpt', 'author', 'custom-fields', 'publicize' );
		$featured_image_support = get_option( 'wp_lokr_enable_featured_image_support' );
		if ( 'enable' === $featured_image_support ) {
			$supports[] = 'thumbnail';
		}

        $labels = array(
            'name'                  => _x( 'Jobs', 'Post type general name', 'wp-lokr' ),
            'singular_name'         => _x( 'Job', 'Post type singular name', 'wp-lokr' ),
            'menu_name'             => _x( 'Job Listings', 'Admin Menu text', 'wp-lokr' ),
            'name_admin_bar'        => _x( 'Job Listings', 'Add New on Toolbar', 'wp-lokr' ),
            'add_new'               => __( 'Add New', 'wp-lokr' ),
            'add_new_item'          => __( 'Add New Job', 'wp-lokr' ),
            'new_item'              => __( 'New Job', 'wp-lokr' ),
            'edit_item'             => __( 'Edit Job', 'wp-lokr' ),
            'view_item'             => __( 'View Job', 'wp-lokr' ),
            'all_items'             => __( 'All Jobs', 'wp-lokr' ),
            'search_items'          => __( 'Search Jobs', 'wp-lokr' ),
            'parent_item_colon'     => __( 'Parent Jobs:', 'wp-lokr' ),
            'not_found'             => __( 'No Job Listings found.', 'wp-lokr' ),
            'not_found_in_trash'    => __( 'No Job Listings found in Trash.', 'wp-lokr' ),
            'featured_image'        => _x( 'Job Cover Image', 'Overrides the “Featured Image” phrase for this post type.', 'wp-lokr' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type.', 'wp-lokr' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type.', 'wp-lokr' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type.', 'wp-lokr' ),
            'archives'              => _x( 'Lokr archives', 'The post type archive label used in nav menus. Default “Post Archives”.', 'wp-lokr' ),
        );

        /**
		 * Filters 'wp_lokr_wp-lokr-job-listings_args' post type arguments.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args arguments.
		 */
        $args = apply_filters(
			'wp_lokr_' . $this->_post_type . '_args',
            array(
                'labels'             => $labels,
                'description'        => __( 'Description.', 'wp-lokr' ),
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'menu_icon'          => 'dashicons-portfolio',
                'show_in_rest'       => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'map_meta_cap'       => true,
                'taxonomies'         => array(),
                'rewrite'            => array(
                    'slug'       => get_option( 'awsm_permalink_slug', 'jobs' ),
                    'with_front' => $with_front,
                ),
                'capability_type'    => 'post',
                'has_archive'        => $has_archive,
                'hierarchical'       => true,
                'menu_position'      => 50,
                'supports'           => $supports,
            )
        );

        register_post_type( $this->_post_type, $args );
    }

    /**
     * Add columns to admin post list.
     *
     * @since 1.0
     * @access public
     *
     * @param array $columns Array of columns.
     * @return array
     */
    public function add_columns( $columns ) {
        $columns['title']            = __( 'Job Title', 'wp-lokr' );
        $columns['date']             = __( 'Posted', 'wp-lokr' );
        $columns['wp_lokr_expiry']   = __( 'Expiry', 'wp-lokr' );
        $columns['wp_lokr_view']     = __( 'Views', 'wp-lokr' );
        $columns['wp_lokr_click']    = __( 'Clicks', 'wp-lokr' );
        $columns['wp_lokr_featured'] = '<span class="tips dashicons dashicons-star-filled" data-tip="Featured?"></span>';
        $columns['wp_lokr_filled']   = '<span class="tips dashicons dashicons-yes" data-tip="Featured?"></span>';
        $columns['wp_lokr_actions']  = __( 'Actions', 'wp-lokr' );

        return $columns;
    }

    /**
     * Allow sorting of columns.
     *
     * @since 1.0
     * @access public
     *
     * @param array $columns Array of columns.
     * @return array
     */
    public function sortable_columns( $columns ) {
        $columns['wp_lokr_expiry'] = 'wp_lokr_expiry';
        $columns['wp_lokr_view']   = 'wp_lokr_view';
        $columns['wp_lokr_click']  = 'wp_lokr_click';

        return $columns;
    }

    /**
     * Register taxonomy.
     *
     * @since 1.0
     * @access public
     */
    public function register_taxonomy() {
        $labels = array(
            'name'                       => _x( 'Job Categories', 'Taxonomy General Name', 'wp-lokr' ),
            'singular_name'              => _x( 'Job Category', 'Taxonomy Singular Name', 'wp-lokr' ),
            'menu_name'                  => __( 'Job Categories', 'wp-lokr' ),
            'all_items'                  => __( 'All Job Categories', 'wp-lokr' ),
            'parent_item'                => __( 'Parent Job Category', 'wp-lokr' ),
            'parent_item_colon'          => __( 'Parent Job Category:', 'wp-lokr' ),
            'new_item_name'              => __( 'New Job Category Name', 'wp-lokr' ),
            'add_new_item'               => __( 'Add New Job Category', 'wp-lokr' ),
            'edit_item'                  => __( 'Edit Job Category', 'wp-lokr' ),
            'update_item'                => __( 'Update Job Category', 'wp-lokr' ),
            'view_item'                  => __( 'View Job Category', 'wp-lokr' ),
            'separate_items_with_commas' => __( 'Separate Job Categories with commas', 'wp-lokr' ),
            'add_or_remove_items'        => __( 'Add or remove Job Categories', 'wp-lokr' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'wp-lokr' ),
            'popular_items'              => __( 'Popular Job Categories', 'wp-lokr' ),
            'search_items'               => __( 'Search Job Categories', 'wp-lokr' ),
            'not_found'                  => __( 'Not Found', 'wp-lokr' ),
            'no_terms'                   => __( 'No Job Categories', 'wp-lokr' ),
            'items_list'                 => __( 'Job Categories list', 'wp-lokr' ),
            'items_list_navigation'      => __( 'Job Categories list navigation', 'wp-lokr' ),
        );

        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
        );

        register_taxonomy( $this->_constants->TAXONOMY_WP_LOKR_JOB_LISTINGS_CATEGORIES, array( $this->_post_type ), $args );
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
        add_action( 'init', array( $this, 'register_custom_post_type' ) );

        // Add columns to admin post list.
        add_filter( 'manage_' . $this->_post_type . '_posts_columns', array( $this, 'add_columns' ) );

        // Allow sorting of columns.
        add_filter( 'manage_edit-' . $this->_post_type . '_sortable_columns', array( $this, 'sortable_columns' ) );

        // Register taxonomy.
        add_action( 'init', array( $this, 'register_taxonomy' ) );
    }
}
