<?php

class WP_Securing_Setup
{
    const ROOT = WPSS_ROOT;

    const DOMAIN = 'wp-securing-setup';

    const VERSION = "0.1.0";

    public $domain;

    public $root;

    public $version;

    public $root_url;

    public $name;

    public $js_handle;

    public $css_handle;

    public $nonce_action;

    public $settings;

    public function __construct()
    {
        $this->name = __("WP Securing Setup", $this->domain);
        $this->root = WPSS_ROOT;
        $this->domain = WPSS_DOMAIN;
        $this->root_url = WPSS_URL;
        $this->js_handle = "wpss-primary-js";
        $this->js_handle = "wpss-primary-css";
        $this->nonce_action = "wpss-rest";
        $this->settings = WPSS_SETTINGS;
        $this->init();
        $this->admin_rest();
    }

    public function init()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_js']);
        $this->admin_pages();
        $this->xml_rpc_config();
    }

    public function enqueue_admin_js($admin_page)
    {
        global $wpss;
        wp_enqueue_script('wp-api-request');
        include_once WPSS_ROOT . "/includes/enqueue-scripts/wpss-enqueue-admin-scripts.php";
    }

    public function enqueue_admin_css()
    {

    }

    public function admin_pages()
    {
        global $wpss;
        include_once WPSS_ROOT . "/admin/wpss-files-permissions-tools-page.php";
    }

    public function admin_rest()
    {
        include_once WPSS_ROOT . "/includes/REST/file-permission.php";
        include_once WPSS_ROOT . "/includes/REST/htaccess-protect.php";
    }

    public function xml_rpc_config(){
        require_once($this->root .DIRECTORY_SEPARATOR. "includes/wpss-xml-rpc.php");
    }

    public function get_extension_map(){
        return  (get_option($this->settings))["htaccess"]["extension_map"];
    }

    public function get_ht_form(){
        return  (get_option($this->settings))["htaccess"]["ht_form"];
    }

    public function get_file_types(){
        return  (get_option($this->settings))["htaccess"]["file_types"];
    }
}

