<?php
/**
 * IMS Settings — Stripe Settings tab config
 *
 * @since   3.1.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ims_stripe_fields = array();

$ims_stripe_fields[] = array(
	'id'            => 'ims_stripe_enable',
	'name'          => 'ims_stripe_enable',
	'type'          => 'buttons',
	'title'         => esc_html__( 'Enable Stripe', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Check this to enable Stripe payments.', IMS_TEXT_DOMAIN ),
	'options'       => array(
		'on'  => esc_html__( 'Enable', IMS_TEXT_DOMAIN ),
		'off' => esc_html__( 'Disable', IMS_TEXT_DOMAIN ),
	),
	'default'       => 'off',
	'parent_option' => 'ims_stripe_settings',
);

$ims_stripe_fields[] = array(
	'id'            => 'ims_stripe_publishable',
	'name'          => 'ims_stripe_publishable',
	'type'          => 'text',
	'title'         => esc_html__( 'Publishable Key', IMS_TEXT_DOMAIN ),
	'subtitle'      => sprintf(
		esc_html__( 'Paste your account publishable key here. For help consult %1$sStripe Settings Guide%2$s.', IMS_TEXT_DOMAIN ),
		'<a href="https://inspirythemes.com/realhomes-memberships-setup/#stripe-settings" target="_blank">',
		'</a>'
	),
	'default'       => '',
	'parent_option' => 'ims_stripe_settings',
	'condition'     => array( 'ims_stripe_settings[ims_stripe_enable]' => 'on' ),
);

$ims_stripe_fields[] = array(
	'id'            => 'ims_stripe_secret',
	'name'          => 'ims_stripe_secret',
	'type'          => 'text',
	'title'         => esc_html__( 'Secret Key', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Paste your stripe account secret key here.', IMS_TEXT_DOMAIN ),
	'default'       => '',
	'parent_option' => 'ims_stripe_settings',
	'condition'     => array( 'ims_stripe_settings[ims_stripe_enable]' => 'on' ),
);

$ims_stripe_fields[] = array(
	'id'            => 'ims_stripe_btn_label',
	'name'          => 'ims_stripe_btn_label',
	'type'          => 'text',
	'title'         => esc_html__( 'Stripe Button Label', IMS_TEXT_DOMAIN ),
	'subtitle'      => esc_html__( 'Default: Pay with Card', IMS_TEXT_DOMAIN ),
	'default'       => 'Pay with Card',
	'parent_option' => 'ims_stripe_settings',
	'condition'     => array( 'ims_stripe_settings[ims_stripe_enable]' => 'on' ),
);

$ims_stripe_fields[] = array(
	'id'            => 'ims_stripe_webhook_url',
	'name'          => 'ims_stripe_webhook_url',
	'type'          => 'text',
	'title'         => esc_html__( 'Stripe WebHook URL', IMS_TEXT_DOMAIN ),
	'subtitle'      => sprintf(
		esc_html__( '%1$sImportant:%2$s Webhook URL plays an important role in accepting recurring payments through Stripe. It is important to set the webhook URL correctly otherwise recurring memberships through Stripe will not work. For help consult %3$sStripe Settings Guide%4$s.', IMS_TEXT_DOMAIN ),
		'<strong>',
		'</strong>',
		'<a href="https://inspirythemes.com/realhomes-memberships-setup/#stripe-settings" target="_blank">',
		'</a>'
	),
	'default'       => esc_url( add_query_arg( array( 'ims_stripe' => 'membership_event' ), home_url( '/' ) ) ),
	'parent_option' => 'ims_stripe_settings',
	'condition'     => array( 'ims_stripe_settings[ims_stripe_enable]' => 'on' ),
);

$ims_stripe_config = array(
	'id'     => 'ims-stripe',
	'title'  => esc_html__( 'Stripe Settings', IMS_TEXT_DOMAIN ),
	'group'  => 'memberships',
	'order'  => 2,
	'fields' => $ims_stripe_fields,
);

return apply_filters( 'ims_stripe_panel_config', $ims_stripe_config );
