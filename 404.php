<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Invisio
 */

get_header();
?>

	<main id="primary" class="site-main">
		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'That page can&rsquo;t be found', 'invisio' ); ?></h1>
			</header><!-- .page-header -->

			<div class="page-content">
				<p><?php esc_html_e( 'Nothing was found at this location. Try a search, or head back to the homepage.', 'invisio' ); ?></p>
				<?php get_search_form(); ?>
			</div><!-- .page-content -->
		</section><!-- .error-404 -->
	</main><!-- #primary -->

<?php
get_footer();
