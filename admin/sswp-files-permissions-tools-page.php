<?php
/**
 * Adds a new page to the WordPress Tools admin menu.
 *
 * This function creates a new page under the Tools menu and calls the `file_permission_page_html` function to render its content.
 */
function sswp_files_permission_page() {
	// Add the page to the Tools menu
	add_management_page(
		__( 'Files Permission', 'secure-setup' ), // Page title
		__( 'Files Permission', 'secure-setup' ), // Menu title
		'manage_options', // Required capability
		'sswp-files-permission', // Page slug
		'sswp_file_permission_page_html' // Callback function
	);
}

// Register the `wpss_files_permission_page` function to be executed when the `admin_menu` action is triggered
add_action( 'admin_menu', 'sswp_files_permission_page' );

/**
 * Renders the HTML content for the Files Permission page.
 *
 * This function is called by the `wpss_files_permission_page` function to display the page's content.
 */
function sswp_file_permission_page_html() {

	global $wpss;


	include_once SSWP_ROOT . '/admin/templates/page-htm.php';


}
