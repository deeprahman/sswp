<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// $sswp_file_permission = array
// (
// 'wp-config.php' =>
// array(
// 'exists' => true,
// 'permission' => '777',
// 'writable' => true,
// 'recommended' => '0444',
// ),
// 'wp-login.php' =>
// array(
// 'exists' => true,
// 'permission' => '777',
// 'writable' => true,
// 'recommended' => '0644',
// ),
// 'wp-content' =>
// array(
// 'exists' => true,
// 'permission' => '777',
// 'writable' => true,
// 'recommended' => '0755',
// ),
// 'wp-content/uploads' =>
// array(
// 'exists' => true,
// 'permission' => '777',
// 'writable' => true,
// 'recommended' => '0755',
// ),
// 'wp-content/plugins' =>
// array(
// 'exists' => true,
// 'permission' => '777',
// 'writable' => true,
// 'recommended' => '0755',
// ),
// 'wp-content/themes' =>
// array(
// 'exists' => true,
// 'permission' => '777',
// 'writable' => true,
// 'recommended' => '0755',
// ),
// );

require_once SSWP_ROOT . 'includes/class-sswp-file-permission-manager.php';
// TODO: Get file permissions
try {
	$sswp_check_res['rcmnd_perms'] = array(
		'directory'     => '0755',
		'file'          => '0644',
		'wp-config.php' => '0400',
	);

	$sswp_check_res['paths']       = array( '.' . DIRECTORY_SEPARATOR, 'wp-config.php', 'wp-content', 'wp-content' . DIRECTORY_SEPARATOR . 'uploads', 'wp-content' . DIRECTORY_SEPARATOR . 'plugins', 'wp-content' . DIRECTORY_SEPARATOR . 'themes' );
	$sswp_check_res['chk_results'] = ( new Sswp_File_Permission_Manager( $sswp_check_res['paths'] ) )->check_permissions();
	return $sswp_check_res;
} catch ( \Exception $ex ) {

	sswp_logger( 'Error', $ex->getMessage(), __FILE__ );
}
