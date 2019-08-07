<?php
/**
 * Class Woffice_Blog
 *
 * This class handles all the changes we'd make on the WordPress blog feature
 * It also handles the Like button feature for the blog
 * Note that the one related to the Wiki can be found in the extension files
 *
 * @since 2.1.3
 * @author Alkalab
 */
if( ! class_exists( 'Woffice_Blog' ) ) {
    class Woffice_Blog
    {

        /**
         * Woffice_Blog constructor
         */
        public function __construct()
        {
            add_action( 'wp_footer' , array($this,'like_buttons_js'));
            add_action( 'wp_ajax_nopriv_blogPostLike', array($this, 'like_ajax_handle'));
            add_action( 'wp_ajax_blogPostLike', array($this, 'like_ajax_handle'));
            add_filter( 'comment_reply_link', array($this,'replace_reply_link_class'));
            add_action( 'comment_form', array($this,'custom_comment_button'));
            add_filter( 'post_class', array($this,'filter_theme_post_classes'));
        }

        /**
         * Check if the current user has already voted for a certain POST ID
         * This is done by IP for the moment
         *
         * @param $post_ID
         * @return bool
         */
        static public function like_user_has_already_voted($post_ID) {

            // We get the Likes "Engine" :
            $like_engine = woffice_get_settings_option('like_engine');
            if($like_engine == 'members') {

                $voted_IDs = get_post_meta($post_ID, "voted_IDs", true);
                $voted_IDs = is_array($voted_IDs) ? $voted_IDs : array($voted_IDs);
                if (empty($voted_IDs) && !is_array($voted_IDs) && !is_user_logged_in()) {
                    return false;
                }
                $user_id = get_current_user_id();
                if(in_array($user_id, $voted_IDs)) {
                    return true;
                } else {
                    return false;
                }

            } else {
                $timebeforerevote = 240; // = 4 hours
                // Retrieve post votes IPs
                $meta_IP = get_post_meta($post_ID, "voted_IP");
                if (empty($meta_IP)) {
                    return false;
                }
                $voted_IP = $meta_IP[0];
                if (!is_array($voted_IP))
                    $voted_IP = array();
                // Retrieve current user IP
                $ip = $_SERVER['REMOTE_ADDR'];
                // If user has already voted
                if (in_array($ip, array_keys($voted_IP))) {
                    $time = $voted_IP[$ip];
                    $now = time();
                    // Compare between current time and vote time
                    if (round(($now - $time) / 60) > $timebeforerevote)
                        return false;
                    return true;
                }
                return false;
            }

        }

        /**
         * Jquery code used for the button
         * We output the JS in the footer
         *
         * @return void
         */
        public function like_buttons_js(){
            if (is_singular("post")) {
                /*Ajax URL*/
                $ajax_url = admin_url('admin-ajax.php');
                /*Ajax Nonce*/
                $ajax_nonce = wp_create_nonce('ajax-nonce');
                ?>
                <script type="text/javascript">
                    jQuery(function () {
                        jQuery(".wiki-like a").click(function () {
                            like = jQuery(this);
                            post_id = like.data("post_id");
                            // Ajax call
                            jQuery.ajax({
                                type: "post",
                                url: "<?php echo $ajax_url; ?>",
                                data: "action=blogPostLike&nonce=<?php echo $ajax_nonce; ?>&post_like=&post_id=" + post_id,
                                success: function (count) {
                                    if (count != "already") {
                                        like.closest(".wiki-like").addClass("voted");
                                        like.siblings(".count").text(count);
                                    }
                                }
                            });
                            return false;
                        });
                    });
                </script>
                <?php
            }
        }

        /**
         * Handles the AJAX call
         * Dynamically check whether the user can like and if so we save to the DB
         *
         * @return void
         */
        public function like_ajax_handle(){

            // Check for nonce security
            $nonce = $_POST['nonce'];

            if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
                die ( 'Busted!');

            if(isset($_POST['post_like'])){
                // Retrieve user IP address
                $ip = $_SERVER['REMOTE_ADDR'];
                $post_id = $_POST['post_id'];

                // We get the Likes "Engine" :
                $like_engine = woffice_get_settings_option('like_engine');
                if($like_engine == 'members') {



                } else {

                    // Get voters'IPs for the current post
                    $meta_IP = get_post_meta($post_id, "voted_IP");
                    $voted_IP = (empty($meta_IP)) ? 0 : $meta_IP[0];

                    if(!is_array($voted_IP))
                        $voted_IP = array();

                }

                // Get votes count for the current post
                $meta_count = get_post_meta($post_id, "votes_count", true);

                // Use has already voted ?
                if(!self::like_user_has_already_voted($post_id))
                {
                    if($like_engine == 'members') {

                        $voted_IDs = get_post_meta($post_id, "voted_IDs", true);
                        $user_id = (is_user_logged_in()) ? get_current_user_id() : 0;
                        if(empty($voted_IDs))
                            $voted_IDs = array();
                        $voted_IDs[$user_id] = $user_id;
                        // Save it :
                        update_post_meta($post_id, "voted_IDs", $voted_IDs);

                    } else {
                        $voted_IP[$ip] = time();
                        // Save IP and increase votes count
                        update_post_meta($post_id, "voted_IP", $voted_IP);
                    }
                    // Increase votes count
                    update_post_meta($post_id, "votes_count", ++$meta_count);

                    // Display count (ie jQuery return value)
                    echo $meta_count;

                    // Add notification
                    if (Woffice_Notification_Handler::is_notification_enabled('blog-like')) {
                        $post_object = get_post($post_id);

                        bp_notifications_add_notification( array(
                            'user_id'           => $post_object->post_author,
                            'item_id'           => $post_id,
                            'secondary_item_id' => get_current_user_id(),
                            'component_name'    => 'woffice_blog',
                            'component_action'  => 'woffice_blog_like',
                            'date_notified'     => bp_core_current_time(),
                            'is_new'            => 1,
                        ) );
                    }

                    $current_user_id = get_current_user_id();

                    if($current_user_id != 0 && Woffice_Activity_Handler::is_activity_enabled('blog-like')) {
                        $activity_args = array(
                            'action' => '<a href="'.bp_loggedin_user_domain().'">'.woffice_get_name_to_display($current_user_id).'</a> '.__('liked the post ','woffice').' <a href="'.get_the_permalink($post_id).'">'.get_the_title($post_id).'</a>',
                            'component' => 'blog',
                            'type' => 'blog-like',
                            'item_id' => $post_id,
                            'user_id' => $current_user_id,
                        );
                        bp_activity_add( $activity_args );
                    }
                }
                else
                    echo "already";
            }
            exit;
        }

        /**
         * Adding a custom class to the reply link
         *
         * @param $class
         * @return mixed
         */
        public function replace_reply_link_class( $class ) {
            $class = str_replace( "class='comment-reply-link", "class='btn btn-default btn-sm", $class );
            return $class;
        }

        /**
         * Creating a custom comment button in order to add Bootstrap HTML Markup
         *
         * @return void
         */
        public function custom_comment_button() {
            echo '<div class="control-group text-right"><button class="btn btn-default" type="submit"><i class="fa fa-paper-plane"></i>' . __( 'Post Comment', 'woffice' ) . '</button></div>';
        }

        /**
         * Extend the default WordPress post classes.
         * @param $classes the current classes
         * @return array
         */
        public function filter_theme_post_classes($classes){
            if ( ! post_password_required() && ! is_attachment() && has_post_thumbnail() ) {
                $classes[] = 'has-post-thumbnail';
            }
            // We remove the hentry to add it back at a higher level
	        $classes = array_diff( $classes, array( 'hentry' ) );
            return $classes;
        }

    }
}

/**
 * Let's fire it :
 */
new Woffice_Blog();



