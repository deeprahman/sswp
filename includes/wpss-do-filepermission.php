<?php
// TODO Debug Load WordPress core
require_once('C:/xampp/htdocs/wp/wp-load.php');
require_once('C:/xampp/htdocs/wp/wp-admin/includes/file.php');
require_once('D:\MISC\Projects\wp-securing-setup\includes\class-wpss-file-permission-manager.php');
WP();
// Usage
$files = ["wp-config.php", "wp-login.php", "wp-content", "wp-content/uploads", "wp-content/plugins", "wp-content/themes", 'wp-cat.php'];

$checker = new WPSS_File_Permission_Manager($files);
// $checker->display_results();
print_r($checker->check_permissions());

echo PHP_EOL;
/*
// Example usage of new methods:
// $checker->change_file_permission(ABSPATH . 'wp-config.php', '644');
// $checker->change_to_recommended_permissions(ABSPATH . 'wp-content');
// $current_permission = $checker->get_current_permission(ABSPATH . 'wp-config.php');
// $checker->set_recommended_permission('file', '640');
// $tmp = $checker->check_permissions();

// print_r(get_home_path() . "wp-login.php");
$test_path = get_home_path() . "wp-login.php";
// echo $test_path . PHP_EOL;
echo "Current permission of " . basename($test_path), " ", $tmp = $checker->get_current_permission($test_path), PHP_EOL;
echo " Recommended permission of  the file ", basename($test_path), " ", $tmp = $checker->get_recommended_permission($test_path);
echo "Setting Permission... ", $checker->set_permission($test_path, 400), PHP_EOL;
echo "Current permission after change  ", " ", $tmp = $checker->get_current_permission($test_path), PHP_EOL;
// print_r($tmp);
// $checker->display_results();


/**
 * Stores the given data in the options table.
 *
 * This function should be called during plugin activation to store the result of
 * calling the check_permissions() method of WPSS_Filesystem_Manager class.
 *
 * @param array $data The result of the call to the WPSS_Filesystem_Manager::check_permissions() method.
 * @return bool True if the data was successfully stored, false otherwise.
 */
function store_plugin_activation_data(array $data): bool {
    $activation_options = get_option('_wpss_activation_options', []);

    if ( ! isset( $activation_options['initial_fs_data'] ) ) {
        $activation_options = $data;
        update_option('_wpss_activation_options', $activation_options);
        return true;
    }

    return false;
}   

/**
 * This function sets permission as per the return value of WPSS_Filesystem_Manager::check_permissions()
 * @param array $data The result of the call to the WPSS_Filesystem_Manager::check_permissions() method.    
 * @return bool on success  
 */
$is_permission_set = function (array $permission_data) use ( &$wpss_filesystem_permission): bool {
    array_walk($permission_data, function($k,$v) use($wpss_filesystem_permission){
        $wpss_filesystem_permission->set_permission($k,$v['permission']);
    });
    return true;
};

