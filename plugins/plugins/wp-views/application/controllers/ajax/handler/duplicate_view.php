<?php

/**
 * Duplicate a View
 *
 * @since 1.3
 * @since 2.8 Moved the callback here.
 * @see WPV_View::duplicate
 */
class WPV_Ajax_Handler_Duplicate_View extends Toolset_Ajax_Handler_Abstract {

	public function process_call( $arguments ) {
		$ajax_manager = $this->get_ajax_manager();

		$ajax_manager->ajax_begin(
			array(
				'nonce' => WPV_Ajax::CALLBACK_DUPLICATE_VIEW,
			)
		);

		$post_id = (int) toolset_getpost( 'id', 0 );
		$post_name = sanitize_text_field( toolset_getpost( 'name', '' ) );

		if (
			0 == $post_id
			|| empty( $post_name )
		) {
			$data = array(
				'message' => __( 'Wrong data', 'wpv-views' ),
			);
			$ajax_manager->ajax_finish(
				$data,
				false
			);
			return;
		}

		if ( WPV_View_Base::is_name_used( $post_name ) ) {
			$data = array(
				'message' => __( 'A View with that name already exists. Please use another name.', 'wpv-views' ),
			);
			$ajax_manager->ajax_finish(
				$data,
				false
			);
			return;
		}

		// Get the original View.
		$original_view = WPV_View::get_instance( $post_id );

		if ( null === $original_view ) {
			$data = array(
				'message' => __( 'Wrong data', 'wpv-views' ),
			);
			$ajax_manager->ajax_finish(
				$data,
				false
			);
			return;
		}

		$duplicate_view_id = $original_view->duplicate( $post_name );

		if ( $duplicate_view_id ) {
			// New View id
			$ajax_manager->ajax_finish(
				array(),
				true
			);
			return;
		} else {
			$data = array(
				'message' => __( 'Unexpected error', 'wpv-views' )
			);
			$ajax_manager->ajax_finish(
				$data,
				false
			);
			return;
		}

	}

}
