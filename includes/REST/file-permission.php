<?php



add_action('rest_api_init', function () {
    register_rest_route('wpss/v1', '/file-permissions', array(
        'methods' => ['GET', 'PATCH', 'PUT', 'POST', 'DELETE'],
        'callback' => 'wpss_file_permissions_callback',
        'permission_callback' => 'wpss_file_permissions_permission_check',
        'args' => array(
            'nonce' => array(
                'required' => true,
            ),
        ),
    ));
});

function wpss_file_permissions_permission_check($request)
{
    global $wpss;

    if (!current_user_can('manage_options')) {
        return false;
    }

    return true;
}

function wpss_file_permissions_callback($request)
{
    $message = '';
    write_log(["Function: ".__FUNCTION__, $request]);
    switch ($request->get_method()) {
        case 'GET':
            $fs_permission = get_file_permissions();
            break;
        case 'POST':
            $data = $request->get_params(); // The data sent from the frontend
            $message .= do_recommended_permission();
            $fs_permission = get_file_permissions();
            break;
        case 'PUT':
            break;
        case 'PATCH':
            break;
    }

    // Add your file permissions logic here
    $response = array(
        'success' => true,
        'data' => array(
            "message" => $message,
            "fs_data" => isset($fs_permission) ? json_encode($fs_permission, JSON_NUMERIC_CHECK) : NULL
        )
    );
    return rest_ensure_response($response);
}


function get_file_permissions()
{
    global $wpss;
    include_once $wpss->root . DIRECTORY_SEPARATOR . "includes/class-wpss-file-permission-manager.php";

    $checker = new WPSS_File_Permission_Manager($wpss->file_paths);

    return $checker->check_permissions();

}

function do_recommended_permission(): string
{

    global $wpss;
    include_once $wpss->root . DIRECTORY_SEPARATOR . "includes/class-wpss-file-permission-manager.php";

    $checker = new WPSS_File_Permission_Manager($wpss->file_paths);
    $fitered_files = array_filter($wpss->file_paths, function ($v) use ($checker) {
        return !($checker->change_to_recommended_permissions($v));
    });

    $message = '';

    if (!empty($fitered_files)) {
        $e_files = implode(',', $fitered_files);
        $message = __("Could not change permissoin for the given files: ", $wpss->domain) . $e_files;
        $err_msg = "Function: " . __FUNCTION__ . "Message: " . $message;
        error_log($err_msg);
    }

    return $message;
}
