<?php

class Wdm_ld_buddypress
{
    public function __construct()
    {
        add_filter('login_redirect', array($this, 'wdm_bpdev_redirect_to_profile'), 10, 3);
        add_action('bp_setup_nav', array($this, 'wdm_lnlb_profile_new_nav_item'), 10);
        add_action('bp_setup_nav', array($this, 'wdm_lnlb_profile_new_commission'), 15);
        add_action('bp_setup_nav', array($this, 'wdm_lnlb_profile_new_commission_export'), 15);
        add_action('bp_setup_nav', array($this, 'wdm_lnlb_profile_new_nav_course'), 15);
        add_action('bp_setup_nav', array($this, 'wdm_lnlb_profile_new_nav_lesson'), 15);
        add_action('bp_setup_nav', array($this, 'wdm_lnlb_profile_new_nav_topic'), 15);
        add_action('bp_setup_nav', array($this, 'wdm_lnlb_profile_new_nav_quiz'), 15);
        add_shortcode('wdm_commission_report', array($this, 'wdm_commission_report_frontend'), 10);
        add_shortcode('wdm_commission_report_export', array($this, 'wdm_commission_report_export_frontend'), 10);
    }

    
    public function wdm_commission_report_export_frontend()
    {
        if (class_exists('WDM_FCC_COMM')) {
            $WDM_FCC_COMM = new WDM_FCC_COMM();
            $WDM_FCC_COMM->wdm_course_author_third_tab();
        }
    }

    public function wdm_commission_report_frontend()
    {
        if (class_exists('WDM_FCC_COMM')) {
            $WDM_FCC_COMM = new WDM_FCC_COMM();
            $WDM_FCC_COMM->wdm_fcc_commission_report(get_current_user_id());
        }
    }

    public function wdm_bpdev_redirect_to_profile($redirect_to_calculated, $redirect_url_specified, $user)
   {
       global $wdm_plugin_data;
       if (!isset($user->ID)) {
           return $redirect_to_calculated;
       }

       if ($redirect_to_calculated != '') {
           if (strpos($redirect_to_calculated, '?redirect_to=') !== false) {
               //echo "in";exit;
               $url = explode('redirect_to=', $redirect_to_calculated);
               $url = $url[ count($url) - 1 ];
               return $url;
           }
       }
       //echo $redirect_to_calculated;

       if (empty($redirect_to_calculated)) {
           $redirect_to_calculated = admin_url();
       }
       //echo bp_core_get_user_domain($user->ID );exit;
       /* if the user is not site admin,redirect to his/her profile */
       if (!is_super_admin($user->user_login)) {
           $core_url = bp_core_get_user_domain($user->ID);
           return !empty($core_url)?$core_url:$redirect_to_calculated;
       } elseif (is_super_admin($user->user_login)) {
           return $redirect_to_calculated;
       } /* if site admin or not logged in,do not do anything much */
       else {
           return $redirect_to_calculated;
       }

       return $redirect_to_calculated;
   }

    public static function wdmGetPrivateAttribute($obj, $prop)
    {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);

