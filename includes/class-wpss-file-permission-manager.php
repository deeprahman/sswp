<?php
require_once $wpss->root . DIRECTORY_SEPARATOR . "includes/interface-wpss-file-permission-manager.php";

class WPSS_File_Permission_Manager implements IWPSS_File_Permission_manager
{
    /**
     * List of files and directories to check permissions for, relative to WordPress root.
     * Default includes critical files like wp-config.php and upload directories.
     * 
     * @var array
     */
    private $files_to_check;

    /**
     * Standard recommended permissions for files and directories.
     * - directory: '755' (owner: rwx, group: rx, others: rx)
     * - file: '644' (owner: rw, group: r, others: r)
     * 
     * @var array
     */
    private $recommended_permissions;

    /**
     * Constructor initializes the file permission checker with optional custom paths.
     * If no paths provided, uses default critical WordPress paths.
     *
     * @param array $files_to_check Optional array of file/directory paths to check
     */
    public function __construct($files_to_check = [])
    {

        global $wp_filesystem;

        // Include WordPress filesystem functions if not already loaded
        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            // Initialize WordPress filesystem
            WP_Filesystem();


        }

        $this->files_to_check = !empty($files_to_check) ? $files_to_check : [
            'wp-config.php',
            'wp-content',
            'wp-content/uploads'
        ];
        $this->recommended_permissions = [
            'directory' => '755',
            'file' => '644',
            'wp-config.php' => '444'
        ];
    }

    /**
     * Validates if a given path is within the WordPress installation directory.
     * Uses realpath() to resolve any symbolic links and normalize the path.
     * Prevents unauthorized access to files outside WordPress root.
     *
     * @param string $path Absolute path to check
     * @return bool True if path is within WordPress directory, false otherwise
     */
    private function is_within_wordpress($path)
    {
        $real_path = realpath($path);
        $wp_path = realpath(ABSPATH);
        return strpos($real_path, $wp_path) === 0;
    }

    /**
     * Checks permissions for all configured files and directories.
     * For each path, collects:
     * - Existence status
     * - Current permissions
     * - Writability status
     * - Recommended permissions
     * - Any errors encountered
     *
     * @return array Associative array of permission check results for each path
     */
    public function check_permissions()
    {
        $results = [];

        foreach ($this->files_to_check as $file) {
            $path = ABSPATH . $file;
            if ($this->is_within_wordpress($path)) {
                $results[$file] = $this->get_file_permission($path);
            } else {
                $results[$file] = [
                    'exists' => 'N/A',
                    'permission' => 'N/A',
                    'writable' => 'N/A',
                    'recommended' => 'N/A',
                    'error' => __('Path not found inside WordPress')
                ];
            }
        }

        return $results;
    }

    /**
     * Gets detailed permission information for a specific file or directory.
     * Uses WordPress Filesystem API to safely check file properties.
     * Initializes filesystem if not already done.
     *
     * @param string $path Absolute path to check
     * @return array Permission details including:
     *               - exists: bool
     *               - permission: string|null
     *               - writable: bool
     *               - recommended: string
     */
    private function get_file_permission($path)
    {
        global $wp_filesystem;

        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            WP_Filesystem();
        }


        if (!$wp_filesystem->exists($path)) {
            return [
                'exists' => false,
                'permission' => null,
                'writable' => false,
                'recommended' => $this->get_recommended_permission($path)
            ];
        }

        $perms = $wp_filesystem->getchmod($path);
        $writable = $wp_filesystem->is_writable($path);

        return [
            'exists' => true,
            'permission' => $perms,
            'writable' => $writable,
            'recommended' => $this->get_recommended_permission($path)
        ];
    }

    /**
     * Determines recommended permissions based on whether path is file or directory.
     * Uses WordPress Filesystem API to check path type.
     * Special handling for wp-config.php which should be more restrictive.
     *
     * @param string $path Path to get recommended permissions for
     * @return string Recommended permission string ('755' for directories, '644' for files, '444' for wp-config.php)
     */
    public function get_recommended_permission($path)
    {
        global $wp_filesystem;
        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            WP_Filesystem();
        }


        // Check if the path contains wp-config.php
        if (strpos($path, 'wp-config.php') !== false) {
            return $this->recommended_permissions['wp-config.php'];
        }

        if ($wp_filesystem->is_dir($path)) {
            return $this->recommended_permissions['directory'];
        } else {
            return $this->recommended_permissions['file'];
        }
    }

    /**
     * Displays permission check results in a formatted table.
     * Table columns:
     * - File/Directory path
     * - Existence status
     * - Current permissions
     * - Writability status
     * - Recommended permissions
     * - Any error messages
     */
    public function display_results()
    {
        $results = $this->check_permissions();

        $widths = [
            'file' => 30,
            'exists' => 10,
            'permission' => 15,
            'writable' => 10,
            'recommended' => 15,
            'error' => 40
        ];

        $this->print_row("File/Directory", "Exists", "Permission", "Writable", "Recommended", "Error", $widths);
        $this->print_separator($widths);

        foreach ($results as $file => $info) {
            $this->print_row(
                $file,
                isset($info['error']) ? 'N/A' : ($info['exists'] ? 'Yes' : 'No'),
                isset($info['error']) ? 'N/A' : ($info['exists'] ? $info['permission'] : 'N/A'),
                isset($info['error']) ? 'N/A' : ($info['exists'] ? ($info['writable'] ? 'Yes' : 'No') : 'N/A'),
                isset($info['error']) ? 'N/A' : $info['recommended'],
                isset($info['error']) ? $info['error'] : '',
                $widths
            );
        }
    }

    /**
     * Helper function to print a formatted table row with specified column widths.
     * Truncates values that exceed column width to maintain table formatting.
     *
     * @param string $file File/directory path
     * @param string $exists Existence status
     * @param string $permission Current permissions
     * @param string $writable Writability status
     * @param string $recommended Recommended permissions
     * @param string $error Error message if any
     * @param array $widths Column widths for formatting
     */
    private function print_row($file, $exists, $permission, $writable, $recommended, $error, $widths)
    {
        printf(
            "%-{$widths['file']}s %-{$widths['exists']}s %-{$widths['permission']}s %-{$widths['writable']}s %-{$widths['recommended']}s %-{$widths['error']}s\n",
            substr($file, 0, $widths['file']),
            substr($exists, 0, $widths['exists']),
            substr($permission, 0, $widths['permission']),
            substr($writable, 0, $widths['writable']),
            substr($recommended, 0, $widths['recommended']),
            substr($error, 0, $widths['error'])
        );
    }

    /**
     * Prints a separator line for the results table.
     * Calculates total width based on column widths plus spacing.
     *
     * @param array $widths Column widths array
     */
    private function print_separator($widths)
    {
        $total_width = array_sum($widths) + count($widths) - 1;
        echo str_repeat('-', $total_width) . "\n";
    }

    /**
     * Changes permissions for a specific file or directory.
     * Validates path is within WordPress installation and exists.
     * Uses WordPress Filesystem API to modify permissions.
     *
     * @param string $path Target file/directory path
     * @param string $permission Permission string in octal format (e.g., '644', '755')
     * @return bool True if permissions were changed successfully
     */
    public function change_file_permission($path, $permission)
    {
        global $wp_filesystem;

        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            WP_Filesystem();
        }



        if (!$this->is_within_wordpress($path)) {
            return false;
        }

        if (!$wp_filesystem->exists($path)) {
            return false;
        }

        return $wp_filesystem->chmod($path, $this->string_to_int($permission));
    }

    /**
     * Recursively changes permissions for a directory and its contents.
     * Sets recommended permissions:
     * - Directories: typically '755'
     * - Files: typically '644'
     * - Skip: wp-content directory
* @param string $path Directory path to process
    * @return bool True if all permissions were changed successfully
     */
    public function recursively_change_to_recommended_permissions($path)
    {
        // Get access to WordPress filesystem functionality
        global $wp_filesystem;

        if (strpos($path, 'wp-content') !== false) {
            write_log("Message: wp-content dir", __FUNCTION__);
            return true;
        }

        // Include WordPress filesystem functions if not already loaded
        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            // Initialize WordPress filesystem
            WP_Filesystem();
            write_log("Message: WP_Filesystem instantiated.", __FUNCTION__);
        }


        // Check if path is within WordPress installation directory for security
        if (!$this->is_within_wordpress($path)) {
            write_log("Message: Path is Outside of WP " . $path . ".", __FUNCTION__);
            return false;
        }

        // If path is not a directory, change its permissions and return
        if (!$wp_filesystem->is_dir($path)) {
            write_log("Message: Path is file " . $path . ".", __FUNCTION__);
            return $this->change_file_permission($path, $this->get_recommended_permission($path));
        }

        // Initialize success flag
        $success = true;

        // Change the directory's own permissions to 755 (recommended for directories)
        $success &= $this->change_file_permission($path, $this->recommended_permissions['directory']);

        // Get list of all files and directories within this directory
        $files = $wp_filesystem->dirlist($path, true);

        // Loop through each item in the directory
        foreach ($files as $file => $file_info) {
            $file_path = trailingslashit($path) . $file;
            if ($file_info['type'] == 'd') {
                // If item is a directory, recursively process it
                $success &= $this->recursively_change_to_recommended_permissions($file_path);
            } else {
                // If item is a file, change its permissions to 644 (recommended for files)
                $success &= $this->change_file_permission($file_path, $this->recommended_permissions['file']);
            }
        }

        return $success;
    }

    /**
     * Converts octal permission string to decimal number.
     * Required for WordPress filesystem chmod operation.
     *
     * @param string $octal Permission string in octal format (e.g., '644')
     * @return int Decimal representation of permission
     */
    private function string_to_int($str)
    {
        return intval($str);
    }

    /**
     * Gets current permission string for a path.
     * Returns null if file doesn't exist.
     *
     * @param string $path Path to check
     * @return string|null Current permission string or null if path doesn't exist
     */
    public function get_current_permission($path)
    {
        $info = $this->get_file_permission($path);
        return $info['exists'] ? $info['permission'] : null;
    }

    /**
     * Updates recommended permissions for either files or directories.
     * Validates:
     * - Type is either 'directory' or 'file'
     * - Permission is valid octal string (e.g., '644', '755')
     *
     * @param string $type Path type ('directory' or 'file')
     * @param string $permission New recommended permission
     * @return bool True if recommended permission was updated successfully
     */
    public function set_recommended_permission($type, $permission)
    {
        if (!in_array($type, ['directory', 'file'])) {
            return false;
        }

        if (!preg_match('/^[0-7]{3}$/', $permission)) {
            return false;
        }

        $this->recommended_permissions[$type] = $permission;
        return true;
    }

    /**
     * Sets permissions for a specific path.
     * Validates path exists and is within WordPress installation.
     * Uses WordPress Filesystem API to modify permissions.
     *
     * @param string $path Target path
     * @param string $permission Permission string in octal format (e.g., '644', '755')
     * @return bool True if permission was set successfully
     */
    public function set_permission($path, $permission)
    {
        global $wp_filesystem;
        if (!$this->is_within_wordpress($path)) {
            return false;
        }
        WP_Filesystem();
        if (!$wp_filesystem->exists($path)) {
            return false;
        }

        return $wp_filesystem->chmod($path, $this->string_to_int($permission));
    }

    /**
     * Example usage of WPSS_File_Permission_Manager to change to recommended permissions.
     * 
     * @param array $paths Array of paths to update to recommended permissions
     * @return array Response array with status and data/error message
     */
    function apply_recommended_permissions($paths)
    {
        try {
  
            $results = array();
            $all_success = true;

            foreach ($paths as $path) {
                $full_path = ABSPATH . $path;

                // Check if path is valid
                if (!$this->is_within_wordpress($full_path)) {
                    $results[$path] = array(
                        'success' => false,
                        'message' => 'Path is outside WordPress installation'
                    );
                    $all_success = false;
                    continue;
                }

                // Apply recommended permissions
                $success = $this->change_file_permission($full_path, $this->get_recommended_permission($full_path));

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
            $final_status = $this->check_permissions();

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
}
