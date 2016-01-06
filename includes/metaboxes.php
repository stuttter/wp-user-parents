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
		__( 'Relationships', 'wp-user-parents' ),
		'wp_user_parents_metabox',
		$type,
		'normal',
		'high',
		$user
	);
}

/**
 * Output the metabox used to display item metadata
 *
 * @param object $user
 */
function wp_user_parents_metabox( $user = false ) {

	// Bail if no rental
	if ( empty( $user ) ) {
		return;
	}

	// Get parent user IDs
	$parent_ids = wp_get_user_parents( $user->ID );

	// Get child user IDs
	$child_ids = wp_get_user_children( $user->ID ); ?>

	<table class="form-table">

		<?php if ( apply_filters( 'wp_user_can_have_parents', true, $user ) ) :

			// Get child user objects
			$parents = wp_get_eligable_user_parents( array(
				'exclude' => array_merge( $child_ids, array( $user->ID ) )
			) );

			// Fallback for empty children but not empty child IDs
			if ( empty( $parents ) && ! empty( $parent_ids ) ) {
				$parents = get_users( array(
					'include' => $parent_ids,
					'orderby' => 'display_name'
				) );
			}

			// Only show parents if there are parents to show
			if ( ! empty( $parents ) ) : ?>

				<tr class="user-url-wrap">
					<th><label for="wp_user_parents[]"><?php esc_html_e( 'Parents', 'wp-user-parents' ); ?></label></th>
					<td>
						<select data-placeholder="<?php esc_html_e( 'Select Parents', 'wp-user-parents' ); ?>" name="wp_user_parents[]" id="wp_user_parents" multiple="multiple">

							<?php foreach ( $parents as $parent ) : ?>

								<option value="<?php echo esc_attr( $parent->ID ); ?>" <?php selected( in_array( $parent->ID, $parent_ids ) ); ?>><?php echo esc_html( wp_user_parents_prefer_fullname( $parent ) ); ?></option>

							<?php endforeach; ?>

						</select>
					</td>
				</tr>

			<?php endif; ?>

		<?php endif; ?>

		<?php if ( apply_filters( 'wp_user_can_have_children', true, $user ) ) :

			// Get child user objects
			$children = wp_get_eligable_user_children( array(
				'exclude' => array_merge( $parent_ids, array( $user->ID ) )
			) );

			// Fallback for empty children but not empty child IDs
			if ( empty( $children ) && ! empty( $child_ids ) ) {
				$children = get_users( array(
					'include' => $child_ids,
					'orderby' => 'display_name'
				) );
			}

			// Only show if there are children to show
			if ( ! empty( $children ) ) : ?>

				<tr class="user-url-wrap">
					<th><label for="wp_user_children[]"><?php esc_html_e( 'Children', 'wp-user-parents' ); ?></label></th>
					<td>
						<select data-placeholder="<?php esc_html_e( 'Select Children', 'wp-user-parents' ); ?>" name="wp_user_children[]" id="wp_user_children" multiple="multiple">

							<?php foreach ( $children as $child ) : ?>

								<option value="<?php echo esc_attr( $child->ID ); ?>" <?php selected( in_array( $child->ID, $child_ids ) ); ?>><?php echo esc_html( wp_user_parents_prefer_fullname( $child ) ); ?></option>

							<?php endforeach; ?>

						</select>
					</td>
				</tr>

			<?php endif; ?>

		<?php endif; ?>

	</table>

	<?php
}
