
const fp = {};

fp.getFilePermissionsWP = function () {

    return wp.apiRequest({
        path: '/wpss/v1/file-permissions',
        method: 'GET'
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




fp.getFilePermissionsWP()
    .then(permissions => {
        // Handle the permissions data
        console.log("File Permissions\n", permissions);
    })
    .catch(error => {
        // Handle any errors
        console.log(error);
    });