        return $property->getValue($obj);
    }

    public function wdm_lnlb_profile_new_nav_item()
    {
        global $bp;
        global $current_user;
        $displayed_user = '';
        $logged_in_user = '';
        $yes_or_no = false;
        if (isset($bp->loggedin_user->id)) {
            wp_enqueue_script('wdm-scroll-js', plugins_url('js/wdm_scroll.js', dirname(dirname(__FILE__))), array('jquery'));
            $logged_in_user = $bp->loggedin_user->id;
            foreach ($current_user->roles as $k => $v) {
                if ($v == 'wdm_course_author' || $v == 'administrator') {
                    $yes_or_no = true;
                    break;
                }
            }

            if (is_super_admin(get_current_user_id())) {
                $yes_or_no = true;
            }
        }
        $default_selected_tab = '';
        if ($yes_or_no) {
            $default_selected_tab = 'view_my_listing';

            bp_core_new_nav_item(
                array(
                'name' => __('Meine Trainings', 'fcc'),
                'slug' => 'listing',
                'default_subnav_slug' => 'course_listing', // We add this submenu item below
                'screen_function' => array($this, 'wdm_lnlb_view_manage_listing_main'),
                )
            );
            if (wdm_is_course_author($logged_in_user)) {
                bp_core_new_nav_item(
                    array(
                    'name' => __('Commission', 'fcc'),
                    'slug' => 'commission',
                    'default_subnav_slug' => 'commission_report', // We add this submenu item below
                    'screen_function' => array($this, 'wdm_lnlb_view_commission_report_main'),
                    )
                );
            }
        }
    }

    public static function wdmGetPrivateArrayObject($obj, $prop)
    {
        $reflection = new \ReflectionObject($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);

        return $property->getValue($obj);
    }

    public function wdm_lnlb_view_manage_listing_main()
    {
        //         add_action( 'bp_template_content', 'bp_template_content_main_function'  );
        bp_core_load_template('wdm_listing_content');
    }

    public function wdm_lnlb_view_commission_report_main()
    {
        //         add_action( 'bp_template_content', 'bp_template_content_main_function'  );
        bp_core_load_template('wdm_listing_content');
    }

    public function bp_template_content_main_function()
    {
        echo do_shortcode('[wdm_group_users]');
    }

    public function wdm_lnlb_profile_new_commission()
    {
        global $current_user;

        if (!in_array('wdm_course_author', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
            return;
        }
        global $bp;

        $bpDataObj = $this->wdmGetPrivateArrayObject($bp, 'data');
        $currentBpVersion = $bpDataObj['version'];

        global $current_user;
        $displayed_user = '';
        $logged_in_user = '';
        $yes_or_no = false;

        if (version_compare($currentBpVersion, '2.5.3') <=0) {
            if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && wdm_is_course_author($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                bp_core_new_subnav_item(array(
                    'name' => __('Report', 'fcc'),
                    'slug' => 'commission_report',
                    'parent_url' => $bp->loggedin_user->domain.$bp->bp_nav[ 'commission' ][ 'slug' ].'/',
                    'parent_slug' => $bp->bp_nav[ 'commission' ][ 'slug' ],
                    'position' => 10,
                    'screen_function' => array($this, 'wdm_lnlb_bp_commission_report'),
                ));
            }
        } else {
            $navObj = $bpDataObj['members']->nav;
            $navArray = $this->wdmGetPrivateAttribute($navObj, 'nav');
            // $commissionObj = $navArray[get_current_user_id()]['commission'];
            // echo "<pre>";
            // print_r($navArray);
            // echo "</pre>";
            // die();
            if (isset($navArray[get_current_user_id()]) && !empty($navArray[get_current_user_id()]) && isset($navArray[get_current_user_id()]['commission'])) {
                $commissionObj = $navArray[get_current_user_id()]['commission'];
            }
            
            if (!empty($commissionObj)) {
                $commissionArray = $commissionObj->getArrayCopy();

                if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && wdm_is_course_author($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                    bp_core_new_subnav_item(array(
                        'name' => __('Report', 'fcc'),
                        'slug' => 'commission_report',
                        'parent_url' => $bp->loggedin_user->domain.$commissionArray['slug'].'/',
                        'parent_slug' => $commissionArray['slug'],
                        'position' => 10,
                        'screen_function' => array($this, 'wdm_lnlb_bp_commission_report'),
                    ));
                }
            }
        }
    }

    public function wdm_lnlb_profile_new_commission_export()
    {
        global $current_user;

        if (!in_array('wdm_course_author', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
            return;
        }
        global $bp;

        $bpDataObj = $this->wdmGetPrivateArrayObject($bp, 'data');
        $currentBpVersion = $bpDataObj['version'];

        global $current_user;
        $displayed_user = '';
        $logged_in_user = '';
        $yes_or_no = false;

        if (version_compare($currentBpVersion, '2.5.3') <=0) {
            if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && wdm_is_course_author($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                bp_core_new_subnav_item(array(
                    'name' => __('Export', 'fcc'),
                    'slug' => 'commission_report_export',
                    'parent_url' => $bp->loggedin_user->domain.$bp->bp_nav[ 'commission' ][ 'slug' ].'/',
                    'parent_slug' => $bp->bp_nav[ 'commission' ][ 'slug' ],
                    'position' => 10,
                    'screen_function' => array($this, 'wdm_lnlb_bp_commission_report_export'),
                ));
            }
        } else {
            $navObj = $bpDataObj['members']->nav;
            $navArray = $this->wdmGetPrivateAttribute($navObj, 'nav');
            

            if (isset($navArray[get_current_user_id()]) && !empty($navArray[get_current_user_id()]) && isset($navArray[get_current_user_id()]['commission'])) {
                $commissionObj = $navArray[get_current_user_id()]['commission'];
            }

            if (!empty($commissionObj)) {
                $commissionArray = $commissionObj->getArrayCopy();

                if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && wdm_is_course_author($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                    bp_core_new_subnav_item(array(
                        'name' => __('Export', 'fcc'),
                        'slug' => 'commission_report_export',
                        'parent_url' => $bp->loggedin_user->domain.$commissionArray['slug'].'/',
                        'parent_slug' => $commissionArray['slug'],
                        'position' => 10,
                        'screen_function' => array($this, 'wdm_lnlb_bp_commission_report_export'),
                    ));
                }
            }
        }
    }

    public function wdm_lnlb_bp_commission_report_export()
    {
        add_action('bp_template_content', array($this, 'bp_template_wdm_lnlb_bp_commission_report_export'));
        bp_core_load_template('wdm_listing_content');
    }

    public function bp_template_wdm_lnlb_bp_commission_report_export()
    {
        echo do_shortcode('[wdm_commission_report_export]');
    }

    public function wdm_lnlb_bp_commission_report()
    {
        add_action('bp_template_content', array($this, 'bp_template_wdm_lnlb_bp_commission_report'));
        bp_core_load_template('wdm_listing_content');
    }

    public function bp_template_wdm_lnlb_bp_commission_report()
    {
        echo do_shortcode('[wdm_commission_report]');
    }

//Main listing tab starts here
    public function wdm_lnlb_profile_new_nav_course()
    {

        if (is_user_logged_in()) {
            global $current_user;

            if (!in_array('wdm_course_author', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
                if (!is_super_admin(get_current_user_id())) {
                    return;
                }
            }
            global $bp;

            $bpDataObj = $this->wdmGetPrivateArrayObject($bp, 'data');
            $currentBpVersion = $bpDataObj['version'];

            global $current_user;
            $displayed_user = '';
            $logged_in_user = '';
            $yes_or_no = false;

            if (version_compare($currentBpVersion, '2.5.3') <=0) {
                if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                    bp_core_new_subnav_item(array(
                        'name' => LearnDash_Custom_Label::get_label('course'),
                        'slug' => 'course_listing',
                        'parent_url' => $bp->loggedin_user->domain.$bp->bp_nav[ 'listing' ][ 'slug' ].'/',
                        'parent_slug' => $bp->bp_nav[ 'listing' ][ 'slug' ],
                        'position' => 10,
                        'screen_function' => array($this, 'wdm_lnlb_bp_course_listing'),
                    ));
                }
            } else {
                $navObj = $bpDataObj['members']->nav;
                $navArray = $this->wdmGetPrivateAttribute($navObj, 'nav');
                // $listingObj = $navArray[get_current_user_id()]['listing'];

                if (isset($navArray[get_current_user_id()]) && !empty($navArray[get_current_user_id()])) {
                    $listingObj = $navArray[get_current_user_id()]['listing'];
                }

                if (!empty($listingObj)) {
                    $listingArray = $listingObj->getArrayCopy();

                    if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                        bp_core_new_subnav_item(array(
                            'name' => LearnDash_Custom_Label::get_label('course'),
                            'slug' => 'course_listing',
                            'parent_url' => $bp->loggedin_user->domain.$listingArray['slug'].'/',
                            'parent_slug' => $listingArray['slug'],
                            'position' => 10,
                            'screen_function' => array($this, 'wdm_lnlb_bp_course_listing'),
                        ));
                    }
                }
            }
        }
    }

    public function wdm_lnlb_bp_course_listing()
    {
        add_action('bp_template_content', array($this, 'bp_template_wdm_lnlb_bp_course_listing'));
        bp_core_load_template('wdm_listing_content');
    }

    public function bp_template_wdm_lnlb_bp_course_listing()
    {
        echo do_shortcode('[wdm_course_list]');
    }

    public function wdm_lnlb_profile_new_nav_lesson()
    {
        if (is_user_logged_in()) {
            global $current_user;
            if (!in_array('wdm_course_author', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
                if (!is_super_admin(get_current_user_id())) {
                    return;
                }
                // return;
            }
            global $bp;

            $bpDataObj = $this->wdmGetPrivateArrayObject($bp, 'data');
            $currentBpVersion = $bpDataObj['version'];

            global $current_user;
            $displayed_user = '';
            $logged_in_user = '';
            $yes_or_no = false;

            if (version_compare($currentBpVersion, '2.5.3') <=0) {
                if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                    bp_core_new_subnav_item(array(
                        'name' => LearnDash_Custom_Label::get_label('lesson'),
                        'slug' => 'lesson_listing',
                        'parent_url' => $bp->loggedin_user->domain.$bp->bp_nav[ 'listing' ][ 'slug' ].'/',
                        'parent_slug' => $bp->bp_nav[ 'listing' ][ 'slug' ],
                        'position' => 10,
                        'screen_function' => array($this, 'wdm_lnlb_bp_unit_listing'),
                    ));
                }
            } else {
                $navObj = $bpDataObj['members']->nav;
                $navArray = $this->wdmGetPrivateAttribute($navObj, 'nav');
                // $listingObj = $navArray[get_current_user_id()]['listing'];

                if (isset($navArray[get_current_user_id()]) && !empty($navArray[get_current_user_id()])) {
                    $listingObj = $navArray[get_current_user_id()]['listing'];
                }

                if (!empty($listingObj)) {
                    $listingArray = $listingObj->getArrayCopy();

                    if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                        bp_core_new_subnav_item(array(
                            'name' => LearnDash_Custom_Label::get_label('lesson'),
                            'slug' => 'lesson_listing',
                            'parent_url' => $bp->loggedin_user->domain.$listingArray['slug'].'/',
                            'parent_slug' => $listingArray['slug'],
                            'position' => 10,
                            'screen_function' => array($this, 'wdm_lnlb_bp_unit_listing'),
                        ));
                    }
                }
            }
        }
    }

    public function wdm_lnlb_bp_unit_listing()
    {
        add_action('bp_template_content', array($this, 'bp_template_wdm_lnlb_bp_unit_listing'));
        bp_core_load_template('wdm_listing_content');
    }

    public function bp_template_wdm_lnlb_bp_unit_listing()
    {
        echo do_shortcode('[wdm_lesson_list]');
    }

