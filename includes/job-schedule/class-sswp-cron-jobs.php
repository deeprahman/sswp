<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once $GLOBALS['sswp']::ROOT . '/includes/job-schedule/class-sswp-cron-jobs-old-logs.php';
class Sswp_Cron_Job {

	public function __construct() {
		$this->init();
	}

	public function init() {
		Sswp_Cron_Jobs_Old_Logs::instance();
	}
}
