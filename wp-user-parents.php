<?php

/*
 * Plugin Name: WP User Parents
 * Plugin URI:  http://wordpress.org/plugins/wp-user-parents/
 * Author:      John James Jacoby
 * Author URI:  https://profiles.wordpress.org/johnjamesjacoby/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Allow parent users to manage their children
 * Version:     0.1.0
 * Text Domain: wp-user-parents
 * Domain Path: /assets/lang/
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Enqueue assets
 *
 * @since 0.1.0
 */
function _wp_user_parents() {

	// Get the plugin path
	$plugin_path = plugin_dir_path( __FILE__ );

	// Required Files
	require_once $plugin_path . 'includes/admin.php';
	require_once $plugin_path . 'includes/capabilities.php';
	require_once $plugin_path . 'includes/functions.php';
	require_once $plugin_path . 'includes/metaboxes.php';
	require_once $plugin_path . 'includes/user-dashboard.php';
	require_once $plugin_path . 'includes/hooks.php';
}
add_action( 'plugins_loaded', '_wp_user_parents' );

/**
 * Return the plugin's URL
 *
 * @since 0.1.0
 *
 * @return string
 */
function wp_user_parents_get_plugin_url() {
	return plugin_dir_url( __FILE__ );
}

/**
 * Return the asset version
 *
 * @since 0.1.0
 *
 * @return int
 */
function wp_user_parents_get_asset_version() {
	return 201601060001;
}
