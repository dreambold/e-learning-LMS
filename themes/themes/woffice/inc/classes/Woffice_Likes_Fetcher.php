<?php
/**
 * Class WofficeLikesFetcher
 *
 * Fetch all user name who liked a single post/wiki page/blog page in a popover
 *
 */
if( ! class_exists( 'WofficeLikesFetcher' ) ) {
    class WofficeLikesFetcher
    {

        /**
         * Define maximum name to be display in the popover
         * @var int MAX_NO_NAMES
         */
        const MAX_NO_NAMES = 10;

        /**
         * WofficeLikesFetcher constructor.
         *
         * Register all hooks
         */
        public function __construct()
        {
            add_action('wp_footer', array($this, 'customJS'));
            add_action('wp_ajax_likes_fetcher', array($this, 'ajaxCallback'));
            add_action('wp_ajax_nopriv_likes_fetcher', array($this, 'ajaxCallback'));
        }

        /**
         * Get liker user names with formatting
         *
         * @param array $user_ids
         *
         * @return string
         */
        public function getNames($user_ids)
        {
            $likers = array();

            foreach (array_slice($user_ids, 0, 10) as $id) {
                $likers[] = woffice_get_name_to_display($id);
            }

            $users_names = _e('Liked by ', 'woffice') . implode(', ', $likers);

            if (count($user_ids) > self::MAX_NO_NAMES) {
                $users_names .=  ' & ' . count($user_ids) - self::MAX_NO_NAMES . _e(' other members', 'woffice');
            }

            return $users_names;
        }

        /**
         * Erase IP of the likers
         *
         * @deprecated 2.8.0.3
         *
         * @return void
         */
        public function eraseIPLikes()
        {
            $posts = get_posts(array(
                'posts_per_page' => -1,
                'fields' => 'ids',
            ));

            foreach ($posts as $post) {
                delete_post_meta($post->ID, 'voted_IP');
            }

        }

        /**
         * List user names called in ajax
         *
         * @return string
         */
        public function ajaxCallback()
        {
            if (!defined('DOING_AJAX')) return;

            $post_id = (isset($_POST['postID'])) ? $_POST['postID'] : '';

            if ($post_id) {

                $user_ids = get_post_meta($post_id, 'voted_IDs', true);

                if (!empty($user_ids)) {
                    echo $this->getNames($user_ids);
                } else {
                    _e('No user liked it yet', 'woffice');
                }
            }
            wp_die();
        }

        /**
         * Set JS  for fetch post likers
         */
        public function customJS()
        {
	        // Check like_engine setting
	        $like_engine = woffice_get_settings_option('like_engine');

	        if ($like_engine !== 'members') {
		        return;
	        }

            $ajax_url = admin_url('admin-ajax.php');
            ?>
            <script type='text/javascript'>

                jQuery(function () {
                    var LikeFetcherTriggers = '.wiki-like, .list-styled.list-wiki .count';

                    jQuery(LikeFetcherTriggers).on('mouseenter', function () {

                        var $wrapper = jQuery(this);
                        var IDcontainer = $wrapper.closest('article.box.type-wiki,article.box.type-post');
                        if (IDcontainer.length !== 0) {
                            var IDfull = IDcontainer.attr('id');
                            var postID = IDfull.substring(5);
                        } else {
                            var postID = $wrapper.closest('a').data('post-id');
                            if (postID.length === 0) {
                                var postID = $wrapper.closest('a').data('post_id');
                            }
                        }
                        var action = "likes_fetcher";
                        var theData = {
                            'action': action,
                            'postID': postID
                        };

                        $wrapper.popover({
                            'content': '<?php  _e('No user liked it yet', 'woffice'); ?>',
                            'html': true,
                            'placement': 'top',
                            'container': 'body',
                            'trigger': 'hover'
                        });

                        jQuery.ajax({
                            type: 'post',
                            url: "<?php echo $ajax_url; ?>",
                            data: theData,
                            success: function (response) {

                                $wrapper.popover('dispose');

                                setTimeout(function () {
                                    $wrapper.popover({
                                        'content': response,
                                        'html': true,
                                        'placement': 'top',
                                        'container': 'body',
                                        'trigger': 'hover'
                                    });
                                    $wrapper.popover('show');
                                }, 300);
                            }
                        });

                        return false;

                    });
                    jQuery(LikeFetcherTriggers).on('mouseleave click', function () {

                        var $wrapper = jQuery(this);
                        $wrapper.popover('dispose');

                    });
                });
            </script>
            <?php
        }
    }

    new WofficeLikesFetcher();
}
