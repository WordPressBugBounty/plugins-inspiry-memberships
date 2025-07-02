<?php
/**
 * Custom Columns for `Receipt` Post Type
 *
 * Creates and manages custom columns for `Receipt` post type.
 *
 * @since 1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'IMS_Receipt_Custom_Columns' ) ) {
	/**
	 * IMS_Receipt_Custom_Columns.
	 *
	 * This class creates and manages custom columns for `Receipt` post type.
	 *
	 * @since 1.0.0
	 */
	class IMS_Receipt_Custom_Columns {

		/**
		 * Register receipt custom post type columns.
		 *
		 * @since 1.0.0
		 * @param array $columns Existing columns.
		 */
		public function register_columns( $columns ) {

			$columns = apply_filters(
				'ims_receipt_custom_column_names',
				array(
					'cb'            => '<input type="checkbox" />',
					'title'         => esc_html__( 'Receipt', IMS_TEXT_DOMAIN ),
					'receipt_for'   => esc_html__( 'Receipt For', IMS_TEXT_DOMAIN ),
					'membership'    => esc_html__( 'Membership', IMS_TEXT_DOMAIN ),
					'price'         => esc_html__( 'Price', IMS_TEXT_DOMAIN ),
					'user_id'       => esc_html__( 'User', IMS_TEXT_DOMAIN ),
					'vendor'        => esc_html__( 'Vendor', IMS_TEXT_DOMAIN ),
					'purchase_date' => esc_html__( 'Date of Purchase', IMS_TEXT_DOMAIN ),
					'status'        => esc_html__( 'Status', IMS_TEXT_DOMAIN ),
				),
			);

			/**
			 * Reverse the array for RTL
			 */
			if ( is_rtl() ) {
				$columns = array_reverse( $columns );
			}

			return $columns;

		}

		/**
		 * Display column values.
		 *
		 * @since 1.0.0
		 * @param string $column Display column value according to the current column name.
		 */
		public function display_column_values( $column ) {

			global $post;

			$prefix = apply_filters( 'ims_receipt_meta_prefix', 'ims_receipt_' );

			switch ( $column ) {

				case 'receipt_for':
					$receipt_for = get_post_meta( $post->ID, "{$prefix}receipt_for", true );
					if ( ! empty( $receipt_for ) ) {
						echo esc_html( $receipt_for );
					} else {
						esc_html_e( 'Not Available', IMS_TEXT_DOMAIN );
					}
					break;

				case 'membership':
					$membership_id  = get_post_meta( $post->ID, "{$prefix}membership_id", true );
					$membership_obj = get_post( $membership_id );
					if ( 'object' === gettype( $membership_obj ) ) {
						$membership_title = $membership_obj->post_title;
						if ( ! empty( $membership_title ) ) {
							echo '<a href="' . esc_url( get_edit_post_link( $membership_id ) ) . '">' . esc_html( $membership_title ) . '</a>';
						} else {
							esc_html_e( 'Not Available', IMS_TEXT_DOMAIN );
						}
					} else {
						esc_html_e( 'Not Available', IMS_TEXT_DOMAIN );
					}
					break;

				case 'price':
					$currency_settings = get_option( 'ims_basic_settings' );
					$price             = get_post_meta( $post->ID, "{$prefix}price", true );
					$currency_position = $currency_settings['ims_currency_position'];
					$formatted_price   = '';
					if ( 'after' === $currency_position ) {
						$formatted_price = $price . $currency_settings['ims_currency_symbol'];
					} else {
						$formatted_price = $currency_settings['ims_currency_symbol'] . $price;
					}
					if ( ! empty( $price ) ) {
						echo esc_html( $formatted_price );
					} else {
						esc_html_e( 'Free', IMS_TEXT_DOMAIN );
					}
					break;

				case 'user_id':
					$user_id = intval( get_post_meta( $post->ID, "{$prefix}user_id", true ) );
					$user    = get_user_by( 'id', $user_id );
					if ( ! empty( $user ) ) {
						$user_name = $user->user_login;
					}
					if ( ! empty( $user_name ) ) {
						echo '<a href="' . esc_url( get_edit_user_link( $user_id ) ) . '">' . esc_html( $user_name ) . '</a>';
					} else {
						esc_html_e( 'Not Available', IMS_TEXT_DOMAIN );
					}
					break;

				case 'vendor':
					$vendor = get_post_meta( $post->ID, "{$prefix}vendor", true );
					if ( ! empty( $vendor ) && ( 'stripe' === $vendor ) ) {
						esc_html_e( 'Stripe', IMS_TEXT_DOMAIN );
					} elseif ( ! empty( $vendor ) && ( 'paypal' === $vendor ) ) {
						esc_html_e( 'PayPal', IMS_TEXT_DOMAIN );
					} elseif ( ! empty( $vendor ) && ( 'wire' === $vendor ) ) {
						esc_html_e( 'Wire Transfer', IMS_TEXT_DOMAIN );
					} elseif ( ! empty( $vendor ) && ( 'woocommerce' === $vendor ) ) {
						esc_html_e( 'WooCommerce', IMS_TEXT_DOMAIN );
					} else {
						esc_html_e( 'Not Available', IMS_TEXT_DOMAIN );
					}
					break;

				case 'purchase_date':
					$purchase_date = get_post_meta( $post->ID, "{$prefix}purchase_date", true );
					if ( ! empty( $purchase_date ) ) {
						echo esc_html( $purchase_date );
					} else {
						esc_html_e( 'Not Available', IMS_TEXT_DOMAIN );
					}
					break;
				case 'status':
					$status = esc_attr( get_post_meta( $post->ID, 'ims_receipt_status', true ) );

					// Get user id and current user membership id.
					$user_id                     = get_post_meta( $post->ID, 'ims_receipt_user_id', true );
					$current_membership_id       = get_user_meta( $user_id, 'ims_current_membership', true );
					$current_membership_due_date = get_user_meta( $user_id, 'ims_membership_due_date', true );
					$receipt_membership_id       = get_post_meta( $post->ID, 'ims_receipt_membership_id', true );
					$receipt_membership_due_date = get_post_meta( $post->ID, 'ims_receipt_membership_due_date', true );

					if ( empty( $status ) ) :
						echo '<span class="pending">' . esc_html__( 'Pending', IMS_TEXT_DOMAIN ) . '</span>';
					elseif ( $status && ( $current_membership_id !== $receipt_membership_id ) || ( $current_membership_due_date !== $receipt_membership_due_date ) ) :
						echo '<span class="expired">' . esc_html__( 'Expired', IMS_TEXT_DOMAIN ) . '</span>';
					else :
						echo '<span class="active">' . esc_html__( 'Active', IMS_TEXT_DOMAIN ) . '</span>';
					endif;
					break;

				default:
					break;

			}

		}

	}
}
