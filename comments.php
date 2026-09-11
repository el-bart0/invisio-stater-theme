<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both the current comments and the comment
 * form.
 *
 * @package Invisio
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">
	<?php
	if ( have_comments() ) :
		?>
		<h2 class="comments-title">
			<?php
			$invisio_comment_count = get_comments_number();

			if ( '1' === (string) $invisio_comment_count ) {
				printf(
					/* translators: %s: post title. */
					esc_html__( 'One thought on &ldquo;%s&rdquo;', 'invisio' ),
					'<span>' . esc_html( get_the_title() ) . '</span>'
				);
			} else {
				printf(
					/* translators: 1: comment count number, 2: post title. */
					esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $invisio_comment_count, 'comments title', 'invisio' ) ),
					esc_html( number_format_i18n( $invisio_comment_count ) ),
					'<span>' . esc_html( get_the_title() ) . '</span>'
				);
			}
			?>
		</h2><!-- .comments-title -->

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol><!-- .comment-list -->

		<?php
		the_comments_navigation();

		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'invisio' ); ?></p>
			<?php
		endif;

	endif;

	comment_form();
	?>
</div><!-- #comments -->
