const path = require( 'path' );
// const webpack = require("webpack");
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
const StyleLintPlugin = require('stylelint-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
// const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
// Const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );

// Set different CSS extraction for editor only and common block styles
const blocksCSSPlugin = new ExtractTextPlugin( {
    filename: ( getPath ) => {
        return getPath( './css/[name].style.css').replace('editor.', '' );
    }
} );
const editBlocksCSSPlugin = new ExtractTextPlugin( {
    filename: ( getPath ) => {
        return getPath( './css/[name].editor.css').replace('editor.', '' );
    }
} );

// Configuration for the ExtractTextPlugin.
const extractConfig = {
    use: [
        { loader: 'raw-loader' },
        {
            loader: 'postcss-loader',
            options: {
                plugins: [
                    require( 'autoprefixer' ),
                    // require( 'cssnano' ),
                ]
            }
        },
        {
            loader: 'sass-loader',
            query: {
                includePaths: [ 'assets/stylesheets' ],
                data: '@import "colors"; @import "variables";',
                outputStyle:
                    'production' === process.env.NODE_ENV ? 'compressed' : 'nested'
            }
        }
    ]
};

module.exports = {
    entry: {
        'paragraph.block.editor': './blocks/paragraph/index.js',
        'custom.html.block.editor': './blocks/custom-html/index.js',
    },
    output: {
        path: path.resolve( __dirname, 'assets' ),
        filename: './js/[name].js'
    },
    watch: true,
    devtool: 'source-map',
    module: {
        rules: [
            // Setup ESLint loader for JS.
            {
                enforce: 'pre',
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                loader: 'eslint-loader',
                options: {
                    emitWarning: true,
                }
            },
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader'
                }
            },
            {
                test: /style\.s?css$/,
                use: blocksCSSPlugin.extract( extractConfig )
            },
            {
                test: /editor\.s?css$/,
                use: editBlocksCSSPlugin.extract( extractConfig )
            },
        ]
    },
    plugins: [
        blocksCSSPlugin,
        editBlocksCSSPlugin,
        new StyleLintPlugin({
            syntax: 'scss'
        }),
		new CopyWebpackPlugin([
			// third-party CSS
			{ from: './node_modules/react-select/dist/react-select.css', to: './css/third-party'  },
		]),
        // new webpack.optimize.UglifyJsPlugin({
		// 	include: /\.min\.js$/,
		// }),
        // new UglifyJSPlugin({
			// test: /\.min\.js($|\?)/i,
        //     uglifyOptions: {
        //         mangle: {
        //             // Dont mangle these
        //             reserved: ['$super', '$', 'exports', 'require']
        //         }
        //     },
        //     sourceMap: true
        // }),
        // New BrowserSyncPlugin({
        //   // Load localhost:3333 to view proxied site
        //   host: 'localhost',
        //   port: '3333',
        //   // Change proxy to your local WordPress URL
        //   proxy: 'https://gutenberg.local'
        // })
    ]
};
