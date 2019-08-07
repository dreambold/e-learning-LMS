<?php
namespace OTGS\Toolset\Views\Controller\Compatibility\EditorBlocks\View;

use \OTGS\Toolset\Views\Controller\Compatibility\EditorBlocks as ViewsEditorBlocks;

/**
 * Handles the creation of the Toolset View editor (Gutenberg) block to allow Views embedding inside the editor.
 *
 * @since 2.6.0
 * @since 2.7.0 Moved here from Toolset Common.
 */
class Block extends ViewsEditorBlocks\Base {
	const BLOCK_NAME = 'toolset/view';

	/**
	 * Initializes the hooks for the Content Template block.
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'register_block_editor_assets' ) );

		add_action( 'init', array( $this, 'register_block_type' ) );

		// Hook scripts function into block editor hook.
		add_action( 'enqueue_block_editor_assets', array( $this, 'blocks_editor_scripts' ) );
	}

	/**
	 * Register the needed assets for the Toolset View editor (Gutenberg) block.
	 *
	 * @since 2.6.0
	 */
	public function register_block_editor_assets() {
		$this->toolset_assets_manager->register_script(
			'toolset-view-block-js',
			$this->constants->constant( 'WPV_URL' ) . '/public/js/view.block.js',
			array( 'wp-editor' ),
			$this->constants->constant( 'WPV_VERSION' )
		);

		$locale = null;
		if ( function_exists( 'wp_get_jed_locale_data' ) ) {
			$locale = wp_get_jed_locale_data( 'wpv-views' );
		} elseif ( function_exists( 'gutenberg_get_jed_locale_data' ) ) {
			$locale = gutenberg_get_jed_locale_data( 'wpv-views' );
		} else {
			$locale = array(
				array(
					'domain' => 'wpv-views',
					'lang' => 'en_US',
				),
			);
		}

		wp_localize_script(
			'toolset-view-block-js',
			'toolset_view_block_strings',
			array(
				'blockName' => self::BLOCK_NAME,
				'blockCategory' => \Toolset_Blocks::TOOLSET_GUTENBERG_BLOCKS_CATEGORY_SLUG,
				'publishedViews' => apply_filters( 'wpv_get_available_views', array() ),
				'wpnonce' => wp_create_nonce( \WPV_Ajax::CALLBACK_GET_VIEW_BLOCK_PREVIEW ),
				'actionName' => $this->toolset_ajax_manager->get_action_js_name( \WPV_Ajax::CALLBACK_GET_VIEW_BLOCK_PREVIEW ),
				'locale' => $locale,
			)
		);

		$this->toolset_assets_manager->register_style(
			'toolset-view-block-editor-css',
			$this->constants->constant( 'WPV_URL' ) . '/public/css/view.block.css',
			array(),
			$this->constants->constant( 'WPV_VERSION' )
		);
	}

	/**
	 * Register block type. We can use this method to register the editor & frontend scripts as well as the render callback.
	 *
	 * @since 2.6.0
	 */
	public function register_block_type() {
		register_block_type(
			self::BLOCK_NAME,
			array(
				'attributes' => array(
					'view' => array(
						'type' => 'string',
						'default' => '',
					),
					'hasCustomSearch' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'hasSubmit' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'hasExtraAttributes' => array(
						'type' => 'array',
						'default' => array(),
						'items' => array(
							'type' => 'object',
						),
					),
					'formDisplay' => array(
						'type' => 'string',
						'default' => 'full',
					),
					'formOnlyDisplay' => array(
						'type' => 'string',
						'default' => 'samePage',
					),
					'otherPage' => array(
						'type' => 'object',
						'default' => '',
					),
					'limit' => array(
						'type' => 'integer',
						'default' => '-1',
					),
					'offset' => array(
						'type' => 'integer',
						'default' => '0',
					),
					'orderby' => array(
						'type' => 'string',
						'default' => '',
					),
					'order' => array(
						'type' => 'string',
						'default' => '',
					),
					'secondaryOrderby' => array(
						'type' => 'string',
						'default' => '',
					),
					'secondaryOrder' => array(
						'type' => 'string',
						'default' => '',
					),
					'queryFilters' => array(
						'type' => 'object',
						'default' => new \stdClass(),
					),
				),
				'editor_script' => 'toolset-view-block-js', // Editor script.
				'editor_style' => 'toolset-view-block-editor-css', // Editor style.
			)
		);
	}

	/**
	 * Enqueue assets, needed on the editor side, for the Toolset View editor (Gutenberg) blocks
	 *
	 * @since 2.6.0
	 */
	public function blocks_editor_scripts() {
		do_action( 'toolset_enqueue_styles', array( 'toolset-blocks-react-select-css' ) );
	}
}
