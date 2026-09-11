<?php
/**
 * Block editor lockdown.
 *
 * Invisio builds content exclusively from its own ACF blocks. This file limits
 * the inserter to those blocks, removes core block patterns, and drops the core
 * block library styles from the front end.
 *
 * @package Invisio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Restrict the inserter to Invisio's ACF blocks.
 *
 * Blocks already saved in existing content still render — they're only removed
 * from the inserter. Falls back to allowing everything if no Invisio blocks are
 * registered (e.g. ACF PRO inactive) so the editor is never left unusable.
 *
 * @param bool|string[]           $allowed_blocks Current allow list.
 * @param WP_Block_Editor_Context $context        Editor context.
 * @return bool|string[]
 */
function invisio_allowed_block_types( $allowed_blocks, $context ) {
	unset( $allowed_blocks, $context );

	$invisio_blocks = array();

	foreach ( WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $block_type ) {
		unset( $block_type );
		if ( str_starts_with( $name, 'acf/invisio-' ) ) {
			$invisio_blocks[] = $name;
		}
	}

	return empty( $invisio_blocks ) ? true : $invisio_blocks;
}
add_filter( 'allowed_block_types_all', 'invisio_allowed_block_types', 10, 2 );

/**
 * Remove core block patterns and the remote pattern directory.
 */
function invisio_remove_core_patterns() {
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'invisio_remove_core_patterns', 20 );

add_filter( 'should_load_remote_block_patterns', '__return_false' );

/**
 * Dequeue the core block library styles on the front end.
 *
 * Invisio's own blocks ship their own CSS, so wp-block-library / global styles
 * are dead weight on the front end. They stay enqueued in the editor.
 */
function invisio_dequeue_core_block_styles() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'invisio_dequeue_core_block_styles', 100 );

/**
 * Stop the separate global-styles <style> block (SVG filters / presets) from
 * printing on the front end.
 */
function invisio_remove_global_styles_actions() {
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
}
add_action( 'init', 'invisio_remove_global_styles_actions' );
