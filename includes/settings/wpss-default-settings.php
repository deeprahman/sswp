<?php

$file_permission = include WPSS_ROOT . '/includes/settings/wpss-file-permission-settings.php';
$htaccess_form   = include WPSS_ROOT . '/includes/settings/wpss-htaccess-settings.php';
$defaults        = array();

// write_log(['_wpss_settings', $file_permission]);
$defaults['file_permission'] = $file_permission;
$defaults['htaccess']        = $htaccess_form;

// Register the setting with the default value
add_option( WPSS_SETTINGS, $defaults );

// Register the setting
register_setting( 'wpss_options_group', WPSS_SETTINGS );
