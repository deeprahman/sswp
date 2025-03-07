<?php


if ( ! function_exists( 'sswp_logger' ) ) {

	function sswp_logger( $type, $log, $function ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'sswp_logs';

		$formatted_log = array(
			'type'     => sanitize_text_field( $type ),
			'function' => sanitize_text_field( $function ),
			'log'      => ( is_array( $log ) || is_object( $log ) ) ? serialize( $log ) : sanitize_text_field( $log ),
		);

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table requires direct query.
		$result = $wpdb->insert(
			$table_name,
			array(
				'log_time' => current_time( 'mysql', true ),
				'log_text' => serialize( $formatted_log ),
			)
		);

		return $result !== false; // Optional: return success/failure
	}
}
