<?php
/**
 * Do .htaccess form related stuff
 */
require_once($wpss->root . DIRECTORY_SEPARATOR . "includes/class-wpss-server-directives-apache.php");
require_once($wpss->root . DIRECTORY_SEPARATOR . "includes/class-wpss-server-directives-factory.php");

try {
  $sd = WPSS_Server_directives_Factory::create_server_directives();
  write_log(["Instantiated the WPSS_Server_Directives ", $sd]);
} catch (Exception $ex) {
  write_log($ex->getMessage(), __FILE__);
}

$allowed_functions = [
  "protect-debug-log" => "protect_debug_log",
  "allowed_files" => "protect_update_directory", // NOTE: make the file name consistent 

  "protect-rest-endpoint" => "protect_rest_endpoint",
];

/**
 * Handles the post
 * @param array $data  the htaccess form settings values; example: $data = array(
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
 * @return void
 */
function handle_htaccess_post_req($data)
{
  global $sd;

  $htaccess_from_settings = wpss_save_htaccess_option($data);

  // Walk through the $data array
  foreach ($htaccess_from_settings as $item) {
    $name = $item['name'];
    $value = $item['value'];

    // Check if the name exists in the allowed_functions array
    if (array_key_exists($name, $GLOBALS['allowed_functions'])) {
      $function_name = $GLOBALS['allowed_functions'][$name];

      // Call the appropriate function if it exists
      if (!empty($function_name) && function_exists($function_name)) {
        $function_name($value, $sd);
      } else {
        // Handle the case where the function doesn't exist
        write_log("Function not found for: " . $name, __FILE__);
      }
    } else {
      // Handle the case where the name is not in allowed_functions
      write_log("Unrecognized setting: " . $name, __FILE__);
    }
  }
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
function protect_debug_log($d, IWPSS_Server_Directives $sd)
{
  if ($d === "on") {
    $sd->unprotect_debug_log();
  } else {
    $sd->protect_debug_log();
  }
}

function protect_update_directory($d, IWPSS_Server_Directives $sd)
{
  $files = allowed_files($d);
  if (empty($files)) {
    $sd->disallow_file_access();
  } else {

    $sd->allow_file_access($files);
  }
}



function protect_rest_endpoint($d, IWPSS_Server_Directives $sd)
{
  if ($d === "on") {
    $sd->unprotect_user_rest_apt();
  } else {
    $sd->protect_user_rest_apt();
  }
}

/**
 *  filter out the unallowed files types
 * @param array $d  files extensions
 * @return array allowed files 
 */
function allowed_files($d): array
{
  global $htaccess_from_settings;
  if (empty($d["value"])) {
    return [];
  }

  $allowed = $htaccess_from_settings["file_types"];
  // The filtered files
  $files = array_filter($d["value"], function ($v) use ($allowed) {
    return (array_search($v, $allowed["file_types"]) !== false);
  });
  return $files;
}
