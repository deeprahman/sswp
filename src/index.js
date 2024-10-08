import "./components/index";
import "./index.scss";
jQuery(document).ready(function($) {
    console.log("Script Loaded");
    // Initialize tabs
    $("#my-tabs").tabs();

    // Handle form submissions
    $(".tab-form").on("submit", function(e) {
        e.preventDefault();
        var $form = $(this);
        var formId = $form.attr("id");
        var formData = $form.serialize();

        wp.apiRequest({
            path: '/custom/v1/' + formId,
            method: 'POST',
            data: formData
        }).then(function(response) {
            alert('Form submitted successfully: ' + JSON.stringify(response));
        }, function(error) {
            alert('Error submitting form: ' + error.responseJSON.message);
        });
    });
});
