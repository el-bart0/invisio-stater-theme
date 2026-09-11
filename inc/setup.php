<?php
/**
 * Theme setup: supports, menus, image sizes, textdomain.
 *
 * @package Invisio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme features.
 */
function invisio_setup() {
	load_theme_textdomain( 'invisio', INVISIO_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	// Compiled editor styles (present after `npm run build`).
	add_editor_style( 'build/editor.css' );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 100,
			'width'       => 300,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary', 'invisio' ),
			'footer'  => __( 'Footer', 'invisio' ),
		)
	);
}
add_action( 'after_setup_theme', 'invisio_setup' );

/**
 * Register widget areas.
 */
function invisio_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'invisio' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Add widgets here.', 'invisio' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'invisio_widgets_init' );

/**
 * Set the content width.
 */
function invisio_content_width() {
	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	$GLOBALS['content_width'] = apply_filters( 'invisio_content_width', 800 );
}
add_action( 'after_setup_theme', 'invisio_content_width', 0 );
