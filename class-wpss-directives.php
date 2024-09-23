<?php
class WP_Server_Config {
    private $is_apache;
    private $is_nginx;
    private $is_litespeed;
    private $is_iis;
    private $home_path;
    private $wp_rewrite;
    private $wp_filesystem;

    public function __construct() {
        global $is_apache, $is_nginx, $is_IIS, $is_iis7, $wp_rewrite;

        $this->is_apache = $is_apache;
        $this->is_nginx = $is_nginx;
        $this->is_litespeed = strpos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false;
        $this->is_iis = $is_IIS || $is_iis7;
        $this->home_path = get_home_path();
        $this->wp_rewrite = $wp_rewrite;

        // Initialize WP_Filesystem
        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        WP_Filesystem();
        global $wp_filesystem;
        $this->wp_filesystem = $wp_filesystem;
    }

    public function add_rule($rules, $htaccess_path = '') {
        if ($this->is_apache || $this->is_litespeed) {
            return $this->add_apache_rule($rules, $htaccess_path);
        } elseif ($this->is_nginx) {
            return $this->add_nginx_rule($rules);
        } elseif ($this->is_iis) {
            return $this->add_iis_rule($rules);
        }
        return false;
    }

    private function add_apache_rule($rules, $htaccess_path = '') {
        $htaccess_file = $htaccess_path ?: $this->home_path . '.htaccess';
        
        if (!$this->wp_filesystem->exists($htaccess_file)) {
            if (!$this->wp_filesystem->is_writable(dirname($htaccess_file))) {
                return false;
            }
            $this->wp_filesystem->touch($htaccess_file);
        } elseif (!$this->wp_filesystem->is_writable($htaccess_file)) {
            return false;
        }

        $current_rules = extract_from_markers($htaccess_file, 'WordPress');
        $new_rules = array_merge($current_rules, explode("\n", $rules));
        
        if (!$this->validate_htaccess_syntax($new_rules)) {
            return false;
        }

        return insert_with_markers($htaccess_file, 'WordPress', $new_rules);
    }

    public function protect_debug_log() {
        $htaccess_path = WP_CONTENT_DIR . '/.htaccess';
        $rules = <<<EOD
<Files debug.log>
    Order allow,deny
    Deny from all
</Files>
EOD;
        return $this->add_apache_rule($rules, $htaccess_path);
    }

    public function allow_file_access($file_pattern) {
        if (!$this->is_valid_file_pattern($file_pattern)) {
            return false;
        }

        $htaccess_path = WP_CONTENT_DIR . '/uploads/.htaccess';
        $rules = <<<EOD
Order Allow,Deny
<FilesMatch "{$file_pattern}">
    Allow From All
</FilesMatch>
EOD;
        
        return $this->add_apache_rule($rules, $htaccess_path);
    }

    private function is_valid_file_pattern($pattern) {
        $allowed_patterns = [
            '\.(jpe?g|gif|png|webp)$',
            '\.(pdf|docx?|xlsx?|pptx?)$',
            '\.(mp3|wav|ogg)$',
            '\.(mp4|avi|mov)$',
            '\.(zip|rar|tar|gz)$'
        ];
        return in_array($pattern, $allowed_patterns, true);
    }

    private function validate_htaccess_syntax($rules) {
        // This is a basic validation. For a more thorough check, you might need to use
        // the Apache binary to test the configuration, which is beyond the scope of this function.
        $valid_directives = ['RewriteEngine', 'RewriteBase', 'RewriteRule', 'RewriteCond', 'Order', 'Allow', 'Deny', 'Files', 'FilesMatch'];
        
        foreach ($rules as $rule) {
            $rule = trim($rule);
            if (empty($rule) || $rule[0] === '#') {
                continue; // Skip empty lines and comments
            }
            $directive = strtok($rule, ' ');
            if (!in_array($directive, $valid_directives)) {
                return false; // Invalid directive found
            }
        }
        return true;
    }

    public function are_apache_directives_compatible() {
        // ... (unchanged)
    }
}