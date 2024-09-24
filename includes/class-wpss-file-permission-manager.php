<?php

// Load WordPress core
//require_once('/home/deep/Websites/woocommerce.lo/wp/wp-load.php');

/**
 * Class WP_File_Permission_Checker
 * 
 * Checks and displays file permissions for critical WordPress files and directories.
 *
 * @property array $files_to_check List of files and directories to check permissions for.
 * @property array $recommended_permissions Recommended permissions for files and directories.
 */
class WPSS_File_Permission_Manager {
    /**
     * @var array $files_to_check List of files and directories to check permissions for.
     */
    private $files_to_check;

    /**
     * @var array $recommended_permissions Recommended permissions for files and directories.
     */
    private $recommended_permissions;

    /**
     * Constructor to initialize the files to check and recommended permissions.
     *
     * @param array $files_to_check List of files and directories to check permissions for.
     */
    public function __construct($files_to_check = []) {
        $this->files_to_check = !empty($files_to_check) ? $files_to_check : [
            'wp-config.php',
            'wp-content',
            'wp-content/uploads'
        ];
        $this->recommended_permissions = [
            'directory' => '755',
            'file' => '644'
        ];
    }

    /**
     * Check if a given path is within the WordPress installation directory.
     *
     * @param string $path The path to check.
     * @return bool True if the path is within the WordPress installation, false otherwise.
     */
    private function is_within_wordpress($path) {
        $real_path = realpath($path);
        $wp_path = realpath(ABSPATH);
        return strpos($real_path, $wp_path) === 0;
    }

    /**
     * Check permissions for all specified files and directories.
     *
     * @return array An array of permission check results for each file/directory.
     */
    public function check_permissions() {
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
                    'error' => 'Path is outside WordPress installation'
                ];
            }
        }

        return $results;
    }

    /**
     * Get file permissions for a specific path.
     *
     * @param string $path The path to check permissions for.
     * @return array An array containing permission details for the given path.
     */
    private function get_file_permission($path) {
        global $wp_filesystem;

        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        WP_Filesystem();

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
     * Get recommended permissions for a file or directory.
     *
     * @param string $path The path to get recommended permissions for.
     * @return string The recommended permission string ('755' for directories, '644' for files).
     */
    private function get_recommended_permission($path) {
        global $wp_filesystem;

        if ($wp_filesystem->is_dir($path)) {
            return $this->recommended_permissions['directory'];
        } else {
            return $this->recommended_permissions['file'];
        }
    }

    /**
     * Display the results of permission checks in a command-line friendly format.
     */
    public function display_results() {
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
     * Print a row of the results table.
     */
    private function print_row($file, $exists, $permission, $writable, $recommended, $error, $widths) {
        printf("%-{$widths['file']}s %-{$widths['exists']}s %-{$widths['permission']}s %-{$widths['writable']}s %-{$widths['recommended']}s %-{$widths['error']}s\n",
            substr($file, 0, $widths['file']),
            substr($exists, 0, $widths['exists']),
            substr($permission, 0, $widths['permission']),
            substr($writable, 0, $widths['writable']),
            substr($recommended, 0, $widths['recommended']),
            substr($error, 0, $widths['error'])
        );
    }

    /**
     * Print a separator line for the results table.
     */
    private function print_separator($widths) {
        $total_width = array_sum($widths) + count($widths) - 1;
        echo str_repeat('-', $total_width) . "\n";
    }

    /**
     * Change the file permission to a given permission value.
     *
     * @param string $path The path to the file or directory.
     * @param string $permission The permission to set (e.g., '644', '755').
     * @return bool True if the permission was changed successfully, false otherwise.
     */
    public function change_file_permission($path, $permission) {
        global $wp_filesystem;

        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        WP_Filesystem();

        if (!$this->is_within_wordpress($path)) {
            return false;
        }

        if (!$wp_filesystem->exists($path)) {
            return false;
        }

        return $wp_filesystem->chmod($path, $this->octal_to_decimal($permission));
    }

    /**
     * Change the directory and file permissions to recommended values.
     *
     * @param string $path The path to the directory.
     * @return bool True if all permissions were changed successfully, false otherwise.
     */
    public function change_to_recommended_permissions($path) {
        global $wp_filesystem;

        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        WP_Filesystem();

        if (!$this->is_within_wordpress($path)) {
            return false;
        }

        if (!$wp_filesystem->is_dir($path)) {
            return $this->change_file_permission($path, $this->get_recommended_permission($path));
        }

        $success = true;

        // Change directory permission
        $success &= $this->change_file_permission($path, $this->recommended_permissions['directory']);

        // Recursively change permissions for all files and subdirectories
        $files = $wp_filesystem->dirlist($path, true);
        foreach ($files as $file => $file_info) {
            $file_path = trailingslashit($path) . $file;
            if ($file_info['type'] == 'd') {
                $success &= $this->change_to_recommended_permissions($file_path);
            } else {
                $success &= $this->change_file_permission($file_path, $this->recommended_permissions['file']);
            }
        }

        return $success;
    }

    /**
     * Convert octal permission string to decimal.
     *
     * @param string $octal The octal permission string (e.g., '644').
     * @return int The decimal representation of the permission.
     */
    private function octal_to_decimal($octal) {
        return octdec($octal);
    }

    /**
     * Get the current permission for a given path.
     *
     * @param string $path The path to check permissions for.
     * @return string|null The current permission string or null if the file doesn't exist.
     */
    public function get_current_permission($path) {
        $info = $this->get_file_permission($path);
        return $info['exists'] ? $info['permission'] : null;
    }

    /**
     * Set the recommended permission for a given path type.
     *
     * @param string $type The type of path ('directory' or 'file').
     * @param string $permission The permission to set (e.g., '644', '755').
     * @return bool True if the recommended permission was set successfully, false otherwise.
     */
    public function set_recommended_permission($type, $permission) {
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
     * Set the permission for a given path.
     *
     * @param string $path The path to set permissions for.
     * @param string $permission The permission to set (e.g., '644', '755').
     * @return bool True if the permission was set successfully, false otherwise.
     */
    public function set_permission($path, $permission) {
        global $wp_filesystem;
        if (!$this->is_within_wordpress($path)) {
            return false;
        }
        WP_Filesystem();
        if (!$wp_filesystem->exists($path)) {
            return false;
        }

        return $wp_filesystem->chmod($path, $this->octal_to_decimal($permission));
    }
}

// Usage
$checker = new WPSS_File_Permission_Manager();
$checker->display_results();

// Example usage of new methods:
// $checker->change_file_permission(ABSPATH . 'wp-config.php', '644');
// $checker->change_to_recommended_permissions(ABSPATH . 'wp-content');
// $current_permission = $checker->get_current_permission(ABSPATH . 'wp-config.php');
// $checker->set_recommended_permission('file', '640');
// $tmp = $checker->check_permissions();

// print_r(get_home_path() . "wp-login.php");
$test_path = get_home_path() . "wp-login.php";
// echo $test_path . PHP_EOL;
// $tmp = $checker->get_current_permission($test_path);
// $tmp = $checker->get_recommended_permission($test_path);
// $tmp = $checker->get_recommended_permission($test_path);
$checker->set_permission($test_path, 400);
$tmp = $checker->get_current_permission($test_path);
print_r($tmp);
// $checker->display_results();
?>
