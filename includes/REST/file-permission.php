<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once Sswp_Securing_Setup::ROOT . 'includes/sswp-file-permission.php';

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'sswp/v1',
			'/file-permissions',
			array(
				'methods'             => array( 'GET', 'PATCH', 'PUT', 'POST', 'DELETE' ),
				'callback'            => 'sswp_file_permissions_callback',
				'permission_callback' => 'sswp_file_permissions_permission_check',
				'args'                => array(
					'nonce' => array(
						'required' => true,
					),
				),
			)
		);
	}
);

function sswp_file_permissions_permission_check( $request ) {
	global $sswp;

	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	return true;
}

function sswp_file_permissions_callback( $request ) {
	global $sswp;
	$message = '';
	try {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			$message = __( 'WP_Filesystem() function is not defined', 'secure-setup' );

			// Log the error
			sswp_logger( 'DEBUG', 'Filesystem Function Not Found', __FUNCTION__ );

			// Create a WP_Error object
			$error = new WP_Error( 'dependency_error', $message );

			return $error;
		}

		switch ( $request->get_method() ) {
			case 'GET':
				$fs_permission = sswp_get_file_permissions();
				break;
			case 'POST':
				$message      .= sswp_do_recommended_permission();
				$fs_permission = sswp_get_file_permissions();
				break;
			case 'PUT':
				if ( 'revert' == ( $request->get_params() )['action'] ) {
					$message .= is_wp_error( $res = sswp_revert_to_original() ) ? $res->get_error_message() : $res;
				} else {
					$message = __( 'Action not found', 'secure-setup' );

					sswp_logger( 'Error', $message, __METHOD__ );
				}
				$fs_permission = sswp_get_file_permissions();
				break;
			case 'PATCH':
				break;
		}

		// Add your file permissions logic here
		$response = array(
			'success' => true,
			'data'    => array(
				'message' => $message,
				'fs_data' => isset( $fs_permission ) ? wp_json_encode( $fs_permission, JSON_NUMERIC_CHECK ) : null,
			),
		);
		return rest_ensure_response( $response );
	} catch ( \Throwable $ex ) {
		sswp_logger( 'Error', $ex->getMessage(), __FUNCTION__ );
		// $response = array(
		// 'success' => false,
		// 'data' => array(
		// 'message' => __('File permission  cannot be Changed!', 'secure-setup'),
		// ),
		// );
		// return rest_ensure_response($response);
		return new WP_Error( 'file_perm_error', __( 'File permission  cannot be Changed!', 'secure-setup' ), array( 'status' => 500 ) );
	}
}
