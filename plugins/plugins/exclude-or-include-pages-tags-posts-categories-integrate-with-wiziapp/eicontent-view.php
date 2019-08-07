<?php if ( ! defined( 'EICONTENT_EXCLUDE_PATH' ) ) exit( "Can not be called directly." );

	class EIContent_View {

		public static function print_checkboxes($checked_array) {
		?>
		<div>
			<input type="checkbox" name="wizi_included_site" <?php echo $checked_array['wizi_included_site']; ?> />
			Display on your website
		</div>
		<div>
			<input type="hidden" name="wiziapp_ctrl_present" />
			<input type="checkbox" name="wizi_included_app" <?php echo $checked_array['wizi_included_app']; ?> />
			Display on your <a href="http://www.wiziapp.com/">mobile App</a>
		</div></br>
		<?php
		}

	}

?>