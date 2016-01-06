<?php

/**
 * Return an array of user parents
 *
 * @since 0.1.0
 *
 * @param  int  $user_id
 *
 * @return array
 */
function wp_get_user_parents( $user_id = 0 ) {
	$ids     = get_user_meta( $user_id, 'user_parent' );
	$parents = ! empty( $ids )
		? $ids
		: array();

	return apply_filters( 'wp_get_user_parents', $parents, $user_id );
}

/**
 * Return an array of user children
 *
 * @since 0.1.0
 *
 * @param  int  $user_id
 *
 * @return array
 */
function wp_get_user_children( $user_id = 0 ) {

	// Get children IDs
	$ids = get_users( array(
		'meta_key'   => 'user_parent',
		'meta_value' => $user_id,
		'fields'     => 'ID'
	) );

	// Make empty array if error or empty
	if ( is_wp_error( $ids ) || empty( $ids ) ) {
		$ids = array();
	}

	return apply_filters( 'wp_get_user_children', $ids, $user_id );
}

/**
 * Return an array of user parents
 *
 * @since 0.1.0
 *
 * @param  int  $user_id
 *
 * @return array
 */
function wp_get_user_pending_parents( $user_id = 0 ) {
	$ids     = get_user_meta( $user_id, 'user_parent_pending' );
	$parents = ! empty( $ids )
		? $ids
		: array();

	return apply_filters( 'wp_get_user_pending_parents', $parents, $user_id );
}

/**
 * Return an array of user parents
 *
 * @since 0.1.0
 *
 * @param  int  $user_id
 *
 * @return array
 */
function wp_get_user_pending_children( $user_id = 0 ) {

	// Get children IDs
	$ids = get_users( array(
		'meta_key'   => 'user_parent_pending',
		'meta_value' => $user_id,
		'fields'     => 'ID'
	) );

	// Make empty array if error or empty
	if ( is_wp_error( $ids ) || empty( $ids ) ) {
		$ids = array();
	}

	return apply_filters( 'wp_get_user_pending_children', $parents, $user_id );
}

/**
 * Check if a user is a parent of another user
 *
 * @since 0.1.0
 *
 * @param  int  $parent
 * @param  int  $child
 *
 * @return bool
 */
function wp_is_user_parent_of_user( $parent = 0, $child = 0 ) {
	$parents   = wp_get_user_parents( $child );
	$is_parent = in_array( $parent, $parents, true );

	return apply_filters( 'wp_is_user_parent_of_user', $is_parent, $parent, $child );
}

/**
 * Check if a user is a parent of another user
 *
 * @since 0.1.0
 *
 * @param  int  $child
 * @param  int  $parent
 *
 * @return bool
 */
function wp_is_user_child_of_user( $child = 0, $parent = 0 ) {
	$children = wp_get_user_children( $parent );
	$is_child = in_array( $child, $children, true );

	return apply_filters( 'wp_is_user_child_of_user', $is_child, $child, $parent );
}

/**
 * Register user meta keys & sanitization callbacks
 *
 * @since 0.1.0
 */
function wp_user_parents_register_metadata() {
	register_meta( 'user', 'user_parent',         'wp_user_parents_sanitize_user' );
	register_meta( 'user', 'user_parent_pending', 'wp_user_parents_sanitize_user' );
}

/**
 * Update user parents
 *
 * @since 0.1.0
 */
function wp_user_parents_save_meta_data( $user_id = 0 ) {

	/** Parents ***************************************************************/

	// Attempt to save parents
	$posted_parent_ids = ! empty( $_POST['wp_user_parents'] )
		? wp_parse_id_list( $_POST['wp_user_parents'] )
		: array();

	// Delete user parents
	delete_user_meta( $user_id, 'user_parent' );

	// Add user metas
	foreach ( $posted_parent_ids as $id ) {
		add_user_meta( $user_id, 'user_parent', $id, false );
	}

	/** Children **************************************************************/

	// Attempt to save parents
	$posted_children_ids = ! empty( $_POST['wp_user_children'] )
		? wp_parse_id_list( $_POST['wp_user_children'] )
		: array();

	// Get parent user IDs
	$children_ids = wp_get_user_children( $user_id );
	foreach ( $children_ids as $child_id ) {
		delete_user_meta( $child_id, 'user_parent', $user_id );
	}

	// Add user metas
	foreach ( $posted_children_ids as $id ) {
		add_user_meta( $id, 'user_parent', $user_id, false );
	}
}

/**
 * Sanitize user parent for saving
 *
 * @since 0.1.0
 *
 * @param array $user_id
 */
function wp_user_parents_sanitize_user( $user_id = 0 ) {

	// Try to get user data
	$user = get_userdata( $user_id );

	// Return user ID if found, or false if not
	return ! empty( $user->ID )
		? (int) $user->ID
		: false;
}
