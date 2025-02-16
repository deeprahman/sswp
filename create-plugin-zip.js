const fs = require( 'fs' );
const archiver = require( 'archiver' );
const path = require( 'path' );

// Create output directory if it doesn't exist
const outputDir = path.join( __dirname, 'dist' );
if ( ! fs.existsSync( outputDir ) ) {
	fs.mkdirSync( outputDir );
}

// Create a write stream for our zip file
const output = fs.createWriteStream(
	path.join( outputDir, 'wp-securing-setup.zip' )
);
const archive = archiver( 'zip', {
	zlib: { level: 9 }, // Sets the compression level
} );

// Listen for all archive data to be written
output.on( 'close', () => {
	console.log(
		`Archive created successfully! Total bytes: ${ archive.pointer() }`
	);
} );

// Handle warnings during archiving
archive.on( 'warning', ( err ) => {
	if ( err.code === 'ENOENT' ) {
		console.warn( 'Warning:', err );
	} else {
		throw err;
	}
} );

// Handle errors
archive.on( 'error', ( err ) => {
	throw err;
} );

// Pipe archive data to the output file
archive.pipe( output );

// List of files and directories to include
const filesToInclude = [
	'admin',
	'includes',
	'build',
	'public',
	'wpss-logger.php',
	'wp-securing-setup.php',
	'wpss-misc.php',
	'readme.txt',
	'languages',
];

// Add files and directories to the archive
filesToInclude.forEach( ( item ) => {
	const itemPath = path.join( __dirname, item );

	try {
		if ( fs.existsSync( itemPath ) ) {
			const stats = fs.statSync( itemPath );

			if ( stats.isDirectory() ) {
				// If it's a directory, add it recursively
				archive.directory( itemPath, item );
				console.log( `Added directory: ${ item }` );
			} else {
				// If it's a file, add it directly
				archive.file( itemPath, { name: item } );
				console.log( `Added file: ${ item }` );
			}
		} else {
			console.warn(
				`Warning: ${ item } does not exist and will be skipped`
			);
		}
	} catch ( err ) {
		console.error( `Error processing ${ item }:`, err );
	}
} );

// Finalize the archive
archive.finalize();
