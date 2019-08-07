<?php
/**
 * Class Woffice_Welcome
 *
 * Used to create a Welcome page when Woffice is installed and activated.
 *
 * @since 2.1.3
 * @author Alkalab
 */

if( ! class_exists( 'Woffice_Welcome' ) ) {
    class Woffice_Welcome
    {

        /**
         * Woffice_Welcome constructor
         */
        public function __construct()
        {
            add_action('admin_init', array($this, 'woffice_theme_activation_redirect'));
            add_action('admin_menu', array($this, 'welcome_screen_pages'));
            add_action('admin_head', array($this, 'woffice_welcome_screen_remove_menus'));
            add_action( 'admin_notices', array($this, 'woffice_newsletter_subscription'), 20);
	        add_action( 'admin_head', array( $this, 'dismiss_alert' ) );
        }


        /**
         * Redirect after Woffice has been activated
         */
        public function woffice_theme_activation_redirect()
        {
            if (isset($_GET['activated'])) {
                wp_redirect(admin_url('index.php?page=woffice-welcome'));
            }
        }

        /**
         * Add the page to dashboard
         */
        public function welcome_screen_pages()
        {
            add_dashboard_page(
                'Welcome to Woffice !',
                'Welcome to Woffice !',
                'read',
                'woffice-welcome',
                array($this, 'woffice_welcome_screen_content')
            );
        }

        /**
         * Remove the menu from the submenu
         */
        public function woffice_welcome_screen_remove_menus()
        {
            remove_submenu_page('index.php', 'woffice-welcome');
        }

        /**
         * Content of the dashboard page
         * Called from : welcome_screen_pages()
         *
         * @return void
         */
        public function woffice_welcome_screen_content()
        {

            ?>
            <div class="wrap woffice-welcome">
                <div class="woffice-welcome-pre-header">
                    <h1>Welcome on Woffice, Congratulations !</h1>
                </div>
                <div class="woffice-welcome-box woffice-welcome-showcase">
                    <div class="woffice-welcome-message">
                        <p><strong>One more thing and you're ready to go !</strong> Woffice comes with several plugins
                            that you need to install in order to make it work correctly.
                            Therefore, you can reach <a href="themes.php?page=tgmpa-install-plugins">this page</a> to
                            install them all in one single click.
                            Thank you so much for your purchase, we really hope you'll enjoy the time we've spent on
                            Woffice's development and feel the passion and love we share with this product.
                            If you have any feedback, idea or suggestion for us, don't hesitate a minute to get in touch
                            with us</p>
                    </div>
                    <div class="woffice-welcome-version">
                        <h1>W</h1>
                        <?php if (function_exists('fw')) : ?>
                            <h4>Version</h4>
                            <h3><?php echo fw()->theme->manifest->get('version'); ?></h3>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="woffice-welcome-grid">
                    <div class="woffice-welcome-row">
                        <h2>Getting starting with Woffice</h2>
                        <div class="woffice-welcome-col-4">
                            <h3>One Click Setup</h3>
                            <p>
                                Using the backup / demo install extension, you can import any demo with only one single
                                click.
                                Choosing from Business, Community or School demo. Make sure you're on a new Wordpress
                                setup,
                                because your content will be erased. Then you'll be able to change directly the content
                                and adapt the dsign to your own brand.
                            </p>
                            <div class="text-right">
                                <a href="https://alkaweb.atlassian.net/wiki/spaces/WOF/pages/33160/Theme+Installation" class="woffice-welcome-btn"
                                   target="_blank">Read Tutorial</a>
                            </div>
                        </div>
                        <div class="woffice-welcome-col-4">
                            <h3>Setup the Auto Update</h3>
                            <p>
                                By registering your copy, you can enable the Woffice auto-update feature. Your Woffice
                                copy will be updated
                                automatically whenever we release an update. As we're working on lot on this project, we
                                release an update
                                every 1-2 weeks, adding new features and patches. Always with one goal in mind, making
                                Woffice better.
                            </p>
                            <div class="text-right">
                                <a href="https://alkaweb.atlassian.net/wiki/spaces/WOF/pages/229722/Updates+Licenses" class="woffice-welcome-btn"
                                   target="_blank">Read Tutorial</a>
                            </div>
                        </div>
                        <div class="woffice-welcome-col-4">
                            <h3>Customize Woffice</h3>
                            <p>
                                You can do basic customizations (CSS, JS) through the Theme Settings tabs. But if you
                                want to edit Woffice's files
                                or make more changes, we highly recommend you to use the Woffice Child Theme included
                                within the main package (from Themeforest).
                                Once enabled, you'll be able to update Woffice without loosing any of your previous
                                changes.
                            </p>
                            <div class="text-right">
                                <a href="https://codex.wordpress.org/Child_Themes" class="woffice-welcome-btn"
                                   target="_blank">Read Tutorial</a>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="woffice-welcome-row">
                        <h2>Use Woffice's environment</h2>
                        <div class="woffice-welcome-col-4">
                            <h3>Online Changelog</h3>
                            <p>
                                As we're making a lot of improvements as well as adding your great ideas, you might need
                                to see on what we've been working on in order
                                to improve your experience. The Online Changelog is here to sum up all our changes since
                                day 0.
                            </p>
                            <div class="text-right">
                                <a href="https://woffice.io/changelog" class="woffice-welcome-btn" target="_blank">Online
                                    Changelog</a>
                            </div>
                        </div>
                        <div class="woffice-welcome-col-4">
                            <h3>Online Updater</h3>
                            <p>
                                With your purchase code / username, you can access all the released versions of Woffice
                                from everywhere around the world.
                                It can also be used as a backup safety whenever you have an issue with the theme. Keep
                                in mind that you always come back.
                            </p>
                            <div class="text-right">
                                <a href="https://woffice.io/updater/" class="woffice-welcome-btn" target="_blank">See
                                    the Updater</a>
                            </div>
                        </div>
                        <div class="woffice-welcome-col-4">
                            <h3>Online Documentation</h3>
                            <p>
                                The Woffice documentation is open from everywhere and every device. We're improving it
                                on every update in order to make
                                Woffice's setup easier and easier throughout its development. The documentation is based
                                on 80% of captioned images and useful links.
                            </p>
                            <div class="text-right">
                                <a href="https://woffice.io/documentation/" class="woffice-welcome-btn" target="_blank">Go
                                    to the Documentation</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="woffice-welcome-box">
                    <h2>Need Help ? We're here !</h2>
                    <p>If you need any help with Woffice, or if you find any bug. Feel free to contact us through a new
                        ticket on our support helpdesk.
                        We're here 7/7 and always happy to help.</p>
                    <div class="text-right">
                        <a href="https://alkaweb.ticksy.com/" class="woffice-welcome-btn" target="_blank">Open a new
                            ticket</a>
                    </div>
                </div>
            </div>

            <?php
        }

        /**
         * Content of the dashboard page
         * Called from : welcome_screen_pages()
         *
         * @return void
         */
        public function woffice_newsletter_subscription()
        {

            // Check if the alert can be displayed on the current page, for the current user
            if ( woffice_is_core_update_page()
                 || get_user_meta( get_current_user_id(), 'woffice_newsletter_dismissed', true )
                 || ! current_user_can( 'manage_options' )
            ) {
              return;
            }

            $current_user = wp_get_current_user();
            $email = $current_user->user_email;

            /*
             * We call our API
             */
            $raw_response = wp_remote_get('https://hub.alka-web.com/api/newsletter/check/'.$email);

            /*
             * We check the response
             */
            $response = null;
            if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
                $response = json_decode($raw_response['body'], true);
            }

            // if the user is not yet registered to the newsletter
            if ($response['resultMailchimp'] !== 'subscribed') {

            ?>

                <div class="is-dismissible notice info woffice-newsletter">
                    <div>
                        <h2>Alkalab is growing, we now have a brand new newsletter!</h2>
                        <p>
                            Don't want to miss our next Woffice updates, polls? But also new products, support updates and some interesting blog articles from Alkalab's blog.
                            We hate spammers, we won't spam you and you can unsubscribe at anytime!<br>
                            <i>- Your friends at Alkalab</i>
                            Alkalab

                        <div id="mc_embed_signup" class="text-right">
                            <form action="//alka-web.us16.list-manage.com/subscribe/post?u=cd5291c429df8270607277d16&amp;id=2311005f93" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                                <div id="mc_embed_signup_scroll">
                                    <input type="email" value="<?php echo $current_user->user_email ?>" name="EMAIL" class="hidden" id="mce-EMAIL">
                                    <input type="text" value="<?php echo $current_user->user_firstname ?>" name="FNAME" class="hidden" id="mce-FNAME">
                                    <input type="text" value="<?php echo $current_user->user_lastname ?>" name="LNAME" class="hidden" id="mce-LNAME">
                                    <div id="mce-responses" class="hidden">
                                        <div class="response" id="mce-error-response" style="display:none"></div>
                                        <div class="response" id="mce-success-response" style="display:none"></div>
                                    </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_cd5291c429df8270607277d16_2311005f93" tabindex="-1" value=""></div>
                                    <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="woffice-welcome-btn input-newsletter">
                                    <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'woffice-newsletter-dismiss', '1' ), 'woffice-dismiss-' . get_current_user_id() ) ) ?>" class="dismiss-notice" target="_parent"><?php esc_html_e( 'Dismiss this notice', 'woffice' ) ?></a>
                                </div>
                            </form>
                        </div>

                        <!--End mc_embed_signup-->
                    </div>
                </div>

            <?php

            } // end if

        } //end function

        /**
         * Register dismissal of admin notices.
         *
         * Acts on the dismiss link in the admin nag messages.
         * If clicked, the admin notice disappears and will no longer be visible to this user.
         */
        public function dismiss_alert() {
            if ( isset( $_GET['woffice-newsletter-dismiss'] ) && check_admin_referer( 'woffice-dismiss-' . get_current_user_id() ) ) {
                update_user_meta( get_current_user_id(), 'woffice_newsletter_dismissed', 1 );
            }
        }

    }
}

/**
 * Let's fire it :
 */
 new Woffice_Welcome();



