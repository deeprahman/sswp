<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once Sswp_Securing_Setup::ROOT . DIRECTORY_SEPARATOR . 'includes/traits/class-sswp-ownership-permission-trait.php';
require_once Sswp_Securing_Setup::ROOT . DIRECTORY_SEPARATOR . 'includes/traits/class-sswp-print-permissions.php';


/**
 * Class WP_File_Permission_Checker
 *
 * Checks and displays file permissions for critical WordPress files and directories.
 *
 * @property array $files_to_check List of files and directories to check permissions for.
 * @property array $recommended_permissions Recommended permissions for files and directories.
 */
class Sswp_File_Permission_Manager {


	use Sswp_Ownership_Permission_Trait;
	use Sswp_Print_Permissions;

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
	public function __construct( $files_to_check = array(), $recommended_permissions = array() ) {


		if ( ! $this->initializeFilesystem() ) {
						sswp_logger( 'Info', __( 'File System initialization failed.', 'secure-setup' ), __METHOD__ );
			return;
		}

		$this->files_to_check          = ! empty( $files_to_check ) ? $files_to_check : array(
			'wp-config.php',
			'wp-content',
			'wp-content/uploads',
		);
		$this->recommended_permissions = ! empty( $recommended_permissions ) ? $recommended_permissions : array(
			'directory'     => '0755',
			'file'          => '0644',
			'wp-config.php' => '0444',
		);
	}

	

	/**
	 * Check if a given path is within the WordPress installation directory.
	 *
	 * @param  string $path The path to check.
	 * @return bool True if the path is within the WordPress installation, false otherwise.
	 */
	private function is_within_wordpress( $path ) {
		$real_path = realpath( $path );
		$wp_path   = realpath( ABSPATH );
		return strpos( $real_path, $wp_path ) === 0;
	}

	/**
	 * Check permissions for all specified files and directories.
	 *
	 * @return array An array of permission check results for each file/directory.
	 */
	public function check_permissions() {

		$results = array();

		foreach ( $this->files_to_check as $file ) {
			$path = ABSPATH . $file;
			if ( $this->is_within_wordpress( $path ) ) {
				$results[ $file ] = $this->get_file_permission( $path );
			} else {
				$results[ $file ] = array(
					'exists'      => 'N/A',
					'permission'  => 'N/A',
					'writable'    => 'N/A',
					'recommended' => 'N/A',
					'error'       => 'Path is outside WordPress installation',
				);
			}
		}

		return $results;
	}

	/**
	 * Get file permissions for a specific path.
	 *
	 * @param  string $path The path to check permissions for.
	 * @return array An array containing permission details for the given path.
	 */
	private function get_file_permission( $path ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php'; // TODO: Remove Direct inclusion of WP Core Files
		}

		WP_Filesystem();

		// TODO: check if posix_getpwuid() is available
		$recommended = $this->get_recommended_permission( $path ); // NOTE: requires posix_getpwuid()

		if ( ! $wp_filesystem->exists( $path ) ) {
			return array(
				'exists'      => false,
				'permission'  => null,
				'writable'    => false,
				'recommended' => $recommended??null,
			);
		}

		$perms    = $wp_filesystem->getchmod( $path );
		$writable = $wp_filesystem->is_writable( $path );

