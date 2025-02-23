<?php

class Sswp_Cron_Jobs_Old_Logs
{
    private static $instance = null;
    public static function instance(){
        if ( is_null( self::$instance ) ){
            self::$instance = new Sswp_Cron_Jobs_Old_Logs();
        }
        return self::$instance;
    }
    public function __construct()
    {
        $this->init();
        if (!wp_next_scheduled('sswp_clear_old_logs')) {
            wp_schedule_event(time(), 'daily', 'sswp_clear_old_logs');
        }
    }

    public function init()
    {
        add_action('sswp_clear_old_logs', [$this, 'sswp_clear_old_logs_function']);
    }

    public function sswp_clear_old_logs_function()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sswp_logs';
        $sql = $wpdb->prepare(
            "DELETE FROM $table_name WHERE log_time < NOW() - INTERVAL %d DAY",
            30
        );
        $wpdb->query($sql);
    }
}