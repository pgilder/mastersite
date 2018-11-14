<?php

/**
 * Account settings
 */
class SQ_Core_BlockSettings extends SQ_Classes_BlockController {

    function hookGetContent() {
        parent::preloadSettings();

        if(SQ_Classes_Tools::getOption('sq_api') <> '') {
            SQ_Classes_ObjController::getClass('SQ_Classes_Error')->hookNotices();
            echo '<script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery("#sq_settings").find("select[name=sq_google_country]").val("' . SQ_Classes_Tools::getOption('sq_google_country') . '");
                    });
             </script>';
        }
    }

    /**
     * Called when Post action is triggered
     *
     * @return void
     */
    public function action() {
        parent::action();


        switch (SQ_Classes_Tools::getValue('action')) {

            case 'sq_settings_update':
                if (!current_user_can('manage_options')) {
                    return;
                }

                if (SQ_Classes_Tools::getIsset('sq_post_types')) {
                    SQ_Classes_Tools::$options['sq_post_types'] = array();
                    foreach (SQ_Classes_Tools::getValue('sq_post_types') as $key) {
                        array_push(SQ_Classes_Tools::$options['sq_post_types'], $key);
                    }
                    if (!in_array('product', get_post_types())) {
                        array_push(SQ_Classes_Tools::$options['sq_post_types'], 'product');
                    }
                }else{
                    SQ_Classes_Tools::$options['sq_post_types'] = array();
                }

                SQ_Classes_Tools::saveOptions('sq_google_country', SQ_Classes_Tools::getValue('sq_google_country'));
                SQ_Classes_Tools::saveOptions('sq_google_country_strict', SQ_Classes_Tools::getValue('sq_google_country_strict'));

                if(SQ_Classes_Tools::getIsset('sq_google_ranksperhour')) {
                    SQ_Classes_Tools::saveOptions('sq_google_ranksperhour', SQ_Classes_Tools::getValue('sq_google_ranksperhour'));
                }
                if(SQ_Classes_Tools::getIsset('sq_google_serpsperhour')) {
                    SQ_Classes_Tools::saveOptions('sq_google_serpsperhour', SQ_Classes_Tools::getValue('sq_google_serpsperhour'));
                }

                SQ_Classes_Tools::saveOptions('sq_keyword_help', (int)SQ_Classes_Tools::getValue('sq_keyword_help'));
                SQ_Classes_Tools::saveOptions('sq_keyword_information', (int)SQ_Classes_Tools::getValue('sq_keyword_information'));
                SQ_Classes_Tools::saveOptions('sq_force_savepost', (int)SQ_Classes_Tools::getValue('sq_force_savepost'));


                SQ_Classes_Tools::saveOptions('sq_sla', (int)SQ_Classes_Tools::getValue('sq_sla'));
                SQ_Classes_Tools::saveOptions('sq_local_images', (int)SQ_Classes_Tools::getValue('sq_local_images'));
                SQ_Classes_Tools::saveOptions('sq_url_fix', (int)SQ_Classes_Tools::getValue('sq_url_fix'));
                SQ_Classes_Tools::saveOptions('sq_auto_robots', (int)SQ_Classes_Tools::getValue('sq_auto_robots'));

                //Save custom robots
                $robots = SQ_Classes_Tools::getValue('sq_robots_permission', '', true);
                $robots = explode(PHP_EOL, $robots);
                $robots = str_replace("\r", "", $robots);

                if (!empty($robots)) {
                    $robots = array_unique($robots);
                    SQ_Classes_Tools::saveOptions('sq_robots_permission', $robots);
                }

                SQ_Classes_Action::apiSaveSettings();

                if (SQ_Classes_Tools::isAjax()) {
                    SQ_Classes_Tools::setHeader('json');
                    echo json_encode(array('saved' => true));
                    exit();
                } else {
                    SQ_Classes_Tools::emptyCache();
                }
                break;
        }
    }

}
