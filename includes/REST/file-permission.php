<?php
add_action('rest_api_init', function () {
    register_rest_route('wpss/v1', '/file-permissions', array(
        'methods' => 'GET',
        'callback' => 'wpss_file_permissions_callback',
        'permission_callback' => 'wpss_file_permissions_permission_check',
        'args' => array(
            'nonce' => array(
                'required' => true,
            ),
        ),
    ));
    write_log("Route Register: wpss/v1/file-permission");
});

function wpss_file_permissions_permission_check($request) {
    global $wpss;

    write_log($request);
    if (!current_user_can('manage_options')) {
        write_log("User does not have manage_option permission");
        return false;
    }

    return true;
}

function wpss_file_permissions_callback($request) {
    global $wpss;
    include_once $wpss->root . DIRECTORY_SEPARATOR . "includes\class-wpss-file-permission-manager.php";
    $files = ["wp-config.php", "wp-login.php", "wp-content", "wp-content/uploads", "wp-content/plugins", "wp-content/themes", 'wp-cat.php'];

    $checker = new WPSS_File_Permission_Manager($files);
        
    $fs_permission = $checker->check_permissions();
    write_log($fs_permission);
    
    // Add your file permissions logic here
    $response = array(
        'success' => true,
        'data' => array(
           "fs_data" => json_encode($fs_permission, JSON_NUMERIC_CHECK) 
        )
    );
    return rest_ensure_response($response);
}

