<?php
/**
 * The sidebar containing the main widget area.
 *
 * Renders nothing until a widget is added to the "Sidebar" area.
 *
 * @package Invisio
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area" aria-label="<?php esc_attr_e( 'Sidebar', 'invisio' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->
