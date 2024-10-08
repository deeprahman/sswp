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

    public function __construct()
    {
        $this->name = __("WP Securing Setup", $this->domain);
        $this->root = WPSS_ROOT; 
        $this->domain = WPSS_DOMAIN;
        $this->root_url = WPSS_URL;
        $this->init();
    }

    public function init()
    {
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_js'] );
        $this->admin_pages();
    }
    public function enqueue_admin_js( $admin_page )
    {

        wp_enqueue_script('wp-api-request');
        include_once WPSS_ROOT . "/includes/enqueue-scripts/wpss-enqueue-admin-scripts.php";
    }

    public function enqueue_admin_css(){
        
    }

    public function admin_pages(){
        include_once WPSS_ROOT . "/admin/wpss-files-permissions-tools-page.php";
    }
}

