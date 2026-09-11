<?php
/**
 * Functions that hook into WordPress to tweak core markup and behaviour.
 *
 * @package Invisio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom classes to the array of body classes.
 *
 * @param string[] $classes Existing body classes.
 * @return string[] Filtered body classes.
 */
function invisio_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'invisio_body_classes' );

/**
 * Add a pingback URL auto-discovery header for singular content.
 */
function invisio_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'invisio_pingback_header' );
