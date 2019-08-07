<?php

namespace WdmWuspAddDataFCC;

if (! class_exists('WdmAddLicenseData')) {

    class WdmAddLicenseData
    {

        /**
         * @var string Short Name for plugin.
         */
        private $pluginShortName = '';

        /**
         * @var string Slug to be used in url and functions name
         */
        private $pluginSlug = '';

        /**
         * @var string stores the current plugin version
         */
        private $pluginVersion = '';

        /**
         * @var string Handles the plugin name
         */
        private $pluginName = '';

        /**
         * @var string  Stores the URL of store. Retrieves updates from
         *              this store
         */
        private $storeUrl = '';

        /**
         * @var string  Name of the Author
         */
        private $authorName = '';

        private $pluginTextDomain='';

        public function __construct($plugin_data)
        {
            $this->authorName       = $plugin_data[ 'authorName' ];
            $this->pluginName       = $plugin_data[ 'pluginName' ];
            $this->pluginShortName = $plugin_data[ 'pluginShortName' ];
            $this->pluginSlug       = $plugin_data[ 'pluginSlug' ];
            $this->pluginVersion    = $plugin_data[ 'pluginVersion' ];
            $this->storeUrl         = $plugin_data[ 'storeUrl' ];
            $this->pluginTextDomain = $plugin_data[ 'pluginTextDomain' ];

            add_action('init', array($this, 'addData'));
            add_action('admin_menu', array( $this, 'licenseMenu' ));
        }

        public function licenseMenu()
        {
            add_plugins_page(
                sprintf(__('%s License', $this->pluginTextDomain), $this->pluginShortName),
                sprintf(__('%s License', $this->pluginTextDomain), $this->pluginShortName),
                apply_filters($this->pluginSlug . '_license_page_capability', 'manage_options'),
                $this->pluginSlug . '-license',
                array($this, 'licensePage')
            );
        }

        public function licensePage()
        {
            include_once trailingslashit(dirname(dirname(__FILE__))) . 'templates/license-page.php';
        }

        /**
         * Updates license status in the database and returns status value.
         *
         * @param object $licenseData License data returned from server
         * @param  string $pluginSlug  Slug of the plugin. Format of the key in options table is 'edd_<$pluginSlug>_license_status'
         *
         * @return string              Returns status of the license
         */
        public static function updateStatus($licenseData, $pluginSlug)
        {
            $status = '';
            if (isset($licenseData->success)) {
                // Check if request was successful
                if ($licenseData->success === false) {
                    if (! isset($licenseData->error) || empty($licenseData->error)) {
                        $licenseData->error = 'invalid';
                    }
                }
                // Is there any licensing related error?
                $status = self::checkLicensingError($licenseData);

                if (!empty($status)) {
                    update_option('edd_'.$pluginSlug.'_license_status', $status);

                    return $status;
                }
                $status = 'invalid';
                //Check license status retrieved from EDD
                $status = self::checkLicenseStatus($licenseData, $pluginSlug);
            }

            $status = (empty($status)) ? 'invalid' : $status;
                    update_option('edd_' . $pluginSlug . '_license_status', $status);

            return $status;
        }

        /**
         * Checks if there is any error in response.
         *
         * @param object $licenseData License Data obtained from server
         *
         * @return string empty if no error or else error
         */
        public static function checkLicensingError($licenseData)
        {
            $status = '';
            if (isset($licenseData->error) && !empty($licenseData->error)) {
                switch ($licenseData->error) {
                    case 'revoked':
                        $status = 'disabled';
                        break;

                    case 'expired':
                        $status = 'expired';
                        break;
                }
            }

                    return $status;
        }

        public static function checkLicenseStatus($licenseData, $pluginSlug)
        {
                $status = 'invalid';
            if (isset($licenseData->license) && !empty($licenseData->license)) {
                switch ($licenseData->license) {
                    case 'invalid':
                        $status = 'invalid';
                        if (isset($licenseData->activations_left) && $licenseData->activations_left == '0') {
                            include_once plugin_dir_path(__FILE__).'class-wdm-get-license-data.php';
                            $activeSite = WdmGetLicenseData::getSiteList($pluginSlug);

                            if (!empty($activeSite) || $activeSite != '') {
                                $status = 'no_activations_left';
                            } else {
                                $status = 'valid';
                            }
                        }

                        break;
                        
                    case 'failed':
                        $status = 'failed';
                        $GLOBALS[ 'wdm_license_activation_failed' ] = true;
                        break;

                    default:
                        $status = $licenseData->license;
                }
            }

               return $status;
        }

        /**
         * Checks if any response received from server or not after making an API call. If no response obtained, then sets next api request after 24 hours.
         *
         * @param object $licenseData         License Data obtained from server
         * @param  string   $currentResponseCode    Response code of the API request
         * @param  array    $validResponseCode      Array of acceptable response codes
         *
         * @return bool returns false if no data obtained. Else returns true.
         */
        public function checkIfNoData($licenseData, $currentResponseCode, $validResponseCode)
        {
            if ($licenseData == null || ! in_array($currentResponseCode, $validResponseCode)) {
                $GLOBALS[ 'wdm_server_null_response' ] = true;
                set_transient('wdm_' . $this->pluginSlug . '_license_trans', 'server_did_not_respond', 60 * 60 * 24);

                return false;
            }

            return true;
        }

        /**
         * Activates License.
         */
        public function activateLicense()
        {

            $licenseKey = trim($_POST[ 'edd_' . $this->pluginSlug . '_license_key' ]);

            if ($licenseKey) {
                update_option('edd_' . $this->pluginSlug . '_license_key', $licenseKey);
                $apiParams = array(
                    'edd_action'         => 'activate_license',
                    'license'            => $licenseKey,
                    'item_name'          => urlencode($this->pluginName),
                    'current_version' => $this->pluginVersion,
                );

                $response = wp_remote_get(add_query_arg($apiParams, $this->storeUrl), array(
                    'timeout' => 15, 'sslverify' => false, 'blocking' => true, ));

                if (is_wp_error($response)) {
                    return false;
                }

                $licenseData = json_decode(wp_remote_retrieve_body($response));

                $validResponseCode = array( '200', '301' );

                $currentResponseCode = wp_remote_retrieve_response_code($response);

                $isDataAvailable = $this->checkIfNoData($licenseData, $currentResponseCode, $validResponseCode);
                //cspPrintDebug($licenseData); exit;
                if ($isDataAvailable == false) {
                    return;
                }

                $expirationTime = $this->getExpirationTime($licenseData);
                $currentTime = time();

                if (isset($licenseData->expires) && ($licenseData->expires !== false) && ($licenseData->expires != 'lifetime') && $expirationTime <= $currentTime && $expirationTime != 0 && !isset($licenseData->error)) {
                    $licenseData->error = 'expired';
                }

                if (isset($licenseData->renew_link) && (!empty($licenseData->renew_link) || $licenseData->renew_link != '')) {
                    update_option('wdm_' . $this->pluginSlug . '_product_site', $licenseData->renew_link);
                }
                
                $this->updateNumberOfSitesUsingLicense($licenseData);

                $licenseStatus = self::updateStatus($licenseData, $this->pluginSlug);
                
                $this->setTransientOnActivation($licenseStatus);
            }
        }

        public function getExpirationTime($licenseData)
        {
            $expirationTime = 0;
            if (isset($licenseData->expires)) {
                $expirationTime = strtotime($licenseData->expires);
            }

            return $expirationTime;
        }

        public function updateNumberOfSitesUsingLicense($licenseData)
        {
            if (isset($licenseData->sites) && (!empty($licenseData->sites) || $licenseData->sites != '')) {
                update_option('wdm_' . $this->pluginSlug . '_license_key_sites', $licenseData->sites);
                update_option('wdm_' . $this->pluginSlug . '_license_max_site', $licenseData->license_limit);
            } else {
                update_option('wdm_' . $this->pluginSlug . '_license_key_sites', '');
                update_option('wdm_' . $this->pluginSlug . '_license_max_site', '');
            }
        }

        public function setTransientOnActivation($licenseStatus)
        {
            $transVar = get_transient('wdm_' . $this->pluginSlug . '_license_trans');
            if (isset($transVar)) {
                delete_transient('wdm_' . $this->pluginSlug . '_license_trans');
                if (! empty($licenseStatus)) {
                    if ($licenseStatus == 'valid') {
                        $time = 60 * 60 * 24 * 7;
                    } else {
                        $time = 60 * 60 * 24;
                    }
                    set_transient('wdm_' . $this->pluginSlug . '_license_trans', $licenseStatus, $time);
                }
            }
        }

        /**
         * Deactivates License.
         */
        public function deactivateLicense()
        {
            $licenseKey = trim(get_option('edd_' . $this->pluginSlug . '_license_key'));

            if ($licenseKey) {
                $apiParams = array(
                    'edd_action'         => 'deactivate_license',
                    'license'            => $licenseKey,
                    'item_name'          => urlencode($this->pluginName),
                    'current_version' => $this->pluginVersion,
                );

                $response = wp_remote_get(add_query_arg($apiParams, $this->storeUrl), array(
                    'timeout' => 15, 'sslverify' => false, 'blocking' => true, ));

                if (is_wp_error($response)) {
                    return false;
                }

                $licenseData = json_decode(wp_remote_retrieve_body($response));

                $validResponseCode = array( '200', '301' );

                $currentResponseCode = wp_remote_retrieve_response_code($response);

                $isDataAvailable = $this->checkIfNoData($licenseData, $currentResponseCode, $validResponseCode);

                if ($isDataAvailable == false) {
                    return;
                }

                if ($licenseData->license == 'deactivated' || $licenseData->license == 'failed') {
                    update_option('edd_' . $this->pluginSlug . '_license_status', 'deactivated');
                }
                //delete_transient( 'wdm_' . $this->pluginSlug . '_license_trans' );
                delete_transient('wdm_' . $this->pluginSlug . '_license_trans');

                set_transient('wdm_' . $this->pluginSlug . '_license_trans', $licenseData->license, 0);
            }
        }

        public function addData()
        {
            if (isset($_POST[ 'edd_' . $this->pluginSlug . '_license_activate' ])) {
                if (! check_admin_referer('edd_' . $this->pluginSlug . '_nonce', 'edd_' . $this->pluginSlug . '_nonce')) {
                    return;
                }
                $this->activateLicense();
            } elseif (isset($_POST[ 'edd_' . $this->pluginSlug . '_license_deactivate' ])) {
                if (! check_admin_referer('edd_' . $this->pluginSlug . '_nonce', 'edd_' . $this->pluginSlug . '_nonce')) {
                    return;
                }
                $this->deactivateLicense();
            }
        }
    }
}
