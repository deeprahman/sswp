<?php

class WP_Securing_Setup
{
    const ROOT = WPSS_ROOT;
    const DOMAIN = 'wp-securing-setup';
    const VERSION = "0.1.0";

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $this->enqueue_js();
        $this->enqueue_css();
        $this->admin_pages();
    }
    private function enqueue_js()
    {
        include_once WPSS_ROOT . "/includes/enqueue-scripts/wpss-enqueue-admin-scripts.php";
    }
    private function enqueue_css()
    {
    }

    private function admin_pages(){
        include_once WPSS_ROOT . "/admin/wpss-files-permissions-tools-page.php";
    }
}

