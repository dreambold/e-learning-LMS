<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(class_exists('fileaway_admin') && !class_exists('fileaway_notices'))
{
	class fileaway_notices extends fileaway_admin
	{
		public function __construct()
		{
			parent::__construct();
			add_action('admin_notices', array($this, 'baseconfig'));
			add_action('admin_init', array($this, 'baseignore'));
		}
		public function baseconfig(){
			global $pagenow;
			$uid = get_current_user_id();
    		if(!$this->options['base1'] || !$this->options['bs1name'])
			{
				if(!get_user_meta($uid, 'fileaway_dismiss_config_notice'))
				{
					$dismiss = add_query_arg('fileaway_dismiss_config_notice', '0');
					if($pagenow == 'plugins.php')
					{
						echo '<div class="updated"><p>';
						printf('File Away Notice: Your shortcode generator on the TinyMCE panel will not offer '.
							'full functionality until you assign your first Base Directory and give it a display name. '.
							'<a href="'.get_admin_url ().'admin.php?page=file-away">Get Started</a> | <a href="%1$s">Dismiss</a>', $dismiss);
		        		echo '</p></div>';
					}
					elseif(isset($_REQUEST['page']) && $_GET['page'] === 'file-away')
					{
						echo '<div class="updated"><p>';
						printf('File Away Notice: Your shortcode generator on the TinyMCE panel will not offer '.
							'full functionality until you assign your first Base Directory and give it a display name. '.
							'<a href="%1$s">Dismiss</a>', $dismiss);
		        		echo '</p></div>';
					}
				}
			}
		}
		public function baseignore(){
			$uid = get_current_user_id();
			if(isset($_GET['fileaway_dismiss_config_notice']) && '0' == $_GET['fileaway_dismiss_config_notice']) 
				add_user_meta($uid, 'fileaway_dismiss_config_notice', 'true', true);
		}
	}
}