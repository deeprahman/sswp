import { checkHtaccessProtection } from "./wpss-htaccess-protect.js";

jQuery(document).ready(function($) {
    $('.htaccess-form').on('submit', function(e) {
        e.preventDefault();
        
        // Custom serialization
        var serializedData = [];
        
        // Process checkboxes
        $(this).find('input[type="checkbox"]').each(function() {
            serializedData.push({
                name: $(this).attr('name'),
                value: $(this).prop('checked') ? "on" : "off"
            });
        });
        
        // Process multi-select
        $(this).find('select[multiple]').each(function() {
            serializedData.push({
                name: $(this).attr('name'),
                value: $(this).val() || [] // If nothing selected, use empty array
            });
        });
        
        // Process other inputs (text, radio, single select, etc.)
        $(this).find('input:not([type="checkbox"]), select:not([multiple]), textarea').each(function() {
            if ($(this).val()) {
                serializedData.push({
                    name: $(this).attr('name'),
                    value: $(this).val()
                });
            }
        });
        
        // Remove duplicates (keeping the last occurrence)
        var uniqueSerializedData = [];
        var seenKeys = {};
        
        for (var i = serializedData.length - 1; i >= 0; i--) {
            var item = serializedData[i];
            if (!seenKeys[item.name]) {
                seenKeys[item.name] = true;
                uniqueSerializedData.unshift(item);
            }
        }
        
        // Call the sendData function with the serialized data
        sendData(uniqueSerializedData);
    });
    
    // Toggle visibility of update directory options
    $('#protect-update-directory').on('change', function() {
        $('#update-directory-options').toggle(this.checked);
    });
});

// Placeholder for the sendData function
function sendData(data) {
    console.log('Sending data:', data);
    // Implement your data sending logic here
    checkHtaccessProtection(data);
}
