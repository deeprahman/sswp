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

$paths = [ "wp-config-sample.php","wp-config.php", "wp-login.php", "wp-content", "wp-content/uploads", "wp-content/plugins", "wp-content/themes"];
//


try{
    $pm = new WPSS_File_Permission_Manager($paths);
    $file = ABSPATH . "wp-config-sample.php";
    echo "old param ". decoct(fileperms($file)) . " \n";
    $perms = '0755';
    echo "Perms to be set ".$perms."\n";

    //echo "Permission of the file ". $file. " is ". $pm->get_current_permission($file) . "\n";
    $pm->change_file_permission($file, $perms);

    $r = $pm->get_current_permission($file);

    echo "After Change:  Permission of the file ". $file. " is ". $r. "\n";

//    echo "After Change: Using - fileperms() -  Permission of the file ". $file. " is ". decoct(fileperms($file)). "\n";

    //$pm->display_results();

}catch(Exception $e){
    die($e->getMessage());
}

