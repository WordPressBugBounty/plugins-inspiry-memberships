<?php
/**
 * Assign Membership from Front-end Dashboard
 *
 * @since   3.0.8
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'IMS_Membership_Frontend_Assign' ) ) :

	/**
	 * IMS_Membership_Frontend_Assign.
	 */
	class IMS_Membership_Frontend_Assign {

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Change Top level menu label
			add_filter( 'realhomes_dashboard_menu', array( $this, 'modify_membership_menu_label' ), 10, 1 );

			// Add submenu item
			add_filter( 'realhomes_dashboard_submenu', array( $this, 'add_assign_membership_submenu' ), 10, 2 );

			// Render content
			add_action( 'realhomes_dashboard_after_content', array( $this, 'render_assign_membership_page' ) );

			// Form processing
			add_action( 'admin_post_ims_assign_membership', array( $this, 'process_assign_membership' ) );

			// AJAX info retrieval
			add_action( 'wp_ajax_ims_get_user_membership_info', array( $this, 'get_user_membership_info_ajax' ) );
		}

		/**
		 * Modify the top level membership menu label for admins
		 */
		public function modify_membership_menu_label( $menu ) {
			if ( current_user_can( 'manage_options' ) && isset( $menu['membership'] ) ) {
				$menu['membership'][0] = esc_html__( 'Memberships', IMS_TEXT_DOMAIN );
				$menu['membership'][1] = esc_html__( 'Memberships', IMS_TEXT_DOMAIN );
			}
			return $menu;
		}

		/**
		 * Add Assign Membership submenu item under Membership menu.
		 */
		public function add_assign_membership_submenu( $submenu, $menu ) {
			if ( current_user_can( 'manage_options' ) && isset( $menu['membership'] ) ) {
				$submenu['membership']['my-membership'] = array(
					esc_html__( 'My Memberships', IMS_TEXT_DOMAIN ),
					esc_html__( 'My Memberships', IMS_TEXT_DOMAIN ),
					array(), // No query param submodules, just falls back to default
					true // show in menu
				);
				
				$submenu['membership']['assign-membership'] = array(
					esc_html__( 'Assign Membership', IMS_TEXT_DOMAIN ),
					esc_html__( 'Assign Membership to User', IMS_TEXT_DOMAIN ),
					array( 'submodule' => 'assign-membership' ),
					true // show in menu
				);
			}
			return $submenu;
		}

		/**
		 * Render the frontend dashboard page for assigning memberships.
		 */
		public function render_assign_membership_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			global $dashboard_globals;
			if ( ! isset( $dashboard_globals['submodule'] ) || 'assign-membership' !== $dashboard_globals['submodule'] ) {
				return;
			}

			$packages = get_posts( array(
				'post_type'      => 'ims_membership',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			) );

			$package_details_json = array();
			if ( ! empty( $packages ) ) {
				foreach ( $packages as $pkg ) {
					$pkg_obj = ims_get_membership_object( $pkg->ID );
					$duration = $pkg_obj->get_duration();
					$unit = $pkg_obj->get_duration_unit();
					$allowed_props = $pkg_obj->get_properties();
					$featured_props = $pkg_obj->get_featured_properties();
					
					$package_details_json[ $pkg->ID ] = array(
						'title'    => $pkg->post_title,
						'duration' => $duration . ' ' . $unit,
						'allowed'  => ( ! empty( $allowed_props ) ) ? $allowed_props : '0',
						'featured' => ( ! empty( $featured_props ) ) ? $featured_props : '0'
					);
				}
			}

			?>
			<div class="dashboard-membership assign-memberships">
				<?php
				if ( isset( $_GET['success'] ) && '1' === $_GET['success'] ) {
					echo '<div class="dashboard-notice success is-dismissible"><div class="rh_alert-box__content"><h4 class="rh_alert-box__heading"><i class="fas fa-check-circle"></i> ' . esc_html__( 'Success', IMS_TEXT_DOMAIN ) . '</h4><p class="rh_alert-box__message">' . esc_html__( 'Membership assigned successfully.', IMS_TEXT_DOMAIN ) . '</p></div></div>';
				} elseif ( isset( $_GET['success'] ) && '0' === $_GET['success'] ) {
					echo '<div class="dashboard-notice error is-dismissible"><div class="rh_alert-box__content"><h4 class="rh_alert-box__heading"><i class="fas fa-exclamation-circle"></i> ' . esc_html__( 'Error', IMS_TEXT_DOMAIN ) . '</h4><p class="rh_alert-box__message">' . esc_html__( 'Failed to assign membership or invalid user data provided.', IMS_TEXT_DOMAIN ) . '</p></div></div>';
				}
				?>
				
				<div class="dashboard-user-profile">
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="assign-membership-form">
						<input type="hidden" name="action" value="ims_assign_membership">
						<?php wp_nonce_field( 'ims_assign_membership_nonce', 'ims_assign_membership_nonce_field' ); ?>

						<div class="dashboard-user-profile-inner">
							<div class="form-fields">
								<div class="row">
									<div class="col-lg-6">
										<p>
											<label for="ims_user_id"><?php esc_html_e( 'Select User', IMS_TEXT_DOMAIN ); ?></label>
											<?php
											/**
											 * Limit the number of users fetched for the dropdown to prevent
											 * unbounded DB queries and memory exhaustion on large sites.
											 * Override via: define( 'IMS_USER_DROPDOWN_LIMIT', 500 ); in wp-config.php
											 */
											$ims_user_limit = defined( 'IMS_USER_DROPDOWN_LIMIT' ) ? (int) IMS_USER_DROPDOWN_LIMIT : 600;
											wp_dropdown_users( array(
												'name'              => 'ims_user_id',
												'id'                => 'ims_user_id',
												'show_option_none'  => esc_html__( '-- Select User --', IMS_TEXT_DOMAIN ),
												'option_none_value' => '',
												'class'             => 'inspiry_select_picker_trigger show-tick',
												'role__not_in'      => array( 'administrator' ),
												'exclude'           => array( get_current_user_id() ),
												'number'            => $ims_user_limit,
												'orderby'           => 'display_name',
												'order'             => 'ASC',
											) );
											?>
										</p>
									</div>

									<div class="col-lg-6">
										<p>
											<label for="ims_membership_id"><?php esc_html_e( 'Select Membership Package', IMS_TEXT_DOMAIN ); ?></label>
											<select name="ims_membership_id" id="ims_membership_id" class="inspiry_select_picker_trigger show-tick">
												<option value="none"><?php esc_html_e( 'None (Cancel Current)', IMS_TEXT_DOMAIN ); ?></option>
												<?php
												if ( ! empty( $packages ) ) {
													foreach ( $packages as $package ) {
														echo '<option value="' . esc_attr( $package->ID ) . '">' . esc_html( $package->post_title ) . '</option>';
													}
												}
												?>
											</select>
										</p>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-6">
										<!-- Container for currently active membership info -->
										<div id="ims_current_user_membership_info"></div>
									</div>
									<div class="col-lg-6">
										<!-- Container for selected package info -->
										<div id="ims_selected_package_info"></div>
									</div>
								</div>
							</div> <!-- .form-fields -->
						</div> <!-- .dashboard-user-profile-inner -->

						<div class="submit dashboard-form-actions">
							<button class="btn btn-primary" type="submit"><?php esc_html_e( 'Assign Membership', IMS_TEXT_DOMAIN ); ?></button>
						</div>
					</form>
				</div>
			</div>
			<?php
		}

		/**
		 * AJAX handler for fetching user's current membership
		 */
		public function get_user_membership_info_ajax() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( esc_html__( 'Permission denied.', IMS_TEXT_DOMAIN ) );
			}

			$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
			if ( ! $user_id ) {
				wp_send_json_error( esc_html__( 'Invalid user ID.', IMS_TEXT_DOMAIN ) );
			}

			$current_membership_id = get_user_meta( $user_id, 'ims_current_membership', true );
			if ( empty( $current_membership_id ) ) {
				wp_send_json_success( array( 'html' => '<p>' . esc_html__( 'This user does not currently have an active membership package.', IMS_TEXT_DOMAIN ) . '</p>' ) );
			}

			$package = get_post( $current_membership_id );
			if ( ! $package || 'ims_membership' !== $package->post_type ) {
				wp_send_json_success( array( 'html' => '<p>' . esc_html__( 'User has an invalid or expired membership.', IMS_TEXT_DOMAIN ) . '</p>' ) );
			}

			$due_date = get_user_meta( $user_id, 'ims_membership_due_date', true );
			$due_date_formatted = ! empty( $due_date ) ? date_i18n( get_option( 'date_format' ), strtotime( $due_date ) ) : esc_html__( 'N/A', IMS_TEXT_DOMAIN );

			$html = '<div class="membership-info"><div class="dl-list">';
			$html .= '<h4>' . esc_html__( 'Currently Active Package', IMS_TEXT_DOMAIN ) . '</h4>';
			$html .= '<dl><dt>' . esc_html__( 'Package Title', IMS_TEXT_DOMAIN ) . '</dt><dd>' . esc_html( $package->post_title ) . '</dd></dl>';
			$html .= '<dl><dt>' . esc_html__( 'Expiry Date', IMS_TEXT_DOMAIN ) . '</dt><dd>' . $due_date_formatted . '</dd></dl>';
			
			$allowed_properties = get_user_meta( $user_id, 'ims_package_properties', true );
			$remaining_properties = get_user_meta( $user_id, 'ims_current_properties', true );
			
			$html .= '<dl><dt>' . esc_html__( 'Allowed Properties', IMS_TEXT_DOMAIN ) . '</dt><dd>' . esc_html( $allowed_properties ?: '0' ) . '</dd></dl>';
			$html .= '<dl><dt>' . esc_html__( 'Properties Remained', IMS_TEXT_DOMAIN ) . '</dt><dd>' . esc_html( $remaining_properties ?: '0' ) . '</dd></dl>';
			$html .= '</div></div>';

			wp_send_json_success( array( 'html' => $html ) );
		}

		/**
		 * Process the form submission to assign the membership.
		 */
		public function process_assign_membership() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You are not allowed to do this.', IMS_TEXT_DOMAIN ) );
			}

			if ( ! isset( $_POST['ims_assign_membership_nonce_field'] ) || ! wp_verify_nonce( $_POST['ims_assign_membership_nonce_field'], 'ims_assign_membership_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed.', IMS_TEXT_DOMAIN ) );
			}

			// Get the URL to redirect back to.
			$redirect_url = false;
			if ( function_exists( 'realhomes_get_dashboard_page_url' ) ) {
				$redirect_url = realhomes_get_dashboard_page_url( 'membership', array( 'submodule' => 'assign-membership' ) );
			}
			if ( ! $redirect_url ) {
				$redirect_url = admin_url(); // Fallback
			}

			$user_id = isset( $_POST['ims_user_id'] ) ? intval( $_POST['ims_user_id'] ) : 0;
			if ( empty( $user_id ) || $user_id === -1 ) {
				wp_redirect( add_query_arg( 'success', '0', $redirect_url ) );
				exit;
			}

			$membership_id = isset( $_POST['ims_membership_id'] ) ? sanitize_text_field( $_POST['ims_membership_id'] ) : '';

			$membership_methods = new IMS_Membership_Method();
			$current_membership_id = get_user_meta( $user_id, 'ims_current_membership', true );

			if ( 'none' === $membership_id ) {
				if ( ! empty( $current_membership_id ) ) {
					$membership_methods->cancel_user_membership( $user_id, $current_membership_id );
				}
				wp_redirect( add_query_arg( 'success', '1', $redirect_url ) );
				exit;
			} elseif ( ! empty( $membership_id ) ) {
				$membership_id = intval( $membership_id );

				// Ensure the membership ID corresponds to a published ims_membership post type.
				if ( get_post_type( $membership_id ) !== 'ims_membership' || get_post_status( $membership_id ) !== 'publish' ) {
					wp_redirect( add_query_arg( 'success', '0', $redirect_url ) );
					exit;
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
				wp_redirect( add_query_arg( 'success', '1', $redirect_url ) );
				exit;
			}

			wp_redirect( add_query_arg( 'success', '0', $redirect_url ) );
			exit;
		}
	}

endif;
