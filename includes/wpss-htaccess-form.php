<?php

/**
 * Do .htaccess form related stuff
 */



$allowed_functions = [
    "protect-debug-log" => "protect_debug_log",
    "protect-update-directory" => "",
    "protect-xml-rpc" => "",
    "protect-rest-endpoint" => "",
 ];

 function handle_htaccess_post_req($data){
 
    $data = (object) wpss_save_htaccess_option($data);
 }
function wpss_save_htaccess_option($new = array()){
    global $wpss;
    $cur = get_options([$wpss->settings]);
    $cur['_wpss_settings']['htaccess']['ht_form'] = $new;
    update_option($wpss->settings,$cur['_wpss_settings']);
    $new = get_options([$wpss->settings]);
    return $new[$wpss->settings]['htaccess'];
 
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

function allowed_files($d){
    global $htaccess_from_settings;

    // The filtered files
    $files = array_filter($d["value"], function($v) use($htaccess_from_settings) {
        return (array_search($v, $htaccess_from_settings["file_types"]) !== false);
});


}
