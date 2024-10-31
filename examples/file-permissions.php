<?php
require_once("/home/deep/wsl.deeprahman.lo/wp-load.php");
wp(); // For query
require_once ABSPATH . "wp-content/plugins/wp-securing-setup/wpss-logger.php";
//========================================================================================

require_once ABSPATH. "wp-admin/includes/misc.php";
//
require_once( ABSPATH .  "wp-content/plugins/wp-securing-setup/includes/interface-wpss-file-permission-manager.php");
require_once(ABSPATH . "wp-content/plugins/wp-securing-setup/includes/class-wpss-file-permission-manager.php");
//

$paths = ["wp-config.php", "wp-login.php", "wp-content", "wp-content/uploads", "wp-content/plugins", "wp-content/themes"];
//


try{
$pm = new WPSS_File_Permission_Manager($paths);

if (!function_exists('WP_Filesystem')) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    WP_Filesystem();
}
WP_Filesystem();
xdebug_break();
$wp_filesystem->chmod(ABSPATH . 'wp-config.php', 777);
$perms = $wp_filesystem->chmod(ABSPATH . 'wp-config.php');

echo "The permission of " ."wp-config.php". " is ". $perms  . "\n";

$pm->display_results();
}catch(Exception $e){
    printf($e->getMessage());
}

