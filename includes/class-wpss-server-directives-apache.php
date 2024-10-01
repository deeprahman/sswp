<?php


class WPSS_Server_Directives_Apache extends WPSS_Server_Directives
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add_rule($rules, $htaccess_path = '', $marker = 'wpss'): bool
    {
        if ($this->is_apache || $this->is_litespeed) {
            return $this->add_apache_rule($rules, $htaccess_path, $marker);
        }
        return false;
    }
    private function add_apache_rule($rules, $htaccess_path = '', string $marker = 'wpss')
    {
        print_r($rules,); echo PHP_EOL; // TODO: For Debug
        if (!$this->validate_htaccess_syntax($rules)) {
            echo  "htaccess validataion failed" .PHP_EOL; // TODO: For Debug
            return false;
        }


        $htaccess_file = $htaccess_path ?: $this->home_path . '.htaccess';

        if (!$this->wp_filesystem->exists($htaccess_file)) {
            if (!$this->wp_filesystem->is_writable(dirname($htaccess_file))) {
                return false;
            }
            $this->wp_filesystem->touch($htaccess_file);
        } elseif (!$this->wp_filesystem->is_writable($htaccess_file)) {
            return false;
        }

        $current_rules = extract_from_markers($htaccess_file, $marker);
        $new_rules = array_merge($current_rules, explode("\n", $rules));

        return insert_with_markers($htaccess_file, $marker, $new_rules);
    }

    public function protect_debug_log()
    {
        $htaccess_path = WP_CONTENT_DIR . '/.htaccess';
        $rules = <<<EOD
<Files debug.log>
    Order allow,deny
    Deny from all
</Files>
EOD;
        return $this->add_rule($rules, $htaccess_path, 'protect-log');
    }

    public function allow_file_access($file_pattern)
    {
        $htaccess_path = WP_CONTENT_DIR . '/uploads/.htaccess';
        $rules = <<<EOD
Order Allow,Deny
<FilesMatch "{$file_pattern}">
    Allow From All
</FilesMatch>
EOD;
        return $this->add_rule($rules, $htaccess_path, 'protect-uploads');
    }

    protected function validate_htaccess_syntax(string $rules): bool
    {
        $validator = new WPSS_Apache_Directives_Validator();
        return $validator->is_valid($rules);  
    }

}
