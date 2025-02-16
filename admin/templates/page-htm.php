<?php
/**
 * Main Plugin Page
 */
?>

<h1 id="wpss-page-heading"> <?php esc_html_e( 'Secure WordPress Installation', 'secure-setup' ); ?> </h1>
<hr>


<?php

// Display settings errors and messages for 'secure_setup_messages'
settings_errors('file_permission_messages');
?>

<div id="my-tabs">
	<ul>
		<li><a href="#tab-1"><?php esc_html_e( 'File Permission Page', 'secure-setup' ); ?></a></li>
		<li><a href="#tab-2"><?php esc_html_e( '.htacces Config', 'secure-setup' ); ?></a></li>
		<li><a href="#tab-3"><?php esc_html_e( 'Site Migration', 'secure-setup' ); ?></a></li>
	</ul>
	<div id="tab-1">
		
		<wp-permissions-table></wp-permissions-table>
	</div>
	<div id="tab-2">
		<?php require_once Sswp_Securing_Setup::ROOT . DIRECTORY_SEPARATOR . 'admin/templates/protection-form-htm.php'; ?>
	</div>
	<div id="tab-3">
		<form id="form-3" class="tab-form" disabled>
			<h3><?php esc_html_e( 'Comming Soon...', 'secure-setup' ); ?></h3>
		</form>
	</div>
</div>

