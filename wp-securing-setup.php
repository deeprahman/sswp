<?php
/**
 * Plugin Name: WP Securing Setup (Simple and Effective User Protection)
 * Plugin URI: https://deeprahman.com/wp-securing-setup
 * Description: This plugin helps secure your WordPress website by implementing various security measures.
 * Version: 0.1.0
 * Author: Deep
 * Author URI: https://deeprahman.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-securing-setup  * Domain Path: /languages
 */
// Check for plugin activation (optional)
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Logger
 */
if (!function_exists('write_log')) {

    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}
// Set Plugin Root
define("WPSS_ROOT", plugin_dir_path(__FILE__));

// Set Plugin URL
define("WPSS_URL", plugin_dir_url(__FILE__));

// Set Domain
define("WPSS_DOMAIN" , "wp-securing-setup");

define("WPSS_VERSION" , "0.1.0");

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'wpss_activate');
register_deactivation_hook(__FILE__, 'wpss_deactivate');

// Function to handle plugin activation
function wpss_activate()
{
    // Add your activation logic here
    // For example, create options, update database tables, etc.
}

// Function to handle plugin deactivation
function wpss_deactivate()
{
    // Add your deactivation logic here
    // For example, delete options, remove database tables, etc.
}

// Include the plugin class
require_once plugin_dir_path(__FILE__) . 'includes/class-wp-securing-setup.php';


try {

    $wpss = new WP_Securing_Setup();

} catch (\Exception $ex) {
    error_log($ex->getMessage());
}

