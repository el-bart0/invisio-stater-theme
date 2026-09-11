<?php
/**
 * Template part shown when the loop returns no posts.
 *
 * @package Invisio
 */

?>
<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing found', 'invisio' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: %s: URL of the WP admin "new post" screen. */
					__( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'invisio' ),
					array(
						'a' => array( 'href' => array() ),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>
			<p><?php esc_html_e( 'Sorry, nothing matched your search terms. Try again with different keywords.', 'invisio' ); ?></p>
			<?php
			get_search_form();

		else :
			?>
			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps a search will help.', 'invisio' ); ?></p>
			<?php
			get_search_form();

		endif;
		?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
