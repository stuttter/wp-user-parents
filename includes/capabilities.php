<?php

/**
 * User Parents Capabilities
 *
 * @package Plugins/Users/Parents/Capabilities
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Filter `edit_user` capability and allow if user is parent
 *
 * @since 0.1.0
 *
 * @param  array   $caps
 * @param  string  $cap
 * @param  int     $user_id
 * @param  array   $args
 */
function wp_user_parents_map_meta_cap( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {

	// Copy the caps array so a pristene one can be passed below
	$new_caps = $caps;

	// Modify core caps, and check for newly mapped ones
	switch ( $cap ) {

		// Parent management
		case 'edit_user' :

			// Break if editing self
			if ( $user_id === $args[0] ) {
				break;
			}

			// Grant cap if parent
			if ( wp_is_user_parent_of_user( $args[0], $user_id ) ) {
				$new_caps = array( 'read' );
			}

			break;

		// Have
		case 'have_user_parents'  :
		case 'have_user_children' :
			$new_caps = array( 'exist' );
			break;

		// View
		case 'view_user_parents'  :
		case 'view_user_children' :
			$new_caps = array( 'exist' );
			break;

		// Edit
		case 'edit_user_parents'  :
		case 'edit_user_children' :
			$new_caps = array( 'exist' );
			break;
	}

	// Return possibly modified capabilities array
	return apply_filters( 'wp_user_parents_map_meta_cap', $new_caps, $caps, $cap, $user_id, $args );
}
