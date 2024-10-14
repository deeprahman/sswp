<?php
/**
 * Contains default settings
 * for htaccess protect from
 */


 $htaccess_from_settings["ht_form"] = array(
   array(
       "name" => "protect-debug-log",
       "value" => "off"
   ),
   array(
       "name" => "protect-update-directory",
       "value" => "on"
   ),
   array(
       "name" => "protect-xml-rpc",
       "value" => "on"
   ),
   array(
       "name" => "protect-rest-endpoint",
       "value" => "off"
   ),
   array(
       "name" => "allowed_files",
       "value" => array(
           "jpeg",
           "gif"
       )
   )
);

$htaccess_from_settings["file_types"] = array(
   "jpeg",
   "gif",
   "pdf",
   "doc",
   "mov",
   "png",
   "mkv",
   "txt",
   "xls"
);

return $htaccess_from_settings;