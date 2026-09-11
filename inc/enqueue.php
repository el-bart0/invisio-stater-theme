<?php
/**
 * Enqueue front-end assets from the build directory.
 *
 * Assets are compiled by @wordpress/scripts to build/. Each entry has a
 * matching build/<entry>.asset.php with its dependencies and a content hash
 * used for cache busting. If the build directory is missing (before the first
 * `npm run build`), enqueuing is skipped and the theme still loads.
 *
 * @package Invisio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the main stylesheet and script.
 */
function invisio_enqueue_assets() {
	$meta = invisio_asset_meta( 'index' );

	if ( invisio_asset_exists( 'index.css' ) ) {
		wp_enqueue_style(
			'invisio-style',
			invisio_asset_uri( 'index.css' ),
			array(),
			$meta['version']
		);
	}

	if ( invisio_asset_exists( 'index.js' ) ) {
		wp_enqueue_script(
			'invisio-script',
			invisio_asset_uri( 'index.js' ),
			$meta['dependencies'],
			$meta['version'],
			array( 'in_footer' => true )
		);
	}

	if ( is_singular() && comments_open() && (bool) get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'invisio_enqueue_assets' );
