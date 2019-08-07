<?php
/**
 * The Content Sidebar
 */
?>
	<!-- RIGHT SIDE -> SIDEBAR-->
	<aside id="right-sidebar" role="complementary">

		<?php
        /**
         * You can override the slug of the sidebar loaded
         *
         * @param string $slug
         */
        $sidebar_slug = apply_filters('woffice_override_content_sidebar', 'content');

        dynamic_sidebar( $sidebar_slug ); ?>

	</aside>