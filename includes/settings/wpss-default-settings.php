<?php

$htaccess_form = include( WPSS_ROOT . "/includes/settings/wpss-htaccess-settings.php");
$default = [];
write_log(['htaccess from options', $htaccess_form]);
$defaults['htaccess'] = $htaccess_form;

// Register the setting with the default value
add_option(WPSS_SETTINGS, $defaults);

// Register the setting
register_setting('wpss_options_group', WPSS_SETTINGS);

