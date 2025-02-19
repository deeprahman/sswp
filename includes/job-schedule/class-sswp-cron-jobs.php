<?php
require_once $GLOBALS['sswp']::ROOT . "/includes/job-schedule/class-sswp-cron-jobs-old-logs.php";
class Class_Sswp_Cron_Job
{
    public function __construct(){
        $this->init();
    }

    public function init(){
        Class_Sswp_Cron_Jobs_Old_Logs::instance();
    }
    
}