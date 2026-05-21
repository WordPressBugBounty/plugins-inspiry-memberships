<?php
/**
 * Assign Membership from WP Admin
 *
 * Provides a native WordPress admin page for manually assigning
 * membership packages to users, mirroring the frontend dashboard feature.
 *
 * @since   3.0.8
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'IMS_Membership_Admin_Assign' ) ) :

	/**
	 * IMS_Membership_Admin_Assign.
	 *
	 * Handles the WP admin "Assign Membership" sub-menu page under Memberships.
	 */
	class IMS_Membership_Admin_Assign {

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Register the WP admin sub-menu page (priority 15 — after main menu at 10).
			add_action( 'admin_menu', array( $this, 'register_submenu' ), 15 );

			// Handle the admin form POST (separate action from the frontend handler).
			add_action( 'admin_post_ims_assign_membership_admin', array( $this, 'process_assign_membership' ) );

			// Admin-specific AJAX handler for user membership info (returns WP admin-safe HTML).
			add_action( 'wp_ajax_ims_get_user_membership_info_admin', array( $this, 'get_user_membership_info_ajax' ) );
		}

		/**
		 * Register the "Assign Membership" sub-menu page under Memberships.
		 */
		public function register_submenu() {
			add_submenu_page(
				'inspiry_memberships',
				esc_html__( 'Assign Membership', IMS_TEXT_DOMAIN ),
				esc_html__( 'Assign Membership', IMS_TEXT_DOMAIN ),
				'manage_options',
				'ims_assign_membership',
				array( $this, 'render_page' )
			);
		}

		/**
		 * Render the WP admin assign membership page.
		 */
		public function render_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You are not allowed to view this page.', IMS_TEXT_DOMAIN ) );
			}

			$packages = get_posts(
				array(
					'post_type'      => 'ims_membership',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'orderby'        => 'title',
					'order'          => 'ASC',
				)
			);

			$package_details_json = array();
			if ( ! empty( $packages ) ) {
				foreach ( $packages as $pkg ) {
					$pkg_obj  = ims_get_membership_object( $pkg->ID );
					$duration = $pkg_obj->get_duration();
					$unit     = $pkg_obj->get_duration_unit();
					$allowed  = $pkg_obj->get_properties();
					$featured = $pkg_obj->get_featured_properties();

					$package_details_json[ $pkg->ID ] = array(
						'title'    => $pkg->post_title,
						'duration' => $duration . ' ' . $unit,
						'allowed'  => ! empty( $allowed ) ? $allowed : '0',
						'featured' => ! empty( $featured ) ? $featured : '0',
					);
				}
			}
			?>
			<div class="wrap">
				<h1 class="wp-heading-inline"><?php esc_html_e( 'Assign Membership', IMS_TEXT_DOMAIN ); ?></h1>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=ims_membership' ) ); ?>" class="page-title-action">
					<?php esc_html_e( 'View Packages', IMS_TEXT_DOMAIN ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=ims_receipt' ) ); ?>" class="page-title-action">
					<?php esc_html_e( 'View Receipts', IMS_TEXT_DOMAIN ); ?>
				</a>
				<hr class="wp-header-end">

				<?php if ( isset( $_GET['success'] ) && '1' === $_GET['success'] ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
					<div class="notice notice-success is-dismissible">
						<p><?php esc_html_e( 'Membership assigned successfully.', IMS_TEXT_DOMAIN ); ?></p>
					</div>
				<?php elseif ( isset( $_GET['success'] ) && '0' === $_GET['success'] ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
					<div class="notice notice-error is-dismissible">
						<p><?php esc_html_e( 'Failed to assign membership. Please check user and package selection.', IMS_TEXT_DOMAIN ); ?></p>
					</div>
				<?php endif; ?>

				<div class="ims-admin-assign-wrap">
					<div class="postbox">
						<div class="postbox-header">
							<h2 class="hndle"><?php esc_html_e( 'Manual Membership Assignment', IMS_TEXT_DOMAIN ); ?></h2>
						</div>
						<div class="inside">
							<p class="description">
								<?php esc_html_e( 'Select a user and a membership package to assign it manually. This bypasses payment and grants the membership immediately. Selecting "None" will cancel the user\'s current membership.', IMS_TEXT_DOMAIN ); ?>
							</p>

							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<input type="hidden" name="action" value="ims_assign_membership_admin">
								<?php wp_nonce_field( 'ims_assign_membership_nonce', 'ims_assign_membership_nonce_field' ); ?>

								<table class="form-table" role="presentation">
									<tbody>
										<tr>
											<th scope="row">
												<label for="ims_user_id"><?php esc_html_e( 'User', IMS_TEXT_DOMAIN ); ?></label>
											</th>
											<td>
												<?php
												/**
												 * Limit the number of users fetched for the dropdown to prevent
												 * unbounded DB queries and memory exhaustion on large sites.
												 * Override via: define( 'IMS_USER_DROPDOWN_LIMIT', 500 ); in wp-config.php
												 */
												$ims_user_limit = defined( 'IMS_USER_DROPDOWN_LIMIT' ) ? (int) IMS_USER_DROPDOWN_LIMIT : 600;
												wp_dropdown_users(
													array(
														'name'              => 'ims_user_id',
														'id'                => 'ims_user_id',
														'show_option_none'  => esc_html__( '— Select User —', IMS_TEXT_DOMAIN ),
														'option_none_value' => '',
														'role__not_in'      => array( 'administrator' ),
														'exclude'           => array( get_current_user_id() ),
														'number'            => $ims_user_limit,
														'orderby'           => 'display_name',
														'order'             => 'ASC',
													)
												);
												?>
												<div id="ims_current_user_membership_info"></div>
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="ims_membership_id"><?php esc_html_e( 'Package', IMS_TEXT_DOMAIN ); ?></label>
											</th>
											<td>
												<select name="ims_membership_id" id="ims_membership_id">
													<option value="none"><?php esc_html_e( 'None (Cancel Current Membership)', IMS_TEXT_DOMAIN ); ?></option>
													<?php
													if ( ! empty( $packages ) ) {
														foreach ( $packages as $package ) {
															echo '<option value="' . esc_attr( $package->ID ) . '">' . esc_html( $package->post_title ) . '</option>';
														}
													} else {
														echo '<option value="" disabled>' . esc_html__( 'No published packages found', IMS_TEXT_DOMAIN ) . '</option>';
													}
													?>
												</select>
												<div id="ims_selected_package_info"></div>
											</td>
										</tr>
									</tbody>
								</table>

								<p class="submit">
									<?php submit_button( esc_html__( 'Assign Membership', IMS_TEXT_DOMAIN ), 'primary', 'submit', false ); ?>
								</p>
							</form>
						</div><!-- .inside -->
					</div><!-- .postbox -->
				</div><!-- .ims-admin-assign-wrap -->
			</div><!-- .wrap -->
			<?php
		}

		/**
		 * AJAX handler — returns the user's current membership info as WP admin-safe HTML.
		 *
		 * Uses plain inline styles (no frontend CSS variables or theme classes).
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
				$html = '<div class="membership-info-box info">';
				$html .= '<p>' . esc_html__( 'This user does not currently have an active membership package.', IMS_TEXT_DOMAIN ) . '</p>';
				$html .= '</div>';
				wp_send_json_success( array( 'html' => $html ) );
			}

			$package = get_post( $current_membership_id );
			if ( ! $package || 'ims_membership' !== $package->post_type ) {
				$html = '<div class="membership-info-box error">';
				$html .= '<p class="error">' . esc_html__( 'User has an invalid or expired membership.', IMS_TEXT_DOMAIN ) . '</p>';
				$html .= '</div>';
				wp_send_json_success( array( 'html' => $html ) );
			}

			$due_date           = get_user_meta( $user_id, 'ims_membership_due_date', true );
			$due_date_formatted = ! empty( $due_date )
				? date_i18n( get_option( 'date_format' ), strtotime( $due_date ) )
				: esc_html__( 'N/A', IMS_TEXT_DOMAIN );

			$allowed_properties   = get_user_meta( $user_id, 'ims_package_properties', true );
			$remaining_properties = get_user_meta( $user_id, 'ims_current_properties', true );

			$html  = '<div class="membership-info-box">';
			$html .= '<strong>' . esc_html__( 'Currently Active Package', IMS_TEXT_DOMAIN ) . '</strong>';
			$html .= '<table>';
			$html .= '<tr><td class="label">' . esc_html__( 'Package Title', IMS_TEXT_DOMAIN ) . '</td><td>' . esc_html( $package->post_title ) . '</td></tr>';
			$html .= '<tr><td class="label">' . esc_html__( 'Expiry Date', IMS_TEXT_DOMAIN ) . '</td><td>' . esc_html( $due_date_formatted ) . '</td></tr>';
			$html .= '<tr><td class="label">' . esc_html__( 'Allowed Properties', IMS_TEXT_DOMAIN ) . '</td><td>' . esc_html( $allowed_properties ?: '0' ) . '</td></tr>';
			$html .= '<tr><td class="label">' . esc_html__( 'Properties Remaining', IMS_TEXT_DOMAIN ) . '</td><td>' . esc_html( $remaining_properties ?: '0' ) . '</td></tr>';
			$html .= '</table>';
			$html .= '</div>';

			wp_send_json_success( array( 'html' => $html ) );
		}

		/**
		 * Process the form submission (WP admin context).
		 *
		 * Separate from the frontend handler — redirects back to the WP admin page.
		 */
		public function process_assign_membership() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You are not allowed to do this.', IMS_TEXT_DOMAIN ) );
			}

			if (
				! isset( $_POST['ims_assign_membership_nonce_field'] ) ||
				! wp_verify_nonce( $_POST['ims_assign_membership_nonce_field'], 'ims_assign_membership_nonce' )
			) {
				wp_die( esc_html__( 'Security check failed.', IMS_TEXT_DOMAIN ) );
			}

			$redirect_url = admin_url( 'admin.php?page=ims_assign_membership' );

			$user_id = isset( $_POST['ims_user_id'] ) ? intval( $_POST['ims_user_id'] ) : 0;
			if ( empty( $user_id ) ) {
				wp_redirect( add_query_arg( 'success', '0', $redirect_url ) );
				exit;
			}

			$membership_id         = isset( $_POST['ims_membership_id'] ) ? sanitize_text_field( wp_unslash( $_POST['ims_membership_id'] ) ) : '';
			$membership_methods    = new IMS_Membership_Method();
			$current_membership_id = get_user_meta( $user_id, 'ims_current_membership', true );

			if ( 'none' === $membership_id ) {
				// Cancel current membership.
				if ( ! empty( $current_membership_id ) ) {
					$membership_methods->cancel_user_membership( $user_id, $current_membership_id );
				}
				wp_redirect( add_query_arg( 'success', '1', $redirect_url ) );
				exit;
			}

			if ( ! empty( $membership_id ) ) {
				$membership_id = intval( $membership_id );

				// Ensure the membership ID corresponds to a published ims_membership post type.
				if ( get_post_type( $membership_id ) !== 'ims_membership' || get_post_status( $membership_id ) !== 'publish' ) {
					wp_redirect( add_query_arg( 'success', '0', $redirect_url ) );
					exit;
				}

				if ( $current_membership_id != $membership_id ) {
					if ( ! empty( $current_membership_id ) ) {
						// Update to a different membership.
						$membership_methods->update_user_membership( $user_id, $membership_id, 'manual' );
					} else {
						// Assign fresh membership.
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
