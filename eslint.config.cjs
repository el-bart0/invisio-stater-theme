/**
 * Extends the default @wordpress/scripts ESLint config: bin/ holds Node CLI
 * scripts (rename, make-pot), which legitimately log to the console.
 */
const defaultConfig = require( '@wordpress/scripts/config/eslint.config.cjs' );

module.exports = [
	...defaultConfig,
	{
		files: [ 'bin/**/*.mjs' ],
		rules: {
			'no-console': 'off',
		},
	},
];
