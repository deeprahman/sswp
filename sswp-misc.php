<?php

function sswp_convert_to_octal_pers_from_string(string $perms): string|null
{


    // Use regex to check if it conforms to '0xxx' format
    $reg_ex_oct = '/^0([1-7]{3})$/';
    $reg_ex_string = '/^([1-7]{3})$/';
    if (preg_match($reg_ex_string, $perms)) {

        $ret = "0" . $perms;
    } else if (preg_match($reg_ex_oct, $perms)) {

        $ret = $perms;
    } else {

        $ret = null;
    }

    return $ret;

}

/**
 * Get the client IP address reliably.
 *
 * @return string Client IP address or '0.0.0.0' if no valid IP found.
 */
function sswp_get_client_ip()
{
    $ip_keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            // Handle cases with multiple IPs (e.g., proxies).
            $ip_list = explode(',', $_SERVER[$key]);
            foreach ($ip_list as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
    }

    return '0.0.0.0'; // Fallback if no valid IP found.
}





function sswp_sanitize_rest_api($rest_api)
{
    if (!is_array($rest_api)) {
        return array();
    }

    if (isset($rest_api['rate_limit_endpoints'])) {
        $rest_api['rate_limit_endpoints'] = array_map('sanitize_text_field', $rest_api['rate_limit_endpoints']);
    }

    if (isset($rest_api['max_calls'])) {
        $rest_api['max_calls'] = absint($rest_api['max_calls']);
    }

    if (isset($rest_api['time_window_in_sec'])) {
        $rest_api['time_window_in_sec'] = absint($rest_api['time_window_in_sec']);
    }

    return $rest_api;
}

function sswp_sanitize_htaccess($htaccess)
{
    if (!is_array($htaccess)) {
        return array();
    }

    if (isset($htaccess['ht_form'])) {
        $htaccess['ht_form'] = sswp_sanitize_ht_form($htaccess['ht_form']);
    }

    if (isset($htaccess['file_types'])) {
        $htaccess['file_types'] = array_map('sanitize_text_field', $htaccess['file_types']);
    }

    if (isset($htaccess['extension_map'])) {
        $htaccess['extension_map'] = sswp_sanitize_extension_map($htaccess['extension_map']);
    }

    return $htaccess;
}

function sswp_sanitize_ht_form($ht_form)
{
    if (!is_array($ht_form)) {
        return array();
    }

    $sanitized_form = array();
    foreach ($ht_form as $item) {
        if (is_array($item)) {
            $sanitized_form[] = array(
                'name' => isset($item['name']) ? sanitize_text_field($item['name']) : '',
                'value' => isset($item['value']) ? sanitize_text_field($item['value']) : '',
            );
        }
    }

    return $sanitized_form;
}

function sswp_sanitize_extension_map($extension_map)
{
    if (!is_array($extension_map)) {
        return array();
    }

    $sanitized_map = array();
    foreach ($extension_map as $key => $value) {
        $sanitized_map[sanitize_text_field($key)] = sanitize_text_field($value);
    }

    return $sanitized_map;
}

function sswp_sanitize_file_permission($file_permission)
{
    if (!is_array($file_permission)) {
        return array();
    }

    if (isset($file_permission['rcmnd_perms'])) {
        $file_permission['rcmnd_perms'] = sswp_sanitize_rcmnd_perms($file_permission['rcmnd_perms']);
    }

    if (isset($file_permission['paths'])) {
        $file_permission['paths'] = array_map('sanitize_text_field', $file_permission['paths']);
    }

    if (isset($file_permission['chk_results'])) {
        $file_permission['chk_results'] = sswp_sanitize_chk_results($file_permission['chk_results']);
    }

    return $file_permission;
}

function sswp_sanitize_rcmnd_perms($rcmnd_perms)
{
    if (!is_array($rcmnd_perms)) {
        return array();
    }

    return array_map('sanitize_text_field', $rcmnd_perms);
}

function sswp_sanitize_chk_results($chk_results)
{
    if (!is_array($chk_results)) {
        return array();
    }

    $sanitized_results = array();
    foreach ($chk_results as $path => $data) {
        if (is_array($data)) {
            $sanitized_results[$path] = array(
                'exists' => isset($data['exists']) ? (bool) $data['exists'] : false,
                'permission' => isset($data['permission']) ? sanitize_text_field($data['permission']) : '',
                'writable' => isset($data['writable']) ? (bool) $data['writable'] : false,
                'recommended' => isset($data['recommended']) ? sanitize_text_field($data['recommended']) : '',
            );
        }
    }

    return $sanitized_results;
}

function sswp_sanitize_secure_setup_settings($value)
{
    if (!is_array($value)) {
        return array();
    }

    if (isset($value['file_permission'])) {
        $value['file_permission'] = sswp_sanitize_file_permission($value['file_permission']);
    }

    if (isset($value['htaccess'])) {
        $value['htaccess'] = sswp_sanitize_htaccess($value['htaccess']);
    }

    if (isset($value['rest_api'])) {
        $value['rest_api'] = sswp_sanitize_rest_api($value['rest_api']);
    }

    return $value;
}

function sswp_check_os_compatibility()
{

    // Check the OS
    if (PHP_OS_FAMILY !== 'Linux' && PHP_OS_FAMILY !== 'BSD' && PHP_OS_FAMILY !== 'Darwin') {
        // If not Unix-based system, show error message
        $mesg = __('The file-permission system works best with Unix based system', 'secure-setup');
        add_settings_error(
            'file_permission_messages', // Setting slug
            'file_permission_warning', // Error code
            $mesg, // Message text
            'warning' // Type of message ('error' or 'updated')
        );
    }
}
function sswp_deactivation_prompt()
{
    ob_start();
    ?>
    <div class="wrap">
        <h2><?php esc_html_e('Deactivate Plugin', 'secure-setup'); ?></h2>
        <form method="post">
            <p><?php esc_html_e('Do you want to delete the log table?', 'secure-setup'); ?></p>
            <input type="submit" name="sswp_delete_table" value="<?php esc_attr_e('Yes', 'secure-setup'); ?>" />
            <input type="submit" name="sswp_keep_table" value="<?php esc_attr_e('No', 'secure-setup'); ?>" />
        </form>
    </div>
    <?php
    $content = ob_get_clean();
    echo $content;

    if (isset($_POST['sswp_delete_table'])) {
        sswp_delete_log_table();
    }
}

function sswp_delete_log_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'sswp_logs';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}


