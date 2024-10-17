<?php
// Conditionally disable the system.multicall method in XML-RPC
add_filter('xmlrpc_methods', 'handle_xml_rpc_method');

function handle_xml_rpc_method($methods)
{
    global $wpss;
    $ht_form_settings = (get_options([$wpss->settings]))['_wpss_settings']['htaccess']['ht_form'];
    array_walk($ht_form_settings, function ($v) use ($methods) {
        if ($v["name"] !== "protect-xml-rpc") {
            return;
        }
        if ($v["value"] == "on") {
            if (isset($methods['system.multicall'])) {
                unset($methods['system.multicall']);
            }
        }
    });
}