<?php

/**
 * User Parents Admin
 *
 * @package Plugins/Users/Parents/Admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Enqueue chosen
 *
 * @since 0.1.0
 */
function wp_user_parents_admin_assets() {

	// Vars
	$url = wp_user_parents_get_plugin_url();
	$ver = wp_user_parents_get_asset_version();

	// Styles
	wp_enqueue_style( 'wp-user-parents', $url . 'assets/css/wp-user-parents.css', array(), $ver );
}
