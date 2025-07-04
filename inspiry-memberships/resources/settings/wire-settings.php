<?php
/**
 * Wire Settings File
 *
 * File for adding wire settings.
 *
 * @since   1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $ims_settings;

$ims_wire_settings_arr = apply_filters(
	'ims_wire_settings',
	array(
		array(
			'id'   => 'ims_wire_enable',
			'type' => 'checkbox',
			'name' => esc_html__( 'Enable Wire Transfer', IMS_TEXT_DOMAIN ),
			'desc' => esc_html__( 'Check this to enable wire transfer.', IMS_TEXT_DOMAIN ),
		),
		array(
			'id'      => 'ims_wire_transfer_instructions',
			'type'    => 'textarea',
			'name'    => esc_html__( 'Instructions for Wire Transfer', IMS_TEXT_DOMAIN ),
			'desc'    => esc_html__( 'Enter the instructions for wire transfer.', IMS_TEXT_DOMAIN ),
			'default' => 'Please include the following information on all wire transfers to our bank account and use your order ID as the payment reference.',
		),
		array(
			'id'      => 'ims_wire_account_name',
			'type'    => 'text',
			'name'    => esc_html__( 'Account Name', IMS_TEXT_DOMAIN ),
			'desc'    => esc_html__( 'Enter your account name.', IMS_TEXT_DOMAIN ),
			'default' => esc_html( get_bloginfo( 'name' ) ),
		),
		array(
			'id'      => 'ims_wire_account_number',
			'type'    => 'text',
			'name'    => esc_html__( 'Account Number', IMS_TEXT_DOMAIN ),
			'desc'    => esc_html__( 'Enter your account number.', IMS_TEXT_DOMAIN ),
			'default' => esc_html__( '1111-2222-33333-44-5', IMS_TEXT_DOMAIN ),
		),
	)
);

if ( ! empty( $ims_wire_settings_arr ) && is_array( $ims_wire_settings_arr ) ) {
	foreach ( $ims_wire_settings_arr as $ims_wire_setting ) {
		$ims_settings->add_field( 'ims_wire_settings', $ims_wire_setting );
	}
}
