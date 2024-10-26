/**
 * Function to get the htaccess protection status using WordPress REST API
 * @returns {Promise} A promise that resolves with the API response
 */


import { handleFormData } from "./form-data-handler.js";

function getHtaccessProtected(data) {
    return wp.apiRequest({
        path: '/wpss/v1/htaccess-protect',
        method: 'POST',
        data: {
            nonce: wpApiSettings.nonce,
            from: data
        },
        headers: {
            'X-WP-Nonce': wpApiSettings.nonce,
        },
    })
    .then(function (response) {
        console.log('HTACCESS Protection Status:', response);
        return response;
    })
    .catch(function (error) {
        console.error('Error getting HTACCESS protection status:', error);
        throw error;
    });
}

// Example usage
export function checkHtaccessProtection(data) {
    getHtaccessProtected(data)
        .then(response => {
            if (response.success) {
                console.log("Htform success response: ", response.data.message.data);
                // Update UI here, for example:
              let form_data =  JSON.parse(response.data.message.data);
             

                handleFormData(form_data); 
            } else {
                console.error('Failed to get protection status');
            }
        })
        .catch(error => {
            console.error('REST API request error:', error);
            if (error.responseText) {
                console.error('Error details:', error.responseText);
            }
            // Update UI to show error here
        });
}

// Attach to a button click event (if applicable)
// document.getElementById('checkProtectionButton')?.addEventListener('click', checkHtaccessProtection);

// Or call immediately if needed

