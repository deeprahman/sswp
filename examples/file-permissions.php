<?php
require_once("C:\\xampp\\htdocs\\wp\\wp-load.php");
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

$pm = new WPSS_File_Permission_Manager($paths);

//$ret = $pm->apply_recommended_permissions($paths);

$pm->set_permission('wp-config.php', '777');

//print_r($ret);
$pm->display_results();
