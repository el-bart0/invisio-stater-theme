<?php
/**
 * Invisio functions and definitions.
 *
 * Loads the PHP partials that make up the theme. Each concern lives in its own
 * file under inc/ and is required here (no autoloader, no Composer by design).
 *
 * @package Invisio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'INVISIO_VERSION', '0.1.0' );
define( 'INVISIO_DIR', get_template_directory() );
define( 'INVISIO_URI', get_template_directory_uri() );

/**
 * PHP partials, loaded in order.
 *
 * @var string[]
 */
$invisio_includes = array(
	'/inc/helpers.php',             // Small utilities used across the theme.
	'/inc/setup.php',               // Theme supports, menus, widgets, textdomain.
	'/inc/enqueue.php',             // Front-end + editor asset loading from build/.
	'/inc/template-tags.php',       // Reusable output helpers for templates.
	'/inc/template-functions.php',  // Hooks that tweak core markup/behaviour.
	'/inc/blocks.php',              // ACF block auto-registration + acf-json sync.
	'/inc/editor.php',              // Inserter allow list + core block library removal.
);

foreach ( $invisio_includes as $invisio_file ) {
	require_once INVISIO_DIR . $invisio_file;
}
unset( $invisio_file );
