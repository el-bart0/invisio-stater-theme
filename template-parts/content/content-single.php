<?php
/**
 * Template part for displaying a single post or custom post type entry.
 *
 * @package Invisio
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta">
				<?php
				invisio_posted_on();
				invisio_posted_by();
				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php invisio_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses_post(
					/* translators: %s: post title. Only visible to screen readers. */
					__( 'Continue reading<span class="screen-reader-text"> &ldquo;%s&rdquo;</span>', 'invisio' )
				),
				wp_kses_post( get_the_title() )
			)
		);

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'invisio' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php invisio_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
