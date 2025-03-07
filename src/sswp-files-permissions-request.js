import { sswpPermissionsTable } from './components/index';

function getFilePermissionsWP() {
	return wp
		.apiRequest( {
			path: '/sswp/v1/file-permissions',
			method: 'GET',
			data: {
				nonce: wpApiSettings.nonce,
			},
			headers: {
				'X-WP-Nonce': wpApiSettings.nonce,
			},
		} )
		.then( function ( response ) {
			console.log( 'File Permissions:', response );
			return response.data.fs_data;
		} )
		.catch( function ( error ) {
			console.error( 'Error fetching file permissions:', error );
			throw error;
		} );
}

// Register the web component
customElements.define( 'sswp-permissions-table', sswpPermissionsTable );

// Example usage
const permissionsTable = document.querySelector( 'sswp-permissions-table' );
// Example of listening for permission updates
permissionsTable.addEventListener( 'permissions-updated', ( e ) => {
	console.log( 'Permissions updated:', e.detail.data );
} );

// Using wp.apiRequest
getFilePermissionsWP()
	.then( ( permissions ) => {
		// Handle the permissions data
		console.log( 'Permission Data', permissions );

		const fa_data = JSON.parse( permissions );
		permissionsTable.data = fa_data;
	} )
	.catch( ( error ) => {
		// Handle any errors
		console.log( 'REST REQ Err' );
		console.log( error.responseText );
	} );
