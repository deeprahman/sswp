<?php 

function wpss_convert_to_octal_pers_from_string(string $perms):string|null{
	    

    // Use regex to check if it conforms to '0xxx' format
    $reg_ex_oct = '/^0([1-7]{3})$/';
    $reg_ex_string = '/^([1-7]{3})$/';
    if(preg_match($reg_ex_string, $perms)){
    	
         $ret = "0" . $perms; 
    } else if (preg_match($reg_ex_oct, $perms)){
    	
        $ret = $perms;        
    }else{
    	
        $ret = null;
    }

    return $ret;

}
