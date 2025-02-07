<?php

add_action(
	'rest_api_init',
	function () {

		register_rest_route(
			'wpss/v1',
			'/htaccess-protect',
			array(
				'methods'             => array( 'GET', 'DELETE', 'POST', 'PATCH', 'PUT' ),
				'callback'            => 'wpss_htaccess_protect_callback',
				'permission_callback' => 'wpss_htaccess_protect_permission_check',
				'args'                => array(
					'nonce' => array(
						'required' => true,

					),
				),
			)
		);
	}
);

function wpss_htaccess_protect_permission_check( $request ) {
	return current_user_can( 'manage_options' );
}

function wpss_htaccess_protect_callback( $request ) {
	global $wpss, $allowed_methods;

	try {
		// if( !array_search($request->method, $allowed_methods,  $strict = false) === true ){
		// return new WP_Error('wpss_error', "Method Disallowed", array('status' => 400));
		// }
		require_once $wpss->root . '/includes/wpss-htaccess-form.php';
		require_once $wpss->root . '/includes/class-sswp-server-directives-apache.php';
		$sd = new Sswp_Server_Directives_Apache();

		$message = '';
		switch ( $request->get_method() ) {
			case 'GET':
				$message = handle_htaccess_get_req();
				break;
			case 'POST':
				$data    = $request->get_params();
				$form    = $data['from'];
				$message = handle_htaccess_post_req( $form );
				sswp_logger( 'Info', 'Message for htaccess post: ' . $message, __FILE__ );
				break;
			case 'DELETE':
				break;
			case 'PUT':
				$data    = $request->get_params();
				$form    = $data['from'];
				$message = handle_htaccess_post_req( $form );
				sswp_logger( 'Info', 'Message for htaccess post: ' . $message, __FILE__ );
				break;
		}

		$response = array(
			'success' => true,
			'data'    => array(
				'message' => $message,
			),
		);

		return rest_ensure_response( $response );
	} catch ( Exception $e ) {
		return new WP_Error( 'wpss_error', $e->getMessage(), array( 'status' => 500 ) );
	}
}
