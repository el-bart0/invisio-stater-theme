<?php
/**
 * The template for displaying singular content when no more specific template
 * (single.php, page.php, single-{post_type}.php) applies — e.g. custom post
 * type single views.
 *
 * @package Invisio
 */

get_header();
?>

	<main id="primary" class="site-main">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content/content', 'single' );

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>
	</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
