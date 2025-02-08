
<?php

require_once __DIR__ . "/../bootstrap.php";
require_once ROOT . "/wp-securing-setup.php";
require_once ROOT . "/includes/wpss-file-permission.php";

$sswp_get_file_permissions = sswp_get_file_permissions();

echo "Get the file permissions"."\n";

print_r($sswp_get_file_permissions);


//echo "Setting recommnde permisssion...\n";
//$do_recommended_permission = do_recommended_permission();
//
//echo "Recommended permisssion set.\n";
//print_r($do_recommended_permission);
//
//
//$sswp_get_file_permissions = sswp_get_file_permissions();
//
//echo "File Permission after setting recommended.."."\n";
//
//print_r($sswp_get_file_permissions);


echo "Reverting back to original...\n";

$revert_to_original = revert_to_original();
echo "Reverted\n";
$sswp_get_file_permissions = sswp_get_file_permissions();

echo "File Permission after setting recommended.."."\n";

print_r($sswp_get_file_permissions);
