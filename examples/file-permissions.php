<?php
require_once("/home/deep/wsl.deeprahman.lo/wp-load.php");
wp(); // For query
require_once ABSPATH . "wp-content/plugins/wp-securing-setup/wpss-logger.php";
//========================================================================================

require_once ABSPATH. "wp-admin/includes/misc.php";
//
require_once( ABSPATH .  "wp-content/plugins/wp-securing-setup/includes/interface-wpss-file-permission-manager.php");
require_once(ABSPATH . "wp-content/plugins/wp-securing-setup/includes/class-wpss-file-permission-manager.php");
require_once(ABSPATH . "wp-content/plugins/wp-securing-setup/includes/traits/class-wpss-ownership-permission-trait.php");


$paths = [ "wp-config-sample.php","wp-config.php", "wp-login.php", "wp-content", "wp-content/uploads", "wp-content/plugins", "wp-content/themes"];
//


try{
    
    $pm = new WPSS_File_Permission_Manager($paths);

    $pm->display_results();
    $pm->change_to_recommended_permissions($paths);    
    $pm->display_results();

}catch(Exception $e){
    die($e->getMessage());
}

