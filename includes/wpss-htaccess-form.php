<?php

/**
 * Do .htaccess form related stuff
 */


 require_once($wpss->root . DIRECTORY_SEPARATOR . "includes/class-wpss-server-directives-apache.php");
 require_once($wpss->root . DIRECTORY_SEPARATOR . "includes/class-wpss-server-directives-factory.php");


$sd = WPSS_Server_directives_Factory::create_server_directives();



$allowed_functions = [
  "protect-debug-log" => "protect_debug_log",
  "allowed_files" => "protect_update_directory", // NOTE: make the file name consistent 
  "protect-xml-rpc" => "protect_xml_rpc",
  "protect-rest-endpoint" => "",
];

function handle_htaccess_post_req($data)
{
  global $htaccess_from_settings;
  $htaccess_from_settings = wpss_save_htaccess_option($data);
  // Walk through the  the $data array
  // For each item array, get the name-key
  // call the appropriate function from name-key with value key 
}


function wpss_save_htaccess_option($new = array())
{
  global $wpss;
  $cur = get_options([$wpss->settings]);
  $cur['_wpss_settings']['htaccess']['ht_form'] = $new;
  update_option($wpss->settings, $cur['_wpss_settings']);
  $new = get_options([$wpss->settings]);
  return $new[$wpss->settings]['htaccess'];

}
function protect_debug_log($d)
{
  if ($d === "on") {
    
  } else {

  }
}

function protect_update_directory($d)
{
  if (empty($d)) {
    // TODO: Call the method for protection
  } else {
    //  TODO: filter the files
    // TODO: Call the appropriate function with file data
  }
}

function protect_xml_rpc($d)
{
  if ($d === "on") {

  } else {

  }
}

function protect_rest_endpoint($d)
{
  if ($d === "on") {

  } else {

  }
}

/**
 *  filter out the unallowed files types
 * @param array $d  files extensions
 * @return array allowed files 
 */
function allowed_files($d):array
{
  global $htaccess_from_settings;
  $allowed = $htaccess_from_settings["file_types"];
  // The filtered files
  $files = array_filter($d["value"], function ($v) use ( $allowed ) {
    return (array_search($v, $allowed["file_types"]) !== false);
  });
  return $files;
}
