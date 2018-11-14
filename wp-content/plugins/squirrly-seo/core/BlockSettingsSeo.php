<?php

/**
 * Account settings
 */
class SQ_Core_BlockSettingsSeo extends SQ_Classes_BlockController {
    public $post;

    function hookGetContent() {
        parent::preloadSettings();

        if (SQ_Classes_Tools::getOption('sq_api') <> '') {
            SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia('frontmenu.css');

            //Show Import Settings after Install if needed
            if (current_user_can('manage_options') && get_transient('sq_import')) {
                $platforms = SQ_Classes_ObjController::getClass('SQ_Controllers_Menu')->getImportList();
                $imports = array();
                if (!empty($platforms)) {
                    foreach ($platforms as $path => $settings) {
                        if ($path !== 'squirrly-seo') {
                            $imports[] = ucfirst(SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->getName($path));
                        }
                    }

                    if (!empty($imports)) {
                        SQ_Classes_Error::setError(sprintf(__('You can now import into Squirrly SEO all the SEO Settings from %s', _SQ_PLUGIN_NAME_), '<a href="' . admin_url('admin.php?page=sq_import#import') . '">' . join(', ', $imports) . '</a>'));
                    }
                }
            }

            //Upgrade Sitemap List
            $sitemap = SQ_Classes_Tools::getOption('sq_sitemap');
            if (!isset($sitemap['sitemap-attachment'])) $sitemap['sitemap-attachment'] = array('sitemap-attachments.xml', 0);
            SQ_Classes_Tools::saveOptions('sq_sitemap', $sitemap);

            /* Force call of error display */
            SQ_Classes_ObjController::getClass('SQ_Classes_Error')->hookNotices();
            echo '<script type="text/javascript">
                   var __snippetshort = "' . __('Too short', _SQ_PLUGIN_NAME_) . '";
                   var __snippetlong = "' . __('Too long', _SQ_PLUGIN_NAME_) . '";
                   jQuery(document).ready(function () {
                        jQuery("#sq_settings").find("select[name=sq_og_locale]").val("' . SQ_Classes_Tools::getOption('sq_og_locale') . '");
                   });
             </script>';
        }

    }

    function hookHead() {
        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        parent::hookHead();
    }

