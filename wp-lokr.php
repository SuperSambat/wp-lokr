<?php
/**
 * Plugin Name: WP Lokr
 * Plugin URI: https://wp-lokr.com/
 * Description: Simple job board for WordPress. No frills, no bloat, just a simple way to get the job done.
 * Version: 1.0.0
 * Author: Super Sambat
 * Author URI: https://github.com/SuperSambat
 * Text Domain: wp_lokr
 * Domain Path: /languages/
 * Requires at least: 6.2
 * Requires PHP: 7.3
 *
 * @package WP_Lokr
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_LOKR_PLUGIN_FILE' ) ) {
	define( 'WP_LOKR_PLUGIN_FILE', __FILE__ );
}

// Include the main WP_Lokr class.
if ( ! class_exists( 'WP_Lokr', false ) ) {
	include_once dirname( WP_LOKR_PLUGIN_FILE ) . '/includes/class-wp-lokr.php';
}

/**
 * Returns the main instance of WP_Lokr.
 *
 * @since  2.1
 * @return WP_Lokr
 */
function WP_Lokr() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return WP_Lokr::instance();
}

// Global for backwards compatibility.
$GLOBALS['wp_lokr'] = WP_Lokr();
