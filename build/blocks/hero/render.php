<?php
/**
 * Hero block render template.
 *
 * @param array    $block      The block settings and attributes.
 * @param string   $content    The block inner HTML (empty here — no InnerBlocks).
 * @param bool     $is_preview True during backend preview render.
 * @param int|string $post_id  The post ID this block is saved to.
 *
 * @package Invisio
 */

$invisio_eyebrow = (string) get_field( 'eyebrow' );
$invisio_heading = (string) get_field( 'heading' );
$invisio_text    = (string) get_field( 'text' );
$invisio_cta     = get_field( 'cta' );
$invisio_bg      = get_field( 'background_image' );
$invisio_layout  = get_field( 'layout' );
$invisio_layout  = $invisio_layout ? $invisio_layout : 'left';

if ( '' === $invisio_heading && ! empty( $is_preview ) ) {
	$invisio_heading = __( 'Hero block — add a heading in the sidebar', 'invisio' );
}

$invisio_classes = array(
	'invisio-hero',
	'invisio-hero--' . sanitize_html_class( $invisio_layout ),
);

if ( ! empty( $block['className'] ) ) {
	$invisio_classes[] = $block['className'];
}

if ( ! empty( $block['align'] ) ) {
	$invisio_classes[] = 'align' . $block['align'];
}

if ( is_array( $invisio_bg ) && ! empty( $invisio_bg['url'] ) ) {
	$invisio_classes[] = 'invisio-hero--has-bg';
}

$invisio_anchor = ! empty( $block['anchor'] ) ? $block['anchor'] : '';
?>
<section
	<?php if ( $invisio_anchor ) : ?>id="<?php echo esc_attr( $invisio_anchor ); ?>" <?php endif; ?>
	class="<?php echo esc_attr( implode( ' ', $invisio_classes ) ); ?>"
>
	<?php if ( is_array( $invisio_bg ) && ! empty( $invisio_bg['url'] ) ) : ?>
		<img
			class="invisio-hero__bg"
			src="<?php echo esc_url( $invisio_bg['url'] ); ?>"
			alt="<?php echo esc_attr( isset( $invisio_bg['alt'] ) ? $invisio_bg['alt'] : '' ); ?>"
			loading="lazy"
		/>
	<?php endif; ?>

	<div class="invisio-hero__inner">
		<?php if ( '' !== $invisio_eyebrow ) : ?>
			<p class="invisio-hero__eyebrow"><?php echo esc_html( $invisio_eyebrow ); ?></p>
		<?php endif; ?>

		<?php if ( '' !== $invisio_heading ) : ?>
			<h2 class="invisio-hero__heading"><?php echo esc_html( $invisio_heading ); ?></h2>
		<?php endif; ?>

		<?php if ( '' !== $invisio_text ) : ?>
			<div class="invisio-hero__text"><?php echo wp_kses_post( wpautop( $invisio_text ) ); ?></div>
		<?php endif; ?>

		<?php
		if ( is_array( $invisio_cta ) && ! empty( $invisio_cta['url'] ) ) :
			$invisio_cta_target = ! empty( $invisio_cta['target'] ) ? $invisio_cta['target'] : '';
			$invisio_cta_label  = ! empty( $invisio_cta['title'] ) ? $invisio_cta['title'] : __( 'Learn more', 'invisio' );
			?>
			<p class="invisio-hero__actions">
				<a
					class="invisio-hero__cta"
					href="<?php echo esc_url( $invisio_cta['url'] ); ?>"
					<?php if ( $invisio_cta_target ) : ?>
						target="<?php echo esc_attr( $invisio_cta_target ); ?>" rel="noopener"
					<?php endif; ?>
				>
					<?php echo esc_html( $invisio_cta_label ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</section>