		return array(
			'exists'      => true,
			'permission'  => $perms,
			'writable'    => $writable,
			'recommended' => $recommended??null,
		);
	}

	/**
	 * Get recommended permissions for a file or directory.
	 *
	 * @param  string $path The absolute path to get recommended permissions for.
	 * @return string|WP_Error The recommended permission string ('755' for directories, '644' for files, '444' for wp-config.php).
	 */
	public function get_recommended_permission( $path ) {
		global $wp_filesystem;

		if ( $wp_filesystem->is_dir( $path ) ) {
			return $this->recommended_permissions['directory'];
		} elseif ( strpos( basename( $path ), 'wp-config' ) !== false ) {
			return $this->recommended_permissions['wp-config.php'];
		} elseif ( $wp_filesystem->is_file( $path ) ) {
			return $this->recommended_permissions['file'];
		} else {
			return new WP_Error(
				'unknown_type',
				'Unknown file type',
				$path
			);
		}
	}


	public function check_path_validity( $path, $enforec_ownership_check = false ) {

		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php'; // TODO: Remove it ; Unsafe use hook
		}

		WP_Filesystem();
		if ( ! $this->is_within_wordpress( $path ) ) {
			// translators: %s is the file path that is not within the WordPress installation.
			$message = sprintf( __( 'The path is not within WordPress installation: %s.', 'secure-setup' ), $path );
			sswp_logger( 'Info', $message, __METHOD__ );
			return false;
		}

		if ( ! $wp_filesystem->exists( $path ) ) {
			sswp_logger( 'Info ', 'File/Dir does not exists. ' . $path, __METHOD__ );
			return false;
		}
		
		// TODO: Run this line only if posix_getpwuid() is available
		if ( ( $this->get_ownership_Info( $path )['is_wp_owner'] !== true ) ) { // Note: This require posix_getwpwuid()

			sswp_logger( 'Info ', 'Path is not Owned by WordPress Process ' . $path, __METHOD__ );
			if ( $enforec_ownership_check ) {
				return false;
			}
		}
	}

		/**
		 * Change the file permission to a given permission value.
		 *
		 * @param  string $path       The path to the file or directory.
		 * @param  string $permission The permission to set (e.g., '0644', '0755').
		 * @return bool True if the permission was changed successfully, false otherwise.
		 */
	public function change_file_permission( $path, $permission ) {

		if ( is_wp_error( $this->is_valid_path( $path ) ) ) {
			return false;
		}
		return $this->update_permission( $path, $permission );
	}

		/**
		 * Change the directory and file permissions to recommended values.
		 *
		 * @param  array $paths The array of paths to change permissions for
		 * @return array Contains paths for which permission could not be changed
		 */
	public function change_to_recommended_permissions( array $paths ) {
		if ( empty( $paths ) ) {
			return array();
		}

		$errors = array_filter(
			$paths,
			function ( $path ) {

				// Get absolute path
				$abs_path = ABSPATH . $path;

				// Get recommended permission based on whether it's a file or directory
				$recommended_permission = $this->get_recommended_permission( $abs_path );

				if ( is_wp_error( $recommended_permission ) ) {

					sswp_logger(
						'Error',
						array(
							'message'    => $recommended_permission->get_error_message(),
							'Error_Data' => $recommended_permission->get_error_data(),
						),
						__METHOD__
					);
					return true;
				}

				if ( is_wp_error( $this->is_valid_path( $abs_path ) ) ) {
					return true;
				}

				// Attempt to change the permission
				$result = $this->update_permission( $abs_path, $recommended_permission );

				// Return true if there was an error (to keep this path in the errors array)
				return is_wp_error( $result ) || $result === false;
			}
		);

		// Log any errors that occurred
		if ( ! empty( $errors ) ) {
			array_walk(
				$errors,
				function ( $error ) {
					// sswp_logger('Info', 'Could not change file permission ' . $error, __METHOD__);
				}
			);
		}

		return array_values( $errors );
	}

		/**
		 * Get the current permission for a given path.
		 *
		 * @param  string $path The path to check permissions for.
		 * @return string|null The current permission string or null if the file doesn't exist.
		 */
	public function get_current_permission( $path ) {
		$info = $this->get_file_permission( $path );
		return $info['exists'] ? $info['permission'] : null;
	}

		/**
		 * Set the recommended permission for a given path type.
		 *
		 * @param  string $type       The type of path ('directory' or 'file').
		 * @param  string $permission The permission to set (e.g., '644', '755').
		 * @return bool True if the recommended permission was set successfully, false otherwise.
		 */
	public function set_recommended_permission( $type, $permission ) {
		if ( ! in_array( $type, array( 'directory', 'file' ) ) ) {
			return false;
		}

		if ( ! preg_match( '/^[0-7]{3}$/', $permission ) ) {
			return false;
		}

		$this->recommended_permissions[ $type ] = $permission;
		return true;
	}

		/**
		 * Set the permission for a given path.
		 *
		 * @param  string $path       The path to set permissions for.
		 * @param  string $permission The permission to set (e.g., '644', '755').
		 * @return bool True if the permission was set successfully, false otherwise.
		 */
	public function set_permission( $path, $permission ) {
		global $wp_filesystem;
		if ( ! $this->is_within_wordpress( $path ) ) {
			return false;
		}
		WP_Filesystem();
		if ( ! $wp_filesystem->exists( $path ) ) {
			return false;
		}

		return $this->update_permission( $path, $permission );
	}

		/**
		 * Updates the file permission
		 *
		 * @param  string  $path  Absolute path to a file or directory
		 * @param  string  $perms Permission in octal format (e.g. '0744' , '0444' )
		 * @param  boolean $cc    Clear the state cache after permsiion change
		 * @return boolean|WP_Error    On success return true
		 */
	private function update_permission( $path, $perms, bool $cc = true ) {
		$is_changed = $this->wp_filesystem->chmod( $path, octdec( $perms ) );

		if ( ! $is_changed ) {
			sswp_logger( 'Info', 'Permission changed for path: ' . $path . ' to ' . $perms, __FUNCTION__ );
			return new WP_Error( '500', 'File Permission Could Not be changed' );
		}
		if ( $cc ) {
			clearstatcache();
			sswp_logger( 'Info', 'Cache cleared after permission changed for the path ' . $path, __METHOD__ );
		}
		return $is_changed;
	}

		/**
		 * File or Directory is Owned by WordPress
		 *
		 * @param  string $path Absolute path to the directory or file
		 * @return boolean|WP_Error
		 */
	public function is_wp_owner( $path ) {
		$check = $this->check_ownership_permissions( $path );

		if ( is_wp_error( $check ) ) {  // TODO: Handle Error
			sswp_logger( 'Info', ' Message: ' . $check->get_error_message(), __METHOD__ );
			return false;
		}

		// Access detailed information
		if ( ! $check['ownership']['is_wp_owner'] ) {
			// Handle incorrect ownership
			sswp_logger( 'Info', 'File not owned by WordPress user, File Name: ' . $path, __METHOD__ );
		}

		if ( ! empty( $check['security']['warnings'] ) ) {
			// Handle security warnings
			foreach ( $check['security']['warnings'] as $warning ) {
				sswp_logger( 'Info', 'Security warning: ' . $warning, __METHOD__ );
			}
		}

		return $check;
	}

		/**
		 * Check if the given path is valid further processing
		 *
		 * @package $path   Absolute path to the file or directory
		 * @return  boolean|WP_Error    true on success
		 */
	private function is_valid_path( string $path, $enforec_ownership_check = false ) {
		if ( ! $this->is_within_wordpress( $path ) ) {
			sswp_logger( 'Info ', 'Path is not within WordPress installation, ' . $path, __METHOD__ );
			return new WP_Error(
				'invalid_path',
				__( 'Path is not within WordPress installation', 'secure-setup' ),
				$path
			);
		}
		// TODO: only use if posix_getpwuid() is available
		$is_owned = $this->is_wp_owner( $path ); // Note: Requires posix_getpwuid()
		if ( is_wp_error( $is_owned ) ) {
			sswp_logger( 'Info: ', 'Path is not ownerd by WordPress' . $path, __METHOD__ );
			if ( $enforec_ownership_check ) {
				return new WP_Error(
					'ownership_failed',
					__( 'Path is not ownerd by WordPress', 'secure-setup' ),
					$path
				);
			}
		}
		return true;
	}//end is_valid_path()
}
