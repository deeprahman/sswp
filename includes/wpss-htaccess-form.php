<?php

/**
 * Do .htaccess form related stuff
 */
$wpss_settings = [];

 $htaccess_from_settings["form_data"] = array(
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

$default_htaccess_from_settings["file_types"] = array(
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

 $allowed_functions = [
    "protect-debug-log" => "protect_debug_log",
    "protect-update-directory" => "",
    "protect-xml-rpc" => "",
    "protect-rest-endpoint" => "",
 ];
function wpss_save_htaccess_option($option = array()){
   global $default_htaccess_from_settings;
   $option = $option?:$default_htaccess_from_settings;
   
}
 function protect_debug_log($d){
   if($d === "on"){

   }else{

   }
 }

 function protect_update_directory($d){
   if($d === "on"){

   }else{
      
   }
 }

 function protect_xml_rpc($d){
   if($d === "on"){

   }else{
      
   }
 }

 function protect_rest_endpoint($d){
   if($d === "on"){

   }else{
      
   }
 }