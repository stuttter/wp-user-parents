<?php

/**
 * User Parents Metaboxes
 *
 * @package Plugins/Users/Parents/Metaboxes
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Add metaboxes
 *
 * @since 1.0.0
 */
function wp_user_parents_add_meta_boxes( $type = '', $user = '' ) {
	add_meta_box(
		'wp_user_parents',
		esc_html__( 'Relationships', 'wp-user-parents' ),
		'wp_user_parents_metabox',
		$type,
		'normal',
		'core',
		$user
	);
}

/**
 * Output the metabox used to view relationships
 *
 * @param object $user
 */
function wp_user_parents_metabox( $user = false ) {

	// Bail if no rental
	if ( empty( $user ) ) {
		return;
	}

	// Get user IDs
	$parent_ids = wp_get_user_parents( $user->ID );
	$child_ids  = wp_get_user_children( $user->ID ); ?>

	<table class="form-table">

		<?php

		// Parents row
		wp_user_profiles_do_row( array(
			'user'       => $user,
			'type'       => 'parents',
			'parent_ids' => $parent_ids,
			'child_ids'  => $child_ids
		) ); ?>

		<?php

		// Children row
		wp_user_profiles_do_row( array(
			'user'       => $user,
			'type'       => 'children',
			'parent_ids' => $parent_ids,
			'child_ids'  => $child_ids
		) ); ?>

	</table>

	<?php
}

/**
 * Output the parents meta box row
 *
 * @since 0.1.0
 *
 * @param  array  $args
 */
function wp_user_profiles_do_row( $args = array() ) {

	// Parse arguments
	$r = wp_parse_args( $args, array(
		'user'       => false,
		'type'       => '',
		'style'      => 'view',
		'parent_ids' => array(),
		'child_ids'  => array()
	) );

	// Bail if user cannot have parents
	if ( ! user_can( $r['user']->ID, "have_user_{$r['type']}", $r['user'] ) ) {
		return;
	}

	// Set some variables to help with querying users
	if ( 'parents' === $r['type'] ) {
		$cur_ids = 'parent_ids';
		$op_ids  = 'child_ids';
	} elseif ( 'children' === $r['type'] ) {
		$cur_ids = 'child_ids';
		$op_ids  = 'parent_ids';
	}

	// Set empty users array
	$r['users'] = array();

	// User can edit parents
	if ( current_user_can( "edit_user_{$r['type']}", $r['user'] ) ) {

		// Set row style
		$r['style'] = 'edit';

		// Get users
		$r['users'] = call_user_func( "wp_get_eligable_user_{$r['type']}", array(
			'exclude' => array_merge( $r[ $op_ids ], array( $r['user']->ID ) )
		) );

		// Fallback
		if ( empty( $r['users'] ) ) {
			$r['users'] = wp_user_parents_get_specific_users( $r[ $cur_ids ] );
		}

	// User can view parents
	} elseif ( current_user_can( "view_user_{$r['type']}", $r['user'] ) ) {
		$r['users'] = wp_user_parents_get_specific_users( $r[ $cur_ids ] );
	}

	// Set current IDs, used for selecting current users
	$r['cur_ids'] = $r[ $cur_ids ];

	// Output the row
	wp_user_parents_output_row( $r );
}

/**
 * Output the meta box row for user parents or children
 *
 * @since 0.1.0
 *
 * @param array $args
 */
function wp_user_parents_output_row( $args = array() ) {

	// Parse arguments
	$r = wp_parse_args( $args, array(
		'user'    => false,
		'type'    => '',
		'style'   => 'view',
		'cur_ids' => array(),
		'users'   => array()
	) );

	// Get labels
	$labels = wp_user_parents_get_row_labels( $r['type'] );

	// Start the output buffer
	ob_start(); ?>

	<tr class="user-<?php echo esc_attr( $r['type'] ); ?>-wrap">

	<?php

	// Which style?
	switch ( $r['style'] ) {
		case 'edit' :

			// Users to list
			if ( ! empty( $r['users'] ) ) :

				?><th><label for="wp_user_<?php echo esc_attr( $r['type'] ); ?>[]"><?php echo esc_html( $labels['label'] ); ?></label></th>
				<td>
					<select data-placeholder="<?php echo esc_html( $labels['select'] ); ?>" name="wp_user_<?php echo esc_attr( $r['type'] ); ?>[]" id="wp_user_<?php echo esc_attr( $r['type'] ); ?>" multiple="multiple"><?php

						foreach ( $r['users'] as $_user ) :

							?><option value="<?php echo esc_attr( $_user->ID ); ?>" <?php selected( in_array( $_user->ID, $r['cur_ids'] ) ); ?>><?php echo esc_html( wp_user_parents_prefer_fullname( $_user ) ); ?></option><?php

						endforeach;

					?></select>
				</td><?php

			// No users to list
			else : ?><th><label><?php echo esc_html( $labels['label'] ); ?></label></th>
				<td><?php echo $labels['empty']; ?></td><?php

			endif;

			break;
		case 'view' :
		default     :
			?><th><label><?php echo esc_html( $labels['label'] ); ?></label></th>
			<td><?php echo ( ! empty( $r['users'] ) )
					? implode( '<br>', array_map( 'wp_user_parents_prefer_fullname', $r['users'] ) )
					: $labels['no']; ?></td><?php

			break;
	} ?>

	</tr>

	<?php

	// Flush the current output buffer
	ob_end_flush();
}

/**
 * Return an array of users, based on array of user IDs passed in
 *
 * @since 0.1.0
 *
 * @param  array  $user_ids
 * @return array
 */
function wp_user_parents_get_specific_users( $user_ids = array() ) {

	// Bail if no user IDs passed
	if ( empty( $user_ids ) ) {
		return array();
	}

	// Return users
	$users = get_users( array(
		'include' => $user_ids,
		'orderby' => 'display_name'
	) );

	// Bail if no users found
	if ( is_wp_error( $users ) || empty( $users ) ) {
		return array();
	}

	// Return users
	return $users;
}

/**
 * Return array of labels used in row
 *
 * @since 0.1.0
 *
 * @param  string  $type
 * @return array
 */
function wp_user_parents_get_row_labels( $type = '' ) {

	// Set empty labels array
	$labels = array(
		'label'  => '',
		'select' => '',
		'no'     => '',
	);

	// Set some variables to help with querying users
	if ( 'parents' === $type ) {
		$labels = array(
			'label'  => esc_html__( 'Parents',            'wp-user-parents' ),
			'select' => esc_html__( 'Select parents',     'wp-user-parents' ),
			'no'     => esc_html__( 'No parents',         'wp-user-parents' ),
			'empty'  => esc_html__( 'No available users', 'wp-user-parents' )
		);
	} elseif ( 'children' === $type ) {
		$labels = array(
			'label'  => esc_html__( 'Children',           'wp-user-parents' ),
			'select' => esc_html__( 'Select children',    'wp-user-parents' ),
			'no'     => esc_html__( 'No children',        'wp-user-parents' ),
			'empty'  => esc_html__( 'No available users', 'wp-user-parents' )
		);
	}

	// Filter & return
	return apply_filters( 'wp_user_parents_get_row_labels', $labels, $type );
}
