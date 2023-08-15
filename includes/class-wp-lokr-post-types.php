<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @package WP_Lokr\Classes\Jobs
 * @version 2.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post types Class.
 */
class WP_Lokr_Post_Types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
        add_action( 'init', array( __CLASS__, 'register_post_types' ), 10 );
	}

    /**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( 'wp_lokr_job' ) ) {
			return;
		}

		do_action( 'wp_lokr_register_post_type' );

		$supports = array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'publicize', 'wpcom-markdown' );

        register_post_type(
			'wp_lokr_job',
			apply_filters(
				'wp_lokr_register_post_type_job',
				array(
					'labels'              => array(
						'name'                   => __( 'Jobs', 'wp_lokr' ),
						'singular_name'         => __( 'Job', 'wp_lokr' ),
						'all_items'             => __( 'All Jobs', 'wp_lokr' ),
						'menu_name'             => _x( 'Jobs', 'Admin menu name', 'wp_lokr' ),
						'add_new'               => __( 'Add New', 'wp_lokr' ),
						'add_new_item'          => __( 'Add new job', 'wp_lokr' ),
						'edit'                  => __( 'Edit', 'wp_lokr' ),
						'edit_item'             => __( 'Edit job', 'wp_lokr' ),
						'new_item'              => __( 'New job', 'wp_lokr' ),
						'view_item'             => __( 'View job', 'wp_lokr' ),
						'view_items'            => __( 'View jobs', 'wp_lokr' ),
						'search_items'          => __( 'Search jobs', 'wp_lokr' ),
						'not_found'             => __( 'No jobs found', 'wp_lokr' ),
						'not_found_in_trash'    => __( 'No jobs found in trash', 'wp_lokr' ),
						'parent'                => __( 'Parent job', 'wp_lokr' ),
						'featured_image'        => __( 'Job image', 'wp_lokr' ),
						'set_featured_image'    => __( 'Set job image', 'wp_lokr' ),
						'remove_featured_image' => __( 'Remove job image', 'wp_lokr' ),
						'use_featured_image'    => __( 'Use as job image', 'wp_lokr' ),
						'insert_into_item'      => __( 'Insert into job', 'wp_lokr' ),
						'uploaded_to_this_item' => __( 'Uploaded to this job', 'wp_lokr' ),
						'filter_items_list'     => __( 'Filter jobs', 'wp_lokr' ),
						'items_list_navigation' => __( 'Jobs navigation', 'wp_lokr' ),
						'items_list'            => __( 'Jobs list', 'wp_lokr' ),
						'item_link'             => __( 'Job Link', 'wp_lokr' ),
						'item_link_description' => __( 'A link to a job.', 'wp_lokr' ),
					),
					'description'         => __( 'This is where you can browse jobs.', 'wp_lokr' ),
					'public'              => true,
					'show_ui'             => true,
					'menu_icon'           => 'dashicons-archive',
					'capability_type'     => 'job',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					// 'rewrite'             => $permalinks['product_rewrite_slug'] ? array(
					// 	'slug'       => $permalinks['product_rewrite_slug'],
					// 	'with_front' => false,
					// 	'feeds'      => true,
					// ) : false,
					'query_var'           => true,
					'supports'            => $supports,
					'has_archive'         => false,
					'show_in_nav_menus'   => true,
					'show_in_rest'        => true,
				)
			)
		);

		do_action( 'wp_lokr_after_register_post_type' );
	}
}

WP_Lokr_Post_Types::init();
