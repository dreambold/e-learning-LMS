<?php

/**
 * Main shortcodes controller for Views.
 *
 * @since 2.5.0
 */
final class WPV_Shortcodes {

	public function initialize() {

		$relationship_service = new Toolset_Relationship_Service();
		$attr_item_chain = new Toolset_Shortcode_Attr_Item_M2M(
			new Toolset_Shortcode_Attr_Item_Legacy(
				new Toolset_Shortcode_Attr_Item_Id(),
				$relationship_service
			),
			$relationship_service
		);

		$factory = new WPV_Shortcode_Factory( $attr_item_chain );

		$post_shortcodes = array(
			WPV_Shortcode_Post_Title::SHORTCODE_NAME,
			WPV_Shortcode_Post_Link::SHORTCODE_NAME,
			WPV_Shortcode_Post_Url::SHORTCODE_NAME,
			WPV_Shortcode_Post_Body::SHORTCODE_NAME,
			WPV_Shortcode_Post_Excerpt::SHORTCODE_NAME,
			WPV_Shortcode_Post_Read_More::SHORTCODE_NAME,
			WPV_Shortcode_Post_Date::SHORTCODE_NAME,
			WPV_Shortcode_Post_Author::SHORTCODE_NAME,
			WPV_Shortcode_Post_Featured_Image::SHORTCODE_NAME,
			WPV_Shortcode_Post_Id::SHORTCODE_NAME,
			WPV_Shortcode_Post_Slug::SHORTCODE_NAME,
			WPV_Shortcode_Post_Type::SHORTCODE_NAME,
			WPV_Shortcode_Post_Format::SHORTCODE_NAME,
			WPV_Shortcode_Post_Status::SHORTCODE_NAME,
			WPV_Shortcode_Post_Comments_Number::SHORTCODE_NAME,
			WPV_Shortcode_Post_Class::SHORTCODE_NAME,
			WPV_Shortcode_Post_Edit_Link::SHORTCODE_NAME,
			WPV_Shortcode_Post_Menu_Order::SHORTCODE_NAME,
			WPV_Shortcode_Post_Field::SHORTCODE_NAME,
			WPV_Shortcode_Post_Field_Iterator::SHORTCODE_NAME_ALIAS,
			WPV_Shortcode_Post_Next_Link::SHORTCODE_NAME,
			WPV_Shortcode_Post_Previous_Link::SHORTCODE_NAME,
			WPV_Shortcode_Post_Taxonomy::SHORTCODE_NAME,

			WPV_Shortcode_Control_Post_Relationship::SHORTCODE_NAME,
			WPV_Shortcode_Control_Post_Relationship::SHORTCODE_NAME_ALIAS,
			WPV_Shortcode_Control_Post_Ancestor::SHORTCODE_NAME,
			WPV_Shortcode_Control_Post_Ancestor::SHORTCODE_NAME_ALIAS,

			WPV_Shortcode_WPML_Conditional::SHORTCODE_NAME,

			WPV_Shortcode_Loop_Index::SHORTCODE_NAME,

		);

		foreach ( $post_shortcodes as $shortcode_string ) {
			if ( $shortcode = $factory->get_shortcode( $shortcode_string ) ) {
				add_shortcode( $shortcode_string, array( $shortcode, 'render' ) );
			};
		}

		// Initialize the WPV_Views_Conditional::SHORTCODE_NAME shortcode
		$toolset_common_bootstrap = Toolset_Common_Bootstrap::getInstance();
		$wpv_conditional = new WPV_Views_Conditional( $attr_item_chain, $toolset_common_bootstrap );
		$wpv_conditional->initialize();

	}

}
