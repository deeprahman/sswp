<?php
function wpss_enqueue_primary_admin_script( $admin_page ) {
    if ( 'settings_page_unadorned-announcement-bar' !== $admin_page ) {
        return;
    }

    $asset_file = plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

    if ( ! file_exists( $asset_file ) ) {
        return;
    }

    $asset = include $asset_file;

    wp_enqueue_script(
        'wpss-primary-js',
        plugins_url( 'build/index.js', __FILE__ ),
        $asset['dependencies'],
        $asset['version'],
        array(
            'in_footer' => true,
        )
    );
}

add_action( 'admin_enqueue_scripts', 'wpss_enqueue_primary_admin_script' );
