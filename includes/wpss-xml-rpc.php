<?php
// Conditionally disable the system.multicall method in XML-RPC
add_filter('xmlrpc_methods', 'conditional_disable_system_multicall');

function conditional_disable_system_multicall($methods) {
    // Check if the option to disable system.multicall is enabled
    if (get_option('disable_rpc_multicall', false)) {
        // Check if the 'system.multicall' method exists and remove it
        if (isset($methods['system.multicall'])) {
            unset($methods['system.multicall']);
        }
    }
    return $methods;
}

// Add an option to the WordPress settings
add_action('admin_init', 'register_disable_rpc_multicall_setting');

function register_disable_rpc_multicall_setting() {
    register_setting('general', 'disable_rpc_multicall', 'boolval');
    add_settings_field(
        'disable_rpc_multicall',
        'Disable XML-RPC system.multicall',
        'disable_rpc_multicall_callback',
        'general'
    );
}

function disable_rpc_multicall_callback() {
    echo '<input type="checkbox" name="disable_rpc_multicall" value="1" ' . checked(1, get_option('disable_rpc_multicall', 0), false) . '/>';
    echo '<label for="disable_rpc_multicall">Check to disable XML-RPC system.multicall method</label>';
}