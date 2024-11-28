<?php
/**
 * Main Plugin Page
 */
?>

<h1 id="wpss-page-heading"> <?php esc_html_e('WP Securing Setup', WP_Securing_Setup::DOMAIN); ?> </h1>
<hr>
<h2 id="wpss-tab-heading"> <?php esc_html_e('File Permission Page', WP_Securing_Setup::DOMAIN); ?> </h2>

<div id="my-tabs">
    <ul>
        <li><a href="#tab-1"><?php esc_html_e('File Permission Page', WP_Securing_Setup::DOMAIN); ?></a></li>
        <li><a href="#tab-2"><?php esc_html_e('.htacces Config', WP_Securing_Setup::DOMAIN); ?></a></li>
        <li><a href="#tab-3"><?php esc_html_e('Site Migration', WP_Securing_Setup::DOMAIN); ?></a></li>
    </ul>
    <div id="tab-1">
        <wp-permissions-table></wp-permissions-table>
    </div>
    <div id="tab-2">
        <?php require_once WP_Securing_Setup::ROOT . DIRECTORY_SEPARATOR . 'admin/templates/protection-form.htm.php'; ?>
    </div>
    <div id="tab-3">
        <form id="form-3" class="tab-form" disabled>
            <h3><?php esc_html_e('Comming Soon...', WP_Securing_Setup::DOMAIN); ?></h3>
        </form>
    </div>
</div>

