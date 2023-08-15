<?php
/**
 * Installation related functions and actions.
 *
 * @package WP_Lokr\Classes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WP_Lokr_Install Class.
 */
class WP_Lokr_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
	}

	/**
	 * Install WP_Lokr.
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}
		
	}
}

WP_Lokr_Install::init();