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

// Save User Profile
add_action( 'personal_options_update',  'wp_user_parents_save_meta_data' );
add_action( 'edit_user_profile_update', 'wp_user_parents_save_meta_data' );

// Filter meta capabilities
add_filter( 'map_meta_cap', 'wp_user_parents_map_meta_cap', 10, 4 );

// Add "Relationships" meta box
add_action( wp_get_user_parents_section_hook(), 'wp_user_parents_add_meta_boxes', 10, 2 );

// Enqueue assets
add_action( 'admin_enqueue_scripts', 'wp_user_parents_admin_assets' );
