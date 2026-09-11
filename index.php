<?php
/**
 * The main template file — the fallback for every query WordPress can't match
 * to a more specific template, and the blog posts index.
 *
 * @package Invisio
 */

get_header();
?>

	<main id="primary" class="site-main">
		<?php
		if ( have_posts() ) :

			if ( is_home() && ! is_front_page() ) :
				?>
				<header class="page-header">
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>
				<?php
			endif;

			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/content', get_post_type() );
			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content/content', 'none' );

		endif;
		?>
	</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
