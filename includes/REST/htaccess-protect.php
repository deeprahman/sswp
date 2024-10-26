<?php


add_action('rest_api_init', function () {


    register_rest_route('wpss/v1', '/htaccess-protect', array(
        'methods' => ['GET', 'DELETE', 'POST', 'PATCH', 'PUT'],
        'callback' => 'wpss_htaccess_protect_callback',
        'permission_callback' => 'wpss_htaccess_protect_permission_check',
        'args' => array(
            'nonce' => array(
                'required' => true,
                
            ),
        ),
    ));
    if (function_exists('write_log')) {
    }
});

function wpss_htaccess_protect_permission_check($request) {
    return current_user_can('manage_options');
}

function wpss_htaccess_protect_callback($request) {
    global $wpss,$allowed_methods;

    if (function_exists('write_log')) {
    }
    
    try {
        // if( !array_search($request->method, $allowed_methods,  $strict = false) === true ){
        //     return new WP_Error('wpss_error', "Method Disallowed", array('status' => 400));
        // }
       
        require_once $wpss->root . "/includes/class-wpss-server-directives-apache.php";
        $sd = new WPSS_Server_Directives_Apache();
  
        $message ='';
        switch($request->get_method()){
            case 'GET':
                // TODO: call handle_htaccess_get_requests() 
                break;
            case 'POST':
                require_once $wpss->root . "/includes/wpss-htaccess-form.php";
                $data = $request->get_params();
                $form = $data["from"];
                $message=handle_htaccess_post_req($form);
                write_log("Message for htaccess post: ". $message, __FILE__);
                break;
            case 'DELETE':
                $is_debug_unprotected = $sd->unprotect_debug_log();
                $message = "Protection Removed: " . ($is_debug_unprotected ? "Yes" : "No");
                if (function_exists('write_log')) {
                }
                break;
            case 'PUT':  
                break;  
        }


        $response = array(
            'success' => true,
            'data' => array(
                "message" => $message
            )
        );

        return rest_ensure_response($response);
    } catch (Exception $e) {
        return new WP_Error('wpss_error', $e->getMessage(), array('status' => 500));
    }
}
