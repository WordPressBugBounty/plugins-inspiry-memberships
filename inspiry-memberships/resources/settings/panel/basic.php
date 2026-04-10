<?php
/**
 * IMS Settings — Basic Settings tab config
 *
 * @since   3.0.8
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ims_basic_fields = array();

$ims_basic_fields[] = array(
	'id'            => 'ims_memberships_enable',
	'name'          => 'ims_memberships_enable',
	'type'          => 'buttons',
	'title'         => esc_html__( 'Enable Memberships', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Check this to enable memberships on your website.', IMS_TEXT_DOMAIN ),
	'options'       => array(
		'on'  => esc_html__( 'Enable', IMS_TEXT_DOMAIN ),
		'off' => esc_html__( 'Disable', IMS_TEXT_DOMAIN ),
	),
	'default'       => 'off',
	'parent_option' => 'ims_basic_settings',
);

$ims_basic_fields[] = array(
	'id'            => 'ims_payment_method',
	'name'          => 'ims_payment_method',
	'type'          => 'select',
	'title'         => esc_html__( 'Payments Gateway Type', IMS_TEXT_DOMAIN ),
	'subtitle'      => sprintf(
		esc_html__( 'If you choose "Custom" method, then you can use any individual direct payment methods such as Stripe, PayPal and WireTransfer. %3$sChoosing WooCommerce will allow you to use any WooCommerce supported payment method. For more details please check its documentation %1$sPayments Settings%2$s section.', IMS_TEXT_DOMAIN ),
		'<a href="https://docs.woocommerce.com/document/configuring-woocommerce-settings/" target="_blank">',
		'</a>',
		'<br>'
	),
	'options'       => array(
		'custom'      => esc_html__( 'Custom', IMS_TEXT_DOMAIN ),
		'woocommerce' => esc_html__( 'WooCommerce', IMS_TEXT_DOMAIN ),
	),
	'default'       => 'custom',
	'parent_option' => 'ims_basic_settings',
	'condition'     => array( 'ims_basic_settings[ims_memberships_enable]' => 'on' ),
);

$ims_basic_fields[] = array(
	'id'            => 'ims_adjust_current_amount',
	'name'          => 'ims_adjust_current_amount',
	'type'          => 'buttons',
	'title'         => esc_html__( 'Allow to Adjust Current Package Amount', IMS_TEXT_DOMAIN ),
	'subtitle'      => sprintf(
		esc_html__( 'Enable this to adjust the upgrade price automatically based on the user\'s remaining membership days. %1$sIf the newly selected package has a lower price than the price difference, the package will be changed without any additional cost to the user.', IMS_TEXT_DOMAIN ),
		'<br>'
	),
	'options'       => array(
		'on'  => esc_html__( 'Enable', IMS_TEXT_DOMAIN ),
		'off' => esc_html__( 'Disable', IMS_TEXT_DOMAIN ),
	),
	'default'       => 'off',
	'parent_option' => 'ims_basic_settings',
	'condition'     => array( 'ims_basic_settings[ims_memberships_enable]' => 'on' ),
);

$ims_basic_fields[] = array(
	'id'            => 'ims_adjustment_amount_offset',
	'name'          => 'ims_adjustment_amount_offset',
	'type'          => 'text',
	'title'         => esc_html__( 'Additional Fee on Package Change', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Specify a numeric value to deduct as an extra fee when changing package.', IMS_TEXT_DOMAIN ),
	'default'       => '',
	'parent_option' => 'ims_basic_settings',
	'condition'     => array( 'ims_basic_settings[ims_memberships_enable]' => 'on' ),
);

$ims_basic_fields[] = array(
	'id'            => 'ims_recurring_memberships_enable',
	'name'          => 'ims_recurring_memberships_enable',
	'type'          => 'buttons',
	'title'         => esc_html__( 'Enable Recurring Memberships', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Check this to enable recurring memberships on your website. It is available only for "Custom" Payment Method because WooCommerce does not support recurring payments.', IMS_TEXT_DOMAIN ),
	'options'       => array(
		'on'  => esc_html__( 'Enable', IMS_TEXT_DOMAIN ),
		'off' => esc_html__( 'Disable', IMS_TEXT_DOMAIN ),
	),
	'default'       => 'off',
	'parent_option' => 'ims_basic_settings',
	'condition'     => array( 'ims_basic_settings[ims_memberships_enable]' => 'on' ),
);

$ims_basic_fields[] = array(
	'id'            => 'ims_currency_code',
	'name'          => 'ims_currency_code',
	'type'          => 'text',
	'title'         => esc_html__( 'Currency Code', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Provide currency code that you want to use. Example: USD', IMS_TEXT_DOMAIN ),
	'default'       => 'USD',
	'parent_option' => 'ims_basic_settings',
	'condition'     => array( 'ims_basic_settings[ims_memberships_enable]' => 'on' ),
);

$ims_basic_fields[] = array(
	'id'            => 'ims_currency_symbol',
	'name'          => 'ims_currency_symbol',
	'type'          => 'text',
	'title'         => esc_html__( 'Currency Symbol', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Provide currency symbol that you want to use. Example: $', IMS_TEXT_DOMAIN ),
	'default'       => '$',
	'parent_option' => 'ims_basic_settings',
	'condition'     => array( 'ims_basic_settings[ims_memberships_enable]' => 'on' ),
);

$ims_basic_fields[] = array(
	'id'            => 'ims_currency_position',
	'name'          => 'ims_currency_position',
	'type'          => 'select',
	'title'         => esc_html__( 'Currency Symbol Position', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Default: Before', IMS_TEXT_DOMAIN ),
	'options'       => array(
		'before' => esc_html__( 'Before (E.g. $10)', IMS_TEXT_DOMAIN ),
		'after'  => esc_html__( 'After (E.g. 10$)', IMS_TEXT_DOMAIN ),
	),
	'default'       => 'before',
	'parent_option' => 'ims_basic_settings',
	'condition'     => array( 'ims_basic_settings[ims_memberships_enable]' => 'on' ),
);

$ims_basic_config = array(
	'id'     => 'ims-basic',
	'title'  => esc_html__( 'Basic Settings', IMS_TEXT_DOMAIN ),
	'group'  => 'memberships',
	'order'  => 1,
	'fields' => $ims_basic_fields,
);

return apply_filters( 'ims_basic_panel_config', $ims_basic_config );
