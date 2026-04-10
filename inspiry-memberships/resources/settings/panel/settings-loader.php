<?php
/**
 * IMS Settings Loader — hooks memberships configs into ERE Settings.
 *
 * @since 3.1.0
 * @package IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inject IMS config sections into the ERE settings config array.
 *
 * @since 3.1.0
 *
 * @param array $configs All loaded ERE config sections.
 *
 * @return array Modified configs with IMS sections added.
 */
function ims_add_settings_configs( $configs ) {

	$panel_dir = IMS_BASE_DIR . 'resources/settings/panel/';

	$config_files = array(
		'memberships',
		'basic',
		'stripe',
		'paypal',
		'wire',
	);

	foreach ( $config_files as $file ) {
		$file_path = $panel_dir . $file . '.php';

		if ( file_exists( $file_path ) ) {
			$config = include $file_path;

			if ( is_array( $config ) && ! empty( $config['id'] ) ) {
				$configs[ $config['id'] ] = $config;
			}
		}
	}

	return $configs;
}
add_filter( 'ere_settings_config', 'ims_add_settings_configs' );
