<?php
/**
 * Class Woffice_AlkaChat
 *
 * Manage everything related to the AlkaChat
 *
 * @since 2.5.1
 * @author Alkaweb
 */

if( ! class_exists( 'Woffice_AlkaChat' ) ) {
    class Woffice_AlkaChat
    {

	    /**
	     * Returned data handler
	     *
	     * @var array
	     */
	    public $data = array();

	    /**
	     * The request payload
	     *
	     * @var array
	     */
	    public $payload = array();

	    /**
	     * The receiver(s) of the message
	     *
	     * @var array
	     */
	    public $receivers = array();

	    /**
	     * Current thread ID
	     *
	     * @var int
	     */
	    public $thread_id = 0;

	    /**
	     * BuddyPress instance
	     *
	     * @var BuddyPress
	     */
	    public $bp = null;

	    /**
	     * Woffice_AlkaChat constructor.
	     */
	    public function __construct()
	    {
		    add_filter( 'body_class', array( $this, 'assignClassToBody') );

		    add_filter( 'woffice_js_exchanged_data',        array($this, 'exchanger'));
		    add_action( 'wp_footer',                        array($this, 'render'));
		    add_action( 'wp_ajax_woffice_alka_chat',        array($this, 'ajaxCallback'));
		    add_action( 'wp_ajax_nopriv_woffice_alka_chat', array($this, 'ajaxCallback'));
	    }

	    /**
	     * Renders the markup
	     *
	     * @return void
	     */
	    public function render() {
	    	if (static::isChatEnabled())
	    		get_template_part('template-parts/chat');
	    }

	    /**
	     * Pass data to the client
	     *
	     * @param array $data
	     * @return array
	     */
	    public function exchanger($data) {
		    if (!static::isChatEnabled())
			    return $data;

		    $data['alka_chat'] = array(
			    'actions' => array(
				    'new_conversation' => __('New conversation', 'woffice'),
				    'refresh' => __('Refresh', 'woffice'),
			    ),
			    'labels' => array(
				    'new_conversation' => __('Create the conversation', 'woffice'),
				    'new_conversation_conversations_placeholder' => __('Please type member ID(s) or username(s)', 'woffice'),
				    'new_conversation_title_label' => __('Conversation title', 'woffice'),
				    'new_conversation_title' => __('Conversation with', 'woffice'),
				    'send' => __('Send', 'woffice'),
				    'group_start' => __('Start chat', 'woffice'),
				    'not_found' => __('No message found... Send one to start chatting.', 'woffice')
			    ),
			    'first_message' => sprintf(__('New conversation started by %s', 'woffice'), woffice_get_name_to_display(get_current_user_id())),
			    'current_user' => get_current_user_id(),
			    'nonce' => wp_create_nonce('woffice_alka_chat')
		    );

		    return $this->formatData($data);
	    }

	    /**
	     * Formatting the data passed to the client
	     *
	     * @param array $data
	     * @return array
	     */
	    private function formatData($data) {
		    // Will be replaced by some options later
		    $custom_tab_enabled = woffice_get_settings_option('alka_pro_chat_welcome_enabled');
		    $custom_tab_title = woffice_get_settings_option('alka_pro_chat_welcome_title');
		    $custom_tab_content = woffice_get_settings_option('alka_pro_chat_welcome_message');
		    $has_emojis = woffice_get_settings_option('alka_pro_chat_emojis_enabled');
		    $refresh_time = woffice_get_settings_option('alka_pro_chat_refresh_time');

		    $data['alka_chat']['refresh_time'] = $refresh_time;
		    $data['alka_chat']['has_emojis'] = $has_emojis;

		    if($custom_tab_enabled) {
			    $data['alka_chat']['custom_tab'] = $custom_tab_content;
			    $data['alka_chat']['actions']['custom_tab'] = $custom_tab_title;
		    }

		    return $data;
	    }


	    /**
	     * Receive the callbacks from the client
	     */
	    public function ajaxCallback() {
		    // Quick validation
		    if (!wp_verify_nonce($_POST['_nonce'], 'woffice_alka_chat' ) || !defined( 'DOING_AJAX' ) || !DOING_AJAX) {
			    echo json_encode(array(
				    'type' => 'error',
				    'message' => __('There is a security issue in your request.','woffice')
			    ));
			    die();
		    }

		    // We set a default version as it's not a required parameter
		    $this->payload = (!isset($_POST['api_payload'])) ? array() : $_POST['api_payload'];

		    $this->bp = buddypress();

		    switch ($this->payload['type']) {

			    case 'watch':
			    	$this->watch();
				    break;

			    case 'conversation_list':
			    	$this->conversationList();
			    	break;

			    case 'conversation_delete':
				    $this->conversationDelete();
				    break;

			    case 'conversation_get':
				    $this->conversationGet();
				    break;

			    case 'message_delete':
				    $this->messageDelete();
				    break;

			    case 'message_edit':
				    $this->messageEdit();
				    break;

			    case 'message_create':
				    $this->messageCreate();
				    break;

		    }

		    $this->prepare();
	    }

	    /**
	     * Watch for new messages
	     */
	    private function watch()
	    {
		    if((!isset($this->payload['thread_id']) || $this->payload['thread_id'] == 0) && !isset($this->payload['last_message_date'])) {
			    wp_die();
		    }

		    // We get our data
		    $thread = $this->payload['thread_id'];
		    $last_message_displayed_string =  urldecode($this->payload['last_message_date']);
		    $last_message_displayed = new DateTime($last_message_displayed_string);

		    $messages_returned = array();
		    $thread = new BP_Messages_Thread($thread);

		    // If last message of the conversation is older than last message displayed, just do nothing
		    if(new DateTime($thread->last_message_date) <= $last_message_displayed)
			    wp_die();

		    // If we have at least one message
		    if(empty($thread)) {
			    wp_die();
		    }

		    // We build our messages
		    foreach ($thread->messages as $message_object) {

			    //If this message is older of the last message displayed, just skip to the next one
			    if(new DateTime($message_object->date_sent) <= $last_message_displayed)
				    continue;

			    // We retrieve the message
			    $message = new BP_Messages_Message($message_object->id);

			    $this->messageArrayPush($messages_returned, $message);
		    }

		    // If no messages
		    if(empty($messages_returned)) {
			    wp_die();
		    }

		    $this->markThreadAsRead();

		    // We assign the messages
		    $this->data = $messages_returned;
	    }

	    /**
	     * Edit a message
	     *
	     * @return array
	     */
	    private function messageEdit() {
		    global $wpdb;

		    // We validate the request for safety reason
		    if (!isset($this->payload['message_id']) || !isset($this->payload['content']))
			    return $this->data = array(
				    'type' => 'error',
				    'message' => __('The request is not valid', 'woffice')
			    );

		    $wpdb->update(
		    	$this->bp->messages->table_name_messages,
			    array('message' => stripslashes($this->payload['content'])),
			    array('id' => (int) $this->payload['message_id'])
		    );

		    return $this->data = array(
			    'type' => 'success',
			    'message' => __('Message updated', 'woffice')
		    );
	    }

	    /**
	     * Delete a message
	     *
	     * @return array
	     */
	    private function messageDelete() {
		    global $wpdb;

		    // We validate the request for safety reason
		    if (!isset($this->payload['message_id']))
			    return $this->data = array(
				    'type' => 'error',
				    'message' => __('The request is not valid', 'woffice')
			    );

		    $wpdb->delete(
		    	$this->bp->messages->table_name_messages,
			    array('id' => (int) $this->payload['message_id'])
		    );

		    return $this->data = array(
		    	'type' => 'success',
			    'message' => __('Message deleted', 'woffice')
		    );
	    }

	    /**
	     * Create a message
	     *
	     * @return array
	     */
	    private function messageCreate()
	    {
		    // We prepare the args for the new message
		    $this->setThreadId();

		    // We validate the request for safety reason
		    if ((!isset($this->payload['participants']) && !$this->thread_id) || !isset($this->payload['content']) || empty($this->payload['content']))
			    return $this->data = array(
				    'type' => 'error',
				    'message' => __('The request is not valid', 'woffice')
			    );

		    $args = array(
			    'sender_id'     => bp_loggedin_user_id(),
			    'thread_id'     => $this->thread_id,
			    'content'       => wp_encode_emoji($this->payload['content']),
			    'subject'       => _x( 'Chat', 'The subject of the chat conversation messages', 'woffice' ),
			    'date_sent'     => bp_core_current_time(),
			    'error_type'    => 'wp_error',
		    );

		    // If this is a new thread, then set the receiver
		    if(!$this->thread_id)
			    $args['recipients'] = $this->payload['participants'];

		    // Disable temporary the email notification for new messages
		    if (apply_filters('woffice_disable_email_notification_for_chat_messages', true))
			    remove_action('messages_message_sent', 'messages_notification_new_message', 10);

		    // Send the message
		    $this->thread_id = messages_new_message($args);

		    // Enable again the email notification for new messages
		    if (apply_filters('woffice_disable_email_notification_for_chat_messages', true))
			    add_action('messages_message_sent', 'messages_notification_new_message', 10);

		    return $this->data = array(
		    	'type' => 'success',
			    'message' => __('Message sent!', 'woffice'),
			    'thread_id' => $this->thread_id
		    );
	    }

	    /**
	     * List the messages
	     *
	     * @return array
	     */
	    private function conversationGet()
	    {
		    // We validate the request for safety reason
		    if (!$this->setThreadId())
			    return $this->data = array(
				    'type' => 'error',
				    'message' => __('The request is not valid', 'woffice')
			    );

		    // We build our own messages array
		    foreach (BP_Messages_Thread::get_messages($this->thread_id) as $message) {
			    $messages = $this->messageArrayPush( $messages, $message);
		    }

		    // Group messages in the same minute
		    $messages = $this->setSameMinuteClass( $messages );

		    // Mark the messages as read
		    $this->markThreadAsRead();

		    return $this->data = array(
			    'type' => 'success',
		        'recipients' => BP_Messages_Thread::get_recipients_for_thread($this->thread_id),
		        'messages' => $messages
		    );
	    }

	    /**
	     * Delete a conversation
	     *
	     * @return array
	     */
	    private function conversationDelete()
	    {
		    // We validate the request for safety reason
		    if (!$this->setThreadId())
			    return $this->data = array(
				    'type' => 'error',
				    'message' => __('The request is not valid', 'woffice')
			    );

		    BP_Messages_Thread::delete($this->thread_id);

		    return $this->data = array(
			    'type' => 'success',
			    'message' => __('Conversation deleted!', 'woffice'),
			    'thread_id' => $this->thread_id
		    );
	    }

	    /**
	     * List the conversations
	     *
	     * @return array
	     */
	    private function conversationList()
	    {
		    if (!isset($this->payload['user_id']))
			    return $this->data = array(
				    'type' => 'error',
				    'message' => __('The request is not valid', 'woffice')
			    );

	    	$threads_object = BP_Messages_Thread::get_current_threads_for_user(array(
	    		'user_id' => $this->payload['user_id']
		    ));

	    	$threads_object['threads'] = ($threads_object['threads']) ? $threads_object['threads'] : array();

	    	foreach ($threads_object['threads'] as $id=>$thread) {

	    		// Participants
			    $threads_object['threads'][$id]->participants = array();
	    		foreach ($threads_object['threads'][$id]->recipients as $recipient) {
				    $threads_object['threads'][$id]->participants[] = array(
				    	'_avatar' => bp_core_fetch_avatar(array(
						    'item_id'   => $recipient->user_id,
						    'type'      => 'full',
						    'width'     => 80,
						    'height'    => 80,
					    )),
					    '_id' => $recipient->user_id,
					    '_profile' => bp_core_get_user_domain($recipient->user_id),
					    '_name' => woffice_get_name_to_display($recipient->user_id)
				    );
			    }

			    // Created at date
			    $threads_object['threads'][$id]->created_at = bp_format_time(strtotime($this->getFirstMessage($threads_object['threads'][$id]->messages)->date_sent));

	    		// Conversation title
			    $threads_object['threads'][$id]->title = $this->getConversationTitle($threads_object['threads'][$id]->participants);

		    }

	    	return $this->data = array(
	    		'type' => 'success',
	    	    'threads' => $threads_object
		    );
	    }

	    /**
	     * Get the conversation title from the participants
	     *
	     * @param array $participants
	     *
	     * @return string
	     */
	    private function getConversationTitle($participants)
	    {
	    	$name = __('Conversation with', 'woffice') . ' ';

	    	foreach ($participants as $participant) {

	    		if (get_current_user_id() === $participant['_id'])
	    			continue;

			    $name .= $participant['_name'] . ', ';

		    }

	    	return substr($name, 0, -2);
	    }

	    /**
	     * Returns the first message of BuddyPress message array
	     *
	     * @param array $messages
	     * @return BP_Messages_Message
	     */
	    private function getFirstMessage($messages)
	    {
	    	$first_message = null;
	    	$last_date = null;

	    	foreach ($messages as $message) {

			    $last_date = (!$last_date) ? $message->date_sent : $last_date;

				if (strtotime($message->date_sent) <= strtotime($last_date))
					$first_message = $message;

				$last_date = $message->date_sent;

		    }

		    return $first_message;
	    }

	    /**
	     * Assign the right class to the body depending on the activation of the chat
	     *
	     * @param $classes
	     *
	     * @return array
	     */
	    public function assignClassToBody( $classes )
	    {
		    if (static::isChatEnabled())
			    $classes[] = 'woffice-chat-enabled';
		    else
			    $classes[] = 'woffice-chat-disabled';

		    return $classes;
	    }

	    /**
	     * Check if the chat can be rendered
	     *
	     * @return bool
	     */
	    public static function isChatEnabled() {
		    if (!is_user_logged_in())
			    return false;

		    $chat_enabled_option = (bool) woffice_get_settings_option('alka_pro_chat_enabled', false);

		    $private_messaging_enabled = woffice_bp_is_active('messages');

		    $is_chat_enabled = ($chat_enabled_option && $private_messaging_enabled);

		    $is_chat_enabled = apply_filters( 'woffice_chat_is_enabled', $is_chat_enabled);

		    return $is_chat_enabled;
	    }

	    /**
	     * Prepare the response to pass to the Vue.js render
	     * We basically just json encode for now
	     */
	    protected function prepare() {
		    echo json_encode($this->data);

		    wp_die();
	    }

	    /**
	     * Push a new message into a messages array
	     *
	     * @param array $messages
	     * @param object $new_message_object
	     *
	     * @return array
	     */
	    protected function messageArrayPush(&$messages, $new_message_object) {
		    $messages[] = array(
			    'id'                => $new_message_object->id,
			    'sender_id'         => $new_message_object->sender_id,
			    'content'           => $new_message_object->message,
			    'created_at'        => $new_message_object->date_sent,
			    'sending_status'    => bp_format_time(strtotime($new_message_object->date_sent)),
			    'same_minute'       => false,
			    'avatar_tag'        => bp_core_fetch_avatar(array(
				    'item_id'   => $new_message_object->sender_id,
				    'type'      => 'full',
				    'width'     => 80,
				    'height'    => 80,
			    ))
		    );

		    return $messages;
	    }

	    /**
	     * Parse the whole array of messages and add the flag to group the messages sent in the same minute
	     *
	     * @param $messages
	     *
	     * @return array
	     */
	    protected function setSameMinuteClass( &$messages ) {
		    $messages_n = count($messages);
		    for ($i = 0; $i < ($messages_n-1); $i++) {

			    if($messages[$i]['sending_status'] == $messages[$i+1]['sending_status'] && $messages[$i]['sender_id'] == $messages[$i+1]['sender_id'])
				    $messages[$i]['same_minute'] = true;
		    }

		    return $messages;
	    }

	    /**
	     * Mark the thread as read
	     */
	    protected function markThreadAsRead() {
		    // Mark message as read
		    BP_Messages_Thread::mark_as_read($this->thread_id);

		    // Mark notification as read
		    $messages = BP_Messages_Thread::get_messages($this->thread_id);
		    foreach ($messages as $message) {
			    bp_notifications_mark_notifications_by_item_id(get_current_user_id(), $message->id, buddypress()->messages->id, 'new_message');
		    }
	    }

	    /**
	     * Used to sort array of thread
	     *
	     * @param BP_Messages_Thread $a
	     * @param BP_Messages_Thread $b
	     *
	     * @return bool
	     */
	    protected function uSortByLastMessageDate($a, $b) {
		    $date_a = new DateTime($a->last_message_date);
		    $date_b = new DateTime($b->last_message_date);

		    return $date_a < $date_b;
	    }

	    /**
	     * Get last activity from the receiver
	     *
	     * @return string
	     */
	    protected function getLastActive() {
		    $last_activity = bp_get_user_last_activity( $this->receivers );
		    if (isset( $last_activity ) && !empty($last_activity)) {
			    $last_activity_converted = bp_core_get_last_activity( $last_activity, esc_html__( 'Active %s', 'woffice' ) );
		    } else {
			    $last_activity_converted = esc_html__( 'Never active', 'woffice' );
		    }

		    return $last_activity_converted;
	    }

	    /**
	     * Get the thread id from the $this->payload and set it as class property.
	     * It return true if the receiver has been set successfully, otherwise false
	     *
	     * @return bool
	     */
	    protected function setThreadId() {
		    if (isset($this->payload['thread_id'])) {
			    $this->thread_id = (int) $this->payload['thread_id'];
			    return true;
		    }

		    $this->thread_id = false;

		    return false;
	    }
    }
}

/**
 * Let's fire it :
 */
new Woffice_AlkaChat();