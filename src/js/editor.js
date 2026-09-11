/**
 * Invisio block-editor entry point.
 *
 * Imports editor styles so @wordpress/scripts extracts them to build/editor.css,
 * which is loaded into the editor via add_editor_style() in inc/setup.php. This
 * file carries no JS; the empty script bundle is dropped from the build output.
 */
import '../scss/editor.scss';
