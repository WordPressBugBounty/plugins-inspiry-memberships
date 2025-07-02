<?php
/**
 * `Receipt` Post Type
 *
 * Class to create `receipt` post type.
 *
 * @since   1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'IMS_CPT_Receipt' ) ) {
	/**
	 * IMS_CPT_Receipt.
	 *
	 * Class to create `receipt` post type.
	 *
	 * @since 1.0.0
	 */
	class IMS_CPT_Receipt {

		/**
		 * Register Receipt custom post type.
		 */
		public function register() {

			$labels = array(
				'name'               => esc_html__( 'Receipts', IMS_TEXT_DOMAIN ),
				'singular_name'      => esc_html__( 'Receipt', IMS_TEXT_DOMAIN ),
				'add_new'            => esc_html__( 'Add New Receipt', IMS_TEXT_DOMAIN ),
				'add_new_item'       => esc_html__( 'Add New Receipt', IMS_TEXT_DOMAIN ),
				'edit_item'          => esc_html__( 'Edit Receipt', IMS_TEXT_DOMAIN ),
				'new_item'           => esc_html__( 'New Receipt', IMS_TEXT_DOMAIN ),
				'view_item'          => esc_html__( 'View Receipt', IMS_TEXT_DOMAIN ),
				'search_items'       => esc_html__( 'Search Receipts', IMS_TEXT_DOMAIN ),
				'not_found'          => esc_html__( 'No Receipts found', IMS_TEXT_DOMAIN ),
				'not_found_in_trash' => esc_html__( 'No Receipts found in Trash', IMS_TEXT_DOMAIN ),
				'parent_item_colon'  => esc_html__( 'Parent Receipt:', IMS_TEXT_DOMAIN ),
				'menu_name'          => esc_html__( 'Receipts', IMS_TEXT_DOMAIN ),
			);

			$rewrite = array(
				'slug'       => apply_filters( 'ims_receipt_post_type_slug', esc_html__( 'receipt', IMS_TEXT_DOMAIN ) ),
				'with_front' => true,
				'pages'      => true,
				'feeds'      => true,
			);

			$args = array(
				'labels'              => apply_filters( 'ims_receipt_post_type_labels', $labels ),
				'hierarchical'        => false,
				'description'         => esc_html__( 'Represents a receipt of membership.', IMS_TEXT_DOMAIN ),
				'public'              => true,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'menu_position'       => 10,
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'has_archive'         => true,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => apply_filters( 'ims_receipt_post_type_rewrite', $rewrite ),
				'capability_type'     => 'post',
				'supports'            => apply_filters( 'ims_receipt_post_type_supports', array( 'title' ) )
			);

			register_post_type( 'ims_receipt', apply_filters( 'ims_receipt_post_type_args', $args ) );

			// Membership post type registered action hook.
			do_action( 'ims_receipt_post_type_registered' );

		}

	}
}
