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
    /**
     * file-paths: for file permissions to be checked
     * @var array
     */
    public $file_paths;

    /**
     * @var WPSS_File_Permission_Manager
     */
    public $fpm;

    public function __construct()
    {
        $this->name = __("WP Securing Setup", $this->domain);
        $this->root = WPSS_ROOT;
        $this->domain = WPSS_DOMAIN;
        $this->root_url = WPSS_URL;
        $this->js_handle = "wpss-primary-js";
        $this->css_handle = "wpss-primary-css";
        $this->nonce_action = "wpss-rest";
        $this->settings = WPSS_SETTINGS;

        $this->file_paths = ["wp-config.php", "wp-login.php", "wp-content", "wp-content/uploads", "wp-content/plugins", "wp-content/themes"];
        $this->set_fpm();
        $this->init();
    }

    public function init()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_js']);
        $this->admin_pages();
        $this->xml_rpc_config();
        $this->admin_rest();
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

    /**
     * Set file permission manager
     *
     * @return $this
     */
    public function set_fpm(){
        require_once $this->root . "includes/class-wpss-file-permission-manager.php";
        $this->fpm = new WPSS_File_Permission_Manager();
        return $this;
    }
    public function get_fpm(): WPSS_File_Permission_Manager{
        return empty($this->fpm) ? $this->set_fpm()->fpm : $this->fpm;
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

    public function get_original_permission(){

        return  (get_option($this->settings))["file_permission"]["chk_results"];
    }

    public function get_file_paths(){

        return  (get_option($this->settings))["file_permission"]["paths"];
    }
}

