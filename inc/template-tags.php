<?php
/**
 * Custom template tags for this theme.
 *
 * Output helpers used by templates and template parts.
 *
 * @package Invisio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'invisio_posted_on' ) ) :
	/**
	 * Print HTML with meta information for the current post date/time.
	 */
	function invisio_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		printf(
			'<span class="posted-on"><a href="%1$s" rel="bookmark">%2$s</a></span>',
			esc_url( get_permalink() ),
			$time_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
endif;

if ( ! function_exists( 'invisio_posted_by' ) ) :
	/**
	 * Print HTML with meta information for the current author.
	 */
	function invisio_posted_by() {
		printf(
			'<span class="byline"> %1$s <span class="author vcard"><a class="url fn n" href="%2$s">%3$s</a></span></span>',
			esc_html_x( 'by', 'post author', 'invisio' ),
			esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}
endif;

if ( ! function_exists( 'invisio_entry_footer' ) ) :
	/**
	 * Print HTML with meta information for categories, tags and comments.
	 */
	function invisio_entry_footer() {
		if ( 'post' === get_post_type() ) {
			$categories_list = get_the_category_list( esc_html__( ', ', 'invisio' ) );
			if ( $categories_list ) {
				printf(
					/* translators: 1: list of categories. */
					'<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'invisio' ) . '</span>',
					$categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'invisio' ) );
			if ( $tags_list ) {
				printf(
					/* translators: 1: list of tags. */
					'<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'invisio' ) . '</span>',
					$tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title. */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'invisio' ),
						array(
							'span' => array( 'class' => array() ),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: post title. Only visible to screen readers. */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'invisio' ),
					array(
						'span' => array( 'class' => array() ),
					)
				),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'invisio_post_thumbnail' ) ) :
	/**
	 * Display an optional post thumbnail.
	 *
	 * Wraps the thumbnail in a link on lists; plain on singular views.
	 */
	function invisio_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>
			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div>
			<?php
		else :
			?>
			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
				the_post_thumbnail(
					'post-thumbnail',
					array(
						'alt' => the_title_attribute(
							array( 'echo' => false )
						),
					)
				);
				?>
			</a>
			<?php
		endif;
	}
endif;
