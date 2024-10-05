<?php
/**
 * Adds a new page to the WordPress Tools admin menu.
 *
 * This function creates a new page under the Tools menu and calls the `file_permission_page_html` function to render its content.
 */
function wpss_files_permission_page() {
    // Add the page to the Tools menu
    add_management_page(
        __( 'Files Permission', 'wpss' ), // Page title
        __( 'Files Permission', 'wpss' ), // Menu title
        'manage_options', // Required capability
        'wpss-files-permission', // Page slug
        'file_permission_page_html' // Callback function
    );
}

// Register the `wpss_files_permission_page` function to be executed when the `admin_menu` action is triggered
add_action( 'admin_menu', 'wpss_files_permission_page' );

/**
 * Renders the HTML content for the Files Permission page.
 *
 * This function is called by the `wpss_files_permission_page` function to display the page's content.
 */
function file_permission_page_html() {
    // Print the HTML content for the page
    printf(
        '<div class="wrap" id="wpss-files-permissions-settings">%s</div>',
        esc_html__( 'Loading…', 'Files Permission' )
    );
}
