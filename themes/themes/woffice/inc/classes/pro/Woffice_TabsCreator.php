<?php
/**
 * Class Woffice_TabsCreator
 *
 * Manage everything related to the AlkaChat
 *
 * @since 2.5.1
 * @author Alkaweb
 */

if( ! class_exists( 'Woffice_TabsCreator' ) ) {
    class Woffice_TabsCreator
    {

        /**
         * Woffice_TabsCreator constructor
         */
        public function __construct()
        {

            // Only if BP is enabled as well as PHP > 5.3
            if(!function_exists('bp_core_new_nav_item') || strnatcmp(phpversion(),'5.3.0') <= 0)
                return;

            add_action( 'bp_setup_nav', array($this, 'setup_nav'));

        }

        /**
         * Setup the tabs in the nav with d
         */
        public function setup_nav() {

            $tabs = woffice_get_settings_option('buddypress-tabs');

            if(!is_array($tabs) || empty($tabs)) return;

            foreach ($tabs as $index => $tab) {

                if(empty($tab['name']) || empty($tab['content']))
                    continue;

                $this->createProfileTab($tab, $index);

            }

        }

        /**
         * Creates a profile tab
         *
         * @param array     $tab
         * @param integer   $index - the position
         */
        private function createProfileTab($tab, $index) {

            $slug = sanitize_title_with_dashes($tab['name']);

            $callback = function () use ($tab) {

                add_action('bp_template_content', function () use ($tab) {
                    echo $tab['content'];
                    do_action($tab['action']);
                });

                bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));

            };

            // Create the nav icon
            bp_core_new_nav_item(array(
                'name' => $tab['name'],
                'slug' => $slug,
                'default_subnav_slug' => $slug,
                'position' => (90 + $index),
                'show_for_displayed_user' => true,
                'screen_function' => $callback,
            ));

            // Apply the icon
            add_filter('bp_get_displayed_user_nav_'.$slug, function ($nav_item) use ($tab) {
                if(empty($tab['icon']))
                    return $nav_item;
                $parsed = explode("href",$nav_item);
                return $parsed[0] .' class="has-icon '.str_replace('fa ', '',$tab['icon']).'" href'. $parsed[1];

            });

        }

    }
}

/**
 * Let's fire it :
 */
new Woffice_TabsCreator();

