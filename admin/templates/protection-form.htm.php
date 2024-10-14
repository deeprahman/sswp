<?php
/**
 * Protection form
 */
?>

<form class="htaccess-form">
    <h3 class="">Protection Settings</h3>
    <ul>
        <li>
            <input type="checkbox" id="protect-debug-log" name="protect-debug-log">
            <label for="protect-debug-log">Protect Debug Log</label>
        </li>
        <li>
            <input type="checkbox" id="protect-update-directory" name="protect-update-directory">
            <label for="protect-update-directory">Protect Update Directory</label>
            <div id="update-directory-options" style="display: none;">
                <h4>Only Execute the Following Files:</h4>
                <select id="mySelect" name="allowed_files"  multiple>
                    <option value="jpeg">JPEG</option>
                    <option value="png">PNG</option>
                    <option value="gif">GIF</option>
                    <option value="webp">WEBP</option>
                    <option value="mkv">MKV</option>
                </select>
            </div>
        </li>
        <li>
            <input type="checkbox" id="protect-xml-rpc" name="protect-xml-rpc">
            <label for="protect-xml-rpc">Protect XML-RPC</label>
        </li>
        <li>
            <input type="checkbox" id="protect-rest-endpoint" name="protect-rest-endpoint">
            <label for="protect-rest-endpoint">Protect REST Endpoint</label>
        </li>
    </ul>
    <button type="submit" class="button button-primary">Save Settings</button>
</form>

