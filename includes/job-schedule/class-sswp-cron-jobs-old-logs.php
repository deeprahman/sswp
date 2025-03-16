<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Sswp_Cron_Jobs_Old_Logs {

	private static $instance = null;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Sswp_Cron_Jobs_Old_Logs();
		}
		return self::$instance;
	}
	public function __construct() {
		if ( ! wp_next_scheduled( 'sswp_clear_old_logs' ) ) {
			wp_schedule_event( time(), 'daily', 'sswp_clear_old_logs' );
		}
		$this->init();
	}

	public function init() {
		add_action( 'sswp_clear_old_logs', array( $this, 'sswp_clear_old_logs_function' ) );
	}

	public function sswp_clear_old_logs_function() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'sswp_logs';

		$sql = $wpdb->prepare(
			'DELETE FROM %s WHERE log_time < NOW() - INTERVAL %d DAY',
			$table_name,
			30
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared -- Custom table operation requires direct query; caching not applicable; query is safely prepared.
		$wpdb->query( $sql );
	}
}
