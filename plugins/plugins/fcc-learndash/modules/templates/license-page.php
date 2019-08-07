<?php

//Get License key
$licenseKey = trim(get_option('edd_' . $this->pluginSlug .'_license_key'));

//Get License Status
$status = get_option('edd_' . $this->pluginSlug . '_license_status');

$previousStatus = "";

if (isset($GLOBALS['wdm_server_null_response']) && $GLOBALS['wdm_server_null_response'] == true) {
    $status = 'server_did_not_respond';
    $previousStatus = get_option('edd_' . $this->pluginSlug . '_license_status');
} elseif (isset($GLOBALS['wdm_license_activation_failed']) && $GLOBALS['wdm_license_activation_failed'] == true) {
    $status = 'license_activation_failed';
} elseif (isset($_POST['edd_' . $this->pluginSlug .'_license_key']) && empty($_POST['edd_' . $this->pluginSlug .'_license_key'])) {
    $status = 'no_license_key_entered';
}


$renewLink = get_option('wdm_'.$this->pluginSlug.'_product_site');

include_once(dirname(plugin_dir_path(__FILE__)).'/includes/class-wdm-get-license-data.php');

$activeSite = \WdmWuspAddDataFCC\WdmGetLicenseData::getSiteList($this->pluginSlug);

$display="";

if (!empty($activeSite)||$activeSite!="") {
    $display = "<ul>".$activeSite."</ul>";
}

$successMessages = array(
    'valid' =>  __('Your license key is activated.', $this->pluginTextDomain),
);

$errorMessages = array(
    'server_did_not_respond'  =>  __('No response from server. Please try again later.', $this->pluginTextDomain),
    'license_activation_failed' =>  __('License Activation Failed. Please try again or contact support on support@wisdmlabs.com', $this->pluginTextDomain),
    'no_license_key_entered'    =>  __('Please enter license key.', $this->pluginTextDomain),
    'no_activations_left'   =>  ( !empty($display) ) ? sprintf(__('Your License Key is already activated at : %s Please deactivate the license from one of the above site(s) to successfully activate it on your current site.', $this->pluginTextDomain), $display) : __('No Activations Left.', $this->pluginTextDomain),
    'expired'   =>  __('Your license key has Expired. Please, Renew it.', $this->pluginTextDomain),
    'disabled'  =>  __('Your License key is disabled', $this->pluginTextDomain),
    'invalid'   =>  __('Please enter valid license key', $this->pluginTextDomain),
    'inactive'  =>  __('Please try to activate license again. If it does not activate, contact support on support@wisdmlabs.com', $this->pluginTextDomain),
    'site_inactive' =>  ( !empty($display) ) ? sprintf(__('Your License Key is already activated at : %s Please deactivate the license from one of the above site(s) to successfully activate it on your current site.', $this->pluginTextDomain), $display) : __('Site inactive (Press Activate license to activate plugin)', $this->pluginTextDomain),
    'deactivated'   =>  __('License Key is deactivated', $this->pluginTextDomain),
    'default'       =>  sprintf(__('Following Error Occurred: %s. Please contact support on support@wisdmlabs.com if you are not sure why this error is occurring', $this->pluginTextDomain), $status),
)

