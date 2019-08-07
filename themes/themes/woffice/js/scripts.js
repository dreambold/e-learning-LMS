/**
 * Main Woffice object
 *
 * @since 2.5.0
 * @type {{}}
 */
var Woffice = {

    /**
	 * Initialize the Woffice's JS
	 *
	 * @param {jQuery} $
     */
    init: function ($) {

        "use strict";

    	var self = this;

    	self.$ = (typeof $ === 'undefined') ? jQuery : $;
    	self.data = (typeof WOFFICE !== 'undefined') ? WOFFICE : {};

        /*
         * Markup attributes
         */
        self.$body           = self.$('body');
        self.$main           = self.$("#main-content");
        self.$navigation     = self.$("#navigation");
        self.$navbar         = self.$('#navbar');
        self.$adminBar       = self.$('#wpadminbar');
        self.$searchTrigger  = self.$("#search-trigger");
        self.$formMembers    = self.$("#members-directory-form");
        self.$mainSearch     = self.$("#main-search");
        self.$navWrapper     = self.$("#nav-languages");
        self.$navTrigger     = self.$("a#nav-trigger");
        self.$scrollHelper   = self.$('#can-scroll');
        self.$rightSidebar   = self.$("#right-sidebar");
        self.$sidebarTrigger = self.$("a#nav-sidebar-trigger");

        // This override the Menu Threshold set in the Theme Settings. It would be a great idea to just remove that option
        if( Woffice.$body.hasClass('menu-is-horizontal') ) {
            Woffice.data.menu_threshold = 992;
        } else {
            Woffice.data.menu_threshold = 450;
        }

        self.buddyPress.clearCache();

        /*
		 * When the page is starting
         */
        self.$(window).load(function(){

            self.userSidebar.start();
            self.alerts.start();
            self.customScrollbars.start();
            self.wooCommerce.start();
            self.tooltips.start();
            self.frontend.start();
            self.sliders.start();
            self.animatedNumbers.start();
            self.footer.start();
            self.menu.start();
            self.masonryLayout.start();
            self.navigation.start();
            self.sidebar.start();

		});

        /*
         * When the page is re sized
         */
        self.$(window).resize(function () {

        	self.wooCommerce.resize();
        	self.menu.submenus();
            self.masonryLayout.refresh();
            self.navigation.resize();
            self.sidebar.resize();

        });

        /*
         * Watchers, binding events after page load
         */
        {
            self.userSidebar.watch();
            self.alerts.watch();
            self.searchBar.watch();
            self.advancedSearch.watch();
            self.rolesFilter.watch();
            self.wooCommerce.watch();
            self.languageSwitcher.watch();
            self.sliders.watch();
            self.buddyPress.watchOldActivities();
            self.buddyPress.watchActivityMeta();
            self.buddyPress.watchAdvancedSearch();
            self.scrollTop.watch();
            self.linkEffect.watch();
            self.masonryLayout.watch();
            self.navigation.watch();
            self.sidebar.watch();
            self.buddyPressNotifications.watch();
        }

    },

    /**
     * Creates a new loader
     *
     * @param {jQuery} $el
     * @param {object} newOptions
     * @return {Spinner}
     */
    loader: function ($el, newOptions) {

        var self = this;

        self.isLoading = false;

        self.options = {
            color: '#757575',
            className: 'woffice-spinner',
            top: '50%',
            speed: 1.6
        };

        // Set default versions
        if(typeof newOptions !== 'undefined')
            Woffice.$.extend( self.options, newOptions );

        /**
         * Creates the loader
         */
        self.create = function () {

            // Loader object
            self.loader = new Spinner(self.options);
            self.loader.spin($el[0]);
            self.loader.element = $el;

            // Element change
            self.loader.element.addClass('has-loader');

            // Watcher variable
            self.isLoading = true;

        };

        /**
         * Removes a loader
         */
        self.remove = function () {

            // Element change
            self.loader.element.removeClass('has-loader');

            // Stop the spinner
            self.loader.stop();

            // Remove the loader
            self.loader = null;

            // Watcher variable
            self.isLoading = false;

        };

        self.create();

        return self;

    },

    /**
     * Main alerts
     */
    alerts: {
        start: function() {

            var $ = Woffice.$,
                self = this;

            var alerts = $('.woffice-main-alert');

            if(alerts.length === 0) {
                return;
            }

            alerts.each(function () {
                var closeTrigger = $(this).find('.woffice-alert-close');
                var alert = $(this);
                var notime = alert.hasClass('no-timeout');

                // We either close it teh button is clicked
                closeTrigger.on('click', function () {
                    alert.slideUp();
                });
                
                if (!notime) {
                    // Or after x sec
                    setTimeout(function () {
                        alert.slideUp();
                    }, parseInt(Woffice.data.alert_timeout));
                }

            });

        },

        watch: function () {

            var $ = Woffice.$;

            $('.woffice-alert-close ').on('click', function () {

                $(this).closest('.woffice-main-alert').slideUp();

            });

        }

    },

    /**
	 * User sidebar functions
     */
    userSidebar: {

        /**
		 * Set the global state on the page load
         */
    	start: function () {

    		var $ = Woffice.$,
			$userSidebar = $("#user-sidebar");

    		// Layout
            if($userSidebar.length >0 ){
                var topbarHeight = $("#topbar").height(),
                    menuHeight = $("#navbar").height(),
                    sidebarTop = 0;
                if($("topbar").hasClass("topbar-closed")){
                    sidebarTop = menuHeight + topbarHeight;
                }
                else{
                    sidebarTop = menuHeight;
                }
                $userSidebar.css("padding-top",sidebarTop);
            }

            // Binding events
            $("#user-sidebar nav ul li.menu-parent a, #user-sidebar #dropdown-user-menu li.menu-item-has-children a").bind('click', false);
            $("#user-sidebar nav ul li.menu-child a, #user-sidebar #dropdown-user-menu li.menu-item-has-children ul a").unbind('click', false);

        },

        /**
		 * Watch any toggling action
         */
        watch: function () {

            var $ = Woffice.$;

            // This is for the main layout: display the sidebar or not
            $(".bp_is_active #user-thumb, #user-close").on("click",function(){
                $("#nav-user, #user-sidebar").toggleClass("active");
            });

            // Submenus within the sidebar
            $("#user-sidebar nav ul li.menu-parent a, #user-sidebar #dropdown-user-menu li.menu-item-has-children a").on("click",function(){
                $(this).toggleClass("dropdownOn");
                $(this).parent("li").toggleClass("dropdownOn");
                $(this).parent("li").find('ul').slideToggle();
            });

        }

    },

    /**
	 * Custom scroll bars
     */
    customScrollbars: {

        /**
		 * Declares the scroll bars using mCustomScrollbar
         */
		start:  function () {

            var $ = Woffice.$;

            /*
             * Navigation scroll bars
             */
            $("body.menu-is-vertical #navigation").mCustomScrollbar({
                axis: "y",
                theme: "minimal"
            });
            if ( window.matchMedia('(max-width: ' + Woffice.data.menu_threshold + 'px)').matches ) {
                $("body.menu-is-horizontal #navigation").mCustomScrollbar({
                    axis: "y",
                    theme: "minimal"
                });
            }

            /*
             * User menu scroll bar
             */
            $("#user-sidebar").mCustomScrollbar({
                axis: "y",
                theme: "minimal-dark"
            });

            /*
             * WooCommerce box scroll bar
             */
            var $wooCommerceCart = $("#woffice-minicart-top");
            if ($wooCommerceCart.length) {
                $wooCommerceCart.mCustomScrollbar({
                    axis: "y",
                    theme: "minimal-dark"
                });
            }

            /*
             * Right sidebar's custom scroll bar
             * See self.sidebar() for more details
             */
            $("#right-sidebar").mCustomScrollbar({
                axis:"y",
                theme:"minimal-dark",
                mouseWheel:{ deltaFactor: 100 },
                callbacks:{
                    onInit:function(){
                        $('#can-scroll').on("click",function(){
                            if(!$('#main-content').hasClass('sidebar-hidden')){
                                $('#can-scroll').show();
                            }
                            if($(this).hasClass('clicked')){
                                $('#right-sidebar').mCustomScrollbar("scrollTo","top");
                                $(this).removeClass('clicked');
                            }
                            else{
                                $('#right-sidebar').mCustomScrollbar("scrollTo","bottom");
                                $(this).addClass('clicked');
                            }
                        });
                    },

                    onUpdate:function(){
                        if ($('#main-content').height() >= $('#right-sidebar')[0].scrollHeight){
                            $('#can-scroll').addClass('clicked');
                        } else {
                            $('#can-scroll').removeClass('clicked');
                        }
                    },
                    onScroll:function(){
                        if ($('#main-content').height() >= $('#right-sidebar')[0].scrollHeight){
                            $('#can-scroll').addClass('clicked');
                        } else {
                            $('#can-scroll').removeClass('clicked');
                        }
                    }
                }
            });

        }

    },

    /**
	 * Search bar
     */
    searchBar: {

        /**
         * Make a search query
         *
         */
        search: _.debounce(function() {
            if (!Woffice.data.has_live_search || Woffice.data.has_live_search !== '1') {
                return;
            }

            var $ = Woffice.$;
            var $results = Woffice.$mainSearch.find('.woffice-search-results');

            var value = Woffice.$mainSearch.find('input#s').val();

            if (!value || (value && value.length < 3)) {
                return false;
            }

            var loader = new Woffice.loader($results, { left: '45%'});

            $.ajax({
                type: "POST",
                url: Woffice.data.ajax_url.toString(),
                data: {
                    action: 'woffice_search',
                    search: value,
                    types: 'all'
                },
                success: function (data) {
                    data = JSON.parse(data);

                    var markup = '';

                    $.each(data, function(key, group){

                        markup += '<div class="woffice-search-results__group" id="woffice-search-results__group--'+ key +'">';

                        markup += '<h3><i class="fa '+ group.icon +'"></i> '+ group.label +'</h3>';

                        if (group.items && group.items.length > 0) {
                            markup += '<ul>';
                            for (var i = 0; i < group.items.length; i++) {
                                var item = group.items[i];

                                markup += '<li class="woffice-search-results__item clearfix" id="woffice-search-results__item--'+ item.id +'" data-type="'+ key +'">';
                                    markup += '<div class="float-left">';
                                        markup += '<span class="woffice-search-results__item__title">'+ item.title +'</span>';
                                        if (item.meta) {
                                            var length = 150;
                                            if (item.meta.length > length) {
                                                item.meta = item.meta.substring(0, length) + '...';
                                            }

                                            markup += '<span class="woffice-search-results__item__meta">' + item.meta + '</span>';
                                        }
                                    markup += '</div>';
                                    markup += '<div class="float-right">';
                                        markup += '<a href="'+ item.link +'" class="btn btn-default"><i class="fa fa-arrow-right"></i></a>';
                                    markup += '</div>';
                                markup += '</li>';
                            }
                            markup += '</ul>';
                        }

                        markup += '</div>';

                    });

                    $results.html(markup);

                    loader.remove();
                }
            });
        }, 500),

        /**
         * Set the width of the main search
         */
        setSize: function() {
            if (Woffice.$body.hasClass('menu-is-vertical') && !Woffice.$navigation.hasClass('navigation-hidden')) {
                Woffice.$mainSearch.css('width', (Woffice.$body.width() - Woffice.$navigation.width()));
            } else {
                Woffice.$mainSearch.css('width', (Woffice.$body.width()));
            }
        },

        /**
         * Prevent default form submit and trigger ajax search
         *
         * @param {Object} e
         */
        searchSubmit: function(e) {
            e.preventDefault();
            Woffice.searchBar.search();
        },


        /**
		 * Whenever someone click on the icon
         */
    	watch: function() {

    		var $ = Woffice.$;

            var offset = Woffice.$navbar.height();
            offset = (Woffice.$body.hasClass('admin-bar')) ? offset + 32 : offset;

    		Woffice.$mainSearch.find('input#s').on('keyup', function() {
    		    Woffice.searchBar.search();
            });

            Woffice.searchBar.setSize();

            $(window).resize(function () {
                Woffice.searchBar.setSize();
            });

            Woffice.$searchTrigger.on("click", function(){

                Woffice.$mainSearch.toggleClass('opened');

                if (Woffice.$mainSearch.find('.woffice-search-results').length === 0) {
                    Woffice.$mainSearch.append('<div class="woffice-search-results"></div>');
                } else {
                    Woffice.$mainSearch.find('.woffice-search-results').remove();
                }

                if (Woffice.$mainSearch.hasClass('opened')) {
                    Woffice.$mainSearch.css('top', offset);
                    Woffice.$searchTrigger.find('i.fa').removeClass('fa-search');
                    Woffice.$searchTrigger.find('i.fa').addClass('fa-times');
                    Woffice.$mainSearch.find('form').on('submit', Woffice.searchBar.searchSubmit);
                } else {
                    Woffice.$mainSearch.css('top', -offset);
                    Woffice.$searchTrigger.find('i.fa').addClass('fa-search');
                    Woffice.$searchTrigger.find('i.fa').removeClass('fa-times');
                    Woffice.$mainSearch.find('form').off('submit', Woffice.searchBar.searchSubmit);
                }

                $('html,body').animate({ scrollTop: 0 }, 'fast');
                document.getElementById("s").focus();
                return false;

            });

		}

	},

    /**
     * Roles filters for Members
     */
    rolesFilter: {
        /**
         * Whenever someone click on a dropdown's link
         */
        watch: function() {
            var $ = Woffice.$;
            var $dropdownItems = $('#woffice-roles-filter a.dropdown-item');

            $dropdownItems.on('click', function(e){
                e.preventDefault();

                var requestObj = {
                    scope:  'roles',
                    filter: 'active',
                    action: 'members_filter',
                    object: 'members'
                };

                $dropdownItems.removeClass('active');
                $(e.target).addClass('active');

                requestObj['role'] = $(e.target).data('role');

                window.bp.Nouveau.objectRequest(requestObj);
            });
        }
    },

    /**
     * Advanced Search for Members
     */
    advancedSearch: {

        /**
         * Whenever someone click on the icon
         */
        watch: function() {
            var $ = Woffice.$;

            $('#advanced-search-submit').on("click", function(e){
                e.preventDefault();

                var requestObj = {
                    scope:  'advanced-search',
                    filter: 'active',
                    action: 'members_filter',
                    object: 'members'
                };

                requestObj['advanced-search-submit'] = true;

                var formObj = {};
                var $inputs = $('#woffice-members-advanced-search').find('input, select');

                $inputs.each(function() {
                    formObj[$(this).attr('name')] = $(this).val();
                });

                $.extend(requestObj, { extras: formObj });

                window.bp.Nouveau.objectRequest(requestObj);
            });

            $('#advanced-search-reset').on("click",function(e){
                e.preventDefault();

                var $inputs = $('#woffice-members-advanced-search').find('input, select');

                $inputs.each(function() {
                    $(this).val('');
                });
            });
        }

    },

    /**
	 * WooCommerce
     */
    wooCommerce : {

        flexsliderWoo: null,

        /**
		 * Watch for the cart's toggling
         */
    	watch: function () {

    		var $ = Woffice.$;

            $("#nav-buttons").on("click","#nav-cart-trigger.active", function(){
                $(this).toggleClass("clicked");
                $("#woffice-minicart-top").slideToggle();
            });

        },

        /**
		 * Get the number of items according to the width
         */
    	getGridSize: function () {

            return 	(window.innerWidth < 400) ? 1 :
                	(window.innerWidth < 600) ? 2 :
                    (window.innerWidth < 910) ? 3 : 4;

        },

        /**
		 * Starts the product carousels
         */
    	start: function () {

    		var $ = Woffice.$,
                self = this;

            $('.flexslider > .woocommerce').flexslider({
                animation: "slide",
                animationLoop: false,
                selector: ".products > li.product",
                itemWidth: 210,
                itemMargin: 0,
                controlNav: false,
                move: 0,
                slideshow: false,
                minItems: self.getGridSize(), // use function to pull in initial value
                maxItems: self.getGridSize(), // use function to pull in initial value
                start: function (slider) {
                    self.flexsliderWoo = slider; //Initializing flexslider here.
                }
            });

        },

        /**
		 * Changes the number of items according to the width
         */
        resize: function () {

        	var self = this,
				$ = Woffice.$;

            if ($('.flexslider > .woocommerce').length > 0) {
                var gridSize = self.getGridSize();
                if (self.flexsliderWoo !== null) {
                    self.flexsliderWoo.vars.minItems = gridSize;
                    self.flexsliderWoo.vars.maxItems = gridSize;
                }
            }

        }

	},

    /**
	 * Language switcher
     */
    languageSwitcher: {

    	watch: function () {

            Woffice.$navWrapper.find('a').on("click", function(){
                Woffice.$navWrapper.find('ul').slideToggle();
            });

        }

	},

	/**
	 * Bootstrap tooltips
	 */
	tooltips: {

    	start: function () {

    		var $ = Woffice.$;

            $('[data-toggle="tooltip"]').tooltip();

        }

	},

    /**
	 * Theme form actions and frontend actions
     */
	frontend: {

        /**
         * Handle the tab navigation (Projects & Wiki)
         */
        tabNav: function() {
            var $ = Woffice.$;
            var $navWrapper = $('.woffice-tab-layout__nav');
            var $contentWrapper = $('.woffice-tab-layout__content');

            $contentWrapper.find('.woffice-tab-layout__tab').addClass('d-none');
            $contentWrapper.find('.woffice-tab-layout__tab').first().removeClass('d-none');

            $navWrapper.on('click', 'li', function(e){
                var $item = $(e.target);

                $navWrapper.find('li').removeClass('active');

                if($(e.target).closest('a').length){
                    $item = $item.closest('li');
                }

                $item.addClass('active');

                $item.trigger('woffice.tab.change', [$item.data('tab')]);

                var loader = new Woffice.loader($contentWrapper);

                $contentWrapper.find('.woffice-tab-layout__tab').addClass('d-none');

                setTimeout(function () {
                    $contentWrapper.find('.woffice-tab-layout__tab[data-tab="'+ $item.data('tab') +'"]').removeClass('d-none');

                    loader.remove();
                }, 1000);
            });

            // Default tab if in the link, use the last part of the div as the unique ID
            var anchor = window.location.hash.substr(1);

            if (anchor.length > 0) {
                var parts = anchor.split('-');

                if (parts.length > 0) {
                    var name = parts[parts.length - 1];

                    $navWrapper.find('li[data-tab="'+ name +'"]').trigger('click');
                }
            }

        },

		checkboxes: function () {
            var $ = Woffice.$;

            $('#page-wrapper .wpcf7-checkbox input:checkbox,#page-wrapper .wpcf7-radio input:radio').change(function(){
                if($(this).is(":checked")) {
                    $(this).parent("label").addClass("checked");
                } else {
                    $(this).parent("label").removeClass("checked");
                }
            });
        },

        toggle: function() {
            var $ = Woffice.$;
            var $content = $('.frontend-wrapper__content');

            if ($content.length === 0) {
                return;
            }

            $content.not('.frontend-wrapper__content--revealed').hide();

            $('.frontend-wrapper__toggle').on('click', function(e){
                e.stopImmediatePropagation();

                var loader = new Woffice.loader($('.frontend-wrapper'));

                if ($(e.target).data('action') === 'display') {
                    $('.frontend-wrapper__toggle[data-action="display"]').hide();
                    setTimeout(function () {
                        $('.frontend-wrapper__content').slideToggle();
                        loader.remove();
                    }, 1000);
                } else {
                    $('.frontend-wrapper__content').slideToggle();
                    setTimeout(function () {
                        $('.frontend-wrapper__toggle[data-action="display"]').show();
                        loader.remove();
                    }, 1000);
                }

                return false;
            });
        },

        initAutocompleteMap : function() {
            // Create the autocomplete object using Google Maps API, restricting the search to geographical location
            var input = document.getElementById(Woffice.data.input_location_bb);
            // We check that this input exist on the dom to avoid any error
            if(input === null)
                return;
            autocomplete = new google.maps.places.Autocomplete(
                (input),
                {types: ['geocode']}
            );

            if (navigator.geolocation && Woffice.data.user_id !== '0') {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        },

		start: function () {
			var self = this;

            self.tabNav();
			self.checkboxes();
            self.toggle();
            self.initAutocompleteMap();
        }

	},

    /**
	 * Sliders
     */
    sliders: {

    	watch: function () {

    		var $ = Woffice.$,
                self = this;

            $("#dashboard .widget a, #nav-trigger, #nav-sidebar-trigger").on('click',function(){
                setTimeout(function () {
                    // Flexslider
                    $('#dashboard').find('.widget_woffice_funfacts .flexslider').flexslider();
                    // Rev slider
                    self.refreshRevSlider();
                }, 2000);
            });

        },

        /**
         * Refreshes Revolution slider on layout changes
         */
        refreshRevSlider: function () {

            var $ = Woffice.$,
                $slider = $(".rev_slider"),
                sliderOnPage = (typeof $slider.revredraw === 'function');

            if(sliderOnPage) {
                setTimeout(function () {
                    $slider.revredraw();
                }, 1000);
            }

        },

        /**
		 * Creates the fun fact and "familiers" sliders
		 * Using the Flexslider plugin
         * We also bind the revolution slider
         */
		start: function () {

            var $ = Woffice.$,
                self = this;

            $('.widget_woffice_funfacts .flexslider').flexslider({
                animation: "slide",
                animationLoop: true,
                slideshow: true,
                directionNav: false,
                selector: ".slides > li",
                smoothHeight: false,
                start: function(){
                    $('.widget_woffice_funfacts .flexslider').resize();
                }
            });

            $('#familiers').find('.flexslider').flexslider({
                animation: "slide",
                animationLoop: true,
                selector: ".slides > li",
                itemWidth: 80,
                itemMargin: 0,
                controlNav: false,
                directionNav: false,
                minItems: 0,
                move: 0,
                slideshow: false
            });

            self.refreshRevSlider();

        }

	},

    /**
	 * Animated numbers animation
     */
	animatedNumbers: {

    	start: function () {
    		var $ = Woffice.$;

            $('.animated-number h1').countTo({
                speed: 4000
            });

        }

	},

    /**
	 * BuddyPress customizations
     */
	buddyPress: {

        /**
         * Clear advanced search cache when page reloading
         */
	    clearCache: function () {
            var $ = Woffice.$;

            if (!$('body').hasClass('directory members')) {
                return;
            }

            var $selector = $('#members-order-by');
            var order     = ($selector.length) ? $selector.find('option').first().val() : 'active';

            var requestObj = {
                scope:  'advanced-search',
                filter: order,
                action: 'members_filter',
                object: 'members'
            };

            $.extend(requestObj, { extras: {} });

            window.bp.Nouveau.objectRequest(requestObj);

            setTimeout(function () {
                var $advancedSearch = $('#members-advanced-search');

                if ($advancedSearch.length) {
                    $advancedSearch.toggleClass('selected');
                }
            }, 1000);
        },

        /**
         * Watch the advanced search button
         */
	    watchAdvancedSearch: function () {
            var $ = Woffice.$;

            $("#members-advanced-search").on('click', function () {
                $(this).toggleClass('active');
                $("#woffice-members-advanced-search").slideToggle('slow');
            });
        },

        /**
         * It allows to display old activity comments, it fixes the compatibility issue between BuddyPress and Bootstrap
         */
		watchOldActivities: function() {
			var $ = Woffice.$;

            $('.activity-comments .show-all').on("click", function () {

                $(this).closest('.activity-comments').find('li').removeClass('hidden');

            });
        },

        /**
         * We bind correctly the icon click for BuddyPress
         */
        watchActivityMeta: function () {
            var $ = Woffice.$;

            $('.activity-meta i').on("click", function (e) {
                e.preventDefault();
                $(this).parent().trigger('click');
            });
        }

	},

    /**
	 * Scroll to the top action
     */
	scrollTop: {

		watch: function () {

            var $ = Woffice.$;

            $("#scroll-top").on("click",function(){
                //SCROLL TO TOP
                $('html, body').animate({
                    scrollTop: $( $.attr(this, 'href') ).offset().top
                }, 500);
                return false;
            });

        }

	},

    /**
	 * Material design inspired effect on links click
     */
    linkEffect: {

    	elements: [
			'#navbar a',
			'.main-menu li > a',
			'a.btn.btn-default',
			'#content-container #buddypress button',
			'#buddypress .button-nav li a,#main-content button[type="submit"]',
			'input[type="submit"]',
			'#user-sidebar nav ul li a',
			'#buddypress #item-nav div.item-list-tabs ul li a',
			'#woffice-login .login-submit input[type="submit"]',
			'#main-content input[type="button"],#learndash_next_prev_link a',
			'#content-container .ssfa_fileup_wrapper span'
		],

    	watch: function () {

    		var self = this,
				selector = self.elements.join(", "),
				$ = Woffice.$;

            var ink, d, x, y;

            $(selector).on("click",function(e){
				if($(this).find(".material").length === 0){
					$(this).prepend("<span class='material'></span>");
				}

				ink = $(this).find(".material");
				ink.removeClass("animate");

				if(!ink.height() && !ink.width()){
					d = Math.max($(this).outerWidth(), $(this).outerHeight());
					ink.css({height: d, width: d});
				}

				x = e.pageX - $(this).offset().left - ink.width()/2;
				y = e.pageY - $(this).offset().top - ink.height()/2;

				ink.css({top: y+'px', left: x+'px'}).addClass("animate");
			});

        }

	},

    /**
	 * Footer functions
     */
    footer: {

        /**
		 * Loads the extra footer once the page ready
         */
        loadExtafooterAvatars : function () {

            var $ = Woffice.$,
				$extrafooter = $('#extrafooter[data-woffice-ajax-load=true]');

            if ($extrafooter.length === 0)
            	return;

            var loader = new Woffice.loader($extrafooter.find('#familiers'), { left: '45%'});

			$.ajax({
				type: "POST",
				url: Woffice.data.ajax_url.toString(),
				data: {
					action: 'load_extrafooter_avatars'
				},
				success: function (data) {

					$extrafooter.find('#familiers').html(data);

                    loader.remove();

				}
			});

        },

        /**
		 * Build the footer layout (widgets)
         */
 		footerWidgetsLayout : function() {

            var $ = Woffice.$;

            if (window.matchMedia("(min-width: 992px)").matches) {

                var $widgets_wrapper = $("#main-footer").find("#widgets"),
                    layout = $widgets_wrapper.attr('data-widgets-layout');

                if (typeof layout !== typeof undefined && layout !== false && $widgets_wrapper.length > 0) {

                    layout = layout.split("-");

                    //For each column assign the width depending on the selected layout
                    $widgets_wrapper.find(".widget").each(function (i) {
                        var layout_length = layout.length;

                        $(this).removeClass("col-md-3");
                        $(this).addClass("col-md-" + layout[i % layout_length]);
                    });

                }

            }

        },

        start: function () {

 			var self = this;

            self.loadExtafooterAvatars();
            self.footerWidgetsLayout();

        }

	},

    /**
	 * Menu
	 * including sub menus and mega menus
     */
    menu: {

    	megaMenuTimer: null,

        megaMenuCol: 200,

        edgeLimit: 0,

        /**
		 * Mega menu
         */
    	megaMenu: function () {

    		var self = this,
				$ = Woffice.$;

            if(!Woffice.$body.hasClass('menu-is-horizontal')) {

                setTimeout(function(){
                    $("#main-menu").find("li").each(function() {
                        if ($(this).hasClass("menu-item-has-mega-menu")){
                            var liheight = $(this).innerHeight();
                            var megamenu = $(this).find('div.mega-menu');
                            $(megamenu).css('margin-top', '-'+(liheight)+'px');
                        }
                    });
                }, 2500);

                self.megaMenuCol = 180;

            }

            $('.main-menu > li.menu-item-has-mega-menu').on({
                mouseenter: function(){

                    // COUNT THE NUMBER OF COLUMN
                    var megamenucontainer = $(this).find("div.mega-menu");
                    var numberofrows = megamenucontainer.find("li.mega-menu-col").length;

                    // SIZE -> 180 per rows
                    megamenucontainer.width(numberofrows*(self.megaMenuCol) + 20);
                    // SHOW IT

                    self.megaMenuTimer = setTimeout(function(){
                        megamenucontainer.addClass('open animated');
                    }, 0);

                },
                mouseleave: function(){

                    var megamenucontainer = $(this).find("div.mega-menu");

                    setTimeout(function(){
                        megamenucontainer.removeClass('open');
                    }, 0);

                }
            });

        },

        /**
         * Checks if the submenu elements is placed over the window limit
         * @param who
         */
        calculateEdge: function(who) {

			var $ = Woffice.$,
				self = this,
				elm = $(who).find('ul').first();

			if(elm.length) {

				var off = elm .offset(),
					l = off.left,
					w = elm.width(),
					isEntirelyVisible = (l+ w <= self.edgeLimit);

				if ( ! isEntirelyVisible ) {
					$(who).addClass('edge');
				} else {
					$(who).removeClass('edge');
				}

			}

		},

        /**
		 * Set the submenus
         */
		setSubMenus: function () {

			var $ = Woffice.$;

            $("#main-menu").find("li").each(function() {

                if ($(this).hasClass("menu-item-has-children")){

                    var lineheight = $(this).height();
                    var submenu = $(this).find('.sub-menu');
                    $(submenu).css('margin-top', '-'+(lineheight)+'px');

                }

            });

        },

        /**
		 * This function is run on the page load and resize
         */
		submenus: function () {

            var self = this,
                $ = Woffice.$;

            self.edgeLimit = $('#page-wrapper').width();

            if(
                window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches
                ||
                (Woffice.navigation.isTouchDevice() && window.innerWidth < 1100)
            ) {
                
                $('.main-menu li.menu-item-has-children > a').not(".binded").addClass("binded").on("click",function(){

                    // TODO move this listener out of the first check. In order to avoid the useless nested check
                    if(
                        window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches
                        ||
                        (Woffice.navigation.isTouchDevice() && window.innerWidth < 1100)
                    ) {
                        $(this).toggleClass("mobile-menu-displayed");
                        var parentContainer = $(this).parent("li");
                        if(parentContainer.hasClass('menu-item-has-mega-menu')) {
                            parentContainer.find('> .mega-menu').slideToggle();
                        } else {
                            parentContainer.find('> .sub-menu').slideToggle();
                        }
                        return false;
                    }

                });

            }
            else {

                $('.main-menu li.menu-item-has-children:not(.menu-item-has-mega-menu)').on({

                    mouseenter: function(){
                        var self = this,
                            submenu = $(self).find('.sub-menu').first();

                        // Without this timeout, the function handleClicks wouldn't work
                        setTimeout( function() {
                            submenu.addClass("display-submenu");
                        }, 10);

                        submenu.attr('style', 'margin-top: ' + '-' + $(this).height() + 'px');

                        var scrollTop     = $(window).scrollTop(),
                            elementOffset = submenu.offset().top,
                            distanceFromTop      = (elementOffset - scrollTop),
                            megamenu_height = 0;

                        var edgeOffset = (distanceFromTop  + submenu.height() - $(window).height());

                        if( $(self).hasClass('mega-menu-col')) {

                            megamenu_height = Math.max.apply(null,$(self).closest('.mega-menu').find('.mega-menu-col').map(function () {
                                return $(this).height();
                            }).get());

                            edgeOffset = (distanceFromTop  + megamenu_height - $(window).height());
                        }

                        var isEntirelyVisible = (edgeOffset < 0);

                        if(!isEntirelyVisible) {
                            if( $(self).hasClass('mega-menu-col')) {

                                //$(self).closest('.mega-menu')[0].style.removeProperty('margin-top');
                                $(self).closest('.mega-menu')[0].style.setProperty( 'margin-top', '-' + parseInt( megamenu_height ) + 'px', 'important' );
                            } else {

                                submenu.attr('style', 'margin-top: ' + '-' +parseInt($(self).height() + edgeOffset) + 'px !important');
                            }
                        }


                        Woffice.menu.calculateEdge(this);

                    },

                    mouseleave: function(){

                        var self = this;
                        var submenu = $(self).find('> .sub-menu');

                        submenu.removeClass("display-submenu");
                        $(self).removeClass('edge');
                    }

                });

                $('body').on({

                    mouseleave: function () {

                        setTimeout(function () {

                            var $navigation = $('#navigation');

                            $navigation.find('.display-submenu').removeClass("display-submenu");
                            $navigation.find('.edge').removeClass('edge');

                        }, 100);

                    }

                });

            }

        },

        /**
         * If a menu item has children, block the link redirection if the submenu isn't displayed
         *
         * This functions assumes that if the submenu isn't displayed when clicked, it means that the mousent hasn't been triggered,
         * which should means that you are using a big touch screen (such as a retina tablet).
         */
        handleClicks: function () {

            var $ = Woffice.$;

            // Todo this only works for the second level menu
            $('.main-menu > li.menu-item-has-children > a').on("click",function( event ){
                var $parentContainer = $(this).parent("li");
                if( ! $parentContainer.find('> .sub-menu').hasClass('display-submenu') ) {
                    event.preventDefault();
                }
            });

        },

		start: function () {

		    var self = this,
                $ = Woffice.$;

            self.edgeLimit = $('#page-wrapper').width();
            self.megaMenu();
            self.setSubMenus();
            self.submenus();
            self.handleClicks();
        }

	},

    /**
     * Masonry layouts used across the theme
     */
    masonryLayout: {

        /**
         * Build helper the Masonry layout
         */
        build: function () {

            var $ = Woffice.$,
                $dashboard = $('#dashboard');

            $('.masonry-layout').isotope({
                // options
                itemSelector: '.box',
                layoutMode: 'masonry'
            });

            if ($dashboard.length > 0 ) {
                if (!$dashboard.hasClass('is-draggie')) {
                    $dashboard.isotope();
                }
                /*
                 * Commented from WOF-92.
                 * It's causing the jQuery memory issue.
                 *
                 * $dashboard.find( '.widget_woffice_funfacts .flexslider' ).resize();
                 */
            }

        },

        /**
         * Refresh layout
         */
        refresh: function() {

            var $ = Woffice.$;

            Woffice.masonryLayout.build();

            // fix ratios for resizing the calendar size
            $('.eventon_fullcal').each(function(){
                var cal_width = $(this).width();
                var strip = $(this).find('.evofc_months_strip');
                var multiplier = strip.attr('data-multiplier');

                if(multiplier<0){
                    strip.width(cal_width*3).css({'margin-left':(multiplier*cal_width)+'px'});
                }
                $(this).find('.evofc_month').width(cal_width);
            });

        },

        /**
         * We watch several events
         */
        watch: function () {

            var self = this,
                $ = Woffice.$;

            $("#dashboard").on('click', 'a.evcal_list_a, .widget a, p.evo_fc_day', function(){
                self.refresh();
            });

            $("#nav-trigger, #nav-sidebar-trigger").on('click', function(){
                setTimeout(self.refresh, 600);
            });

        },

        /**
         * Starts the layouts
         */
        start: function () {

            var $ = Woffice.$,
                $list = null,
                $wrapper = $('#buddypress [data-bp-list]');

            $wrapper.bind('bp_ajax_request', function () {
                var loader = new Woffice.loader($wrapper);

                setTimeout(function () {
                   $list = $('#groups-list, #members-list').isotope({
                       itemSelector: 'li.item-entry',
                       layoutMode: 'fitRows'
                   });

                    loader.remove();
                }, 2000);
            });

            $("#nav-trigger, #nav-sidebar-trigger, #item-nav a").on('click',function(){
                setTimeout(function () {
                    $list = $('#groups-list, #members-list').isotope({
                        itemSelector: 'li.item-entry',
                        layoutMode: 'fitRows'
                    });
                }, 2000);
            });

            setTimeout(function () {
                Woffice.masonryLayout.build();
            }, 200);

            setInterval(function(){
                Woffice.masonryLayout.build();
                Woffice.masonryLayout.refresh();
            }, 2000);

        }

    },

    /**
     * Handles the navigation actions
     */
    navigation: {

        cachedWidth: 0,

        /**
         * Whether we are on a touch device or not
         *
         * @return {boolean}
         */
        isTouchDevice: function(){
            return typeof window.ontouchstart !== 'undefined';
        },

        watch: function () {

            var $ = Woffice.$,
                self = this;

            Woffice.$navTrigger.on("click",function() {
                if ($("#main-content").hasClass("navigation-hidden")){
                    self.showVerticalMenu(true);
                }
                else {
                    self.hideVerticalMenu(true);
                }
                Woffice.searchBar.setSize();
                Woffice.sidebar.setSidebarWidth();
            });

        },

        hideVerticalMenu: function(handleCookie) {

            var $ = Woffice.$;

            // Icon class switching
            Woffice.$navTrigger.find("i").addClass("fa-bars");
            Woffice.$navTrigger.find("i").removeClass("fa-arrow-left");

            $("body, #navigation, #main-content, #main-header, #main-footer").addClass("navigation-hidden");

            if (handleCookie) {
                // Create cookies to save user choice :
                Cookies.set('Woffice_nav_position', 'navigation-hidden', { expires: 7, path: '/' });
            }

            // Rebuild the sliders
            Woffice.sliders.start();

        },

        showVerticalMenu: function(handleCookie) {

            var $ = Woffice.$;

            // Icon class switching
            Woffice.$navTrigger.find("i").removeClass("fa-bars");
            Woffice.$navTrigger.find("i").addClass("fa-arrow-left");

            $("body,#navigation, #main-content, #main-header, #main-footer").removeClass("navigation-hidden");

            if (handleCookie) {
                // ERASE COOKIES
                Cookies.remove('Woffice_nav_position', {expires: 7, path: '/'});
            }

        },

        responsiveMenu: function(onready) {

            var self = this,
                $ = Woffice.$,
                $mainMenu = $('.main-menu'),
                $notificationAlert = $('#woffice-notifications-menu');

            if( $notificationAlert.length > 0 ) {
                var $wpadminbar = $('#wpadminbar'),
                    adminbarHeight = ( $wpadminbar.length > 0 ) ? $wpadminbar.height() : 0,
                    topbarHeight = $("#navbar").height();

                $notificationAlert.css( 'top', parseInt(adminbarHeight + topbarHeight) + 'px' );
            }


            if (
                window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches
                ||
                (Woffice.navigation.isTouchDevice() && window.innerWidth < 1100)
            ) {

                if (onready)
                    self.hideVerticalMenu();
                else {
                    $("body, #navigation, #main-content, #main-header, #main-footer").addClass("navigation-hidden");
                }

                // We create a duplicate of the link
                if (!$mainMenu.hasClass("menu-loop-happened")){
                    $('.main-menu li.menu-item-has-children').each(function(){
                        var linkElement      = $(this).find("> a");
                        var submenuContainer = $(this).find("> ul.sub-menu");
                        var linkElement_href = linkElement.attr('href');

                        if (linkElement_href !== '#' && linkElement_href !== 'javascript:void(0)') {
                            if ($(this).hasClass('menu-item-has-mega-menu')) {
                                submenuContainer = $(this).find(".mega-menu .sub-menu:first-child");
                            }
                            var subElement = '<li class="menu-item mobile-submenu-link"><a href="'+linkElement_href+'" class="center"><i class="fa fa-arrow-right"></i></a></li>';

                            submenuContainer.prepend(subElement);
                        }
                    });
                }

                $mainMenu.addClass("menu-loop-happened");
                $mainMenu.addClass('is-touchable');

            } else {

                $mainMenu.removeClass("menu-loop-happened");
                $mainMenu.removeClass('is-touchable');

                $('.main-menu li.menu-item-has-children').each(function(){
                    $(this).find(".mobile-submenu-link").remove();
                });
                if (!$("#page-wrapper").hasClass("menu-is-closed")) {
                    if(!Woffice.$body.hasClass('menu-is-horizontal')) {
                        self.showVerticalMenu();
                    }
                }
            }

        },

        start: function () {

            var self = this;

            self.responsiveMenu(true);

            //Fix the menu display on load
            setTimeout(function(){
                Woffice.$navigation.removeClass("mobile-hidden");
            }, 600)

        },

        resize: function () {
            var self = this;
            var newWidth = $(window).width();

            if (newWidth !== Woffice.navigation.cachedWidth) {
                self.responsiveMenu(false);
                Woffice.navigation.cachedWidth = newWidth;
            }
        }

    },

    /**
     * Sidebar actions
     */
    sidebar: {

        start: function () {

            var self = this,
                $ = Woffice.$;

            // Setting up the layout correctly
            self.setSidebarWidth();
            self.responsiveSidebar(true);

            self.horizontalMenuAuto();

            if(Woffice.$rightSidebar.length === 0 || Woffice.$main.hasClass('sidebar-hidden'))
                Woffice.$body.addClass('sidebar-hidden');

            Woffice.$scrollHelper.hide();

            if(Woffice.$rightSidebar.length > 0){
                if(Woffice.$main.hasClass('sidebar-hidden')){
                    Woffice.$scrollHelper.fadeOut();
                }
                else{
                    if (Woffice.$main.height() < Woffice.$rightSidebar[0].scrollHeight){
                        Woffice.$scrollHelper.fadeIn('slow');
                    }
                }
            }

            // If the Cookies already exists
            if (Cookies.get('Woffice_sidebar_position') && Woffice.data.cookie_allowed.sidebar){

                Woffice.$sidebarTrigger.addClass("sidebar-hidden");

                // Main Layout changes
                $("#main-content, #main-header, body").addClass("sidebar-hidden");

                // Icon Class
                Woffice.$sidebarTrigger.find('i').addClass("fa-arrow-left");
                Woffice.$sidebarTrigger.find('i').removeClass("fa-arrow-right");

                Woffice.$scrollHelper.fadeOut();

                // Rebuild the sliders
                Woffice.sliders.start();

            }
            // For the default position
            if (!self.isOpen()){
                Woffice.$sidebarTrigger.find('i').addClass("fa-arrow-left");
                Woffice.$sidebarTrigger.find('i').removeClass("fa-arrow-right");
            }

        },

        resize: function () {

            var self = this;

            self.setSidebarWidth();
            self.setSidebarTopOffset();
            self.responsiveSidebar(false);

        },

        watch: function () {

            var self = this,
                $ = Woffice.$;

            $("#nav-sidebar-trigger").on("click",function() {

                self.sidebarToggling();

            });

        },

        /**
         * Calculates the sidebar's width
         */
        setSidebarWidth: function() {

            var $ = Woffice.$,
                $rightSidebar = $("#right-sidebar");

            if($rightSidebar.length === 0 )
                return;

            var SidebarWidth = $rightSidebar.width();

            $('#can-scroll').width(SidebarWidth);

        },

        /**
         * Makes the sidebar responsive (and the menu)
         *
         * // Todo @antonio I'm lost in those lines, any comments would be appreciated :)
         * // It was in the sidebar block but it's related to the menu right?
         *
         * @param {boolean} on_load if this is during the page loading or the resize event
         */
        responsiveSidebar: function (on_load) {

            var $ = Woffice.$,
                width = $(window).width(),
                height = $(window).height();

            var $horizontalMenuWrapper = $("#horizontal-menu-trigger-container");

            var switcher = $("#horizontal-menu-trigger").length;

            if ( window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches ) {
                // Horizontal menu responsive
                if (Woffice.$body.hasClass("menu-is-horizontal")) {

                    Woffice.$navigation.addClass("menu-responsive-horizontal");

                    if (!switcher && $('#horizontal-menu-trigger-container').length === 0) {
                        Woffice.$navigation.find("ul.main-menu").prepend('<li id="horizontal-menu-trigger-container"><a href="#" id="horizontal-menu-trigger"><i class="fa fa-bars"></i></a></li>');
                    }

                }
            }
            else {
                if ($horizontalMenuWrapper.length > 0) {
                    $horizontalMenuWrapper.remove();
                }
            }

            // Don't fire on mobile :
            if($(window).width() !== width && $(window).height() !== height || on_load) {
                if ( window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches ) {

                    // Icon class switching
                    Woffice.$sidebarTrigger.find("i").addClass("fa-arrow-left");
                    Woffice.$sidebarTrigger.find("i").removeClass("fa-arrow-right");

                    // Navigation bar Class
                    Woffice.$sidebarTrigger.addClass("sidebar-hidden");

                    // Main Layout changes
                    $("#main-content, body").addClass("sidebar-hidden");
                    Woffice.$scrollHelper.fadeOut('fast');

                    // Horizontal menu responsiveness
                    if (Woffice.$body.hasClass("menu-is-horizontal")) {

                        Woffice.$navigation.addClass("menu-responsive-horizontal");

                        if (!switcher && $('#horizontal-menu-trigger-container').length == 0) {
                            Woffice.$navigation.find("ul.main-menu").prepend('<li id="horizontal-menu-trigger-container"><a href="#" id="horizontal-menu-trigger"><i class="fa fa-bars"></i></a></li>');
                        }

                    }

                }
                else {

                    if ($horizontalMenuWrapper.length > 0) {
                        $horizontalMenuWrapper.remove();
                    }

                }
            }

        },

        /**
         * Opens or closes the sidebar and handle the cookies
         */
        sidebarToggling: function () {

            var self = this,
                $ = Woffice.$;

            // Open the sidebar
            if (!self.isOpen()) {

                self.openSidebar();

                // Erase the cookies
                Cookies.remove('Woffice_sidebar_position',{ expires: 7, path: '/' });

            }
            // Close the sidebar
            else {

                // If is a mobile device
                if (window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches) {
                    var $search_trigger = Woffice.$searchTrigger,
                        $notification_trigger = $('#nav-notification-trigger');

                    //if the search bar is opened, then close it
                    if($search_trigger.length > 0 && $('#main-search').hasClass('opened'))
                        $($search_trigger).click();

                    //if the notifications bar is opened, then close it
                    if($notification_trigger.length > 0 && $notification_trigger.hasClass('clicked'))
                        $($notification_trigger).click();
                }

                self.closeSidebar();

                // Create cookies to save user choice :
                Cookies.set('Woffice_sidebar_position', 'sidebar-hidden', { expires: 7, path: '/' });
            }

        },

        /**
         * Sets the sidebar offset and animate if required
         */
        setSidebarTopOffset : function () {

            var $ = Woffice.$;

            var $sidebar = $('#right-sidebar'),
                $navbar = $('#navbar'),
                height = 0;

            //If there is no sidebar rendered, then exit from this function
            if($sidebar.length <= 0)
                return;

            if(window.matchMedia('(max-width: 600px)').matches && $navbar.length > 0) {
                height += $navbar.height();
                if (Woffice.$body.hasClass('has-navigation-fixed'))
                    Woffice.$rightSidebar.css('height', '100%').css('height', '-=' + $navbar.height());
                else
                    Woffice.$rightSidebar.css('height', '100%');
            }

            Woffice.$rightSidebar.animate({
                top: height
            });

        },

        /**
         * Checks whether the sidebar is open or not
         *
         * @return {boolean}
         */
        isOpen : function() {

            return !(Woffice.$main.hasClass("sidebar-hidden"));

        },

        /**
         * Opens the sidebar
         */
        openSidebar: function() {

            var self = this,
                $ = Woffice.$;

            // Icon class switching
            Woffice.$sidebarTrigger.find("i").removeClass("fa-arrow-left");
            Woffice.$sidebarTrigger.find("i").addClass("fa-arrow-right");

            // Navbar Class
            Woffice.$sidebarTrigger.removeClass("sidebar-hidden");

            // Main Layout changes
            $("#main-content, #main-header, body").removeClass("sidebar-hidden");

            //Avoid overlap with header
            self.setSidebarTopOffset();

            Woffice.$scrollHelper.fadeIn('fast');

            if(
                Woffice.$body.hasClass('menu-is-vertical') &&
                !Woffice.$body.hasClass('navigation-hidden') &&
                window.matchMedia('(max-width: 450px)').matches
            ) {
                $('#nav-trigger').click();
            }

        },

        /**
         * Closes the sidebar
         */
        closeSidebar: function() {

            var $ = Woffice.$;

            // Icon class switching
            Woffice.$sidebarTrigger.find("i").addClass("fa-arrow-left");
            Woffice.$sidebarTrigger.find("i").removeClass("fa-arrow-right");

            // Navigation bar class
            Woffice.$sidebarTrigger.addClass("sidebar-hidden");

            // Main Layout changes
            $("#main-content, #main-header, body").addClass("sidebar-hidden");

            Woffice.$scrollHelper.fadeOut();

        },

        /**
         * Responsive menu class toggling
         * This is here because we need the right scope to close the sidebar automatically
         */
        horizontalMenuAuto: function () {

            var self = this,
                $ = Woffice.$;

            $('.main-menu').on("click", "#horizontal-menu-trigger", function(){

                Woffice.$navigation.toggleClass("menu-responsive-horizontal-show");

                Woffice.$body.toggleClass("navigation-hidden");

                if(
                    !Woffice.$body.hasClass('navigation-hidden') &&
                    window.matchMedia('(max-width: '+Woffice.data.menu_threshold+'px)').matches
                )
                    self.closeSidebar();

            });

        }

    },

    buddyPressNotifications: {

        /**
         * Close the notification box
         */
        close: function () {

            var $ = Woffice.$;

            $('#woffice-notifications-menu').fadeOut();
            $('#nav-notification-trigger').find('i.fa').removeClass('fa-times').addClass('fa-bell');

        },

        watch: function () {

            var $ = Woffice.$;

            $('#nav-notification-trigger').on('click', function(){

                var $icon = $(this).find('i.fa');

                // if it wasn't opened in the first place
                if($icon.hasClass('fa-bell')) {
                    $icon.removeClass('fa-bell').addClass('fa-times');
                    Woffice.buddyPressNotifications.fetch();
                } else {
                    Woffice.buddyPressNotifications.close();
                }

            });

        },

        /**
         * Mark a message as read
         * @param el
         */
        markRead: function(el) {

            var $ = Woffice.$;

            var readLink = $(el),
                component_action = readLink.data('component-action'),
                component_name = readLink.data('component-name'),
                item_id = readLink.data('item-id');

            $.ajax({
                url: Woffice.data.ajax_url.toString(),
                type: 'POST',
                data: {
                    'action': 'wofficeNoticationsMarked',
                    'component_action': component_action,
                    'component_name': component_name,
                    'item_id': item_id
                },
                success: function() {

                    readLink.parent().closest('div').remove();
                    if ($('#woffice-notifications-content').children().length === 0 ){
                        $('#nav-notification-trigger').removeClass('active');
                        Woffice.buddyPressNotifications.close();
                    }

                },
                error:function(){
                    console.error('Ajax marked failed');
                }
            });

        },

        /**
         * Fetch the notifications
         */
        fetch: function () {

            var $ = Woffice.$;

            var $trigger = $('#nav-notification-trigger'),
                $icon = $trigger.find('i.fa');

                var $wrapper = $('#woffice-notifications-menu');
                $wrapper.slideDown();

                var loader = new Woffice.loader($wrapper);

                $.ajax({
                    url: Woffice.data.ajax_url.toString(),
                    type: 'POST',
                    data: { 'action': 'wofficeNoticationsGet', 'user': Woffice.data.user_id.toString() },
                    success: function(notifications){
                        $wrapper.find('#woffice-notifications-content').empty();
                        $wrapper.find('#woffice-notifications-content').html(notifications);
                        loader.remove();
                        $('a.mark-notification-read').on('click', function(){
                            Woffice.buddyPressNotifications.markRead(this);
                        });
                    },
                    error:function(){
                        console.error('Ajax notifications failed');
                    }
                });

        }



    }

};

/**
 * Start it!
 *
 * We give it a jQuery object to play with
 */
Woffice.init(jQuery);