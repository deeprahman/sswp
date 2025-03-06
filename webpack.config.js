const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, argv) => {
    const config = {
        ...defaultConfig,
        externals: {
            jquery: 'jQuery',
        },
    };

    if (argv.mode === 'production') {
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