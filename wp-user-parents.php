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

/**
 * Enqueue assets
 *
 * @since 0.1.0
 */
function _wp_user_parents() {

	// Vars
	$url = wp_user_parents_get_plugin_url();
	$ver = wp_user_parents_get_asset_version();

	// Styles
	wp_enqueue_style( 'wp-user-parents', $url . 'assets/css/wp-user-parents.css',  array(), $ver );

	// Scripts
	wp_enqueue_script( 'wp-user-parents', $url . 'assets/js/wp-user-parents.js',  array( 'jquery' ), $ver, true );
}
add_action( 'admin_enqueue_scripts', '_wp_user_parents' );

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
	return 201601050001;
}
