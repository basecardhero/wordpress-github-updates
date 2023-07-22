/**
 * Extends the default `webpack.config.js` from the @wordpress/scripts package.
 *
 * @since 0.1.0
 */
const defaultConfig = require( './node_modules/@wordpress/scripts/config/webpack.config' );
const resourcePath = __dirname + '/resources/js/';
const buildPath = __dirname + '/assets/';

module.exports = {
	...defaultConfig,
	entry: {
		admin: resourcePath + 'admin.js',
	},
	output: {
		filename: '[name].js',
		path: buildPath,
	},
};
