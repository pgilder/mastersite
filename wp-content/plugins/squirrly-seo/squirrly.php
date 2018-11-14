<?php

/*
  Copyright (c) 2012-2018, SEO Squirrly.
  The copyrights to the software code in this file are licensed under the (revised) BSD open source license.

  Plugin Name: Squirrly SEO 2018 (Briefcase)
  Plugin URI: https://www.squirrly.co
  Description: SEO By Squirrly is for the NON-SEO experts. Get Excellent Seo with Better Content, Ranking and Analytics. For Both Humans and Search Bots.<BR> <a href="http://my.squirrly.co/user" target="_blank"><strong>Check your profile</strong></a>
  Author: Squirrly SEO
  Version: 8.4.00
  Author URI: https://www.squirrly.co
 */

/* SET THE CURRENT VERSION ABOVE AND BELOW */
define('SQ_VERSION', '8.4.00');

/* Call config files */
if (file_exists(dirname(__FILE__) . '/config/config.php')) {
    require_once(dirname(__FILE__) . '/config/config.php');

    /* important to check the PHP version */
    if (PHP_VERSION_ID >= 5100) {
        /* inport main classes */
        require_once(_SQ_CLASSES_DIR_ . 'ObjController.php');
        require_once(_SQ_CLASSES_DIR_ . 'BlockController.php');

        SQ_Classes_ObjController::getClass('SQ_Classes_FrontController');
        SQ_Classes_ObjController::getClass('SQ_Classes_Tools');

        if (is_admin() || is_network_admin()) {

            SQ_Classes_ObjController::getClass('SQ_Classes_FrontController')->runAdmin();

            /**
             *  Upgrade Squirrly call.
             */
            register_activation_hook(__FILE__, array(SQ_Classes_ObjController::getClass('SQ_Classes_Tools'), 'sq_activate'));
            register_deactivation_hook(__FILE__, array(SQ_Classes_ObjController::getClass('SQ_Classes_Tools'), 'sq_deactivate'));

            if (!SQ_Classes_Tools::getOption('sq_force_savepost')) {
                //Jost for logged users. Send the posts to API
                add_action('sq_processApi', array(SQ_Classes_ObjController::getClass('SQ_Controllers_Cron'), 'processSEOPostCron'));
                SQ_Classes_ObjController::getClass('SQ_Controllers_Cron')->processSEOPostCron();
            }

        } elseif (SQ_Classes_Tools::getOption('sq_use') == 1) {
            SQ_Classes_ObjController::getClass('SQ_Classes_FrontController')->runFrontend();
        }

        //Only if ranking option is activated
        if (SQ_Classes_Tools::getOption('sq_google_ranksperhour') > 0 ||
            SQ_Classes_Tools::getOption('sq_google_serp_active')) {
            add_action('sq_processCron', array(SQ_Classes_ObjController::getClass('SQ_Controllers_Cron'), 'processRankingCron'));
        }
    } else {
        /* Main class call */
        add_action('admin_init', 'sq_phpError');
    }


}

/**
 * Show PHP Error message if PHP is lower the required
 */
function sq_phpError() {
    add_action('admin_notices', 'sq_showError');
}

/**
 * Called in Notice Hook
 */
function sq_showError() {
    echo '<div class="update-nag"><span style="color:red; font-weight:bold;">' . __('For Squirrly to work, the PHP version has to be equal or greater then 5.1', _SQ_PLUGIN_NAME_) . '</span></div>';
}
