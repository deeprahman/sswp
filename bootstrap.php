<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package Sswp_Securing_Setup
 */

//function load_files_in_a_directory($directory)
//{
//    // Load all PHP files from the directory recursively
//    $iterator = new RecursiveIteratorIterator(
//        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
//    );
//
//    foreach ($iterator as $file) {
//        if ($file->isFile() && $file->getExtension() === 'php') {
//            require_once $file->getPathname();
//        }
//    }
//}
//
//load_files_in_a_directory("/home/deep/Websites/woocommerce.lo/wp/");
//load_files_in_a_directory("/home/deep/Websites/woocommerce.lo/wp/wp-includes/");
//load_files_in_a_directory("/home/deep/Websites/woocommerce.lo/wp/wp-admin/");

const ROOT = __DIR__;

const WP_ROOT =   ROOT . "/../../.." ;

require_once ROOT . "/vendor/autoload.php";
require_once(WP_ROOT . "/wp-load.php");
require_once(WP_ROOT . "/wp-admin/includes/misc.php");

require_once(ABSPATH . "wp-content/plugins/wp-securing-setup/wpss-misc.php");
require_once ROOT . "/wpss-logger.php";

require_once ROOT . "/wp-securing-setup.php";



/**
 * WordPress-style autoloader for PHPUnit.
 *
 * @param string $class_name The name of the class to be loaded.
 * @return void|null;
 */
function wpss_autoloader($class_name)
{
    // Check for 'wpss' in class name case-insensitively
    if (!preg_match('/wpss/i', $class_name)) {
        return;
    }

    $class_file = 'class-' . strtolower(str_replace('_', '-', $class_name)) . '.php';
    $directories = [
        ABSPATH . 'wp-content/plugins/wp-securing-setup/includes/',
        ABSPATH . 'wp-content/plugins/wp-securing-setup/tests/'
    ];

    foreach ($directories as $directory) {
        $file_path = $directory . $class_file;
        if (file_exists($file_path)) {
            require_once $file_path;
            return;
        }
    }
}

spl_autoload_register('wpss_autoloader');
