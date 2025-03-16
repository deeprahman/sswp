<?php
/**
 * Contains default settings
 * for htaccess protect from
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$sswp_htaccess_from_settings['ht_form'] = array(
	array(
		'name'  => 'protect-debug-log',
		'value' => 'off',
	),
	array(
		'name'  => 'protect-update-directory',
		'value' => 'off',
	),
	array(
		'name'  => 'protect-xml-rpc',
		'value' => 'off',
	),
	array(
		'name'  => 'protect-rest-endpoint',
		'value' => 'off',
	),
	array(
		'name'  => 'allowed_files',
		'value' => array(),
	),
);

$sswp_htaccess_from_settings['file_types'] = array(
	'jpeg',
	'gif',
	'pdf',
	'doc',
	'mov',
	'png',
	'mkv',
	'txt',
	'xls',
	'webp',
);

$sswp_htaccess_from_settings['extension_map'] = array(
	'jpg'  => 'jpe?g',
	'jpeg' => 'jpe?g',
	'tif'  => 'tiff?',
	'tiff' => 'tiff?',
);

return $sswp_htaccess_from_settings;
