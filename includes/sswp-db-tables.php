<?php

function sswp_create_tables(){
    // Add single table creation functions here
    sswp_create_log_table();
}

function sswp_create_log_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sswp_logs';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        log_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        log_text text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}