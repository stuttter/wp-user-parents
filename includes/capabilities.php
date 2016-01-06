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

	// Bail if not checking `edit_user` cap
	if ( 'edit_user' !== $cap ) {
		return  $caps;
	}

	// Bail if user is checking themselves
	if ( $user_id === $args[0] ) {
		return $caps;
	}

	// Grant cap if parent
	if ( wp_is_user_parent_of_user( $args[0], $user_id ) ) {
		$caps = array( 'read' );
	}

	// Return possibly modified capabilities array
	return $caps;
}
