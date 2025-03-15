<?php

$asset_file = SSWP_ROOT . 'build/admin.asset.php';

if ( ! file_exists( $asset_file ) ) {
	new WP_Error( 'Asset File does not exists' );
}
$index_js  = SSWP_URL . 'build/admin.js';
$index_css = SSWP_URL . 'build/admin.css';

if ( ! file_exists( SSWP_ROOT . 'build/admin.js' ) ) {
	new WP_Error( 'JS File does not exists' );
	sswp_logger( 'Info', 'JS File does not exists', __FILE__ );
}

if ( ! file_exists( SSWP_ROOT . 'build/admin.css' ) ) {
	new WP_Error( 'CSS File does not exists' );

	sswp_logger( 'Info', 'CSS File does not exists', __FILE__ );
}

$asset = include $asset_file;

wp_enqueue_script(
	"sswp-admin-script",
	$index_js,
	$asset['dependencies'],
	$asset['version'],
	array(
		'in_footer' => true,
	)
);

wp_enqueue_style(
	"sswp-admin-style",
	$index_css,
	array(),
	$asset['version']
);


// == For jQuery and related

wp_enqueue_script( 'jquery' );

// Enqueue jQuery UI Core
wp_enqueue_script( 'jquery-ui-core' );


wp_enqueue_script('jquery-ui-dialog');


