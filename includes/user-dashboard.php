<?php

/**
 * Parents User Dashboard
 *
 * @package Plugins/User/Parents/Dashboard
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Filter sections and add "Children" section
 *
 * @since 0.3.0
 *
 * @param array $sections
 */
function wp_user_parents_add_section( $sections = array() ) {

	// Events
	$sections[] = array(
		'id'           => 'children',
		'slug'         => 'children',
		'url'          => '',
		'label'        => esc_html__( 'Children', 'wp-user-parents' ),
		'show_in_menu' => true,
		'order'        => 90
	);

	// Return sections
	return $sections;
}
