<?php
namespace OTGS\Toolset\Views\Controller\Compatibility\EditorBlocks\Paragraph;

/**
 * Handles the extension of the core Paragraph editor (Gutenberg) block to include the button for the
 * Fields and Views shortcodes.
 *
 * @since 2.6.0
 * @since 2.7.0 Moved here from Toolset Common.
 */
class BlockExtension {
	const BLOCK_NAME = 'toolset/paragraph';

	/**
	 * Initializes the hooks for the extension of the core Paragraph block.
	 */
	public function init_hooks() {
		add_action( 'toolset_filter_extend_the_core_paragraph_block', array( $this, 'extend_the_core_paragraph_block' ) );
	}

	/**
	 * Filter "toolset_filter_extend_the_core_paragraph_block" callback.
	 *
	 * Provides the extension information needed for the Paragraph block to be extended with a Fields and Views button.
	 *
	 * @param array $block_buttons The buttons array that will be used to extend the toolbar of the Paragraph block.
	 *
	 * @return mixed
	 */
	public function extend_the_core_paragraph_block( $block_buttons ) {
		$block_buttons['views'] = array(
			'clickCallback' => 'window.WPViews.shortcodes_gui.open_fields_and_views_dialog',
		);

		return $block_buttons;
	}
}
