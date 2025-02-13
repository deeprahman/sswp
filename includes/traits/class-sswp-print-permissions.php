<?php

trait Sswp_Print_Permissions {
       public function display_results()
       {
           $results = $this->check_permissions();
    
           $widths = array(
           'file'        => 30,
           'exists'      => 10,
           'permission'  => 15,
           'writable'    => 10,
           'recommended' => 15,
           'error'       => 40,
           );
    
           $this->print_row('File/Directory', 'Exists', 'Permission', 'Writable', 'Recommended', 'Error', $widths);
           $this->print_separator($widths);
    
           foreach ( $results as $file => $info ) {
               $this->print_row(
                   $file,
                   isset($info['error']) ? 'N/A' : ( $info['exists'] ? 'Yes' : 'No' ),
                   isset($info['error']) ? 'N/A' : ( $info['exists'] ? $info['permission'] : 'N/A' ),
                   isset($info['error']) ? 'N/A' : ( $info['exists'] ? ( $info['writable'] ? 'Yes' : 'No' ) : 'N/A' ),
                   isset($info['error']) ? 'N/A' : $info['recommended'],
                   isset($info['error']) ? $info['error'] : '',
                   $widths
               );
           }
       }
    
       /**
        * Print a row of the results table.
        */
       private function print_row( $file, $exists, $permission, $writable, $recommended, $error, $widths )
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
        * Print a separator line for the results table.
        */
       private function print_separator( $widths )
       {
           $total_width = array_sum($widths) + count($widths) - 1;
           echo str_repeat('-', $total_width) . "\n";
       }
}