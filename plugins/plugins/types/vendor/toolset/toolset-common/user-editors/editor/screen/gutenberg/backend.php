<?php

/**
 * Backend Editor class for Gutenberg.
 *
 * Handles all the functionality needed to allow Gutenberg to work with Content Template editing on the backend.
 *
 * @since 2.5.9
 */
class Toolset_User_Editors_Editor_Screen_Gutenberg_Backend
	extends Toolset_User_Editors_Editor_Screen_Abstract {

	public function initialize() {
		parent::initialize();

		add_action( 'init', array( $this, 'register_assets' ), 50 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_assets' ), 50 );

		add_filter( 'toolset_filter_toolset_registered_user_editors', array( $this, 'register_user_editor' ) );
		add_filter( 'wpv_filter_wpv_layout_template_extra_attributes', array( $this, 'layout_template_attribute' ), 10, 2 );

		add_action( 'wpv_action_wpv_ct_inline_user_editor_buttons', array( $this, 'register_inline_editor_action_buttons' ) );

		// Priority 100 is selected here to prevent Fusion builder from disabling the new editor (Gutenberg) which is basically
		// done in priority 99.
		// This filter is only included in the Gutenberg plugin.
		add_filter( 'gutenberg_can_edit_post', array( $this, 'enable_gutenberg_for_this_content_template' ), 100, 2 );
		// This filter is only included in the core.
		add_filter( 'use_block_editor_for_post', array( $this, 'enable_gutenberg_for_this_content_template' ), 100, 2 );

		add_action( 'enqueue_block_editor_assets', array( $this, 'register_assets_for_gutenberg_compatibility' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'remove_divi_gutenberg_dependencies' ) );
	}

	/**
     * Check if current editor is active.
     *
	 * @return bool
     *
     * @refactoring Change the name of the following function as it is confusing.
     *              Warning!!! This has to be changed for all editors, otherwise it will break the editors integration.
	 */
	public function is_active() {
		if ( ! $this->set_medium_as_post() ) {
			return false;
		}

		$this->action();

		return true;
	}

	private function action() {
		add_action( 'admin_enqueue_scripts', array( $this, 'action_enqueue_assets' ) );
		$this->medium->set_html_editor_backend( array( $this, 'html_output' ) );
		$this->medium->page_reload_after_backend_save();
	}

	public function register_assets() {
		// Content Template own edit screen assets
		$this->assets_manager->register_style(
			'toolset-user-editors-gutenberg-style',
			$this->constants->constant( 'TOOLSET_COMMON_URL' ) . '/user-editors/editor/screen/gutenberg/backend.css',
			array(),
			$this->constants->constant( 'TOOLSET_COMMON_VERSION' )
		);

		$this->assets_manager->register_style(
			'toolset-user-editors-gutenberg-editor-style',
			$this->constants->constant( 'TOOLSET_COMMON_URL' ) . '/user-editors/editor/screen/gutenberg/backend_editor.css',
			array(),
			$this->constants->constant( 'TOOLSET_COMMON_VERSION' )
		);

		// Native post editor screen assets

		$this->assets_manager->register_script(
			'toolset-user-editors-gutenberg-script',
			$this->constants->constant( 'TOOLSET_COMMON_URL' ) . '/user-editors/editor/screen/gutenberg/backend_editor.js',
			array( 'jquery' ),
			$this->constants->constant( 'TOOLSET_COMMON_VERSION' ),
			true
		);

		$ct_id = (int) toolset_getget( 'post', 0 );
		$gutenberg_script_i18n = array(
			'doneEditingNoticeText' => __( 'Done editing here? Return to the', 'wpv-views' ),
			'doneEditingNoticeActionUrl' => admin_url( 'admin.php?page=ct-editor&ct_id=' . $ct_id ),
			'doneEditingNoticeActionText' => __( 'Toolset Content Template editor.', 'wpv-views' ),
		);

		$this->assets_manager->localize_script(
			'toolset-user-editors-gutenberg-script',
			'toolset_user_editors_gutenberg_script_i18n',
			$gutenberg_script_i18n
		);

		// Content Template as inline object assets
		$this->assets_manager->register_script(
			'toolset-user-editors-gutenberg-layout-template-script',
			$this->constants->constant( 'TOOLSET_COMMON_URL' ) . '/user-editors/editor/screen/gutenberg/backend_layout_template.js',
			array( 'jquery', 'views-layout-template-js', 'underscore' ),
			$this->constants->constant( 'TOOLSET_COMMON_VERSION' ),
			true
		);

		$gutenberg_layout_template_i18n = array(
			'template_editor_url' => admin_url( 'admin.php?page=ct-editor' ),
			'template_overlay' => array(
				'title' => sprintf( __( 'You created this template using %1$s', 'wpv-views' ), $this->editor->get_name() ),
				'button' => sprintf( __( 'Edit with %1$s', 'wpv-views' ), $this->editor->get_name() ),
				'discard' => sprintf( __( 'Stop using %1$s for this Content Template', 'wpv-views' ), $this->editor->get_name() ),
			),
		);

		$this->assets_manager->localize_script(
			'toolset-user-editors-gutenberg-layout-template-script',
			'toolset_user_editors_gutenberg_layout_template_i18n',
			$gutenberg_layout_template_i18n
		);
	}

	public function admin_enqueue_assets() {
		if ( $this->is_views_or_wpa_edit_page() ) {
			do_action( 'toolset_enqueue_scripts', array( 'toolset-user-editors-gutenberg-layout-template-script' ) );
		}
	}

	public function action_enqueue_assets() {
		do_action( 'toolset_enqueue_styles', array( 'toolset-user-editors-gutenberg-style' ) );
	}

	private function set_medium_as_post() {
		$medium_id  = $this->medium->get_id();

		if ( ! $medium_id ) {
			return false;
		}

		$medium_post_object = get_post( $medium_id );
		if ( null === $medium_post_object ) {
			return false;
		}

		$this->post = $medium_post_object;

		return true;
	}

	public function register_user_editor( $editors ) {
		$editors[ $this->editor->get_id() ] = $this->editor->get_name();
		return $editors;
	}

	/**
	 * Content Template editor output.
	 *
	 * Displays the Native Editor message and button to fire it up.
	 *
	 * @since 2.5.1
	 */
	public function html_output() {

		if ( ! isset( $_GET['ct_id'] ) ) {
			return 'No valid content template id';
		}

		ob_start();
		include_once( dirname( __FILE__ ) . '/backend.phtml' );
		$output = ob_get_contents();
		ob_end_clean();

		$admin_url = admin_url( 'admin.php?page=ct-editor&ct_id=' . esc_attr( $_GET['ct_id'] ) );
		$output .= '<p>'
				   . sprintf(
					   __( '%1$sStop using %2$s for this Content Template%3$s', 'wpv-views' ),
					   '<a href="' . esc_url( $admin_url ) . '&ct_editor_choice=basic">',
					   $this->editor->get_name(),
					   '</a>'
				   )
				   . '</p>';

		return $output;
	}

	public function register_inline_editor_action_buttons( $content_template ) {
		?>
		<button
			class="button button-secondary js-wpv-ct-apply-user-editor js-wpv-ct-apply-user-editor-<?php echo esc_attr( $this->editor->get_id() ); ?>"
			data-editor="<?php echo esc_attr( $this->editor->get_id() ); ?>"
			<?php disabled( $this->maybe_ct_is_built_with_gutenberg( $content_template->ID ) ); ?>
		>
			<?php echo esc_html( $this->editor->get_name() ); ?>
		</button>
		<?php
	}

	/**
	 * Set the builder used by a Content Template, if any.
	 *
	 * On a Content Template used inside a View or WPA loop output, we set which builder it is using
	 * so we can link to the CT edit page with the right builder instantiated.
	 *
	 * @param array   $attributes
	 * @param WP_POST $content_template
	 *
	 * @return array
	 *
	 * @since 2.5.1
	 */
	public function layout_template_attribute( $attributes, $content_template ) {
		if ( $this->maybe_ct_is_built_with_gutenberg( $content_template->ID ) ) {
			$attributes['builder'] = $this->editor->get_id();
		}
		return $attributes;
	}

	public function register_assets_for_gutenberg_compatibility() {
		if ( $this->maybe_ct_is_built_with_gutenberg() ) {
			do_action( 'toolset_enqueue_scripts', array( 'toolset-user-editors-gutenberg-script' ) );
			do_action( 'toolset_enqueue_styles', array( 'toolset-user-editors-gutenberg-editor-style' ) );
		}
	}

	/**
	 * See "Toolset_User_Editors_Editor_Screen_Abstract::maybe_ct_is_built_with_editor".
	 *
	 * @param int $ct_id
	 *
	 * @return bool
	 */
	public function maybe_ct_is_built_with_gutenberg( $ct_id = null ) {
		if ( null !== $ct_id ) {
			return parent::maybe_ct_is_built_with_editor( $ct_id );
		}

		global $post;
		if (
			$post &&
			$post instanceof \WP_Post
		) {
			return parent::maybe_ct_is_built_with_editor( $post->ID );
		}

		return false;
	}

	/**
	 * Re-enable the new editor (Gutenberg) for this Content Template, if it uses Gutenberg as a Content Template builder.
	 *
	 * @param bool    $is_enabled The status of the new editor (Gutenberg) for the selected post type.
	 * @param WP_Post $post       The selected post type.
	 *
	 * @return bool
	 */
	public function enable_gutenberg_for_this_content_template( $is_enabled, $post ) {
		if (
			'view-template' === $post->post_type &&
			$this->maybe_ct_is_built_with_gutenberg( $post->ID )
		) {
			return true;
		}

		return $is_enabled;
	}

	/**
	 * Removes the Divi dependencies for Gutenberg that are responsible for adding the "Edit with Divi Builder" button
	 * on the top bar of the new editor (Gutenberg).
	 */
	public function remove_divi_gutenberg_dependencies() {
		if (
			$this->maybe_ct_is_built_with_gutenberg()
		) {
			wp_dequeue_script( 'et-builder-gutenberg' );
		}
	}
}
