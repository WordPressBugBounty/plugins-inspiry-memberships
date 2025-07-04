<?php
/**
 * Plugin Settings Initializer
 *
 * Initializer file for plugin settings.
 *
 * @since   1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Membership settings page.
 *
 * @since 1.0.0
 */
if ( file_exists( IMS_BASE_DIR . '/resources/settings/class-wp-osa.php' ) ) {
	include_once IMS_BASE_DIR . '/resources/settings/class-wp-osa.php';
}

if ( class_exists( 'WP_OSA' ) ) {

	// New Settings Menu.
	global $ims_settings;
	$ims_settings = new WP_OSA();

	// Before adding settings hook.
	do_action( 'ims_before_settings_loaded', $ims_settings );

	/**
	 * Adding sections for the settings page.
	 *
	 * @since 1.0.0
	 */
	$ims_sections_arr = apply_filters(
		'ims_settings_sections',
		array(
			array(
				'id'    => 'ims_basic_settings',
				'title' => esc_html__( 'Basic Settings', IMS_TEXT_DOMAIN ),
			),
			array(
				'id'    => 'ims_stripe_settings',
				'title' => esc_html__( 'Stripe Settings', IMS_TEXT_DOMAIN ),
			),
			array(
				'id'    => 'ims_paypal_settings',
				'title' => esc_html__( 'PayPal Settings', IMS_TEXT_DOMAIN ),
			),
			array(
				'id'    => 'ims_wire_settings',
				'title' => esc_html__( 'Wire Transfer Settings', IMS_TEXT_DOMAIN ),
			),
		)
	);

	if ( ! empty( $ims_sections_arr ) && is_array( $ims_sections_arr ) ) {
		foreach ( $ims_sections_arr as $ims_section ) {
			$ims_settings->add_section( $ims_section );
		}
	}

	/**
	 * Basic settings file.
	 *
	 * @since 1.0.0
	 */
	if ( file_exists( IMS_BASE_DIR . 'resources/settings/basic-settings.php' ) ) {
		include_once IMS_BASE_DIR . 'resources/settings/basic-settings.php';
	}

	/**
	 * Stripe settings file.
	 *
	 * @since 1.0.0
	 */
	if ( file_exists( IMS_BASE_DIR . 'resources/settings/stripe-settings.php' ) ) {
		include_once IMS_BASE_DIR . 'resources/settings/stripe-settings.php';
	}

	/**
	 * PayPal settings file.
	 *
	 * @since 1.0.0
	 */
	if ( file_exists( IMS_BASE_DIR . 'resources/settings/paypal-settings.php' ) ) {
		include_once IMS_BASE_DIR . 'resources/settings/paypal-settings.php';
	}

	/**
	 * Wire settings file.
	 *
	 * @since 1.0.0
	 */
	if ( file_exists( IMS_BASE_DIR . 'resources/settings/wire-settings.php' ) ) {
		include_once IMS_BASE_DIR . 'resources/settings/wire-settings.php';
	}

	// After adding settings hook.
	do_action( 'ims_after_settings_loaded', $ims_settings );

}


function enqueue_styles_on_settings_page( $hook_suffix ) {
	// Check if we are on the settings page with the slug "memberships_page_ims_settings"
	if ( $hook_suffix === 'memberships_page_ims_settings' ) {
		// Enqueue your styles here
		wp_enqueue_style( 'ims-settings', IMS_BASE_URL . 'resources/css/settings.css', array(), IMS_VERSION );
	}
}

add_action( 'admin_enqueue_scripts', 'enqueue_styles_on_settings_page' );
