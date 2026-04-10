<?php
/**
 * IMS Settings — Wire Transfer Settings tab config
 *
 * @since 3.1.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ims_wire_fields = array();

$ims_wire_fields[] = array(
	'id'            => 'ims_wire_enable',
	'name'          => 'ims_wire_enable',
	'type'          => 'buttons',
	'title'         => esc_html__( 'Enable Wire Transfer', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Check this to enable wire transfer.', IMS_TEXT_DOMAIN ),
	'options'       => array(
		'on'  => esc_html__( 'Enable', IMS_TEXT_DOMAIN ),
		'off' => esc_html__( 'Disable', IMS_TEXT_DOMAIN ),
	),
	'default'       => 'off',
	'parent_option' => 'ims_wire_settings',
);

$ims_wire_fields[] = array(
	'id'            => 'ims_wire_transfer_instructions',
	'name'          => 'ims_wire_transfer_instructions',
	'type'          => 'textarea',
	'title'         => esc_html__( 'Instructions for Wire Transfer', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Enter the instructions for wire transfer.', IMS_TEXT_DOMAIN ),
	'default'       => 'Please include the following information on all wire transfers to our bank account and use your order ID as the payment reference.',
	'parent_option' => 'ims_wire_settings',
	'condition'     => array( 'ims_wire_settings[ims_wire_enable]' => 'on' ),
);

$ims_wire_fields[] = array(
	'id'            => 'ims_wire_account_name',
	'name'          => 'ims_wire_account_name',
	'type'          => 'text',
	'title'         => esc_html__( 'Account Name', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Enter your account name.', IMS_TEXT_DOMAIN ),
	'default'       => esc_html( get_bloginfo( 'name' ) ),
	'parent_option' => 'ims_wire_settings',
	'condition'     => array( 'ims_wire_settings[ims_wire_enable]' => 'on' ),
);

$ims_wire_fields[] = array(
	'id'            => 'ims_wire_account_number',
	'name'          => 'ims_wire_account_number',
	'type'          => 'text',
	'title'         => esc_html__( 'Account Number', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Enter your account number.', IMS_TEXT_DOMAIN ),
	'default'       => '1111-2222-33333-44-5',
	'parent_option' => 'ims_wire_settings',
	'condition'     => array( 'ims_wire_settings[ims_wire_enable]' => 'on' ),
);

$ims_wire_config = array(
	'id'     => 'ims-wire',
	'title'  => esc_html__( 'Wire Transfer Settings', IMS_TEXT_DOMAIN ),
	'group'  => 'memberships',
	'order'  => 4,
	'fields' => $ims_wire_fields,
);

return apply_filters( 'ims_wire_panel_config', $ims_wire_config );
