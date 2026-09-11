/**
 * Extends the default @wordpress/scripts webpack config.
 *
 *  - Keeps the theme-wide entries (index, editor).
 *  - Lets wp-scripts auto-discover per-block entries under src/blocks/<name>/
 *    (each block ships an index.js that imports its style.scss).
 *  - Forces every .php file under src/ to be copied into build/, so ACF block
 *    renderTemplate files (referenced via the "acf" key, which wp-scripts does
 *    not track by default) end up beside their compiled block.json.
 */
process.env.WP_COPY_PHP_FILES_TO_DIST = 'true';

const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry() {
		const wpEntries =
			typeof defaultConfig.entry === 'function'
				? defaultConfig.entry()
				: defaultConfig.entry;

		return {
			...wpEntries,
			index: path.resolve( __dirname, 'src/js/index.js' ),
			editor: path.resolve( __dirname, 'src/js/editor.js' ),
		};
	},
};
