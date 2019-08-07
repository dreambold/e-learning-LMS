<?php if ( ! defined( 'FW' ) ) {
    die( 'Forbidden' );
}

$options = array(
    'chat' => array(
        'title'   => __( 'Chat', 'woffice' ),
        'type'    => 'tab',
        'options' => array(
            'main-pro-box' => array(
                'title'   => __( 'Features', 'woffice' ),
                'type'    => 'box',
                'options' => array()
            ),
            'chat-box' => array(
                'title'   => __( 'Chat', 'woffice' ),
                'type'    => 'box',
                'options' => array(
                    'alka_pro_chat_enabled' => array(
                        'label' => __( 'Enable the live chat ?', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => true,
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => false,
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => false,
                    ),
                    'alka_pro_chat_refresh_time' => array(
                        'label' => __( 'Refresh time', 'woffice' ),
                        'type'         => 'slider',
                        'value' => 20000,
                        'properties' => array(
                            'min' => 3000,
                            'max' => 60000,
                            'step' => 1000,
                        ),
                        'desc' => __( 'Live refresh time in milliseconds to fetch new messages once the chat is open, server performance are heavily affected by this.', 'woffice' ),
                    ),
                    'alka_pro_chat_emojis_enabled' => array(
                        'label' => __( 'Enable the Emojis picker?', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => true,
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => false,
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => false,
                    ),
                    'alka_pro_chat_welcome_enabled' => array(
                        'label' => __( 'Enable welcome modal ?', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => true,
                            'label' => __( 'Yep', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => false,
                            'label' => __( 'Nope', 'woffice' )
                        ),
                        'value'        => false,
                        'help' => __('This modal will be displayed only one time for each user, it can provide detail or rules for the chat.','woffice'),
                    ),
                    'alka_pro_chat_welcome_title' => array(
                        'label' => __( 'Welcome title', 'woffice' ),
                        'type'         => 'text',
                        'value' => 'Welcome to the live chat',
                    ),
                    'alka_pro_chat_welcome_message' => array(
                        'type'  => 'wp-editor',
                        'label' => __( 'Welcome message', 'woffice' ),
                        'value'  => 'Here are the rules for the live chat... or any content you\'d like',
                        'media_buttons' => false,
                        'teeny' => false,
                        'wpautop' => false,
                        'editor_css' => '',
                        'reinit' => false,
                    ),
                )
            )
        )
    )
);
