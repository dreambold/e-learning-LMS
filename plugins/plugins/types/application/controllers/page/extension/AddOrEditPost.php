<?php

namespace OTGS\Toolset\Types\Page\Extension;

/**
 * Page extension controller that loads on both Add and Edit Post pages.
 *
 * @since 3.2.2
 */
class AddOrEditPost {

	/** @var \Types_Asset_Manager */
	private $asset_manager;

	/** @var \Toolset_Post_Type_Repository */
	private $post_type_repository;


	/**
	 * AddOrEditPost constructor.
	 *
	 * @param \Types_Asset_Manager $asset_manager
	 * @param \Toolset_Post_Type_Repository $post_type_repository
	 */
	public function __construct( \Types_Asset_Manager $asset_manager, \Toolset_Post_Type_Repository $post_type_repository ) {
		$this->asset_manager = $asset_manager;
		$this->post_type_repository = $post_type_repository;
	}


	/**
	 * Initialize the page extension.
	 */
	public function initialize() {
		$post = wpcf_admin_get_edited_post();
		$post_type = wpcf_admin_get_edited_post_type( $post );

		$asset_manager = $this->asset_manager;
		$post_type_repository = $this->post_type_repository;
		add_action( 'admin_enqueue_scripts', function() use( $post_type, $asset_manager, $post_type_repository ) {
			$asset_manager->enqueue_scripts( array(
				// If there are no wp-components available, this will not load, but we don't mind because there's no block editor,
				// for which the script is intended.
				\Types_Asset_Manager::SCRIPT_POST_ADD_OR_EDIT,
				// This will load always.
				\Types_Asset_Manager::SCRIPT_POST_ADD_OR_EDIT_NO_COMPONENTS,
			) );
			$asset_manager->enqueue_styles( \Types_Asset_Manager::STYLE_POST_ADD_OR_EDIT );

			// Tell the script about the editor mode (it will decide whether to show the button to switch back to the classic editor).
			$post_type_model = $post_type_repository->get( $post_type );
			$editor_mode = ( null === $post_type_model ) ? '' : $post_type_model->get_editor_mode();
			wp_localize_script( \Types_Asset_Manager::SCRIPT_POST_ADD_OR_EDIT, 'types_post_add_or_edit_l10n', array(
				'editor_mode' => $editor_mode,
			) );
		} );

	}
}
