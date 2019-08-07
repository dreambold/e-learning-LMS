<?php
//get site url
$str = get_home_url();
$site_url = preg_replace('#^https?://#', '', $str);
return [
    /**
     * Plugins short name appears on the License Menu Page
     */
    'pluginShortName' => __('FrontEnd Course Creation', 'fcc'),

    /**
     * this slug is used to store the data in db. License is checked using two options viz edd_<slug>_license_key and edd_<slug>_license_status
     */
    'pluginSlug' => 'fcc',

    /**
     * Download Id on EDD Server
     */
    'itemId'  => 33523,

    /**
     * Current Version of the plugin. This should be similar to Version tag mentioned in Plugin headers
     */
    'pluginVersion' => FCC_PLUGIN_VERSION,

    /**
     * Under this Name product should be created on WisdmLabs Site
     */
    'pluginName' => __('FrontEnd Course Creation', 'fcc'),

    /**
     * Url where program pings to check if update is available and license validity
     * plugins using storeUrl "https://wisdmlabs.com/check-update" or anything similar should change that to "https://wisdmlabs.com" to avoid future issues.
     */
    'storeUrl' => 'https://wisdmlabs.com/license-check/',

    /**
    * Site url which will pass in API request.
    */
    'siteUrl' => $site_url,

    /**
     * Author Name
     */
    'authorName' => 'WisdmLabs',

    /**
     * Text Domain used for translation
     */
    'pluginTextDomain' => 'fcc',

    /**
     * Base Url for accessing Files
     * Change if not accessing this file from main file
     */
    'baseFolderUrl' => plugins_url('/', __FILE__),

    /**
     * Base Directory path for accessing Files
     * Change if not accessing this file from main file
     */
    'baseFolderDir' => untrailingslashit(plugin_dir_path(__FILE__)),

    /**
     * Plugin Main file name
     * example : product-enquiry-pro.php
     */
    'mainFileName' => 'fcc.php',

    /**
    * Set true if theme
    */
   'isTheme' => false,

   /**
    *  Changelog page link for theme
    *  should be false for plugin
    *  eg : https://wisdmlabs.com/elumine/documentation/
    */
    'themeChangelogUrl' =>  false,

   /**
    * Dependent plugins for plugin
    */
   'dependencies' => array(
    'learndash' => defined('LEARNDASH_VERSION') ? LEARNDASH_VERSION : '',
    'buddypress' => defined('BP_VERSION') ? BP_VERSION : '',
   ),
];
