<?php
/**
 * IMS Settings — PayPal Settings tab config
 *
 * @since   3.1.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ims_paypal_fields = array();

$ims_paypal_fields[] = array(
	'id'            => 'ims_paypal_enable',
	'name'          => 'ims_paypal_enable',
	'type'          => 'buttons',
	'title'         => esc_html__( 'Enable PayPal', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Check this to enable PayPal payments.', IMS_TEXT_DOMAIN ),
	'options'       => array(
		'on'  => esc_html__( 'Enable', IMS_TEXT_DOMAIN ),
		'off' => esc_html__( 'Disable', IMS_TEXT_DOMAIN ),
	),
	'default'       => 'off',
	'parent_option' => 'ims_paypal_settings',
);

$ims_paypal_fields[] = array(
	'id'            => 'ims_paypal_test_mode',
	'name'          => 'ims_paypal_test_mode',
	'type'          => 'buttons',
	'title'         => esc_html__( 'Sandbox Mode', IMS_TEXT_DOMAIN ),
	'subtitle'      => sprintf(
		esc_html__( 'The PayPal sandbox is a self-contained, virtual testing environment that simulates the live PayPal production environment. For more info consult %1$sPayPal sandbox testing guide%2$s.', IMS_TEXT_DOMAIN ),
		'<a href="https://developer.paypal.com/tools/sandbox/" target="_blank">',
		'</a>'
	),
	'options'       => array(
		'on'  => esc_html__( 'Enable', IMS_TEXT_DOMAIN ),
		'off' => esc_html__( 'Disable', IMS_TEXT_DOMAIN ),
	),
	'default'       => 'off',
	'parent_option' => 'ims_paypal_settings',
	'condition'     => array( 'ims_paypal_settings[ims_paypal_enable]' => 'on' ),
);

$ims_paypal_fields[] = array(
	'id'            => 'ims_paypal_client_id',
	'name'          => 'ims_paypal_client_id',
	'type'          => 'text',
	'title'         => esc_html__( 'Client ID', IMS_TEXT_DOMAIN ),
	'subtitle'      => sprintf(
		esc_html__( 'Paste your account Client ID here. For help consult %1$sPayPal Settings Guide%2$s.', IMS_TEXT_DOMAIN ),
		'<a href="https://inspirythemes.com/realhomes-memberships-setup/#paypal-settings" target="_blank">',
		'</a>'
	),
	'default'       => '',
	'parent_option' => 'ims_paypal_settings',
	'condition'     => array( 'ims_paypal_settings[ims_paypal_enable]' => 'on' ),
);

$ims_paypal_fields[] = array(
	'id'            => 'ims_paypal_client_secret',
	'name'          => 'ims_paypal_client_secret',
	'type'          => 'text',
	'title'         => esc_html__( 'Client Secret', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Paste your account Client Secret here.', IMS_TEXT_DOMAIN ),
	'default'       => '',
	'parent_option' => 'ims_paypal_settings',
	'condition'     => array( 'ims_paypal_settings[ims_paypal_enable]' => 'on' ),
);

$ims_paypal_fields[] = array(
	'id'            => 'ims_paypal_ipn_url',
	'name'          => 'ims_paypal_ipn_url',
	'type'          => 'text',
	'title'         => esc_html__( 'PayPal IPN URL', IMS_TEXT_DOMAIN ),
	'subtitle'      => sprintf(
		esc_html__( '%1$sImportant:%2$s Webhook URL plays an important role in accepting recurring payments through PayPal. It is important to set the webhook URL correctly otherwise recurring memberships through PayPal will not work. For help consult %3$sPayPal Settings Guide%4$s.', IMS_TEXT_DOMAIN ),
		'<strong>',
		'</strong>',
		'<a href="https://inspirythemes.com/realhomes-memberships-setup/#paypal-settings" target="_blank">',
		'</a>'
	),
	'default'       => esc_url( add_query_arg( array( 'ims_paypal' => 'notification' ), home_url( '/' ) ) ),
	'parent_option' => 'ims_paypal_settings',
	'condition'     => array( 'ims_paypal_settings[ims_paypal_enable]' => 'on' ),
);

$ims_paypal_config = array(
	'id'     => 'ims-paypal',
	'title'  => esc_html__( 'PayPal Settings', IMS_TEXT_DOMAIN ),
	'group'  => 'memberships',
	'order'  => 3,
	'fields' => $ims_paypal_fields,
);

return apply_filters( 'ims_paypal_panel_config', $ims_paypal_config );
