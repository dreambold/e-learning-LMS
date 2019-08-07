<?php

namespace WdmWuspAddDataFCC;

class WdmGetLicenseData
{

    public static $responseData;

    /**
     * Retrieves licensing information from database. If valid information is not found, sends request to server to get info
     * @param  array  $pluginData Plugin data
     * @param  boolean $cache      When cache is true, it returns the value stored in static variable $responseData. When set to false, it forcefully retrieves value from database. Example: If you want to show plugin's settings page after activating license, then pass false, so that it will forcefully get the data from database
     * @return string              returns 'available' if license is valid or expired else returns 'unavailable'
     */
    public static function getDataFromDb($pluginData, $cache = true)
    {

        if (null !== self::$responseData && $cache === true) {
            return self::$responseData;
        }
   
        $pluginName = $pluginData[ 'pluginName' ];
        $pluginSlug = $pluginData[ 'pluginSlug' ];
        $storeUrl   = $pluginData[ 'storeUrl' ];

        $licenseTransient = get_transient('wdm_' . $pluginSlug . '_license_trans');
        
        if (! $licenseTransient) {
            $licenseKey = trim(get_option('edd_' . $pluginSlug . '_license_key'));

            if ($licenseKey) {
                $apiParams = array(
                    'edd_action'         => 'check_license',
                    'license'            => $licenseKey,
                    'item_name'          => urlencode($pluginName),
                    'current_version'    => $pluginData[ 'pluginVersion' ]
                );

                $response = wp_remote_get(add_query_arg($apiParams, $storeUrl), array(
                    'timeout'    => 15, 'sslverify'  => false, 'blocking'    => true ));

                if (is_wp_error($response)) {
                    return false;
                }


                $licenseData = json_decode(wp_remote_retrieve_body($response));

                $validResponseCode = array( '200', '301' );

                $currentResponseCode = wp_remote_retrieve_response_code($response);

                if ($licenseData == null || ! in_array($currentResponseCode, $validResponseCode)) {
                    //if server does not respond, read current license information
                    $licenseStatus = get_option('edd_' . $pluginSlug . '_license_status', '');
                    if (empty($licenseData)) {
                        set_transient('wdm_' . $pluginSlug . '_license_trans', 'server_did_not_respond', 60 * 60 * 24);
                    }
                } else {
                    include_once(plugin_dir_path(__FILE__) . 'class-wdm-add-license-data.php');
                    $licenseStatus = WdmAddLicenseData::updateStatus($licenseData, $pluginSlug);
                }

                $activeSite = WdmGetLicenseData::getSiteList($pluginSlug);

                self::setResponseData($licenseStatus, $activeSite, $pluginSlug, true);

                return self::$responseData;
            }
        } else {
            $licenseStatus  = get_option('edd_' . $pluginSlug . '_license_status');
            $activeSite     = WdmGetLicenseData::getSiteList($pluginSlug);

            self::setResponseData($licenseStatus, $activeSite, $pluginSlug);
            return self::$responseData;
        }
    }

    public static function setResponseData($licenseStatus, $activeSite, $pluginSlug, $setTransient = false)
    {

        if ($licenseStatus == 'valid') {
            self::$responseData = 'available';
        } elseif ($licenseStatus == 'expired' && ( ! empty($activeSite) || $activeSite != "")) {
            self::$responseData = 'unavailable';
        } elseif ($licenseStatus == 'expired') {
            self::$responseData = 'available';
        } else {
            self::$responseData  = 'unavailable';
        }

        if ($setTransient) {
            if ($licenseStatus == 'valid') {
                $time = 60 * 60 * 24 * 7;
            } else {
                $time = 60 * 60 * 24;
            }
            set_transient('wdm_' . $pluginSlug . '_license_trans', $licenseStatus, $time);
        }
    }

    /**
     * This function is used to get list of sites where license key is already acvtivated.
     *
     * @param type $pluginSlug current plugin's slug
     * @return string  list of site
     *
     * @author Foram Rambhiya
     *
     */
    public static function getSiteList($pluginSlug)
    {
        
        $sites       = get_option('wdm_' . $pluginSlug . '_license_key_sites');
        $max         = get_option('wdm_' . $pluginSlug . '_license_max_site');
        $currentSite    = get_site_url();
        //EDD treats site with www as a different site. Solving this issue.
        $currentSite    = str_ireplace('www.', '', $currentSite);
        $currentSite    = preg_replace('#^https?://#', '', $currentSite);

        $siteCount  = 0;
        $activeSite = "";

        if (! empty($sites) || $sites != "") {
            foreach ($sites as $key) {
                foreach ($key as $value) {
                    $value = rtrim($value, "/");

                    if (strcasecmp($value, $currentSite) != 0) {
                        $activeSite.= "<li>" . $value . "</li>";
                        $siteCount ++;
                    }
                }
            }
        }

        //echo $activeSite; exit;
        if ($siteCount >= $max) {
            return $activeSite;
        } else {
            return "";
        }
    }
}
