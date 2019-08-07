<?php

/**
 * Handles AJAX calls to get the view block preview.
 *
 * @since m2m
 */
class WPV_Ajax_Handler_Get_View_Block_Preview extends Toolset_Ajax_Handler_Abstract {
	/** @var Toolset_Constants|null */
	private $constants;

	/** @var Toolset_Renderer|null */
	private $toolset_renderer;

	/** @var WP_Views_plugin */
	private $wp_views;

	/**
	 * WPV_Ajax_Handler_Get_View_Block_Preview constructor.
	 *
	 * @param \WPV_ajax                                $ajax_manager
	 * @param \Toolset_Constants                  $constants
	 * @param \Toolset_Renderer                   $toolset_renderer
	 * @param \Toolset_Output_Template_Repository $template_repository
	 * @param \WP_Views_plugin                           $wp_views
	 */
	public function __construct(
		\WPV_ajax $ajax_manager,
		\Toolset_Constants $constants,
		\Toolset_Renderer $toolset_renderer,
		\Toolset_Output_Template_Repository $template_repository,
		\WP_Views_plugin $wp_views
	) {
		parent::__construct( $ajax_manager );

		$this->constants = $constants;
		$this->toolset_renderer = $toolset_renderer;
		$this->template_repository = $template_repository;

		$this->wp_views = $wp_views;
	}

	/**
	 * Processes the AJAX call.
	 *
	 * @param array $arguments Original action arguments.
	 *
	 * @return void
	 *
	 * @throws Twig_Error_Loader
	 * @throws Twig_Error_Runtime
	 * @throws Twig_Error_Syntax
	 */
	public function process_call( $arguments ) {
		$this->ajax_begin(
			array(
				'nonce' => \WPV_Ajax::CALLBACK_GET_VIEW_BLOCK_PREVIEW,
				'is_public' => true,
			)
		);

		$view_id = isset( $_POST['view_id'] ) ? sanitize_text_field( $_POST['view_id'] ) : '';

		if ( empty( $view_id ) ) {
			$this->ajax_finish( array( 'message' => __( 'View ID not set.', 'wpv-views' ) ), false );
		}

		$view = WPV_View_Base::get_instance( $view_id );
		if ( null !== $view ) {
			$limit = sanitize_text_field( toolset_getpost( 'limit', -1 ) );
			$offset = sanitize_text_field( toolset_getpost( 'offset', 0 ) );
			$orderby = sanitize_text_field( toolset_getpost( 'orderby', '' ) );
			$order = sanitize_text_field( toolset_getpost( 'order', '' ) );
			$secondary_order_by = sanitize_text_field( toolset_getpost( 'secondaryOrderby', '' ) );
			$secondary_order = sanitize_text_field( toolset_getpost( 'secondaryOrder', '' ) );

			$view_settings = null !== $view ? $view->view_settings : null;
			$view_meta = null !== $view ? $view->loop_settings : null;

			$has_parametric_search = $this->wp_views->does_view_have_form_controls( $view_id );
			$has_submit = $this->wp_views->does_view_have_form_control_with_submit( $view_id );
			$has_extra_attributes = get_view_allowed_attributes( $view_id );

			$view_purpose = '';

			if ( $view->is_a_view() ) {
				$view_output = get_view_query_results(
					$view_id,
					null,
					null,
					array(
						'limit' => $limit,
						'offset' => $offset,
						'orderby' => $orderby,
						'order' => $order,
						'orderby_second' => $secondary_order_by,
						'order_second' => $secondary_order,
					)
				);
				if ( ! isset( $view_settings['view_purpose'] ) ) {
					$view_settings['view_purpose'] = 'full';
				}
				switch ( $view_settings['view_purpose'] ) {
					case 'all':
						$view_purpose = __( 'Display all results', 'wpv-views' );
						break;

					case 'pagination':
						$view_purpose = __( 'Display the results with pagination', 'wpv-views' );
						break;

					case 'slider':
						$view_purpose = __( 'Display the results as a slider', 'wpv-views' );
						break;

					case 'parametric':
						$view_purpose = __( 'Custom search', 'wpv-views' );
						break;
					case 'full':
						$view_purpose = __( 'Displays a fully customized display', 'wpv-views' );
						break;
				}
			} else {
				$view_output = array();

				if (
					'bootstrap-grid' === $view_meta['style']
					|| 'table' === $view_meta['style']
				) {
					if ( 'bootstrap-grid' === $view_meta['style'] ) {
						$col_number = $view_meta['bootstrap_grid_cols'];
					} else {
						$col_number = $view_meta['table_cols'];
					}

					// add 2 rows of items.
					for ( $i = 1; $i <= 2 * $col_number; $i++ ) {
						$item = new stdClass();
						$item->post_title = sprintf( __( 'Post %d', 'wp-views' ), $i );
						$view_output[] = $item;
					}
				} else {
					// just add 3 items
					for ( $i = 1; $i <= 3; $i++ ) {
						$item = new stdClass();
						$item->post_title = sprintf( __( 'Post %d', 'wp-views' ), $i );
						$view_output[] = $item;
					}
				}
			}

			$output = array(
				'view_id' => $view_id,
				'view_title' => null !== $view ? $view->title : '',
				'view_purpose' => $view_purpose,
				'view_meta' => $view_meta,
				'view_output' => $view_output,
				'hasCustomSearch' => $has_parametric_search,
				'hasSubmit' => $has_submit,
				'hasExtraAttributes' => $has_extra_attributes,
				'overlay' => $this->render_view_block_overlay( $view->id, $view->title ),
			);

			$this->ajax_finish( $output, true );
		}

		$this->ajax_finish( array( 'message' => sprintf( __( 'Error while retrieving the View preview. The selected View (ID: %s) was not found.', 'wpv-views' ), $view_id ) ), false );
	}

	/**
	 * Renders the Toolset View Gutenberg block overlay for the block preview on the editor.
	 *
	 * @param string $view_id    The ID of the selected View.
	 * @param string $view_title The title of the selected View.
	 *
	 * @return bool|string
	 *
	 * @throws Twig_Error_Loader
	 * @throws Twig_Error_Runtime
	 * @throws Twig_Error_Syntax
	 */
	public function render_view_block_overlay( $view_id, $view_title ) {
		$renderer = $this->toolset_renderer;
		$context = array(
			'module_title' => $view_title,
			'module_type' => __( 'View', 'wpv-view' ),
		);

		// The edit link is only offered for users with proper permissions.
		if ( current_user_can( 'manage_options' ) ) {
			$context['edit_link'] = admin_url( 'admin.php?page=views-editor&view_id=' . $view_id );
		}

		$html = $renderer->render(
			$this->template_repository->get( $this->constants->constant( 'Toolset_Output_Template_Repository::PAGE_BUILDER_MODULES_OVERLAY' ) ),
			$context,
			false
		);

		return $html;
	}
}
