<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action(
	'rest_api_init',
	function () {

		register_rest_route(
			'sswp/v1',
			'/htaccess-protect',
			array(
				'methods'             => array( 'GET', 'DELETE', 'POST', 'PATCH', 'PUT' ),
				'callback'            => 'sswp_htaccess_protect_callback',
				'permission_callback' => 'sswp_htaccess_protect_permission_check',
				'args'                => array(
					'nonce' => array(
						'required' => true,

					),
				),
			)
		);
	}
);

function sswp_htaccess_protect_permission_check( $request ) {
	return current_user_can( 'manage_options' );
}

function sswp_htaccess_protect_callback( $request ) {
	global $sswp;

	try {
		
		require_once $sswp->root . '/includes/sswp-htaccess-form.php';
		require_once $sswp->root . '/includes/class-sswp-server-directives-apache.php';
		$sd = new Sswp_Server_Directives_Apache();

		$message = '';
		switch ( $request->get_method() ) {
			case 'GET':
				$message = sswp_handle_htaccess_get_req();
				break;
			case 'POST':
				$data    = $request->get_params();
				$form    = $data['from'];
				$message = sswp_handle_htaccess_post_req( $form );
				sswp_logger( 'Info', 'Message for htaccess post: ' . $message, __FILE__ );
				break;
			case 'DELETE':
				break;
			case 'PUT':
				$data    = $request->get_params();
				$form    = $data['from'];
				$message = sswp_handle_htaccess_post_req( $form );
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
	} catch ( \Throwable $ex ) {
		sswp_logger( 'Error', $ex->getMessage(), __FUNCTION__ );
		return new WP_Error( 'sswp_htaccess_error', __( 'Directory/File protection setting cannot be set!', 'secure-setup' ), array( 'status' => 500 ) );
	}
}
