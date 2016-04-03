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
 * @since 0.1.0
 *
 * @param array $sections
 */
function wp_user_parents_add_section( $sections = array() ) {

	// Bail if cannot have or view children
	if ( ! current_user_can( 'have_user_children' ) || ! current_user_can( 'view_user_children' ) ) {
		return $sections;
	}

	// Events
	$sections[] = array(
		'id'           => 'children',
		'slug'         => 'children',
		'url'          => '',
		'label'        => esc_html__( 'Children', 'wp-user-parents' ),
		'show_in_menu' => true,
		'order'        => 150
	);

	// Return sections
	return $sections;
}

/**
 * Maybe add a child from the "Children" section
 *
 * @since 0.1.0
 */
function wp_user_parents_add_child() {

	// Bail if no signup nonce
	if ( empty( $_REQUEST['signup_nonce'] ) ) {
		return;
	}

	// Bail if nonce fails
	if ( ! wp_verify_nonce( $_REQUEST['signup_nonce'], 'wp_user_dashboard_child_signup' ) ) {
		return;
	}

	// Bail if current user cannot have children
	if ( ! current_user_can( 'have_user_children' ) ) {
		return;
	}

	// Sanitize fields
	$redirect  = false;
	$email     = sanitize_email( $_REQUEST['email'] );
	$firstname = ! empty( $_REQUEST['firstname'] ) ? $_REQUEST['firstname'] : '';
	$lastname  = ! empty( $_REQUEST['lastname']  ) ? $_REQUEST['lastname']  : '';
	$password  = ! empty( $_REQUEST['password']  ) ? $_REQUEST['password']  : wp_generate_password( 12, false );
	$username  = ! empty( $_REQUEST['username']  ) ? $_REQUEST['username']  : "{$firstname}-{$lastname}";

	// Names are empty
	if ( empty( $firstname ) || empty( $lastname ) || strlen( $firstname ) < 2 || strlen( $lastname ) < 2 ) {
		$args     = array( 'error' => 'name' );
		$url      = wp_get_user_dashboard_url( 'children' );
		$redirect = add_query_arg( $args, $url );
	}

	// Username exists
	if ( username_exists( $username ) || strlen( $username ) < 4 ) {
		$args     = array( 'error' => 'username' );
		$url      = wp_get_user_dashboard_url( 'children' );
		$redirect = add_query_arg( $args, $url );
	}

	// Email exists
	if ( email_exists( $email ) ) {
		$args     = array( 'error' => 'username' );
		$url      = wp_get_user_dashboard_url( 'children' );
		$redirect = add_query_arg( $args, $url );
	}

	// Redirect
	if ( ! empty( $redirect ) ) {
		wp_safe_redirect( $redirect );
		exit;
	}

	// Requires activation
	if ( is_multisite() && apply_filters( 'wp_join_page_requires_activation', true ) ) {
		wpmu_signup_user( $username, $email, array(
			'add_to_blog' => get_current_blog_id(),
			'new_role'    => get_option( 'default_role' ),
			'first_name'  => $firstname,
			'last_name'   => $lastname,
		) );
	}

	// Create the user account
	$user_id = wpmu_create_user(
		esc_html( sanitize_key( $username ) ),
		$password,
		$email
	);

	// Bail if no user ID for site
	if ( empty( $user_id ) ) {
		$args     = array( 'error' => 'unknown' );
		$url      = wp_get_user_dashboard_url( 'children' );
		$redirect = add_query_arg( $args, $url );
	}

	// Get new userdata
	$user = new WP_User( $user_id );
	$user->add_role( 'pending' );

	// Get the current user ID
	$current_user_id = get_current_user_id();

	// Save fullname to usermeta
	update_user_meta( $user->ID, 'first_name', $firstname );
	update_user_meta( $user->ID, 'last_name',  $lastname  );
	add_user_meta( $user->ID, 'user_parent', $current_user_id, false );

	// Do action
	do_action( 'wp_user_parents_added_child', $user, $current_user_id );

	// Redirect
	$args     = array( 'success' => 'yay' );
	$url      = wp_get_user_dashboard_url( 'children' );
	$redirect = add_query_arg( $args, $url );
	wp_safe_redirect( $redirect );
	die;
}
