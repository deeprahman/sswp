<?php

$file_permission = include SSWP_ROOT . '/includes/settings/wpss-file-permission-settings.php';
$htaccess_form = include SSWP_ROOT . '/includes/settings/wpss-htaccess-settings.php';
$rest_api = include SSWP_ROOT . "/includes/settings/wpss-rest-api-settings.php";
$defaults = array();

$defaults['file_permission'] = $file_permission;
$defaults['htaccess'] = $htaccess_form;
$defaults['rest_api'] = $rest_api;

// Register the setting with the default value
add_option(SSWP_SETTINGS, $defaults);

// Register the setting
register_setting(
    'wpss_options_group',
    SSWP_SETTINGS,
    array(
        'type' => 'array',
        'sanitize_callback' => 'sswp_sanitize_secure_setup_settings',
    )
);
