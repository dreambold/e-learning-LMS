<?php
/*
Widget Name: MV Login
Description: Multiverso Login widget
Author: Alessio Marzo & Andrea Onori
Version: 1.0
Author URI: http://www.webself.it
*/


class multiverso_login_register extends WP_Widget {
	
        public function __construct() {
			
               parent::WP_Widget( 'multiverso', __('MV Login & Register', 'mvafsp'), array('description' => __('Multiverso Login & Register widget', 'mvafsp') ));
        
		}


function form($instance)
  {
	  	$defaults = array( 
            'title' => 'Login & Register',
			'login' => '1',
			'register' => '1',
			'forgot' => '1',
			'profile' => '1',
			'manageyourfiles' => '1',			
        );
		
	    $instance = wp_parse_args( (array) $instance, $defaults );
 	   
		?>
       
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
			<?php _e('Title','mvafsp'); ?>:
			</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
        
        <h3><?php _e( 'Enable/Disable Tabs', 'mvafsp' ); ?></h3>
        <p>
			<label for="<?php echo $this->get_field_id( 'login' ); ?>">
			<?php _e('Login Tab','mvafsp'); ?>:
			</label>
			<input type="checkbox" value="1" class="checkbox" id="<?php echo $this->get_field_id( 'login' ); ?>" name="<?php echo $this->get_field_name( 'login' ); ?>"<?php checked( $instance['login'] ) ?> />
		</p>
        
        <p>
			<label for="<?php echo $this->get_field_id( 'register' ); ?>">
			<?php _e('Register Tab','mvafsp'); ?>:
			</label>
			<input type="checkbox" value="1" class="checkbox" id="<?php echo $this->get_field_id( 'register' ); ?>" name="<?php echo $this->get_field_name( 'register' ); ?>"<?php checked( $instance['register'] ) ?> />
		</p>
        
        <p>
			<label for="<?php echo $this->get_field_id( 'forgot' ); ?>">
			<?php _e('Forgot Tab','mvafsp'); ?>:
			</label>
			<input type="checkbox" value="1" class="checkbox" id="<?php echo $this->get_field_id( 'forgot' ); ?>" name="<?php echo $this->get_field_name( 'forgot' ); ?>"<?php checked( $instance['forgot'] ) ?> />
		</p>
        
        <h3><?php _e('Enable/Disable User links','mvafsp'); ?></h3>
        
        <p>
			<label for="<?php echo $this->get_field_id( 'profile' ); ?>">
			<?php _e('Profile link (backend)','mvafsp'); ?>:
			</label>
			<input type="checkbox" value="1" class="checkbox" id="<?php echo $this->get_field_id( 'profile' ); ?>" name="<?php echo $this->get_field_name( 'profile' ); ?>"<?php checked( $instance['profile'] ) ?> />
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'edityourfiles' ); ?>">
			<?php _e('Manage your files link','mvafsp'); ?>:
			</label>
			<input type="checkbox" value="1" class="checkbox" id="<?php echo $this->get_field_id( 'manageyourfiles' ); ?>" name="<?php echo $this->get_field_name( 'manageyourfiles' ); ?>"<?php checked( $instance['manageyourfiles'] ) ?> />
		</p>
        
                                                                 
<?php
  } //end function form
  
  function update($new_instance, $old_instance)
  {
 		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		
		$instance['profile'] = strip_tags($new_instance['profile']);
		
		$instance['login'] = strip_tags($new_instance['login']);
		
		$instance['register'] = strip_tags($new_instance['register']);
		
		$instance['forgot'] = strip_tags($new_instance['forgot']);
		
		$instance['manageyourfiles'] = strip_tags($new_instance['manageyourfiles']);

		return $instance;
  } // end update
  
  function widget($args, $instance)
  {
	  
		extract($args, EXTR_SKIP);
	  	
		echo $before_widget;
		
		$current_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		
		?>
			
	    <!-- START FORM CODE -->
        
			<div id="mv-login-register-password">

			<?php 
			
			global $user_ID, $user_identity; 
			
			get_currentuserinfo(); 
			
			if (!$user_ID) { ?>
        
            <ul class="tabs_login">
                <?php if($instance['login'] == '1') { ?><li class="active_login"><a href="#tab1_login"><?php _e('Login', 'mvafsp'); ?></a></li><?php } ?>
                <?php if($instance['register'] == '1') { ?><li><a href="#tab2_login"><?php _e('Register', 'mvafsp'); ?></a></li><?php } ?>
                <?php if($instance['forgot'] == '1') { ?><li><a href="#tab3_login"><?php _e('Forgot?', 'mvafsp'); ?></a></li><?php } ?>
            </ul>
            <div class="tab_container_login">
            
            	<?php if($instance['login'] == '1') { ?>
                <div id="tab1_login" class="tab_content_login">
        
                    <?php 
					
					if(isset($_GET['register'])) {
						$register = $_GET['register'];
					}else{
						$register = false;
					}
					
					if(isset($_GET['reset'])) {
						$reset = $_GET['reset'];
					}else{
						$reset = false;
					}
					
					if(empty($user_login)){
						$user_login = '';
					}
					
					if(empty($user_email)){
						$user_email = '';
					}
					
					if ($register == true) { ?>
        
                    <?php _e('<h3>Success!</h3><p>Check your email for the password and then return to log in.</p>', 'mvafsp'); ?>
        
                    <?php } elseif ($reset == true) { ?>
        
                    <?php _e('<h3>Success!</h3><p>Check your email to reset your password.</p>', 'mvafsp'); ?>
        
                    <?php } else { ?>
        
                    <?php _e('<h3>Have an account?</h3><p>Log in now.</p>', 'mvafsp'); ?>
        
                    <?php } ?>
        
                    <form method="post" action="<?php bloginfo('url') ?>/wp-login.php" class="wp-user-form">
                        <div class="username">
                            <label for="user_login"><?php _e('Username', 'mvafsp'); ?>: </label>
                            <input type="text" name="log" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" id="user_login" tabindex="11" />
                        </div>
                        <div class="password">
                            <label for="user_pass"><?php _e('Password', 'mvafsp'); ?>: </label>
                            <input type="password" name="pwd" value="" size="20" id="user_pass" tabindex="12" />
                        </div>
                        <div class="login_fields">
                            <div class="rememberme">
                                <label for="rememberme">
                                    <input type="checkbox" name="rememberme" value="forever" checked="checked" id="rememberme" tabindex="13" /> <?php _e('Remember me', 'mvafsp'); ?></label>
                            </div>
                            <?php do_action('login_form'); ?>
                            <input type="submit" name="user-submit" value="<?php _e('Login', 'mvafsp'); ?>" tabindex="14" class="user-submit" />
                            <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="hidden" name="user-cookie" value="1" />
                        </div>
                    </form>
                </div>
                <?php } ?>
                
                <?php if($instance['register'] == '1') { ?>
                <div id="tab2_login" class="tab_content_login" style="display:none;">
                
                    <?php _e('<h3>Do you need an Account?</h3><p>Sign up now.</p>', 'mvafsp'); ?>
                    
                    <form method="post" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>" class="wp-user-form">
                        <div class="username">
                            <label for="user_login"><?php _e('Username', 'mvafsp'); ?>: </label>
                            <input type="text" name="user_login" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" id="user_login" tabindex="101" />
                        </div>
                        <div class="password">
                            <label for="user_email"><?php _e('Your Email', 'mvafsp'); ?>: </label>
                            <input type="text" name="user_email" value="<?php echo esc_attr(stripslashes($user_email)); ?>" size="25" id="user_email" tabindex="102" />
                        </div>
                        <div class="login_fields">
                            <?php do_action('register_form'); ?>
                            <input type="submit" name="user-submit" value="<?php _e('Sign up!', 'mvafsp'); ?>" class="user-submit" tabindex="103" />
                            <?php if($register == true) { _e('<p>Check your email for the password!</p>', 'mvafsp'); } ?>
                            
                            <?php $query_string = http_build_query(array_merge($_GET, array('register' => 'true'))); ?>
                            <input type="hidden" name="redirect_to" value="<?php echo bloginfo('url').'?'.$query_string; ?>" />
                            
                            <input type="hidden" name="user-cookie" value="1" />
                        </div>
                    </form>
                </div>
                <?php } ?>
                
                <?php if($instance['forgot'] == '1') { ?>
                <div id="tab3_login" class="tab_content_login" style="display:none;">
                
                    <?php _e('<h3>Lose something?</h3><p>Enter your username or email to reset your password.</p>', 'mvafsp'); ?>
                    
                    <form method="post" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" class="wp-user-form">
                        <div class="username">
                            <label for="user_login" class="hide"><?php _e('Username or Email', 'mvafsp'); ?>: </label>
                            <input type="text" name="user_login" value="" size="20" id="user_login" tabindex="1001" />
                        </div>
                        <div class="login_fields">
                            <?php do_action('login_form', 'resetpass'); ?>
                            <input type="submit" name="user-submit" value="<?php _e('Reset my password', 'mvafsp'); ?>" class="user-submit" tabindex="1002" />
                            <?php if($reset == true) { _e('<p>A message will be sent to your email address.</p>', 'mvafsp'); } ?>
                            
                            <?php $query_string = http_build_query(array_merge($_GET, array('reset' => 'true'))); ?>
                            <input type="hidden" name="redirect_to" value="<?php echo bloginfo('url').'?'.$query_string; ?>" />
                            
                            <input type="hidden" name="user-cookie" value="1" />
                        </div>
                    </form>
                </div>
                <?php } ?>
                
            </div>
        
            <?php } else { // is logged in 
        
            global $current_user;
			$userinfo = get_currentuserinfo();
			$userID = $current_user->ID;
			$profile = get_edit_user_link( $userID );
			
			if(!empty($current_user->display_name)){
			$username = $current_user->display_name;
			}else{
			$username = $current_user->user_login;
			}
			
			echo '<h3 class="widget-title">' . __('Howdy', 'mvafsp'). ', ' . $username . '</h3>';	
			
			echo '<ul>';
			
			echo '<li>' . wp_loginout( $current_url, false ) . '</li>';
			
			if ($instance['profile'] == '1') {
				
				
						echo '<li><a href="'. $profile .'" title="Profile">'.__('Your Profile', 'mvafsp').'</a></li>';
				
			}
			
			if ($instance['manageyourfiles'] == '1') {
				
						echo '<li><a href="'. get_permalink( get_option('mv_manage_page') ) .'">'.__('Manage Your Files','mvafsp').'</a></li>';
				
			}
			
			echo '</ul>';
			
        
        } ?>
        
        </div>

		<!-- END FORM CODE -->	
		
			
		<?php
	
		echo $after_widget;
  } // echo widget on page
  
}

function multiverso_login_register_widgets()
{
	register_widget( 'multiverso_login_register' );
}

add_action( 'widgets_init', 'multiverso_login_register_widgets' );


?>