    /**
     * Called when Post action is triggered
     *
     * @return void
     */
    public function action() {
        parent::action();
        switch (SQ_Classes_Tools::getValue('action')) {
            case 'sq_settingsseo_option':
                SQ_Classes_Tools::setHeader('json');

                if (!current_user_can('manage_options')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to activate this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $option = SQ_Classes_Tools::getValue('option');
                $value = (int)SQ_Classes_Tools::getValue('value');
                SQ_Classes_Tools::saveOptions($option, $value);

                echo json_encode(array('saved' => true));
                exit();
            case 'sq_settingsseo_update':
                if (!current_user_can('manage_options')) {
                    return;
                }

                if (!SQ_Classes_Tools::getIsset('sq_use')) {
                    return;
                }
                SQ_Classes_Tools::saveOptions('sq_use', (int)SQ_Classes_Tools::getValue('sq_use'));
                SQ_Classes_Tools::saveOptions('sq_auto_title', (int)SQ_Classes_Tools::getValue('sq_auto_title'));
                SQ_Classes_Tools::saveOptions('sq_auto_description', (int)SQ_Classes_Tools::getValue('sq_auto_description'));
                SQ_Classes_Tools::saveOptions('sq_auto_keywords', (int)SQ_Classes_Tools::getValue('sq_auto_keywords'));
                SQ_Classes_Tools::saveOptions('sq_keywordtag', (int)SQ_Classes_Tools::getValue('sq_keywordtag'));
                SQ_Classes_Tools::saveOptions('sq_use_frontend', (int)SQ_Classes_Tools::getValue('sq_use_frontend'));

                if (SQ_Classes_Tools::getIsset('sq_post_types')) {
                    SQ_Classes_Tools::$options['sq_exclude_post_types'] = array();
                    foreach (SQ_Classes_Tools::getValue('sq_post_types') as $key) {
                        array_push(SQ_Classes_Tools::$options['sq_exclude_post_types'], $key);
                    }
                } else {
                    SQ_Classes_Tools::$options['sq_exclude_post_types'] = array();
                }


                SQ_Classes_Tools::saveOptions('sq_auto_canonical', (int)SQ_Classes_Tools::getValue('sq_auto_canonical'));
                SQ_Classes_Tools::saveOptions('sq_auto_noindex', (int)SQ_Classes_Tools::getValue('sq_auto_noindex'));


                SQ_Classes_Tools::saveOptions('sq_analytics_google_js', SQ_Classes_Tools::getValue('sq_analytics_google_js'));
                SQ_Classes_Tools::saveOptions('sq_auto_amp', (int)SQ_Classes_Tools::getValue('sq_auto_amp'));

                SQ_Classes_Tools::saveOptions('sq_auto_meta', (int)SQ_Classes_Tools::getValue('sq_auto_meta'));
                SQ_Classes_Tools::saveOptions('sq_auto_favicon', (int)SQ_Classes_Tools::getValue('sq_auto_favicon'));

                ///////////////////////////////////////////
                /////////////////////////////SOCIAL OPTION
                SQ_Classes_Tools::saveOptions('sq_auto_facebook', (int)SQ_Classes_Tools::getValue('sq_auto_facebook'));
                SQ_Classes_Tools::saveOptions('sq_auto_twitter', (int)SQ_Classes_Tools::getValue('sq_auto_twitter'));
                SQ_Classes_Tools::saveOptions('sq_og_locale', SQ_Classes_Tools::getValue('sq_og_locale'));

                $socials = array_merge(SQ_Classes_Tools::getOption('socials'), SQ_Classes_Tools::getValue('sq_socials', array()));

                if (isset($socials['twitter_site'])) $socials['twitter_site'] = $this->model->checkTwitterAccount($socials['twitter_site']);
                if (isset($socials['twitter_site'])) $socials['twitter'] = $this->model->checkTwitterAccountName($socials['twitter_site']);
                if (isset($socials['facebook_site'])) $socials['facebook_site'] = $this->model->checkFacebookAccount($socials['facebook_site']);
                if (isset($socials['pinterest_url'])) $socials['pinterest_url'] = $this->model->checkPinterestAccount($socials['pinterest_url']);
                if (isset($socials['instagram_url'])) $socials['instagram_url'] = $this->model->checkInstagramAccount($socials['instagram_url']);
                if (isset($socials['linkedin_url'])) $socials['linkedin_url'] = $this->model->checkLinkeinAccount($socials['linkedin_url']);
                if (isset($socials['myspace_url'])) $socials['myspace_url'] = $this->model->checkMySpaceAccount($socials['myspace_url']);
                if (isset($socials['youtube_url'])) $socials['youtube_url'] = $this->model->checkYoutubeAccount($socials['youtube_url']);

                $fb_admins = SQ_Classes_Tools::getValue('sq_fb_admins', array());
                $socials['fb_admins'] = array();
                if (!empty($fb_admins)) {
                    $fb_admins = array_unique($fb_admins);
                    foreach ($fb_admins as $index => $value) {
                        if (isset($value) && $value == '') {
                            unset($socials['fb_admins'][$index]);
                        } else {
                            $socials['fb_admins'][$index]['id'] = $this->model->checkFavebookAdminCode($value);
                        }
                    }
                }

                //get the facebook app id for sharing
                if (SQ_Classes_Tools::getIsset('sq_fbadminapp')) $socials['fbadminapp'] = SQ_Classes_Tools::getValue('sq_fbadminapp');
                if (SQ_Classes_Tools::getIsset('twitter_card_type')) $socials['twitter_card_type'] = SQ_Classes_Tools::getValue('twitter_card_type');


                SQ_Classes_Tools::saveOptions("socials", $socials);

                ///////////////////////////////////////////
                /////////////////////////////FIRST PAGE OPTIMIZATION
                if ($pageId = get_option('page_on_front')) {
                    $sq_hash = md5($pageId);
                } elseif ($post_id = get_option('page_for_posts')) {
                    $sq_hash = md5($pageId);
                } else {
                    $sq_hash = md5('wp_homepage');
                }

                if ($sq_hash <> '') {
                    $url = home_url();
                    $sq = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->getSqSeo($sq_hash);

                    $sq->doseo = 1;
                    $sq->title = urldecode(SQ_Classes_Tools::getValue('sq_fp_title', false));
                    $sq->description = urldecode(SQ_Classes_Tools::getValue('sq_fp_description', false));
                    $sq->keywords = SQ_Classes_Tools::getValue('sq_fp_keywords', false);
                    $sq->og_media = SQ_Classes_Tools::getValue('sq_fp_ogimage', false);

                    $this->model->db_insert(
                        $url,
                        $sq_hash,
                        (int)$pageId,
                        maybe_serialize($sq->toArray()),
                        gmdate('Y-m-d H:i:s')
                    );
                }

                ///////////////////////////////////////////
                /////////////////////////////SITEMAP OPTION
                SQ_Classes_Tools::saveOptions('sq_auto_sitemap', (int)SQ_Classes_Tools::getValue('sq_auto_sitemap'));
                SQ_Classes_Tools::saveOptions('sq_auto_feed', (int)SQ_Classes_Tools::getValue('sq_auto_feed'));
                SQ_Classes_Tools::saveOptions('sq_sitemap_frequency', SQ_Classes_Tools::getValue('sq_sitemap_frequency'));
                SQ_Classes_Tools::saveOptions('sq_sitemap_ping', (int)SQ_Classes_Tools::getValue('sq_sitemap_ping'));
                SQ_Classes_Tools::saveOptions('sq_sitemap_perpage', (int)SQ_Classes_Tools::getValue('sq_sitemap_perpage'));
                SQ_Classes_Tools::saveOptions('sq_sitemap_combinelangs', (int)SQ_Classes_Tools::getValue('sq_sitemap_combinelangs'));


                foreach (SQ_Classes_Tools::$options['sq_sitemap'] as $key => $value) {
                    if ($key == 'sitemap') {
                        continue;
                    }
                    SQ_Classes_Tools::$options['sq_sitemap'][$key][1] = 0;
                    if ($key == 'sitemap-product' && !$this->model->isEcommerce()) {
                        SQ_Classes_Tools::$options['sq_sitemap'][$key][1] = 2;
                    }
                }
                if (SQ_Classes_Tools::getIsset('sq_sitemap')) {
                    foreach (SQ_Classes_Tools::getValue('sq_sitemap') as $key) {
                        if (isset(SQ_Classes_Tools::$options['sq_sitemap'][$key][1])) {
                            SQ_Classes_Tools::$options['sq_sitemap'][$key][1] = 1;
                        }
                    }
                }

                foreach (SQ_Classes_Tools::$options['sq_sitemap_show'] as $key => $value) {
                    SQ_Classes_Tools::$options['sq_sitemap_show'][$key] = 0;
                }
                if (SQ_Classes_Tools::getIsset('sq_sitemap_show')) {
                    foreach (SQ_Classes_Tools::getValue('sq_sitemap_show') as $key) {
                        if (isset(SQ_Classes_Tools::$options['sq_sitemap_show'][$key])) {
                            SQ_Classes_Tools::$options['sq_sitemap_show'][$key] = 1;
                        }
                    }
                }

                ///////////////////////////////////////////
                ///////SAVE THE CODE
                $codes = array_merge(SQ_Classes_Tools::getOption('codes'), SQ_Classes_Tools::getValue('sq_codes', array()));

                if (!empty($codes)) {
                    //if (isset($codes['facebook_pixel'])) $codes['facebook_pixel'] = $codes['facebook_pixel'];
                    if (isset($codes['google_analytics'])) $codes['google_analytics'] = $this->model->checkGoogleAnalyticsCode($codes['google_analytics']);
                    if (isset($codes['pinterest_verify'])) $codes['pinterest_verify'] = $this->model->checkPinterestCode($codes['pinterest_verify']);
                    if (isset($codes['google_wt'])) $codes['google_wt'] = $this->model->checkGoogleWTCode($codes['google_wt']);
                    if (isset($codes['bing_wt'])) $codes['bing_wt'] = $this->model->checkBingWTCode($codes['bing_wt']);
                    if (isset($codes['alexa_verify'])) $codes['alexa_verify'] = $this->model->checkBingWTCode($codes['alexa_verify']);

                    SQ_Classes_Tools::saveOptions("codes", $codes);
                }


                ///////////////////////////////////////////JSONLD
                SQ_Classes_Tools::saveOptions('sq_auto_jsonld', (int)SQ_Classes_Tools::getValue('sq_auto_jsonld'));
                if (SQ_Classes_Tools::getIsset('sq_jsonld_type') && isset(SQ_Classes_Tools::$options['sq_jsonld'][SQ_Classes_Tools::getValue('sq_jsonld_type')])) {

                    foreach (SQ_Classes_Tools::$options['sq_jsonld'][SQ_Classes_Tools::getValue('sq_jsonld_type')] as $key => $value) {
                        if (isset(SQ_Classes_Tools::$options['sq_jsonld'][SQ_Classes_Tools::getValue('sq_jsonld_type')][$key])) {
                            SQ_Classes_Tools::$options['sq_jsonld'][SQ_Classes_Tools::getValue('sq_jsonld_type')][$key] = SQ_Classes_Tools::getValue('sq_jsonld_' . $key);
                        }
                    }
                    if (isset(SQ_Classes_Tools::$options['sq_jsonld'][SQ_Classes_Tools::getValue('sq_jsonld_type')]['telephone']) &&
                        SQ_Classes_Tools::$options['sq_jsonld'][SQ_Classes_Tools::getValue('sq_jsonld_type')]['telephone'] <> ''
                    ) {
                        SQ_Classes_Tools::$options['sq_jsonld'][SQ_Classes_Tools::getValue('sq_jsonld_type')]['telephone'] = '+' . str_replace('+', '', SQ_Classes_Tools::$options['sq_jsonld'][SQ_Classes_Tools::getValue('sq_jsonld_type')]['telephone']);
                    }
                }
                SQ_Classes_Tools::saveOptions('sq_jsonld_type', SQ_Classes_Tools::getValue('sq_jsonld_type'));

                ///////////////////////////////////////////
                /////////////////////////////FAVICON OPTION

                /* if there is an icon to upload */
                if (!empty($_FILES['favicon'])) {

                    $return = $this->model->addFavicon($_FILES['favicon']);
                    if ($return['favicon'] <> '') {
                        SQ_Classes_Tools::saveOptions('favicon', strtolower(basename($return['favicon'])));
                    }
                    if ($return['message'] <> '') {
                        define('SQ_MESSAGE_FAVICON', $return['message']);
                        SQ_Classes_Error::setError(SQ_MESSAGE_FAVICON . " <br /> ");
                    }
                }

                //Save the api settings for tutorial
                SQ_Classes_Action::apiSaveSettings();

                if (SQ_Classes_Tools::isAjax()) {
                    SQ_Classes_Tools::setHeader('json');
                    echo json_encode(array('saved' => true));
                    exit();
                } else {
                    //Update the rewrite rules with the new options
                    add_filter('rewrite_rules_array', array($this, 'rewrite_rules'), 999, 1);
                    //Flush the rewrite with the new favicon and sitemap
                    flush_rewrite_rules();
                    //empty the cache on settings changed
                    SQ_Classes_Tools::emptyCache();

                }
                break;
            case 'sq_setstickysla':
                SQ_Classes_Tools::saveUserMeta('sq_auto_sticky', (int)SQ_Classes_Tools::getValue('sq_auto_sticky'));

                break;
            case 'sq_checkissues':
                if (!current_user_can('manage_options')) {
                    return;
                }

                SQ_Classes_Tools::saveOptions('sq_checkedissues', 1);

                //Save the api settings for tutorial
                SQ_Classes_Action::apiSaveSettings();

                /* Load the error class */
                SQ_Classes_Tools::checkErrorSettings();

                break;
            case 'sq_fixautoseo':
                if (!current_user_can('manage_options')) {
                    return;
                }

                SQ_Classes_Tools::saveOptions('sq_use', 1);

                //Save the api settings for tutorial
                SQ_Classes_Action::apiSaveSettings();

                break;
            case 'sq_fixprivate':
                if (!current_user_can('manage_options')) {
                    return;
                }

                update_option('blog_public', 1);

                //Save the api settings for tutorial
                SQ_Classes_Action::apiSaveSettings();

                break;
            case 'sq_fixcomments':
                if (!current_user_can('manage_options')) {
                    return;
                }

                update_option('comments_notify', 1);

                //Save the api settings for tutorial
                SQ_Classes_Action::apiSaveSettings();

                break;
            case 'sq_fixpermalink':
                if (!current_user_can('manage_options')) {
                    return;
                }

                $GLOBALS['wp_rewrite'] = new WP_Rewrite();
                global $wp_rewrite;
                $permalink_structure = ((get_option('permalink_structure') <> '') ? get_option('permalink_structure') : '/') . "%postname%/";
                $wp_rewrite->set_permalink_structure($permalink_structure);
                $permalink_structure = get_option('permalink_structure');

                flush_rewrite_rules();
                break;
            case 'sq_fix_ogduplicate':
                if (!current_user_can('manage_options')) {
                    return;
                }

                SQ_Classes_Tools::saveOptions('sq_auto_facebook', 0);

                //Save the api settings for tutorial
                SQ_Classes_Action::apiSaveSettings();

                break;
            case 'sq_fix_tcduplicate':
                if (!current_user_can('manage_options')) {
                    return;
                }

                SQ_Classes_Tools::saveOptions('sq_auto_twitter', 0);

                //Save the api settings for tutorial
                SQ_Classes_Action::apiSaveSettings();

                break;
            case 'sq_fix_titleduplicate':
                if (!current_user_can('manage_options')) {
                    return;
                }

                SQ_Classes_Tools::saveOptions('sq_auto_title', 0);

                //Save the api settings for tutorial
                SQ_Classes_Action::apiSaveSettings();

                break;
            case 'sq_fix_descduplicate':
                if (!current_user_can('manage_options')) {
                    return;
                }

                SQ_Classes_Tools::saveOptions('sq_auto_description', 0);

                //Save the api settings for tutorial
                SQ_Classes_Action::apiSaveSettings();

                break;
            case 'sq_active_help' :
                if (!current_user_can('manage_options')) {
                    return;
                }
                SQ_Classes_Tools::setHeader('json');

                SQ_Classes_Tools::saveOptions('active_help', SQ_Classes_Tools::getValue('active_help'));
                exit();
            case 'sq_warnings_off':
                if (!current_user_can('manage_options')) {
                    return;
                }
                SQ_Classes_Tools::setHeader('json');
                SQ_Classes_Tools::saveOptions('ignore_warn', 1);
                echo json_encode(array('saved' => true));
                exit();
            case 'sq_google_alert_trial':
                if (!current_user_can('manage_options')) {
                    return;
                }
                SQ_Classes_Tools::setHeader('json');
                SQ_Classes_Tools::saveOptions('sq_google_alert_trial', 0);
                echo json_encode(array('saved' => true));
                exit();
            case 'sq_copyright_agreement':
                if (!current_user_can('manage_options')) {
                    return;
                }

                SQ_Classes_Tools::saveOptions('sq_copyright_agreement', 1);
                break;
            case 'sq_get_snippet':

                if (SQ_Classes_Tools::getValue('url') <> '') {
                    $url = SQ_Classes_Tools::getValue('url');
                } else {
                    $url = get_bloginfo('url');
                }
                $snippet = SQ_Classes_Tools::getSnippet($url);

                SQ_Classes_Tools::setHeader('json');
                echo json_encode($snippet);
                exit();
            case 'sq_backup':
                if (!current_user_can('manage_options')) {
                    return;
                }

                SQ_Classes_Tools::setHeader('text');
                header("Content-Disposition: attachment; filename=squirrly-settings-" . gmdate('Y-m-d') . ".txt");

                if (function_exists('base64_encode')) {
                    echo base64_encode(json_encode(SQ_Classes_Tools::$options));
                } else {
                    echo json_encode(SQ_Classes_Tools::$options);
                }
                exit();
                break;
            case 'sq_restore':
                if (!current_user_can('manage_options')) {
                    return;
                }

                if (!empty($_FILES['sq_options']) && $_FILES['sq_options']['tmp_name'] <> '') {
                    $fp = fopen($_FILES['sq_options']['tmp_name'], 'rb');
                    $options = '';
                    while (($line = fgets($fp)) !== false) {
                        $options .= $line;
                    }
                    try {
                        if (function_exists('base64_encode') && base64_decode($options) <> '') {
                            $options = @base64_decode($options);
                        }
                        $options = json_decode($options, true);
                        if (is_array($options) && isset($options['sq_api'])) {
                            if (SQ_Classes_Tools::getOption('sq_api') <> '') {
                                $options['sq_api'] = SQ_Classes_Tools::getOption('sq_api');
                            }
                            SQ_Classes_Tools::$options = $options;
                            SQ_Classes_Tools::saveOptions();

                            //Check if there is an old backup from Squirrly
                            SQ_Classes_Tools::getOptions();
                            SQ_Classes_Tools::checkUpgrade();

                            //Update the rewrite rules with the new options
                            add_filter('rewrite_rules_array', array($this, 'rewrite_rules'), 999, 1);
                            //Flush the rewrite with the new favicon and sitemap
                            flush_rewrite_rules();

                            SQ_Classes_Error::setError(__('Great! The backup is restored.', _SQ_PLUGIN_NAME_) . " <br /> ", 'success');
                        } else {
                            SQ_Classes_Error::setError(__('Error! The backup is not valid.', _SQ_PLUGIN_NAME_) . " <br /> ");
                        }
                    } catch (Exception $e) {
                        SQ_Classes_Error::setError(__('Error! The backup is not valid.', _SQ_PLUGIN_NAME_) . " <br /> ");
                    }
                } else {
                    SQ_Classes_Error::setError(__('Error! You have to enter a previous saved backup file.', _SQ_PLUGIN_NAME_) . " <br /> ");
                }
                break;
            case 'sq_backup_sql':
                if (!current_user_can('manage_options')) {
                    return;
                }

                header('Content-Type: application/octet-stream');
                header("Content-Transfer-Encoding: Binary");
                header("Content-Disposition: attachment; filename=squirrly-seo-" . gmdate('Y-m-d') . ".sql");

                if (function_exists('base64_encode')) {
                    echo base64_encode($this->model->createTableBackup());
                } else {
                    echo $this->model->createTableBackup();
                }
                exit();
                break;
            case 'sq_restore_sql':
                if (!current_user_can('manage_options')) {
                    return;
                }

                if (!empty($_FILES['sq_sql']) && $_FILES['sq_sql']['tmp_name'] <> '') {
                    $fp = fopen($_FILES['sq_sql']['tmp_name'], 'rb');
                    $sql_file = '';
                    while (($line = fgets($fp)) !== false) {
                        $sql_file .= $line;
                    }

                    if (function_exists('base64_encode')) {
                        $sql_file = @base64_decode($sql_file);
                    }

                    if ($sql_file <> '' && strpos($sql_file, 'CREATE TABLE IF NOT EXISTS') !== false) {
                        try {
                            $queries = explode(";\n", $sql_file);
                            $this->model->executeSql($queries);
                            SQ_Classes_Error::setError(__('Great! The SEO backup is restored.', _SQ_PLUGIN_NAME_) . " <br /> ", 'success');

                        } catch (Exception $e) {
                            SQ_Classes_Error::setError(__('Error! The backup is not valid.', _SQ_PLUGIN_NAME_) . " <br /> ");
                        }
                    } else {
                        SQ_Classes_Error::setError(__('Error! The backup is not valid.', _SQ_PLUGIN_NAME_) . " <br /> ");
                    }
                } else {
                    SQ_Classes_Error::setError(__('Error! You have to enter a previous saved backup file.', _SQ_PLUGIN_NAME_) . " <br /> ");
                }
                break;
            case 'sq_dataupgrade':
                //Check if there is an old backup from Squirrly
                SQ_Classes_Tools::getOptions();
                SQ_Classes_Tools::checkUpgrade();
                SQ_Classes_Error::setError(__('Great! Squirrly Data Settings is up to date now.', _SQ_PLUGIN_NAME_) . " <br /> ", 'success');

                break;
            case 'sq_resetsettings':
                if (!current_user_can('manage_options')) {
                    return;
                }

                SQ_Classes_Tools::$options = SQ_Classes_Tools::getOptions('reset');
                SQ_Classes_Tools::saveOptions();
                break;
        }
    }

    /**
     * Add the favicon in the rewrite rule
     * @param type $wp_rewrite
     */
    public function rewrite_rules($wp_rewrite) {
        $rules = array();
        if (SQ_Classes_Tools::getOption('sq_use') == 1) {

            //For Favicon
            if (SQ_Classes_Tools::getOption('sq_auto_favicon') == 1) {
                $rules['favicon\.ico$'] = 'index.php?sq_get=favicon';
                $rules['favicon\.icon$'] = 'index.php?sq_get=favicon';
                $rules['touch-icon\.png$'] = 'index.php?sq_get=touchicon';
                foreach ($this->model->appleSizes as $size) {
                    $size = (int)$size;
                    $rules['touch-icon' . $size . '\.png$'] = 'index.php?sq_get=touchicon&sq_size=' . $size;
                }
            }

            if (SQ_Classes_Tools::getOption('sq_auto_feed') == 1) {
                $rules['sqfeedcss$'] = 'index.php?sq_get=feedcss';
            }
        }
        return array_merge($rules, $wp_rewrite);
    }

}
