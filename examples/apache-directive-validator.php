<?php

require_once "/home/deep/wsl.deeprahman.lo/wp-load.php";
wp(); // For query
require_once ABSPATH . "wp-content/plugins/wp-securing-setup/sswp-logger.php";
//========================================================================================

require_once ABSPATH. "wp-admin/includes/misc.php";

require_once WP_PLUGIN_DIR  . "/wp-securing-setup/includes/class-sswp-apache-directives-validator.php";

$validator = new Sswp_Apache_Directives_Validator();



$singleDirectives = <<<EOD
RewriteEngine On
SetEnvIfNoCase Request_URI "^/wp-json/wp/v2/users" api_rate_limit_time_window=60
RewriteCond %{ENV:api_rate_limit_last_request} ^$ [OR]
RewriteCond %{TIME_EPOCH} > expr=%{ENV:api_rate_limit_last_request} + %{ENV:api_rate_limit_time_window}
RewriteRule ^ - [E=api_rate_limit_count:0,E=api_rate_limit_last_request:%{TIME_EPOCH},NS]
RewriteRule ^ - [E=api_rate_limit_count:%{ENV:api_rate_limit_count}+1,NS]
RewriteCond %{ENV:api_rate_limit_count} > 10
RewriteRule ^ - [R=429,L]
EOD;

 echo "Validating Single Directives:\n";
xdebug_break();
if ($validator->is_valid($singleDirectives)) {
    echo "All single directives are valid.\n";
} else {
    echo "Validation failed for single directives:\n";
    echo $validator->get_last_validation_message();
}
 echo "\n\n";
