<?php
/**
 * Basic Settings File
 *
 * File for adding basic settings.
 *
 * @since   1.0.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $ims_settings;

$ims_basic_settings_arr = apply_filters(
	'ims_basic_settings',
	array(
		array(
			'id'   => 'ims_memberships_enable',
			'type' => 'checkbox',
			'name' => esc_html__( 'Enable Memberships', IMS_TEXT_DOMAIN ),
			'desc' => esc_html__( 'Check this to enable memberships on your website.', IMS_TEXT_DOMAIN ),
		),
		array(
			'id'      => 'ims_payment_method',
			'type'    => 'select',
			'name'    => esc_html__( 'Payments Gateway Type', IMS_TEXT_DOMAIN ),
			'desc'    => sprintf( esc_html__( 'If you choose "Custom" method, then you can use any individual direct payment methods such as Stripe, PayPal and WireTransfer. %3$sChoosing WooCommerce will allow you to use any WooCommerce supported payment method. For more details please check its documentation %1$sPayments Settings%2$s section.', IMS_TEXT_DOMAIN ), '<a href="https://docs.woocommerce.com/document/configuring-woocommerce-settings/" target="_blank">', '</a>', '<br>' ),
			'default' => 'custom',
			'options' => array(
				'custom'      => 'Custom',
				'woocommerce' => 'WooCommerce',
			),
		),
		array(
			'id'   => 'ims_adjust_current_amount',
			'type' => 'checkbox',
			'name' => esc_html__( 'Allow to adjust current package amount', IMS_TEXT_DOMAIN ),
			'desc' => sprintf(esc_html__( 'After enabling this option, if a user already has a membership package, the price will be automatically adjusted based on the remaining days. %1$sIf the newly selected package has a lower price than the price difference, the package will be changed without any additional cost to the user.', IMS_TEXT_DOMAIN ), '<br>'),
		),
		array(
			'id'      => 'ims_adjustment_amount_offset',
			'type'    => 'text',
			'name'    => esc_html__( 'Additional Fee on Package Change', IMS_TEXT_DOMAIN ),
			'desc'    => esc_html__( 'Specify a numeric value to deduct as an extra fee when changing package.', IMS_TEXT_DOMAIN )
		),
		array(
			'id'   => 'ims_recurring_memberships_enable',
			'type' => 'checkbox',
			'name' => esc_html__( 'Enable Recurring Memberships', IMS_TEXT_DOMAIN ),
			'desc' => esc_html__( 'Check this to enable recurring memberships on your website. It is available only for "Custom" Payment Method because WooCommerce does not support recurring payments.', IMS_TEXT_DOMAIN ),
		),
		array(
			'id'      => 'ims_currency_code',
			'type'    => 'text',
			'name'    => esc_html__( 'Currency Code', IMS_TEXT_DOMAIN ),
			'desc'    => esc_html__( 'Provide currency code that you want to use. Example: USD', IMS_TEXT_DOMAIN ),
			'default' => 'USD',
		),
		array(
			'id'      => 'ims_currency_symbol',
			'type'    => 'text',
			'name'    => esc_html__( 'Currency Symbol', IMS_TEXT_DOMAIN ),
			'desc'    => esc_html__( 'Provide currency symbol that you want to use. Example: $', IMS_TEXT_DOMAIN ),
			'default' => '$',
		),
		array(
			'id'      => 'ims_currency_position',
			'type'    => 'select',
			'name'    => esc_html__( 'Currency Symbol Position', IMS_TEXT_DOMAIN ),
			'desc'    => esc_html__( 'Default: Before', IMS_TEXT_DOMAIN ),
			'default' => 'before',
			'options' => array(
				'before' => 'Before (E.g. $10)',
				'after'  => 'After (E.g. 10$)',
			),
		),
	)
);

if ( ! empty( $ims_basic_settings_arr ) && is_array( $ims_basic_settings_arr ) ) {
	foreach ( $ims_basic_settings_arr as $ims_basic_setting ) {
		$ims_settings->add_field( 'ims_basic_settings', $ims_basic_setting );
	}
}
