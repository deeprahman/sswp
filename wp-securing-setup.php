<?php
/**
 * Plugin Name: WP Securing Setup (Simple and Effective User Protection)
 * Plugin URI: https://deeprahman.com/wp-securing-setup
 * Description: This plugin helps secure your WordPress website by implementing various security measures.
 * Version: 1.0.0
 * Author: Deep
 * Author URI: https://deeprahman.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-securing-setup  * Domain Path: /languages
 */
// Check for plugin activation (optional)
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
// Register activation and deactivation hooks
register_activation_hook( __FILE__, 'wpss_activate' );
register_deactivation_hook( __FILE__, 'wpss_deactivate' );

// Function to handle plugin activation
function wpss_activate() {
  // Add your activation logic here
  // For example, create options, update database tables, etc.
}

// Function to handle plugin deactivation
function wpss_deactivate() {
  // Add your deactivation logic here
  // For example, delete options, remove database tables, etc.
}

// Include the plugin class
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-securing-setup.php';

// Hook your plugin functionalities here
add_action( 'plugins_loaded', 'wp_securing_seup_init' );





// Function to initialize your plugin (optional)
function wp_securing_seup_init() {
  // Instantiate the plugin class
  new WP_Securing_Setup();
}
