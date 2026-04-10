<?php
/**
 * IMS Settings — Memberships top-level config (parent container)
 *
 * Creates the "Memberships" main sidebar item in the ERE options panel.
 *
 * @since 3.1.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ims_memberships_config = array(
	'id'    => 'memberships',
	'title' => esc_html__( 'Memberships', IMS_TEXT_DOMAIN ),
	'order' => 21,
	'icon'  => 'property_settings',
);

return apply_filters( 'ims_memberships_panel_config', $ims_memberships_config );
