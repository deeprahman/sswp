<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$sswp_file_permission = include SSWP_ROOT . '/includes/settings/sswp-file-permission-settings.php';
$sswp_htaccess_form   = include SSWP_ROOT . '/includes/settings/sswp-htaccess-settings.php';
$sswp_rest_api        = include SSWP_ROOT . '/includes/settings/sswp-rest-api-settings.php';
$sswp_defaults        = array();

$sswp_defaults['file_permission'] = $sswp_file_permission;
$sswp_defaults['htaccess']        = $sswp_htaccess_form;
$sswp_defaults['rest_api']        = $sswp_rest_api;

// Register the setting with the default value
add_option( SSWP_SETTINGS, $sswp_defaults );

// Register the setting
// phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic -- Sanitization handled by sswp_sanitize_secure_setup_settings.
register_setting(
	'sswp_options_group',
	SSWP_SETTINGS,
	array(
		'type'              => 'array',
		'sanitize_callback' => 'sswp_sanitize_secure_setup_settings',
	)
);
