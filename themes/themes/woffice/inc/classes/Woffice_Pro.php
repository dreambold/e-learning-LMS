<?php
/**
 * Class Woffice_Pro
 *
 * Load everything related to the Woffice Pro service
 * Also includes filters and actions related to Unyson
 *
 * @since 2.4.5
 * @author Alkaweb
 * @deprecated 2.6.2 this service will be fully integrated in Woffice core
 */
if( ! class_exists( 'Woffice_Pro' ) ) {
    class Woffice_Pro
    {

        /**
         * An unique product key (Woffice key)
         *
         * @var string
         */
        static $product_key =  '';

        /**
         * Woffice_Pro constructor
         */
        public function __construct()
        {

            // Set the product key
            $this->set_product_key();

            add_action('admin_menu', array($this, 'alka_pro_screen_pages'));
            add_action('admin_head', array($this, 'woffice_alka_pro_screen_remove_menus'));

        }

        /**
         * Check whether the website is part of a pro plan from the database
         * We save it in an option: alka_pro_last_checked
         * If it's -1 it's not a pro.
         * Otherwise we check the date, if it was more than 2 weeks, we re-check and re-save
         *
         * @return boolean
         */
        static function is_pro() {

            $is_pro_last_checked = (int) get_option('alka_pro_last_checked', 0);

            // Timestamps
            $now = time();
            $two_weeks = 14 * 24 * 60 * 60;
            $two_weeks_ago = $now - $two_weeks;

            // If default or value more than 2 weeks ago, we check again
            if($is_pro_last_checked === 0 || $is_pro_last_checked < $two_weeks_ago )
                self::api_check_pro_account();

            // Last check is to make sure there is a pro key
            $pro_key = get_option('alka_pro_key', false);
            if($pro_key == false)
                return false;

            // It's a pro!
            return true;


        }

        /**
         * Check whether the website is part of a pro plan from the API
         *
         * @return bool
         */
        static function api_check_pro_account() {

            // We update the time it was checked
            update_option('alka_pro_last_checked', time());

            // We build the request object
            $site_url = get_site_url();
            $email = get_option('admin_email');
            $request_string = array(
                'body' => array(
                    'product_sku' => '11671924',
                    'email' => $email,
                    'site_url' => $site_url,
                    'product_key' => self::$product_key
                )
            );

            // We call the API
            $raw_response = wp_remote_post('https://hub.alka-web.com/api/pro/check', $request_string);

            // We check the response
            $response = null;
            if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) )
                $response = json_decode($raw_response['body']);
            if( empty($response) )
                return false;

            // If it's a pro
            if (isset($response->pro_key) && function_exists('woffice_decode')) {
                update_option('alka_pro_key', woffice_decode($response->pro_key));
                return true;
            }
            // If it's NOT a pro
            else {
                return false;
            }

        }

        /**
         * Set the product key
         *
         * It's either a string or false if not set yet
         */
        static function set_product_key() {

            $product_key = get_option('woffice_key');

            self::$product_key = $product_key;

        }

        /**
         * Add the page to dashboard
         */
        public function alka_pro_screen_pages()
        {
            add_dashboard_page(
                'Alka Pro',
                'Alka Pro',
                'read',
                'woffice-alkapro',
                array($this, 'woffice_alka_pro_screen_content')
            );
        }

        /**
         * Remove the menu from the submenu
         */
        public function woffice_alka_pro_screen_remove_menus()
        {
            remove_submenu_page('index.php', 'woffice-alkapro');
        }

        /**
         * The page content
         */
        public function woffice_alka_pro_screen_content() {

            // We update the pro account:
            self::api_check_pro_account();

            $current_user = wp_get_current_user();

            $params = array();
            $params['email'] = get_option('admin_email');
            $params['organization'] = get_option('blogname');
            $params['name'] = $current_user->user_firstname . ' '. $current_user->user_lastname;
            $params['url'] = site_url();
            $params['product_key'] = self::$product_key;
            $params['pk'] = get_option('alka_pro_key');
            $params['license_status'] = get_option('woffice_license');

            $base_url = "https://hub.alka-web.com/pro/";

            $final_url = add_query_arg( $params, $base_url );
            ?>

            <div class="wrap woffice-alka-pro">
                <div class="text-center">
                    <h1>Welcome to the Alka Pro embedded dashboard!</h1>
                    <p>As you can read below, this is an <b>optional</b> set of feature to improve your daily experience with Woffice with more professional tools.</p>
                </div>
                <div class="text-center">
                    <p>In order to enjoy the site reports, live chat and BuddyPress tab creator, please check the following:</p>
                    <ul>
                        <!-- License -->
                        <?php if($params['license_status'] === 'checked') { ?>
                            <li><span class="dashicons dashicons-yes"></span> <b>Site license connected to the pro account:</b> <span class="highlight">Yes, all good!</span></li>
                        <?php } else { ?>
                            <li><span class="dashicons dashicons-no"></span> <b>Site license connected to the pro account:</b> <span class="highlight">No, please set up the <a href="<?php echo admin_url('admin.php?page=fw-extensions&sub-page=extension&extension=woffice-updater'); ?>" target="_blank">Woffice Updater here</a>.</span></li>
                        <?php } ?>
                        <!-- Product Key -->
                        <?php if(!empty($params['pk'])) { ?>
                            <li><span class="dashicons dashicons-yes"></span> <b>Pro account key:</b> <span class="highlight"><?php echo $params['pk']; ?></span></li>
                        <?php } else { ?>
                            <li><span class="dashicons dashicons-no"></span> <b>Pro account key:</b> <span class="highlight">No pro key found, please sign up in the form below.</span></li>
                        <?php } ?>
                        <!-- Product Key -->
                        <?php if(!empty($params['product_key'])) { ?>
                            <li><span class="dashicons dashicons-yes"></span> <b>Unique product key:</b> <span class="highlight"><?php echo $params['product_key']; ?></span></li>
                        <?php } else { ?>
                            <li><span class="dashicons dashicons-no"></span> <b>Unique product key:</b> <span class="highlight">No product key found, please save your Theme Settings to create one.</span></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="text-center">
                    <a href="https://hub.alka-web.com/pro" target="_blank" class="button button-primary button-hero">View in a new secure tab</a>
                    <a href="https://alkaweb.atlassian.net/wiki/spaces/WOF/pages/18087937/Alka+Pro" target="_blank" class="button button-primary button-hero">Step by step tutorial</a>
                </div>
                <hr>
                <iframe src="<?php echo $final_url; ?>" frameborder="0"></iframe>
            </div>

            <?php

        }


    }
}

/**
 * Let's fire it :
 */
new Woffice_Pro();