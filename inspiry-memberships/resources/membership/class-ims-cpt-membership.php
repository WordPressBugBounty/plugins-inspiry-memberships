<?php
/**
 * `Membership` Post Type
 *
 * Class to create `membership` post type.
 *
 * @since   1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IMS_CPT_Membership.
 *
 * Class to create `membership` post type.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'IMS_CPT_Membership' ) ) :

	class IMS_CPT_Membership {

		/**
		 * Register membership post type.
		 */
		public function register_post_type() {

			if ( post_type_exists( 'ims_membership' ) ) {
				return;
			}

			$labels = array(
				'name'               => esc_html__( 'Memberships', IMS_TEXT_DOMAIN ),
				'singular_name'      => esc_html__( 'Membership', IMS_TEXT_DOMAIN ),
				'add_new'            => esc_html__( 'Add New Membership', IMS_TEXT_DOMAIN ),
				'add_new_item'       => esc_html__( 'Add New Membership', IMS_TEXT_DOMAIN ),
				'edit_item'          => esc_html__( 'Edit Membership', IMS_TEXT_DOMAIN ),
				'new_item'           => esc_html__( 'New Membership', IMS_TEXT_DOMAIN ),
				'view_item'          => esc_html__( 'View Membership', IMS_TEXT_DOMAIN ),
				'search_items'       => esc_html__( 'Search Memberships', IMS_TEXT_DOMAIN ),
				'not_found'          => esc_html__( 'No Memberships found', IMS_TEXT_DOMAIN ),
				'not_found_in_trash' => esc_html__( 'No Memberships found in Trash', IMS_TEXT_DOMAIN ),
				'parent_item_colon'  => esc_html__( 'Parent Membership:', IMS_TEXT_DOMAIN ),
				'menu_name'          => esc_html__( 'Memberships', IMS_TEXT_DOMAIN ),
			);

			$rewrite = array(
				'slug'       => apply_filters( 'ims_membership_post_type_slug', esc_html__( 'membership', IMS_TEXT_DOMAIN ) ),
				'with_front' => true,
				'pages'      => true,
				'feeds'      => true,
			);

			$args = array(
				'labels'              => apply_filters( 'ims_membership_post_type_labels', $labels ),
				'hierarchical'        => false,
				'description'         => esc_html__( 'Represents a membership package.', IMS_TEXT_DOMAIN ),
				'public'              => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => 'inspiry_memberships',
				'show_in_admin_bar'   => true,
				'menu_position'       => 10,
				'menu_icon'           => 'dashicons-smiley',
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => false,
				'has_archive'         => true,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => apply_filters( 'ims_membership_post_type_rewrite', $rewrite ),
				'capability_type'     => 'post',
				'supports'            => apply_filters( 'ims_membership_post_type_supports', array( 'title', 'excerpt' ) ),
			);

			register_post_type( 'ims_membership', apply_filters( 'ims_membership_post_type_args', $args ) );

			// Membership post type registered action hook.
			do_action( 'ims_membership_post_type_registered' );

		}

		/**
		 * Modify the Memberships excerpt field labels.
		 *
		 * @param $translation
		 * @param $original
		 *
		 * @return mixed|string
		 * @since 3.0.0
		 */
		public function modify_excerpt_field_labels( $translation, $original ) {
			if ( get_post_type() == 'ims_membership' ) {
				if ( 'Excerpt' === $original ) {
					return esc_html__( 'Package Short Description', IMS_TEXT_DOMAIN );
				} else {
					$pos = strpos( $original, 'Excerpts are optional hand-crafted summaries of your' );
					if ( $pos !== false ) {
						return esc_html__( 'Add a short description for the Package to guide users.', IMS_TEXT_DOMAIN );
					}
				}
			}

			return $translation;
		}

		/**
		 * Method: Create custom schedules for memberships.
		 *
		 * @since 2.0.0
		 *
		 * @param array $schedules Existing schedules.
		 */
		public function create_schedules( $schedules ) {

			$schedules['weekly'] = array(
				'interval' => 7 * 24 * 60 * 60, // 7 days * 24 hours * 60 minutes * 60 seconds
				'display'  => esc_html__( 'Once Weekly', IMS_TEXT_DOMAIN ),
			);

			$schedules['monthly'] = array(
				'interval' => 30 * 24 * 60 * 60, // 30 days * 24 hours * 60 minutes * 60 seconds
				'display'  => esc_html__( 'Once Monthly', IMS_TEXT_DOMAIN ),
			);

			$schedules['yearly'] = array(
				'interval' => 365 * 24 * 60 * 60, // 365 days * 24 hours * 60 minutes * 60 seconds
				'display'  => esc_html__( 'Once Yearly', IMS_TEXT_DOMAIN ),
			);

			return apply_filters( 'ims_create_crons_scedules', $schedules );

		}
	}

endif;



