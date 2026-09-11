<?php
/**
 * Block registration and ACF glue.
 *
 * - Auto-registers every compiled block found in build/blocks/*.
 * - Points ACF Local JSON at the theme's acf-json/ directory (load + save).
 * - Adds an "Invisio" block category.
 *
 * ACF PRO is required for these blocks to render. Without it, registration is
 * skipped and an admin notice is shown — nothing fatals.
 *
 * @package Invisio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Save new/edited ACF field groups into the theme's acf-json/ directory.
 *
 * @param string $path Default ACF save path.
 * @return string
 */
function invisio_acf_json_save_point( $path ) {
	unset( $path );
	return INVISIO_DIR . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'invisio_acf_json_save_point' );

/**
 * Load ACF field groups from the theme's acf-json/ directory.
 *
 * @param string[] $paths Existing ACF load paths.
 * @return string[]
 */
function invisio_acf_json_load_point( $paths ) {
	$paths[] = INVISIO_DIR . '/acf-json';
	return $paths;
}
add_filter( 'acf/settings/load_json', 'invisio_acf_json_load_point' );

/**
 * Register an "Invisio" block category for theme blocks.
 *
 * @param array[] $categories Registered block categories.
 * @return array[]
 */
function invisio_block_categories( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'invisio',
			'title' => __( 'Invisio', 'invisio' ),
			'icon'  => null,
		)
	);

	return $categories;
}
add_filter( 'block_categories_all', 'invisio_block_categories' );

/**
 * Auto-register every compiled block in build/blocks/.
 */
function invisio_register_blocks() {
	$blocks_dir = INVISIO_DIR . '/build/blocks';

	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	$manifests = glob( $blocks_dir . '/*/block.json' );

	if ( empty( $manifests ) ) {
		return;
	}

	if ( ! function_exists( 'acf_register_block_type' ) ) {
		add_action( 'admin_notices', 'invisio_acf_missing_notice' );
		return;
	}

	foreach ( $manifests as $manifest ) {
		register_block_type( dirname( $manifest ) );
	}
}
add_action( 'init', 'invisio_register_blocks' );

/**
 * Notice shown when the theme ships blocks but ACF PRO is not active.
 */
function invisio_acf_missing_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	?>
	<div class="notice notice-warning">
		<p>
			<?php
			esc_html_e(
				'Invisio ships ACF blocks, but Advanced Custom Fields PRO is not active. Install and activate it to use them.',
				'invisio'
			);
			?>
		</p>
	</div>
	<?php
}
