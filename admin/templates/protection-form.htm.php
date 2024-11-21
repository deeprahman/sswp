<?php
/**
 * Protection form
 */
// Get the allowed_files option
global $wpss;

$file_types = $wpss->get_file_types();


?>

<form class="htaccess-form">
    <h3 class="">Protection Settings</h3>
    <ul>
        <li>
        <input type="checkbox" id="protect-debug-log" name="protect-debug-log" >
            <label for="protect-debug-log" title="<?= __("Protect the WordPress log at default location")?>">Protect Debug Log</label>
        </li>
        <li>
            <input type="checkbox" id="protect-update-directory" name="protect-update-directory">
            <label for="protect-update-directory" title="<?= __("Select which file-types should have access to uploads directory",WP_Securing_Setup::DOMAIN) ?>">Protect Update Directory</label>
            <div id="update-directory-options" style="display: none;">
            <h4><?= __("Give access to the selected file-types only", WP_Securing_Setup::DOMAIN) ?></h4>
                <select id="mySelect" name="allowed_files"  multiple="multiple">
                <?php foreach($file_types as $ext): ?>
                    <option value="<?= $ext ?>"><?= strtoupper($ext) ?></option>
                <?php endforeach; ?> 
                    
                </select>
            </div>
        </li>
        <li>
            <input type="checkbox" id="protect-xml-rpc" name="protect-xml-rpc">
            <label for="protect-xml-rpc" title="<?= __("Disable the system.multicall method", WP_Securing_Setup::DOMAIN)?>">Protect XML-RPC</label>
        </li>
        <li>
        <input type="checkbox" id="protect-rest-endpoint" name="protect-rest-endpoint" >
            <label for="protect-rest-endpoint" title="<?= __("Redirect requests to the users REST endpoint to 404 HTTP error",WP_Securing_Setup::DOMAIN) ?>">Protect REST Endpoint</label>
        </li>
    </ul>
    <button type="submit" class="button button-primary">Save Settings</button>
</form>

