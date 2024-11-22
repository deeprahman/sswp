<?php

/**
 * Logger
 */
if ( ! function_exists( 'write_log' ) ) {
	function write_log( $log, $function = __FUNCTION__ ) {

		$formatted_log = '[' . date( 'Y-m-d H:i:s' ) . '] ' . ' Function: ' . $function . ' ';
		if ( is_array( $log ) || is_object( $log ) ) {
			$formatted_log .= print_r( $log, true );
		} else {
			$formatted_log .= $log;
		}

		$formatted_log .= PHP_EOL;

		if ( true === WP_DEBUG && defined( WPSS_ROOT ) ) {
			// $log_file = WP_CONTENT_DIR . '/wpss.log';
			$log_file = WPSS_ROOT . 'wpss.log';

			file_put_contents( $log_file, $formatted_log, FILE_APPEND );
		} else {
			error_log( $formatted_log );
		}
	}
}
