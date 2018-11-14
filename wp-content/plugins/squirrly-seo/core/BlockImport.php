<?php

/**
 * Set the patterns
 */
class SQ_Core_BlockImport extends SQ_Classes_BlockController {

    function hookGetContent() {
        if(SQ_Classes_Tools::getOption('sq_api') <> '') {
            //Remove the notification is Inport Settings are shown
            delete_transient('sq_import');

            parent::preloadSettings();
            SQ_Classes_ObjController::getClass('SQ_Classes_Error')->hookNotices();
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
            case 'sq_importsettings':
                if (!current_user_can('manage_options')) {
                    return;
                }

                $platform = SQ_Classes_Tools::getValue('sq_import_platform', '');
                if ($platform <> '') {
                    if (SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->importDBSettings($platform)) {
                        SQ_Classes_Error::setMessage(__('All the Plugin settings were imported successfuly!', _SQ_PLUGIN_NAME_));
                    }else{
                        SQ_Classes_Error::setMessage(__('No settings found for this plugin/theme.', _SQ_PLUGIN_NAME_));

                    }
                }
                break;

            case 'sq_importseo':
                if (!current_user_can('manage_options')) {
                    return;
                }
                $platform = SQ_Classes_Tools::getValue('sq_import_platform', '');
                if ($platform <> '') {
                    $seo = SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->importDBSeo($platform);
                    if (!empty($seo)) {
                        foreach ($seo as $sq_hash => $metas) {
                            SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->db_insert(
                                (isset($metas['url']) ? $metas['url'] : ''),
                                $sq_hash,
                                (isset($metas['post_id']) && is_numeric($metas['post_id']) ? (int)$metas['post_id'] : 0),
                                maybe_serialize($metas),
                                gmdate('Y-m-d H:i:s'));
                        }
                        SQ_Classes_Error::setMessage(sprintf(__('%s SEO records were imported successfuly! You can now deactivate the %s plugin', _SQ_PLUGIN_NAME_), count($seo), SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->getName($platform)));
                    } else {
                        SQ_Classes_Error::setMessage(sprintf(__('There are no SEO records with this plugin. You can now deactivate the %s plugin', _SQ_PLUGIN_NAME_), SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->getName($platform)));

                    }
                }
                break;
        }
    }


}
