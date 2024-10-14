function getFilePermissionsWP() {
    return wp.apiRequest({
        path: '/wpss/v1/file-permissions',
        method: 'GET',
        data:{
            nonce:wpApiSettings.nonce
        },
        headers: {
            'X-WP-Nonce': wpApiSettings.nonce,
        },
    })
        .then(function (response) {
            console.log('File Permissions:', response);
            return response;
        })
        .catch(function (error) {
            console.error('Error fetching file permissions:', error);
            throw error;
        });
}

// Using wp.apiRequest
// getFilePermissionsWP()
//     .then(permissions => {
//         // Handle the permissions data
//         console.log(permissions);
//     })
//     .catch(error => {
//         // Handle any errors
//         console.log("REST REQ Err");
//         console.log(error.responseText);
//     });


