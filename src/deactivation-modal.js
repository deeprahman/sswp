import $ from 'jquery';
console.log(" In the deactivate script ");
$(document).ready(function ($) {
    $('tr[data-plugin="secure-setup/sswp-secure-setup.php"] .deactivate a').on('click', function (e) {
        e.preventDefault(); // Stop the default deactivation
        console.log(" deactivate button clicked ");

        let deactivateUrl = new URL($(this).attr('href'), window.location.origin); // Get original deactivation URL
        let nonce = deactivateUrl.searchParams.get('_wpnonce'); // Extract the nonce

        // Append modal to body if it doesn't exist
        if ($('#custom-deactivate-modal').length === 0) {
            $('body').append(`
                <div id="custom-deactivate-modal" title="Confirm Deactivation">
                    <p>Do you want to delete the plugin log table?</p>
                </div>
            `);
        }

        // Initialize jQuery UI Dialog
        $('#custom-deactivate-modal').dialog({
            modal: true,
            resizable: false,
            draggable: false,
            width: 400,
            buttons: {
                "Yes, Delete Logs": function () {
                    deactivateUrl.searchParams.set('db-delete', '1'); // Append db-delete=1
                    window.location.href = deactivateUrl.toString();
                },
                "No, Keep Logs": function () {
                    window.location.href = deactivateUrl.toString(); // Proceed without deleting logs
                },
                "Cancel": function () {
                    $(this).dialog("close"); // Just close the modal
                }
            }
        });
    });
});

