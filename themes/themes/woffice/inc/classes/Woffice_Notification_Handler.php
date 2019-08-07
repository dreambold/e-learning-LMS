<?php
/**
 * Class Woffice_Notification_Handler
 *
 * Handle BuddyPress notifications within Woffice
 *
 * @since 2.1.3
 * @author Alkaweb
 */
if( ! class_exists( 'Woffice_Notification_Handler' ) ) {
	class Woffice_Notification_Handler {

        /**
         * Woffice_Notification_Handler constructor.
         */
		public function __construct() {

			//Register componenets
			add_action('bp_setup_globals', array($this, 'register_blog_notification'));

			//Send notifications
			add_action( 'comment_post', array($this, 'on_comment_publishing') , 10, 2 );
			add_action('transition_comment_status', array( $this, 'on_comment_approve') ,10,3);

			//Clear notifications
			add_action('wp', array($this, 'clear_blog_notification') );
		}

        /**
         * Register all notifications regarding the blog
         */
		public function register_blog_notification() {
			// Register component manually into buddypress() singleton
			buddypress()->woffice_blog = new stdClass;
			// Add notification callback function
			buddypress()->woffice_blog->notification_callback = array('Woffice_Notification_Handler', 'blog_format_notifications');

			// Now register components into active components array
			buddypress()->active_components['woffice_blog'] = 1;
		}

        /**
         * Format the notifications
         *
         * @param $action
         * @param $item_id
         * @param $secondary_item_id
         * @param $total_items
         * @param string $format
         * @return mixed|void
         */
		public static function blog_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

			if ( ! ('woffice_blog_like' === $action || 'woffice_blog_comment' === $action)) {
				return $action;
			}

			$post_title = get_the_title( $item_id );

			if ('woffice_blog_like' === $action) {
				$custom_title = sprintf( esc_html__( 'New like received', 'woffice' ), $post_title );
				$custom_link  = get_permalink( $item_id );
				if ( (int) $total_items > 1 ) {
					$custom_text  = sprintf( esc_html__( 'You received "%1$s" new likes', 'woffice' ), $total_items );
					$custom_link = bp_get_notifications_permalink();
				} else {
					$custom_text  = sprintf( esc_html__( 'Your post "%1$s" received a like', 'woffice' ), $post_title );
				}
			}

			if ('woffice_blog_comment' === $action) {
				$custom_title = sprintf( esc_html__( 'New comment received', 'woffice' ), $post_title );
				$custom_link  = get_permalink( $item_id );
				if ( (int) $total_items > 1 ) {
					$custom_text  = sprintf( esc_html__( 'You received "%1$s" new comments', 'woffice' ), $total_items );
					$custom_link = bp_get_notifications_permalink();
				} else {
					$custom_text  = sprintf( esc_html__( 'Your post "%1$s" received a new comment', 'woffice' ), $post_title );
				}

			}

			// WordPress Toolbar
			if ( 'string' === $format ) {
				$message = (!empty($custom_link)) ? '<a href="' . esc_url( $custom_link ) . '" title="' . esc_attr( $custom_title ) . '">' . esc_html( $custom_text ) . '</a>' : $custom_text;
				$return = apply_filters( 'woffice_wiki_like_format', $message, $custom_text, $custom_link );

				// Deprecated BuddyBar
			} else {
				$return = apply_filters( 'woffice_wiki_like_format', array(
					'text' => $custom_text,
					'link' => $custom_link
				), $custom_link, (int) $total_items, $custom_text, $custom_title );
			}

			return $return;

		}
		
		/**
		 * Triggered when a comment is published directly, without waiting for approval
		 * 
		 * @param $comment_ID
		 * @param $comment_approved
		 */
		public function on_comment_publishing( $comment_ID, $comment_approved ) {
			if( 1 === $comment_approved ){
				$comment_object = get_comment($comment_ID);
				$this->send_comment_notification($comment_object);
			}
		}

		/**
		 * Triggered when a comment was pending and now is approved
		 * 
		 * @param $new_status
		 * @param $old_status
		 * @param $comment_object
		 */
		public function on_comment_approve( $new_status, $old_status, $comment_object ) {
			if( $old_status != $new_status && $new_status == 'approved' ){
				$this->send_comment_notification($comment_object);
			}
		}

		/**
		 * Send the comment notification to the author of the post
		 * 
		 * @param $comment_object
		 *
		 * @return bool
		 * @throws Exception
		 */
		protected function send_comment_notification($comment_object){

			if(!woffice_bp_is_active( 'notifications' ))
				return false;

			$post_type = get_post_type($comment_object->comment_post_ID);
			$comment_author = $comment_object->user_id;
			$post_object = get_post($comment_object->comment_post_ID);
			
			switch($post_type) {
				case 'project':
					$label = 'project';
					break;
				case 'wiki':
					$label = 'wiki';
					break;
				case 'post':
					$label = 'blog';
					break;
				default:
					return false;
			}
			
			if(! self::is_notification_enabled($label . '-comment') )
				return false;


			bp_notifications_add_notification( array(
				'user_id'           => $post_object->post_author,
				'item_id'           => $comment_object->comment_post_ID,
				'secondary_item_id' => $comment_author,
				'component_name'    => 'woffice_' . $label,
				'component_action'  => 'woffice_' . $label . '_comment',
				'date_notified'     => bp_core_current_time(),
				'is_new'            => 1,
			) );

		}

		/**
		 * Clear the notification when the author of the post see it
		 */
		public function clear_blog_notification() {
			if ( is_single() && is_user_logged_in() &&  woffice_bp_is_active( 'notifications' ) ) {
				global $post;
				$current_user_id = get_current_user_id();
				if ( $post->post_author == $current_user_id ) {
					bp_notifications_mark_notifications_by_item_id( $current_user_id, $post->ID, 'woffice_blog', 'Woffice_blog_like', false, 0 );
					bp_notifications_mark_notifications_by_item_id( $current_user_id, $post->ID, 'woffice_blog', 'Woffice_blog_comment', false, 0 );
				}
			}
		}

		/**
		 * Check if a given BuddyPress component is active
		 *
		 * @param string $notification
		 * @throws Exception
		 * @return bool
		 */
		public static function is_notification_enabled($notification) {

			if (!woffice_bp_is_active('notifications'))
				return false;

			switch ($notification) {
				case 'blog-comment':
					return (! ( defined( 'WOFFICE_DISABLE_BLOG_POST_COMMENT_NOTIFICATION' ) && ( true == WOFFICE_DISABLE_BLOG_POST_COMMENT_NOTIFICATION ) ) );
				case 'blog-like':
					return (! ( defined( 'WOFFICE_DISABLE_BLOG_POST_LIKE_NOTIFICATION' ) && ( true == WOFFICE_DISABLE_BLOG_POST_LIKE_NOTIFICATION ) ));
				case 'wiki-comment':
					return (! ( defined( 'WOFFICE_DISABLE_WIKI_COMMENT_NOTIFICATION' ) && ( true == WOFFICE_DISABLE_WIKI_COMMENT_NOTIFICATION ) ) );
				case 'wiki-like':
					return (! ( defined( 'WOFFICE_DISABLE_WIKI_LIKE_NOTIFICATION' ) && ( true == WOFFICE_DISABLE_WIKI_LIKE_NOTIFICATION ) ) );
				case 'project-comment':
					return (! ( defined( 'WOFFICE_DISABLE_PROJECT_COMMENT_NOTIFICATION' ) && ( true == WOFFICE_DISABLE_PROJECT_COMMENT_NOTIFICATION ) ) );
				case 'project-todo-assigned':
					return (! ( defined( 'WOFFICE_DISABLE_PROJECT_TODO_NOTIFICATION' ) && ( true == WOFFICE_DISABLE_PROJECT_TODO_NOTIFICATION ) ) );
				case 'project-member-assigned':
					return (! ( defined( 'WOFFICE_DISABLE_PROJECT_MEMBER_NOTIFICATION' ) && ( true == WOFFICE_DISABLE_PROJECT_MEMBER_NOTIFICATION ) ) );
				default:
					throw new Exception('Notification type unknown');
			}

		}

	}
}
/**
 * Let's fire it!
 */
new Woffice_Notification_Handler();