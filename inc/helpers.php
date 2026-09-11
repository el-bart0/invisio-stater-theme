<?php
/**
 * Helper functions.
 *
 * @package Invisio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Full URI for a compiled asset in the build directory.
 *
 * @param string $path Path relative to build/, e.g. 'index.js'.
 * @return string
 */
function invisio_asset_uri( $path ) {
	return INVISIO_URI . '/build/' . ltrim( $path, '/' );
}

/**
 * Whether a compiled asset exists in the build directory.
 *
 * Lets templates and enqueue logic degrade gracefully before the first build.
 *
 * @param string $path Path relative to build/.
 * @return bool
 */
function invisio_asset_exists( $path ) {
	return is_readable( INVISIO_DIR . '/build/' . ltrim( $path, '/' ) );
}

/**
 * Read a wp-scripts "*.asset.php" metadata file for a build entry.
 *
 * Returns the script dependencies and a cache-busting version. Falls back to the
 * theme version when the metadata file is missing.
 *
 * @param string $entry Entry name, e.g. 'index'.
 * @return array{dependencies: string[], version: string}
 */
function invisio_asset_meta( $entry ) {
	$defaults = array(
		'dependencies' => array(),
		'version'      => INVISIO_VERSION,
	);

	$file = INVISIO_DIR . '/build/' . $entry . '.asset.php';

	if ( is_readable( $file ) ) {
		$meta = require $file;

		if ( is_array( $meta ) ) {
			return wp_parse_args( $meta, $defaults );
		}
	}

	return $defaults;
}

/**
 * Keep the theme's development directories out of All-in-One WP Migration backups.
 *
 * node_modules/ and .git/ can each be hundreds of MB and have no place in an
 * export archive. The ai1wm filter expects paths relative to wp-content/ with
 * forward slashes. Resolved from INVISIO_DIR so it still works after renaming
 * the theme folder.
 *
 * @param string[] $exclude_filters Paths already flagged for exclusion.
 * @return string[]
 */
function invisio_ai1wm_exclude_dev_dirs( $exclude_filters ) {
	$relative = ltrim(
		str_replace( wp_normalize_path( WP_CONTENT_DIR ), '', wp_normalize_path( INVISIO_DIR ) ),
		'/'
	);

	foreach ( array( 'node_modules', '.git' ) as $dir ) {
		$exclude_filters[] = $relative . '/' . $dir;
	}

	return $exclude_filters;
}
add_filter( 'ai1wm_exclude_content_from_export', 'invisio_ai1wm_exclude_dev_dirs' );
