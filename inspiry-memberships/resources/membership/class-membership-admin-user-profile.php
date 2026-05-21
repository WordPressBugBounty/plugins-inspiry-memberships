<?php
/**
 * Assign Membership from Admin User Profile
 *
 * @since   3.0.8
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'IMS_Membership_Admin_User_Profile' ) ) :

	/**
	 * IMS_Membership_Admin_User_Profile.
	 */
	class IMS_Membership_Admin_User_Profile {

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Display custom fields on user profile page
			add_action( 'show_user_profile', array( $this, 'display_membership_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'display_membership_fields' ) );

			// Save the custom fields
			add_action( 'personal_options_update', array( $this, 'save_membership_fields' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_membership_fields' ) );
		}

		/**
		 * Display membership assignment fields.
		 *
		 * @param WP_User $user User object.
		 */
		public function display_membership_fields( $user ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$current_membership_id = get_user_meta( $user->ID, 'ims_current_membership', true );

			$args = array(
				'post_type'      => 'ims_membership',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			);
			$membership_packages = get_posts( $args );
			?>
			<h2><?php esc_html_e( 'Membership Assignment', IMS_TEXT_DOMAIN ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="ims_assign_membership"><?php esc_html_e( 'Select Membership', IMS_TEXT_DOMAIN ); ?></label></th>
					<td>
						<select name="ims_assign_membership" id="ims_assign_membership">
							<option value="none"><?php esc_html_e( 'None', IMS_TEXT_DOMAIN ); ?></option>
							<?php
							if ( ! empty( $membership_packages ) ) {
								foreach ( $membership_packages as $package ) {
									$selected = selected( $current_membership_id, $package->ID, false );
									echo '<option value="' . esc_attr( $package->ID ) . '" ' . $selected . '>' . esc_html( $package->post_title ) . '</option>';
								}
							}
							?>
						</select>
						<br/>
						<span class="description"><?php esc_html_e( 'Selecting a membership here will assign it manually to this user, bypassing the payment process. If changed to "None", the current membership will be cancelled.', IMS_TEXT_DOMAIN ); ?></span>
					</td>
				</tr>
			</table>
			<?php
		}

		/**
		 * Save membership assignment.
		 *
		 * @param int $user_id User ID.
		 */
		public function save_membership_fields( $user_id ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			// Explicitly verify the WordPress user profile nonce.
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
				return;
			}

			if ( isset( $_POST['ims_assign_membership'] ) ) {
				
				$membership_methods = new IMS_Membership_Method();
				$current_membership_id = get_user_meta( $user_id, 'ims_current_membership', true );

				if ( 'none' === $_POST['ims_assign_membership'] ) {
					// Cancel current membership
					if ( ! empty( $current_membership_id ) ) {
						$membership_methods->cancel_user_membership( $user_id, $current_membership_id );
					}
				} else {
					$membership_id = intval( $_POST['ims_assign_membership'] );

					// Ensure the membership ID corresponds to a published ims_membership post type.
					if ( get_post_type( $membership_id ) !== 'ims_membership' || get_post_status( $membership_id ) !== 'publish' ) {
						return;
					}

					if ( $current_membership_id != $membership_id ) {
						if ( ! empty( $current_membership_id ) ) {
							// Update membership
							$membership_methods->update_user_membership( $user_id, $membership_id, 'manual' );
						} else {
							// Add membership
							$membership_methods->add_user_membership( $user_id, $membership_id, 'manual' );
						}
					}
				}
			}
		}
	}

endif;
