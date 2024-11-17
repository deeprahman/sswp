import $ from 'jquery';
import "select2/dist/js/select2.min";
import 'jquery-ui/ui/widgets/checkboxradio';  // Explicitly import checkboxradio
import 'jquery-ui/ui/widgets/button';         // For buttons
import 'jquery-ui/ui/widgets/selectmenu';     // For select menus
import 'jquery-ui/themes/base/all.css'; 

$(document).ready(function () {
    // Style checkboxes as buttons
   // $('.htaccess-form input[type="checkbox"]').checkboxradio();

   // // Style the button
   // $('.htaccess-form button').button();

    // Style the select dropdown
   // $('#mySelect').selectmenu();

    // Show/hide directory options based on checkbox state
    $('#protect-update-directory').on('change', function () {
        if ($(this).is(':checked')) {
            $('#update-directory-options').slideDown();
        } else {
            $('#update-directory-options').slideUp();
        }
    });
    $('#mySelect').select2({
        placeholder: 'Select allowed files',
        allowClear: true,
    });
 
      // Apply custom styles to the selected option when the selection changes
    $('#mySelect').on('select2:select', function (e) {
        // This is triggered when an option is selected
        const selectedOption = $(e.params.data.element);
        selectedOption.css({
            'background-color': 'green',
            'color': 'white',
        });
    });

        // Optional: Style the dropdown and selected options
    $('#mySelect').on('select2:unselect', function (e) {
        const unselectedOption = $(e.params.data.element);
        unselectedOption.css({
            'background-color': '',
            'color': '',
        });
    });

});