?>
<div class="wrap">
    <?php

    if ($status !== false) {
        if (array_key_exists($status, $successMessages)) {
            add_settings_error(
                'wdm_' . $this->pluginSlug .'_errors',
                esc_attr('settings_updated'),
                $successMessages[$status],
                'updated'
            );
        } else {
            if (array_key_exists($status, $errorMessages)) {
                add_settings_error(
                    'wdm_' . $this->pluginSlug .'_errors',
                    esc_attr('settings_updated'),
                    $errorMessages[$status],
                    'error'
                );
            } else {
                add_settings_error(
                    'wdm_' . $this->pluginSlug .'_errors',
                    esc_attr('settings_updated'),
                    $errorMessages['default'],
                    'error'
                );
            }
        }
    }

    settings_errors('wdm_' . $this->pluginSlug .'_errors');

    ?>
    <h2><?php echo sprintf(__('%s License Options', $this->pluginTextDomain), $this->pluginName); ?></h2>

    <form method="post" action="">
        <table class="form-table">
            <tbody>
                <!-- Text field to enter license key -->
                <tr valign="top">   
                    <th scope="row" valign="top">
                        <?php _e('License Key', $this->pluginTextDomain); ?>
                    </th>
                    <td>
                        <?php
                        if (($status=="valid"||$status=="expired" || $previousStatus=="valid"||$previousStatus=="expired")) {
                        ?>
                            <input id="<?php echo 'edd_' . $this->pluginSlug .'_license_key'?>" name="<?php echo 'edd_' . $this->pluginSlug .'_license_key'?>" type="text" class="regular-text" value="<?php esc_attr_e($licenseKey); ?>" readonly/>
                        <?php
                        } else {?>
                            <input id="<?php echo 'edd_' . $this->pluginSlug .'_license_key'?>" name="<?php echo 'edd_' . $this->pluginSlug .'_license_key'?>" type="text" class="regular-text" value="<?php esc_attr_e($licenseKey); ?>" />
                        <?php
                        }
                        ?>
                        <label class="description" for="<?php echo 'edd_' . $this->pluginSlug .'_license_key'?>"></label>
                    </td>
                </tr>

                <!-- Current License Status -->
                <tr>
                    <th scope="row" valign="top">
                        <?php _e('License Status', $this->pluginTextDomain); ?>
                    </th>
                    <td>
                        <?php
                        if ($status !== false) {
                            if (($status == 'valid' || $status == 'expired' || $previousStatus=="valid"||$previousStatus=="expired")) { ?>
                                <span style="color:green;"><?php _e('Active', $this->pluginTextDomain); ?></span>
                            <?php
                            } else { ?>
                                <span style="color:red;"><?php _e('Not Active', $this->pluginTextDomain) ?></span>
                            <?php
                            }
                        }

                        if ($status === false) { ?>
                            <span style="color:red;"><?php _e('Not Active', $this->pluginTextDomain) ?></span>
                        <?php
                        } ?>
                    </td>
                </tr>

                <!-- Buttons to Activate / Deactivate License -->
                <tr valign="top">   
                    <th scope="row" valign="top">
                        <?php _e('Activate License', $this->pluginTextDomain); ?>
                    </th>
                    <td>
                        <?php
                        if ($status !== false && ($status == 'valid' || $status == 'expired' || $previousStatus=="valid"||$previousStatus=="expired")) { ?>
                            <?php wp_nonce_field('edd_' . $this->pluginSlug .'_nonce', 'edd_' . $this->pluginSlug .'_nonce'); ?>
                            <input type="submit" class="button-primary" name="<?php echo 'edd_' . $this->pluginSlug .'_license_deactivate'; ?>" value="<?php _e('Deactivate License', $this->pluginTextDomain); ?>"/>
                            <?php
                            if ($status == 'expired') { ?>
                                <input type="button" class="button-primary" name="<?php echo 'edd_' . $this->pluginSlug .'_license_renew'; ?>" value="<?php _e('Renew License', $this->pluginTextDomain); ?>" onclick="window.open('<?php echo $renewLink; ?>')"/>
                            <?php
                            }
                        } else {
                            wp_nonce_field('edd_' . $this->pluginSlug .'_nonce', 'edd_' . $this->pluginSlug .'_nonce'); ?>
                            <input type="submit" class="button-primary" name="<?php echo 'edd_' . $this->pluginSlug .'_license_activate'; ?>" value="<?php _e('Activate License', $this->pluginTextDomain); ?>"/>
                        <?php
                        } ?>
                    </td>
                </tr>

            </tbody>
        </table>
    </form>
</div>
