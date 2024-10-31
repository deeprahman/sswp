/**
 * Example usage of WPSS_File_Permission_Manager to change file permissions.
 * 
 * @param string $path The path to update permissions for
 * @return array Response array with status and data/error message
 */
function wpss_update_file_permissions($path, $permission) {
    try {
        // Initialize the permission manager
        $permission_manager = new WPSS_File_Permission_Manager();
        
        // First check if the path exists and is within WordPress
        $current_status = $permission_manager->get_file_permission(ABSPATH . $path);
        
        if ($current_status['exists'] === 'N/A') {
            return array(
                'success' => false,
                'message' => 'Path is outside WordPress installation',
                'data' => null
            );
        }
        
        if (!$current_status['exists']) {
            return array(
                'success' => false,
                'message' => 'File or directory does not exist',
                'data' => null
            );
        }

        // Change the permission
        $success = $permission_manager->set_permission(ABSPATH . $path, $permission);
        
        if (!$success) {
            return array(
                'success' => false,
                'message' => 'Failed to update permissions',
                'data' => null
            );
        }

        // Get updated permissions after change
        $updated_status = $permission_manager->check_permissions();

        return array(
            'success' => true,
            'message' => 'Permissions updated successfully',
            'data' => $updated_status
        );

    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => $e->getMessage(),
            'data' => null
        );
    }
}

/**
 * Example usage of WPSS_File_Permission_Manager to change to recommended permissions.
 * 
 * @param array $paths Array of paths to update to recommended permissions
 * @return array Response array with status and data/error message
 */
function wpss_apply_recommended_permissions($paths) {
    try {
        // Initialize the permission manager
        $permission_manager = new WPSS_File_Permission_Manager($paths);
        $results = array();
        $all_success = true;

        foreach ($paths as $path) {
            $full_path = ABSPATH . $path;
            
            // Check if path is valid
            if (!$permission_manager->is_within_wordpress($full_path)) {
                $results[$path] = array(
                    'success' => false,
                    'message' => 'Path is outside WordPress installation'
                );
                $all_success = false;
                continue;
            }

            // Apply recommended permissions
            $success = $permission_manager->change_to_recommended_permissions($full_path);
            
            if ($success) {
                $results[$path] = array(
                    'success' => true,
                    'message' => 'Updated to recommended permissions'
                );
            } else {
                $results[$path] = array(
                    'success' => false,
                    'message' => 'Failed to update permissions'
                );
                $all_success = false;
            }
        }

        // Get final permissions status
        $final_status = $permission_manager->check_permissions();

        return array(
            'success' => $all_success,
            'message' => $all_success ? 'All permissions updated successfully' : 'Some permissions failed to update',
            'results' => $results,
            'data' => $final_status
        );

    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => $e->getMessage(),
            'data' => null
        );
    }
}

/**
 * Example usage with custom recommended permissions
 */
function wpss_set_custom_recommended_permissions() {
    $permission_manager = new WPSS_File_Permission_Manager();
    
    // Set custom recommended permissions
    $permission_manager->set_recommended_permission('directory', '750');
    $permission_manager->set_recommended_permission('file', '640');
    
    // Now apply these custom recommendations
    $paths = array(
        'wp-content/uploads',
        'wp-content/plugins'
    );
    
    return wpss_apply_recommended_permissions($paths);
}

/**
 * Example usage for checking current permissions
 */
function wpss_check_current_permissions() {
    $permission_manager = new WPSS_File_Permission_Manager();
    
    // Get current permissions status
    $permissions = $permission_manager->check_permissions();
    
    return array(
        'success' => true,
        'message' => 'Current permissions retrieved',
        'data' => $permissions
    );
}

// Example REST API endpoint implementation
function wpss_handle_permission_update($request) {
    $params = $request->get_json_params();
    
    // Handle bulk permission updates
    if (is_array($params)) {
        $paths = array_keys($params);
        return wpss_apply_recommended_permissions($paths);
    }
    
    return new WP_Error(
        'invalid_request',
        'Invalid request format',
        array('status' => 400)
    );
}

