<?php

/**
 * User Parents Hooks
 *
 * @package Plugins/Users/Parents/Hooks
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Register meta data
add_action( 'init', 'wp_user_parents_register_metadata' );

// Add "Profile" metaxob
add_action( 'wp_user_profiles_add_account_meta_boxes', 'wp_user_parents_add_meta_boxes', 10, 2 );

// Save User Profile
add_action( 'personal_options_update',  'wp_user_parents_save_meta_data' );
add_action( 'edit_user_profile_update', 'wp_user_parents_save_meta_data' );
