<?php

require_once $wpss->root . DIRECTORY_SEPARATOR . "includes/class-wpss-server-directives.php";
require_once $wpss->root . DIRECTORY_SEPARATOR . "includes\interface-wpss-server-directives.php";
require_once ABSPATH . "wp-admin/includes/misc.php";

class WPSS_Server_Directives_Apache extends WPSS_Server_Directives implements IWPSS_Server_Directives
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add_rule($rules, $htaccess_path = '', $marker = 'wpss'): bool
    {
        write_log([
            "htaccess-rules" => $rules,
            "HTACEESS File" => $htaccess_path,
            "marker" => $marker
        ]);
        if ($this->is_apache || $this->is_litespeed) {
            return $this->add_apache_rule($rules, $htaccess_path, $marker);
        }
        return false;
    }

    public function remove_rule($htaccess_path = '', string $marker = 'wpss')
    {
        write_log([
            "HTACEESS File" => $htaccess_path,
            "marker" => $marker
        ]);

        if ($this->is_apache || $this->is_litespeed) {
            return $this->remove_apache_rule($htaccess_path, $marker);
        }
        return false;
    }



    private function add_apache_rule($rules, $htaccess_path = '', string $marker = 'wpss')
    {

        if (!$this->validate_htaccess_syntax($rules)) {
            write_log("htaccess validation failed");
            return false;
        }

        $htaccess_file = $htaccess_path ?: $this->home_path . '.htaccess';

        if (!$this->wp_filesystem->exists($htaccess_file)) {
            if (!$this->wp_filesystem->is_writable(dirname($htaccess_file))) {
                write_log("File system is not writable ");
                return false;
            }
            $this->wp_filesystem->touch($htaccess_file);
        } elseif (!$this->wp_filesystem->is_writable($htaccess_file)) {
            write_log("File system is not writable and file system does not exist");
            return false;
        }

        $current_rules = extract_from_markers($htaccess_file, $marker);

        // TODO: before inserting the new rule - Remove all the blank lines from the bottom and the .htaccess file at the given path
        // Read the contents of the htaccess file
        $htaccess_content = $this->wp_filesystem->get_contents($htaccess_file);
        if ($htaccess_content === false) {
            write_log("Unable to read .htaccess file content");
            return false;
        }

        // Split the file into lines
        $lines = explode("\n", $htaccess_content);

        // Remove trailing blank lines
        while (!empty($lines) && trim(end($lines)) === '') {
            array_pop($lines); // Remove empty lines from the end
        }

        // Rebuild the htaccess content without the trailing blank lines
        $cleaned_htaccess_content = implode("\n", $lines);

        // Write the cleaned content back to the file
        if (!$this->wp_filesystem->put_contents($htaccess_file, $cleaned_htaccess_content)) {
            write_log("Failed to write cleaned .htaccess content");
            return false;
        }

        // Now, merge the current rules with the new rules
        $new_rules = array_merge($current_rules, explode("\n", $rules));

        // Insert the new rules with the marker
        return insert_with_markers($htaccess_file, $marker, $new_rules);
    }


    /**
     * Summary of remove_apache_rule
     * @param mixed $htaccess_path
     * @param string $marker
     * @return bool
     */
    private function remove_apache_rule($htaccess_path = '', string $marker = 'wpss')
    {
        $htaccess_file = $htaccess_path ?: $this->home_path . '.htaccess';

        if (!$this->wp_filesystem->exists($htaccess_file)) {
            write_log("The .htaccess file does not exist.");
            return false;
        }

        if (!$this->wp_filesystem->is_writable($htaccess_file)) {
            write_log("The .htaccess file is not writable.");
            return false;
        }

        // Extract the current rules for the given marker
        $extracted_rules = extract_from_markers($htaccess_file, $marker);

        if (empty($extracted_rules)) {
            write_log("No rules found for the given marker.");
            return true; // Nothing to remove, so we consider it a success
        }

        // Get the current content of the .htaccess file
        $current_content = $this->wp_filesystem->get_contents($htaccess_file);
        if ($current_content === false) {
            write_log("Unable to read the .htaccess file.");
            return false;
        }

        // Remove the extracted rules from the file content
        $start_marker = "# BEGIN {$marker}";
        $end_marker = "# END {$marker}";
        $pattern = "/{$start_marker}.*?{$end_marker}\s*/s";
        $new_content = preg_replace($pattern, '', $current_content);

        if ($new_content === null) {
            write_log("Error occurred during regex replacement.");
            return false;
        }

        // Write the updated content back to the file
        $result = $this->wp_filesystem->put_contents($htaccess_file, $new_content);

        if ($result === false) {
            write_log("Failed to write the updated content to the .htaccess file.");
            return false;
        }

        return true;
    }

    public function protect_debug_log()
    {
        $htaccess_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . '.htaccess';
        $rules = <<<EOD
<Files debug.log>
    Order allow,deny
    Deny from all
</Files>
EOD;
        return $this->add_rule($rules, $htaccess_path, 'protect-log');
    }

    public function unprotect_debug_log(){
        $htaccess_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . '.htaccess';
        return $this->remove_rule($htaccess_path,'protect-log');
    }

    public function protect_user_rest_apt($page = 'home')
    {
        $htaccess_path = ABSPATH . '/.htaccess';
        $rules = <<<EOD
RewriteEngine On

# Block access to the users endpoint for any version of the API
RewriteRule ^wp-json/wp/v[0-9]+/users.*$ - [R=404,L]

# Redirect query strings with author to the provided page
RewriteCond %{QUERY_STRING} author=\d
RewriteRule (.*) {$page} [L,R=301,QSD]
EOD;
        return $this->add_rule($rules, $htaccess_path, 'protect-rest-api');
    }

    public function unprotect_user_rest_apt()
    {
        $htaccess_path = ABSPATH . '/.htaccess';

        return $this->remove_rule( $htaccess_path, 'protect-rest-api');
    }

    public function allow_file_access($file_pattern, $script_pattern)
    {
        $htaccess_path = WP_CONTENT_DIR . '/uploads/.htaccess';
        $rules = <<<EOD
<FilesMatch "{$file_pattern}">
    Require all granted
</FilesMatch>

# Deny everything else
<FilesMatch ".*">
    Require all denied
</FilesMatch>

EOD;
        return $this->add_rule($rules, $htaccess_path, 'protect-uploads');
    }

    public function disallow_file_access()
    {
        $htaccess_path = WP_CONTENT_DIR . '/uploads/.htaccess';

        return $this->remove_rule( $htaccess_path, 'protect-uploads');
    }

    protected function validate_htaccess_syntax(string $rules): mixed
    {
        global $wpss;
        $file_path = $wpss->root . "includes/class-wpss-apache-directives-validator.php";
        if (!file_exists($file_path)) {
            return new WP_Error("File Not Exists: " . $file_path);
        }
        include_once $file_path;
        $validator = new WPSS_Apache_Directives_Validator();
        return $validator->is_valid($rules);
    }

}
