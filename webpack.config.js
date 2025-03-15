const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const TerserPlugin = require('terser-webpack-plugin');
const path = require('path');

module.exports = (env, argv) => {
    console.log('Webpack mode:', argv.mode);
    const config = {
        ...defaultConfig,
        externals: {
            jquery: 'jQuery',
        },
        entry: {
            // Define your two entry points
            main: path.resolve(__dirname, 'src/main.js'), // First entry point
            admin: path.resolve(__dirname, 'src/admin.js'), // Second entry point

        },
        output: {
            ...defaultConfig.output, // Keep the default output settings
            filename: '[name].js', // Use [name] to generate separate bundles (main.js and admin.js)
            path: path.resolve(__dirname, 'build'), // Output directory
        }

    };


    let mode = 'production'; // default to production
    if (process.env.npm_lifecycle_event === 'start') {
        mode = 'development';
    }
    config.mode = mode;

    if (mode === 'production') {
        config.optimization = {
            ...config.optimization,
            minimizer: [
                new TerserPlugin({
                    terserOptions: {
                        compress: {
                            drop_console: true,
                        },
                    },
                }),
            ],
        };
    }

    return config;
};