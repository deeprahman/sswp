<?php
// $admin_page passed from included page
if ( "tools_page_wpss-files-permission" !== $admin_page ) {
    return;
    }

    $asset_file = WPSS_ROOT . 'build/index.asset.php';

    if ( ! file_exists( $asset_file ) ) {
        new WP_Error("Asset File does not exists");
    }
    $index_js =  WPSS_URL .'build/index.js';
    $index_css =  WPSS_URL .'build/index.css';

    if ( ! file_exists( $index_js ) ) {
        new WP_Error("Index File does not exists");
    }

    $asset = include $asset_file;

    wp_enqueue_script(
        'wpss-primary-js',
        $index_js,
        $asset['dependencies'],
        $asset['version'],
        array(
            'in_footer' => true,
        )
    );

    wp_enqueue_style(
        'wpss-primary-css',
        $index_css,
        $asset['dependencies'],
        $asset['version']
    );
    wp_enqueue_style( 'wp-components' );

    //== For jQuery and related


    function enqueue_jquery_scripts() {
        // Enqueue jQuery (already included with WordPress)
        wp_enqueue_script('jquery');

        // Enqueue jQuery UI Core
        wp_enqueue_script('jquery-ui-core');

        // Enqueue jQuery UI Tabs
        wp_enqueue_script('jquery-ui-tabs');

        // Optionally, enqueue the jQuery UI CSS if necessary
        wp_enqueue_style('jquery-ui-theme', 'https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css');
  }


enqueue_jquery_scripts();
wp_localize_script('custom-tabs-script', 'wpApiSettings', array(
    'root' => esc_url_raw(rest_url()),
    'nonce' => wp_create_nonce('wp_rest')
    ));

