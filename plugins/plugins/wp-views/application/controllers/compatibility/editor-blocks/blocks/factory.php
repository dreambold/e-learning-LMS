<?php
namespace OTGS\Toolset\Views\Controller\Compatibility\EditorBlocks;

/**
 * Views editor (Gutenberg) Blocks factory class.
 *
 * @since 2.6.0
 * @since 2.7.0 Moved here from Toolset Common.
 */
class ViewsEditorBlockFactory {
	/**
	 * Get the Toolset Views editor (Gutenberg) Block.
	 *
	 * @param string $block The name of the block.
	 *
	 * @return null|ContentTemplate\Block|CustomHTML\BlockExtension|Paragraph\BlockExtension|View\Block
	 */
	public function get_block( $block ) {
		$return_block = null;

		switch ( $block ) {
			case View\Block::BLOCK_NAME:
				$return_block = new View\Block();
				break;
			case ContentTemplate\Block::BLOCK_NAME:
				$return_block = new ContentTemplate\Block();
				break;
			case CustomHTML\BlockExtension::BLOCK_NAME:
				$return_block = new CustomHTML\BlockExtension();
				break;
			case Paragraph\BlockExtension::BLOCK_NAME:
				$return_block = new Paragraph\BlockExtension();
				break;
		}

		return $return_block;
	}
}
