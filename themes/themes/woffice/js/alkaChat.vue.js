/**
 * Alka Chat Vue Application
 * Render alka chat chat through Vue.js
 *
 * @author Alkaweb Team
 */
var alkaChat = function () {

    var self = this;

    self.wrapper = Woffice.$body.find('#alka-chat-wrapper');
    self.$ = Woffice.$;
    self.refreshInterval = null;
    self.chatStorageKey = 'wofficeChat';

    /**
     * We define some useful helpers
     * @type {*}
     */
    if(self.wrapper.length === 0) {
        return;
    }

    /**
     * Format day filter
     */
    Vue.filter('formatDate', function(value) {
        if (value) {
            var date = new Date(value);
            return date.toLocaleDateString();
        }
    });

    /**
     * Format time filter
     */
    Vue.filter('formatTime', function(value) {
        if (value) {
            var date = new Date(value);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }
    });

    /**
     * Vue Root
     * @type {Vue}
     */
    self.chat = new Vue({

        el: self.wrapper[0],

        /**
         * Data Wrapper
         */
        data: {

            isLoading: false,
            loader: null,

            isOpen: false, // open or other states in the future

            conversations: {},
            convPagination: {
                perPage: 15,
                currentPage: 1,
                totalPages: 1
            },
            lastChecked: null,

            exchanger: Woffice.data.alka_chat,

            showNewConversation: false,
            newConversationSearch: '',
            newConversationPotentialParticipants: [],
            newConversationParticipants: [],

            showCustomTab: false,

            showCurrentConversation: false,
            currentConversation: null,
            currentMessages: [],
            currentMessagesPage: 1,
            newMessage: '',

            alert: null,

            actions: Woffice.data.alka_chat.actions,
            currentAction: ''

        },

        filters: {
            json: function(value) {
                return value.replace('\\\'s', '\'s');
            },
            title: function (value, n) {
                if (value.length <= n)
                    return value;
                var subString = value.substr(0, n-1);
                return subString.substr(0, subString.lastIndexOf(' ')) + "...";
            }
        },

        created: function() {

            var self = this;

            // Create a Spinner.js instance
            self.loader = new Spinner({
                color: "#333",
                scale: 0.6,
                left: "98%"
            });

            // Set the page
            Woffice.$(window).on("resize", function () {
                self.setPerPage();
            }).resize();

            self.customTab('start');

            self.updateLastCheck();

            self.buddyPressGroup();

        },

        watch: {

            /**
             * We set the CSS pagination
             */
            convPagination: {
                handler(){
                    this.setBullets();
                },
                deep: true
            },

            /**
             * Handle the main layout change when the right bottom main button is clicked
             *
             * @param {string} newState
             */
            isOpen: function (newState) {

                var self = this;

                if(newState) {
                    alkaChat.refreshInterval = setInterval(self.refresh,self.exchanger.refresh_time);
                    self.fetchConversations(true);
                } else {
                    clearInterval(alkaChat.refreshInterval);
                }

            },

            /**
             * Adds the Spin.js spinner on loading state change
             * @param {boolean} newState
             */
            isLoading: function (newState) {
                var self = this;
                if(typeof self.loader === 'undefined')
                    return;
                if(newState === true)
                    self.loader.spin(Woffice.$.find('#alka-chat-alerts')[0]);
                else
                    self.loader.stop();
            },

            /**
             * Removes automatically an alert after 5sec
             */
            alert: function () {
                var self = this;
                setTimeout(function () {
                    self.alert = null;
                }, 5000);
            },

            /**
             * On new participant typing
             *
             * @param {string} newVal
             */
            newConversationSearch: function (newVal) {

                if(newVal.length < 3)
                    return;

                var self = this;

                self.autoFetchMembers();

            },

            /**
             * Set the bullets again when the conversation's size changed
             */
            conversations: function () {
                this.setBullets();
            }

        },

        methods: {

            /**
             * Add a custom tab to a BuddyPress group
             */
            buddyPressGroup: function () {

                var $group = Woffice.$('.bp_group.type-bp_group');
                var self = this;

                if ($group.length === 0)
                    return;

                var $nav = $group.find('#item-nav #object-nav ul').first();

                $nav.append('<li id="woffice-chat-trigger-li"><a href="#">'+ Woffice.data.alka_chat.labels.group_start +'</a></li>');

                Woffice.$('#woffice-chat-trigger-li').on('click', function (e) {
                    e.preventDefault();

                    self.switchState();
                    self.startAction('new_conversation');

                    var membersReceived = Woffice.$('#woffice-chat-group-members').data('members');
                    var members = [];

                    for (var i = 0; i < membersReceived.length; i++) {
                        members.push({
                            value: membersReceived[i].id,
                            label: membersReceived[i].username
                        });
                    }

                    self.newConversationParticipants = members;
                });

            },

            /**
             * Set the conversation bullets positions
             */
            setBullets: function () {

                setTimeout(function () {
                    Woffice.$(Woffice.$('#alka-chat-conversations').find('li').get().reverse()).each(function (index) {
                        Woffice.$(this).css('right', index * 90 + 'px');
                    });
                    Woffice.tooltips.start();
                }, 400);

            },

            /**
             * Update the last check count
             */
            updateLastCheck: function () {
                this.lastChecked = new Date().getTime();
            },

            /**
             * Whether the last message is new or nots
             *
             * @param {string} lastMessageDate
             *
             * @return {boolean}
             */
            hasNew: function (lastMessageDate) {

                var date = new Date(lastMessageDate);

                return date.getTime() > this.lastChecked;

            },


            /**
             * Toggles the meta line for each message
             *
             * @param {object} messageObj
             */
            toggleMeta: function (messageObj) {

                var self = this;

                if (messageObj.sender_id !== self.exchanger.current_user)
                    return;

                messageObj._showMeta = (messageObj._showMeta !== true);

                self.$forceUpdate();

            },

            /**
             * Toggles the edit form for a message
             *
             * @param {object} messageObj
             */
            toggleMessageEdit: function (messageObj) {

                var self = this;

                messageObj._showEdit = (messageObj._showEdit !== true);

                self.$forceUpdate();

            },

            /**
             * Sends a message to the current conversation
             */
            sendMessage: function () {

                var self = this,
                    returned;

                if (self.newMessage.length === 0)
                    return;

                self.isLoading = true;

                self.apiRequest({
                    type: 'message_create',
                    thread_id: self.currentConversation.thread_id,
                    content: self.newMessage
                }).done(function (result) {

                    self.isLoading = false;
                    returned = JSON.parse(result);

                    self.alert = {
                        type: returned.type,
                        message: returned.message
                    };

                    if (returned.type !== 'success')
                        return;

                    self.newMessage = '';

                    self.showConversation(self.currentConversation, false);

                    self.autoScroll();


                });

            },

            /**
             * Deletes a given message
             *
             * @param {object} messageObj
             */
            deleteMessage: function (messageObj) {

                var self = this,
                    returned;

                self.isLoading = true;

                self.apiRequest({
                    message_id: messageObj.id,
                    type: 'message_delete'
                }).done(function (result) {

                    self.isLoading = false;

                    returned = JSON.parse(result);

                    self.alert = returned;

                    self.showConversation(self.currentConversation, false);

                });

            },

            /**
             * Edits a given message
             *
             * @param {object} messageObj
             */
            saveMessage: function (messageObj) {

                var self = this,
                    returned;

                self.isLoading = true;

                self.apiRequest({
                    type: 'message_edit',
                    content: messageObj.content,
                    message_id: messageObj.id
                }).done(function (result) {

                    self.isLoading = false;
                    returned = JSON.parse(result);

                    self.alert = returned;

                    self.showConversation(self.currentConversation, false);

                });

            },

            /**
             * Displays a single conversation
             *
             * @param {object} conversation
             * @param {boolean} isInRefresh
             */
            showConversation: function (conversation, isInRefresh) {

                var self = this,
                    returned;

                isInRefresh = (typeof isInRefresh === 'undefined') ? false : isInRefresh;

                if (!isInRefresh) self.isLoading = true;

                self.apiRequest({
                    type: 'conversation_get',
                    thread_id: conversation.thread_id,
                    page: self.currentMessagesPage
                }).done(function (result) {

                    self.isLoading = false;

                    returned = JSON.parse(result);

                    if (returned.type === 'error') {
                        self.alert = returned;
                        return;
                    }

                    self.showCurrentConversation = true;
                    self.currentConversation = conversation;
                    self.currentMessages = returned.messages;

                    // We show the conversation even though it was hidden
                    var wofficeData = JSON.parse(window.localStorage.getItem('woffice'));
                    wofficeData = (wofficeData) ? wofficeData : {};
                    wofficeData.chatHiddenConversations = (wofficeData.chatHiddenConversations) ? wofficeData.chatHiddenConversations : [];
                    for (var i = 0; i < wofficeData.chatHiddenConversations.length; i++) {
                        if (conversation.thread_id !== wofficeData.chatHiddenConversations[i])
                            continue;
                        wofficeData.chatHiddenConversations.splice(wofficeData.chatHiddenConversations[i], 1);
                    }
                    window.localStorage.setItem('woffice', JSON.stringify(wofficeData));


                    // We set the tooltips and popovers
                    setTimeout(function () {

                        // Tooltips
                        Woffice.tooltips.start();

                        // Popover
                        var $popover = Woffice.$('.show-conversation-meta');
                        $popover.attr('data-content', Woffice.$('.conversation-meta-wrapper').html());
                        $popover.popover({
                            html: true,
                            placement: "bottom",
                            content: "Hey"
                        }).on('show.bs.popover', function () {
                            setTimeout(function () {
                                Woffice.tooltips.start();
                                Woffice.$('.conversation-meta').find('a.btn.btn-danger').on('click', function () {
                                    self.deleteConversation(self.currentConversation);
                                });
                            }, 200);
                        });

                        if (self.exchanger.has_emojis) {
                            // Emoji Picker
                            Woffice.$('.alka-chat-new-message-wrapper textarea').emojiPicker({
                                width: '300px',
                                height: '200px',
                                iconBackgroundColor: '#e4e4e8',
                                iconColor: 'black'
                            });
                        }

                        if (!isInRefresh)
                            self.autoScroll();

                    }, 500);

                });

            },

            /**
             * Auto scroll to the last message
             */
            autoScroll: function () {
                setTimeout(function () {
                    var $modalBody = Woffice.$('.alka-chat-modal-body'),
                        height = $modalBody[0].scrollHeight;
                    $modalBody.scrollTop(height);
                }, 200);
            },

            /**
             * Closes the current conversation modal
             */
            closeCurrentConversation: function () {
                var self = this;
                self.showCurrentConversation = false;
                self.currentConversation = null
            },

            /**
             * Paginates the messages
             *
             * @param {integer} index
             */
            messagesPaginate: function (index) {

                var self = this;

                self.currentMessagesPage = self.currentMessagesPage + index;
                self.showConversation(self.currentConversation, false);

            },

            /**
             * Sets the a member to the new conversation participant IDs array
             * This is triggered when the member is clicked from the auto-select list
             *
             * @param {object} member
             */
            setConversationParticipant: function (member) {

                var self = this;

                self.newConversationParticipants.push(member);
                self.newConversationSearch = '';
                self.newConversationPotentialParticipants = [];

            },

            /**
             * Deletes a given conversation
             *
             * @param {object} conversation
             */
            deleteConversation: function (conversation) {

                var self = this,
                    returned;

                self.isLoading = true;

                self.apiRequest({
                    thread_id: conversation.thread_id,
                    type: 'conversation_delete'
                }).done(function (result) {

                    self.isLoading = false;
                    returned = JSON.parse(result);

                    self.alert = returned;

                    self.showCurrentConversation = false;
                    self.currentConversation = null;
                    self.currentMessages = [];

                    var $popover = Woffice.$('.show-conversation-meta');
                    $popover.popover('hide');

                    self.fetchConversations(false);

                });

            },

            /**
             * Creates a new conversation
             */
            newConversation: function () {

                var self = this,
                    returned;

                if (self.newConversationParticipants.length === 0)
                    return;

                self.isLoading = true;

                // We only send the IDs to the API
                var participants = [];
                participants.push(self.exchanger.current_user);
                self.newConversationParticipants.forEach(function(participant) {
                    participants.push(participant.value);
                });

                // We make sure there is no current conversation otherwise, we open it
                var alreadyExists = false;

                self.conversations.forEach(function(conversation) {
                    if (!alreadyExists) {
                        var common = 0;

                        conversation.participants.forEach(function (participant) {
                            if (participant._id !== self.exchanger.current_user && (participants.indexOf(participant._id) !== -1 || participants.indexOf(participant._id.toString()) !== -1))
                                common++;
                        });

                        // We add 1 to add up the current user
                        var match = common + 1;

                        if (match === participants.length && match === conversation.participants.length) {
                            self.showNewConversation = false;
                            self.showConversation(conversation, false);
                            alreadyExists = true;
                        }
                    }
                });

                if (alreadyExists)
                    return;


                self.apiRequest({
                    type: 'message_create',
                    participants: participants,
                    content: self.exchanger.first_message
                }).done(function (result) {
                    self.isLoading = false;
                    returned = JSON.parse(result);

                    self.alert = returned;

                    if (returned.type !== 'success')
                        return;

                    self.newConversationParticipants = [];
                    self.showNewConversation = false;
                    self.showCurrentConversation = false;

                    self.fetchConversations(false);
                });

            },

            /**
             * Fetch all conversations for the current user from the backend
             *
             * @param {boolean} isInRefresh
             */
            fetchConversations: function (isInRefresh) {

                var self = this,
                    returned;

                isInRefresh = (typeof isInRefresh === 'undefined') ? false : isInRefresh;

                self.isLoading = true;

                self.apiRequest({
                    type: 'conversation_list',
                    user_id: self.exchanger.current_user
                }).done(function (result) {

                    self.isLoading = false;

                    returned = JSON.parse(result);
                    if (returned.type === 'error') {
                        self.alert = returned;
                        return;
                    }

                    self.conversations = returned.threads.threads;
                    self.convPagination.totalPages = Math.round(self.conversations.length / self.convPagination.perPage);
                    if (!isInRefresh)
                        self.convPagination.currentPage = 1;

                    setTimeout(function () {
                        Woffice.tooltips.start();
                    }, 500);

                });

            },

            /**
             * Changes the chat wrapper state
             */
            switchState: function () {

                var self = this;

                self.isOpen = !self.isOpen;

            },

            /**
             * Starts an action
             *
             * @param {string} actionId
             */
            startAction: function (actionId) {

                var self = this;

                self.currentAction = actionId;

                if (actionId === 'new_conversation') {
                    self.showCustomTab = false;
                    self.showNewConversation = !self.showNewConversation;
                } else if (actionId === 'refresh') {
                    self.refresh();
                } else if (actionId === 'custom_tab') {
                    self.showNewConversation = false;
                    self.customTab('show');
                }

            },

            /**
             * Refresh the chat
             */
            refresh: function () {
                var self = this;
                self.fetchConversations(true);
                self.updateLastCheck();
                if (self.currentConversation) {
                    self.showConversation(self.currentConversation, true);
                }
            },

            /**
             * Make an API request to the server (which will forward it)
             *
             * @param {object} payload
             */
            apiRequest: function (payload) {

                return Woffice.$.ajax({
                    url: Woffice.data.ajax_url.toString(),
                    type: 'POST',
                    data: {
                        'action': 'woffice_alka_chat',
                        '_nonce': Woffice.data.alka_chat.nonce,
                        'api_payload': payload
                    }
                });

            },

            /**
             * Auto populates the member list on typing
             */
            autoFetchMembers: function () {

                var self = this;

                self.isLoading = true;

                if (self.newConversationSearch.length < 3)
                    return;

                Woffice.$.ajax({
                    url: Woffice.data.ajax_url.toString(),
                    type: 'GET',
                    data: {
                        'action': 'woffice_members_suggestion_autocomplete',
                        'term': self.newConversationSearch
                    },
                    success: function (result) {
                        self.isLoading = false;
                        var potentielParticipants = JSON.parse(result),
                            validatedParticipants = [];
                        potentielParticipants.forEach(function (participant) {
                            var alreadyThere = false;
                            for (var i = 0; i < self.newConversationParticipants.length; i++) {
                                if (self.newConversationParticipants[i].value === participant.value) {
                                    alreadyThere = true;
                                    break;
                                }
                            }
                            // If not current user & not already present
                            if (parseInt(participant.value) !== self.exchanger.current_user && !alreadyThere) {
                                validatedParticipants.push(participant);
                            }
                        });
                        self.newConversationPotentialParticipants = validatedParticipants;
                    }
                });

            },

            /**
             * Set the number of conversations loaded per page according to page width
             */
            setPerPage: function () {

                var self = this;

                // The button and spacing:
                var margin = 750;

                // 90px is the size of the conversation thumbnail + margin
                self.convPagination.perPage = Math.round((window.innerWidth - margin) / 90);

                if (self.convPagination.perPage < 0)
                    self.convPagination.perPage = 2;

            },

            /**
             * Whether a given conv is displayed in the bullet navigation
             *
             * @param {int} index
             * @param {object} conversation
             *
             * @return {boolean}
             */
            isConvDisplayed: function (index, conversation) {

                var self = this;
                var maxIndex = self.convPagination.currentPage * self.convPagination.perPage;
                var minIndex = maxIndex - self.convPagination.perPage;

                var wofficeData = JSON.parse(window.localStorage.getItem('woffice'));
                wofficeData = (wofficeData) ? wofficeData : {};
                wofficeData.chatHiddenConversations = (wofficeData.chatHiddenConversations) ? wofficeData.chatHiddenConversations : [];

                return (index >= minIndex  && index <= maxIndex) && wofficeData.chatHiddenConversations.indexOf(conversation.thread_id) === -1;

            },

            /**
             * Returns the message sender's Avatar HTML for the current conversation
             *
             * @param {integer} sender_id
             */
            getAvatar: function (sender_id) {

                var self = this,
                    avatar = '';

                if (!self.currentConversation)
                    return avatar;

                self.currentConversation.participants.forEach(function (participant) {
                    if (parseInt(participant._id) === parseInt(sender_id))
                        avatar = participant._avatar;
                });

                return avatar;

            },

            /**
             * Handles the custom tab state
             * It's displayed by default if it exist and we save the state into the local storage
             *
             * @param {string} action - start, show, hide
             */
            customTab: function (action) {

                var self = this;

                if (typeof(Storage) === "undefined")
                    return;

                var wofficeData = JSON.parse(localStorage.getItem("woffice"));

                var currentState = (wofficeData !== null && typeof(wofficeData.alka_chat_state) !== 'undefined') ? wofficeData.alka_chat_state : false;

                if (action === 'start') {
                    if (currentState === false || currentState === 'not_displayed') {
                        self.showCustomTab = true;
                    }
                } else if (action === 'show') {
                    self.showCustomTab = true;
                } else {
                    self.showCustomTab = false;
                }

                var newState = 'displayed';

                if (wofficeData === null)
                    wofficeData = {};

                wofficeData.alka_chat_state = newState;

                // We save it back
                localStorage.setItem("woffice", JSON.stringify(wofficeData));

            },

            /**
             * Hide a given cnoversation
             *
             * @param {Object} conversation
             */
            hideConversation: function (conversation) {

                var wofficeData = JSON.parse(window.localStorage.getItem('woffice'));
                wofficeData = (wofficeData) ? wofficeData : {};
                wofficeData.chatHiddenConversations = (wofficeData.chatHiddenConversations) ? wofficeData.chatHiddenConversations : [];
                wofficeData.chatHiddenConversations.push(conversation.thread_id);

                window.localStorage.setItem('woffice', JSON.stringify(wofficeData));

                this.$forceUpdate();

                this.setBullets();

            }

        }

    });

};

/*
 * Start it up!
 */
new alkaChat();