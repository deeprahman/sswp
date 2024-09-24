<?php
/**
 * PHPUnit Bootstrap File
 *
 * This file is used to set up the testing environment for your WordPress plugin or theme.
 * It includes the WordPress `wp-load.php` file, which loads the WordPress environment.
 *
 * Make sure to update the path to the `wp-load.php` file to match your local setup.
 */
// Path to the WordPress file autoloader
require_once __DIR__ . "/../wp-autoloader.php";
// Path to the WordPress directory
$wp_root = '/home/deep/Websites/woocommerce.lo/wp';

// Load the WordPress environment
require_once $wp_root . '/wp-load.php';

// Set up the testing environment
global $wp, $wp_query, $current_site, $current_blog;
$wp->init();
$wp->register_globals();
//$current_site = $wp->get_current_site();
//$current_blog = $wp->get_current_blog_id();
