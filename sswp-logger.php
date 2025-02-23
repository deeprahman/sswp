<?php


if (!function_exists('sswp_logger')) {
    function sswp_logger($type, $log, $function)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sswp_logs';

        $formatted_log = array(
            'type' => $type,
            'function' => $function,
            'log' => (is_array($log) || is_object($log)) ? serialize($log) : $log
        );

        $wpdb->insert(
            $table_name,
            array(
                'log_time' => current_time('mysql'),
                'log_text' => serialize($formatted_log),
            )
        );
    }
}
