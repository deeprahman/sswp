<?php

class WP_Securing_Setup {

	public const ROOT = WPSS_ROOT;

	public const DOMAIN = 'wp-securing-setup';

	public const URL = WPSS_URL;

	public const VERSION = '0.1.0';

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
	 * File-paths: for file permissions to be checked
	 *
	 * @var array
	 */
	public $file_paths = array();

	/**
	 * Recommended  permsiions for files
	 *
	 * @var array
	 */
	public $rcmnd_perms = array();

	/**
	 * @var WPSS_File_Permission_Manager
	 */
	public $fpm;

	public function __construct() {
		$this->name         = __( 'WP Securing Setup', $this->domain );
		$this->root         = self::ROOT;
		$this->domain       = self::DOMAIN;
		$this->root_url     = WPSS_URL;
		$this->js_handle    = 'wpss-primary-js';
		$this->css_handle   = 'wpss-primary-css';
		$this->nonce_action = 'wpss-rest';
		$this->settings     = WPSS_SETTINGS;

		// $this->file_paths = ["wp-config.php", "wp-login.php", "wp-content", "wp-content/uploads", "wp-content/plugins", "wp-content/themes"];
		$this->file_paths  = $this->get_file_paths();
		$this->rcmnd_perms = $this->get_rcmnd_perms();
		$this->set_fpm();
		$this->init();
	}

	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_js' ) );
		$this->admin_pages();
		$this->xml_rpc_config();
		$this->admin_rest();
	}

	public function enqueue_admin_js( $admin_page ) {
		global $wpss;
		wp_enqueue_script( 'wp-api-request' );
		include_once $this->root . '/includes/enqueue-scripts/wpss-enqueue-admin-scripts.php';
	}

	public function enqueue_admin_css() {
	}

	public function admin_pages() {
		global $wpss;
		include_once $this->root . '/admin/wpss-files-permissions-tools-page.php';
	}

	public function admin_rest() {
		include_once $this->root . '/includes/REST/file-permission.php';
		include_once $this->root . '/includes/REST/htaccess-protect.php';
	}

	public function xml_rpc_config() {
		require_once $this->root . DIRECTORY_SEPARATOR . 'includes/wpss-xml-rpc.php';
	}

	/**
	 * Set file permission manager
	 *
	 * @return $this
	 */
	public function set_fpm() {
		require_once $this->root . 'includes/class-wpss-file-permission-manager.php';
		$this->fpm = new WPSS_File_Permission_Manager( $this->file_paths, $this->rcmnd_perms );
		return $this;
	}
	public function get_fpm(): WPSS_File_Permission_Manager {
		return ( ! isset( $this->fpm ) || empty( $this->fpm ) ) ? $this->set_fpm()->fpm : $this->fpm;
	}

	public function get_extension_map() {
		return ( get_option( $this->settings ) )['htaccess']['extension_map'];
	}

	public function get_ht_form() {
		return ( get_option( $this->settings ) )['htaccess']['ht_form'];
	}

	public function get_file_types() {
		return ( get_option( $this->settings ) )['htaccess']['file_types'];
	}

	public function get_original_permission() {

		return ( get_option( $this->settings ) )['file_permission']['chk_results'];
	}

	public function get_file_paths() {

		return ( get_option( $this->settings ) )['file_permission']['paths'];
	}

	public function get_rcmnd_perms() {
		return ( get_option( $this->settings ) )['file_permission']['rcmnd_perms'];
	}
}
