<?php

trait Sswp_Print_Permissions {
       public function display_results() {
           $results = $this->check_permissions();
    
           $widths = array(
               'file'        => 30,
               'exists'      => 10,
               'permission'  => 15,
               'writable'    => 10,
               'recommended' => 15,
               'error'       => 40,
           );
    
           $this->print_row(
               esc_html__( 'File/Directory', 'secure-setup' ),
               esc_html__( 'Exists', 'secure-setup' ),
               esc_html__( 'Permission', 'secure-setup' ),
               esc_html__( 'Writable', 'secure-setup' ),
               esc_html__( 'Recommended', 'secure-setup' ),
               esc_html__( 'Error', 'secure-setup' ),
               $widths
           );
           $this->print_separator( $widths );
    
           foreach ( $results as $file => $info ) {
               $this->print_row(
                   esc_html( $file ),
                   isset( $info['error'] ) ? esc_html__( 'N/A', 'secure-setup' ) : ( $info['exists'] ? esc_html__( 'Yes', 'secure-setup' ) : esc_html__( 'No', 'secure-setup' ) ),
                   isset( $info['error'] ) ? esc_html__( 'N/A', 'secure-setup' ) : ( $info['exists'] ? esc_html( $info['permission'] ) : esc_html__( 'N/A', 'secure-setup' ) ),
                   isset( $info['error'] ) ? esc_html__( 'N/A', 'secure-setup' ) : ( $info['exists'] ? ( $info['writable'] ? esc_html__( 'Yes', 'secure-setup' ) : esc_html__( 'No', 'secure-setup' ) ) : esc_html__( 'N/A', 'secure-setup' ) ),
                   isset( $info['error'] ) ? esc_html__( 'N/A', 'secure-setup' ) : esc_html( $info['recommended'] ),
                   isset( $info['error'] ) ? esc_html( $info['error'] ) : '',
                   $widths
               );
           }
       }
    
       /**
        * Print a row of the results table.
        */
       private function print_row( $file, $exists, $permission, $writable, $recommended, $error, $widths ) {
           // Using wp_kses_post for the format string as it contains markup
           printf(
            wp_kses_post( "%-{$widths['file']}s %-{$widths['exists']}s %-{$widths['permission']}s %-{$widths['writable']}s %-{$widths['recommended']}s %-{$widths['error']}s\n" ),
            esc_html( substr( $file, 0, $widths['file'] ) ),
            esc_html( substr( $exists, 0, $widths['exists'] ) ),
            esc_html( substr( $permission, 0, $widths['permission'] ) ),
            esc_html( substr( $writable, 0, $widths['writable'] ) ),
            esc_html( substr( $recommended, 0, $widths['recommended'] ) ),
            esc_html( substr( $error, 0, $widths['error'] ) )
        );
       }
    
       /**
        * Print a separator line for the results table.
        */
       private function print_separator( $widths ) {
           $total_width = array_sum( $widths ) + count( $widths ) - 1;
           echo wp_kses_post( str_repeat( '-', $total_width ) . "\n" );
       }
}