const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const TerserPlugin = require('terser-webpack-plugin');


module.exports = (env, argv) => {
	console.log('Webpack mode:', argv.mode);
    const config = {
        ...defaultConfig,
        externals: {
            jquery: 'jQuery',
        },
    };


    let mode = 'production'; // default to production
    if(process.env.npm_lifecycle_event === 'start'){
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