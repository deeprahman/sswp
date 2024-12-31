<?php 

function wpss_convert_to_octal_pers_from_string(string $perms):string|null
{
        

    // Use regex to check if it conforms to '0xxx' format
    $reg_ex_oct = '/^0([1-7]{3})$/';
    $reg_ex_string = '/^([1-7]{3})$/';
    if(preg_match($reg_ex_string, $perms)) {
        
         $ret = "0" . $perms; 
    } else if (preg_match($reg_ex_oct, $perms)) {
        
        $ret = $perms;        
    }else{
        
        $ret = null;
    }

    return $ret;

}

/**
 * Get the client IP address reliably.
 *
 * @return string Client IP address or '0.0.0.0' if no valid IP found.
 */
function wpss_get_client_ip()
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