//Main listing tab starts here
    public function wdm_lnlb_profile_new_nav_topic()
    {
        if (is_user_logged_in()) {
            global $current_user;
            if (!in_array('wdm_course_author', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
                if (!is_super_admin(get_current_user_id())) {
                    return;
                }
                // return;
            }
            global $bp;

            $bpDataObj = $this->wdmGetPrivateArrayObject($bp, 'data');
            $currentBpVersion = $bpDataObj['version'];

            global $current_user;
            $displayed_user = '';
            $logged_in_user = '';
            $yes_or_no = false;

            if (version_compare($currentBpVersion, '2.5.3') <=0) {
                if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                    bp_core_new_subnav_item(array(
                        'name' => LearnDash_Custom_Label::get_label('topic'),
                        'slug' => 'topic_listing',
                        'parent_url' => $bp->loggedin_user->domain.$bp->bp_nav[ 'listing' ][ 'slug' ].'/',
                        'parent_slug' => $bp->bp_nav[ 'listing' ][ 'slug' ],
                        'position' => 10,
                        'screen_function' => array($this, 'wdm_lnlb_bp_topic_listing'),
                    ));
                }
            } else {
                $navObj = $bpDataObj['members']->nav;
                $navArray = $this->wdmGetPrivateAttribute($navObj, 'nav');
                // $listingObj = $navArray[get_current_user_id()]['listing'];
               
                if (isset($navArray[get_current_user_id()]) && !empty($navArray[get_current_user_id()])) {
                    $listingObj = $navArray[get_current_user_id()]['listing'];
                }

                if (!empty($listingObj)) {
                    $listingArray = $listingObj->getArrayCopy();

                    if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                        bp_core_new_subnav_item(array(
                            'name' => LearnDash_Custom_Label::get_label('topic'),
                            'slug' => 'topic_listing',
                            'parent_url' => $bp->loggedin_user->domain.$listingArray['slug'].'/',
                            'parent_slug' => $listingArray['slug'],
                            'position' => 10,
                            'screen_function' => array($this, 'wdm_lnlb_bp_topic_listing'),
                        ));
                    }
                }
            }
        }
    }

    public function wdm_lnlb_bp_topic_listing()
    {
        add_action('bp_template_content', array($this, 'bp_template_wdm_lnlb_bp_topic_listing'));
        bp_core_load_template('wdm_listing_content');
    }

    public function bp_template_wdm_lnlb_bp_topic_listing()
    {
        echo do_shortcode('[wdm_topic_list]');
    }

    public function wdm_lnlb_profile_new_nav_quiz()
    {
        if (is_user_logged_in()) {
            global $current_user;
            if (!in_array('wdm_course_author', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
                if (!is_super_admin(get_current_user_id())) {
                    return;
                }
                // return;
            }
            global $bp;

            $bpDataObj = $this->wdmGetPrivateArrayObject($bp, 'data');
            $currentBpVersion = $bpDataObj['version'];

            global $current_user;
            $displayed_user = '';
            $logged_in_user = '';
            $yes_or_no = false;

            if (version_compare($currentBpVersion, '2.5.3') <=0) {
                if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                    bp_core_new_subnav_item(array(
                        'name' => LearnDash_Custom_Label::get_label('quiz'),
                        'slug' => 'quiz_listing',
                        'parent_url' => $bp->loggedin_user->domain.$bp->bp_nav[ 'listing' ][ 'slug' ].'/',
                        'parent_slug' => $bp->bp_nav[ 'listing' ][ 'slug' ],
                        'position' => 10,
                        'screen_function' => array($this, 'wdm_lnlb_bp_quiz_listing'),
                    ));
                }
            } else {
                $navObj = $bpDataObj['members']->nav;
                $navArray = $this->wdmGetPrivateAttribute($navObj, 'nav');
                

                if (isset($navArray[get_current_user_id()]) && !empty($navArray[get_current_user_id()])) {
                    $listingObj = $navArray[get_current_user_id()]['listing'];
                }

                if (!empty($listingObj)) {
                    $listingArray = $listingObj->getArrayCopy();

                    if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                        bp_core_new_subnav_item(array(
                            'name' => LearnDash_Custom_Label::get_label('quiz'),
                            'slug' => 'quiz_listing',
                            'parent_url' => $bp->loggedin_user->domain.$listingArray['slug'].'/',
                            'parent_slug' => $listingArray['slug'],
                            'position' => 10,
                            'screen_function' => array($this, 'wdm_lnlb_bp_quiz_listing'),
                        ));
                    }
                }
            }
        }
    }

    public function wdm_lnlb_bp_quiz_listing()
    {
        add_action('bp_template_content', array($this, 'bp_template_wdm_lnlb_bp_quiz_listing'));
        bp_core_load_template('wdm_listing_content');
    }

    public function bp_template_wdm_lnlb_bp_quiz_listing()
    {
        echo do_shortcode('[wdm_quiz_list]');
    }

    public function wdm_lnlb_profile_new_nav_question()
    {

        global $wdm_plugin_data;

        if (is_user_logged_in()) {
            global $current_user;
            if (!in_array('wdm_course_author', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
                if (!is_super_admin(get_current_user_id())) {
                    return;
                }
                // return;
            }
            global $bp;

            $bpDataObj = $this->wdmGetPrivateArrayObject($bp, 'data');
            $currentBpVersion = $bpDataObj['version'];

            global $current_user;
            $displayed_user = '';
            $logged_in_user = '';
            $yes_or_no = false;

            if (version_compare($currentBpVersion, '2.5.3') <=0) {
                if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                    bp_core_new_subnav_item(array(
                        'name' => __('Question', 'fcc'),
                        'slug' => 'question_listing',
                        'parent_url' => $bp->loggedin_user->domain.$bp->bp_nav[ 'listing' ][ 'slug' ].'/',
                        'parent_slug' => $bp->bp_nav[ 'listing' ][ 'slug' ],
                        'position' => 10,
                        'screen_function' => array($this, 'wdm_lnlb_bp_question_listing'),
                    ));
                }
            } else {
                $navObj = $bpDataObj['members']->nav;
                $navArray = $this->wdmGetPrivateAttribute($navObj, 'nav');
            
                if (isset($navArray[get_current_user_id()]) && !empty($navArray[get_current_user_id()])) {
                    $listingObj = $navArray[get_current_user_id()]['listing'];
                }

                if (!empty($listingObj)) {
                    $listingArray = $listingObj->getArrayCopy();

                    if (isset($bp->displayed_user->id) && isset($bp->loggedin_user->id) && $bp->displayed_user->id == $bp->loggedin_user->id) {
                        bp_core_new_subnav_item(array(
                            'name' => __('Question', 'fcc'),
                            'slug' => 'question_listing',
                            'parent_url' => $bp->loggedin_user->domain.$listingArray['slug'].'/',
                            'parent_slug' => $listingArray['slug'],
                            'position' => 10,
                            'screen_function' => array($this, 'wdm_lnlb_bp_question_listing'),
                        ));
                    }
                }
            }
        }
    }

    public function wdm_lnlb_bp_question_listing()
    {
        add_action('bp_template_content', array($this, 'bp_template_wdm_lnlb_bp_question_listing'));
        bp_core_load_template('wdm_listing_content');
    }

    public function bp_template_wdm_lnlb_bp_question_listing()
    {
        echo do_shortcode('[wdm_question_list]');
    }
}

new Wdm_ld_buddypress();
