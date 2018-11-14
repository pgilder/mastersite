<?php

class SQ_Controllers_Post extends SQ_Classes_FrontController {

    public $saved;

    public function init() {
        parent::init();
        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia('post');
    }

    /**
     * Hook the post save
     */
    public function hookPost() {
        if (SQ_Classes_Tools::getOption('sq_api') == '')
            return;

        //Hook and save the Snippet and Keywords
        add_action('wp_insert_attachment_data', array($this, 'checkSeo'), 11, 2);
        add_filter('wp_insert_post_data', array($this, 'checkSeo'), 11, 2);
        add_filter('wp_insert_post_data', array($this, 'removeHighlight'), 12, 2);
        add_filter('wp_insert_post_data', array($this, 'checkImage'), 13, 2);

        if (SQ_Classes_Tools::getOption('sq_use') && SQ_Classes_Tools::getOption('sq_auto_sitemap')) {
            add_action('transition_post_status', array(SQ_Classes_ObjController::getClass('SQ_Controllers_Sitemaps'), 'refreshSitemap'), 9999, 3);
        }
    }

    /**
     * Initialize the TinyMCE editor for the current use
     *
     * @return void
     */
    public function hookEditor() {
        $this->saved = array();

        //Add the H2 icon on visual editor
        add_filter('mce_external_plugins', array($this->model, 'addHeadingButton'));
        add_filter('mce_buttons', array($this->model, 'registerButton'));
    }

    /**
     * hook the Head
     *
     * @global integer $post_ID
     */
    public function hookHead() {
        global $post_ID;
        parent::hookHead();

        /**
         * Add the post ID in variable
         * If there is a custom plugin post or Shopp product
         *
         * Set the global variable $sq_postID for cookie and keyword record
         */
        if ((int)$post_ID == 0) {
            if (SQ_Classes_Tools::getIsset('id'))
                $GLOBALS['sq_postID'] = (int)SQ_Classes_Tools::getValue('id');
        } else {
            $GLOBALS['sq_postID'] = $post_ID;
        }
        /*         * ****************************** */

        echo '<script type="text/javascript">(function($) {$.sq_tinymce = { callback: function () {}, setup: function(ed){} } })(jQuery);</script>';
    }


    /**
     * Remove the Squirrly Highlights in case there are some left
     * @param array $post_data
     * @param array $postarr
     * @return array
     */
    public function removeHighlight($post_data, $postarr) {
        if (!isset($post_data['post_content']) || !isset($postarr['ID'])) {
            return $post_data;
        }

        if (strpos($post_data['post_content'], '<mark') !== false) {
            $post_data['post_content'] = preg_replace('/<mark[^>]*(data-markjs|mark_counter)[^>]*>([^<]*)<\/mark>/i', '$2', $post_data['post_content']);
        }
        return $post_data;
    }

    /**
     * Check if the image is a remote image and save it locally
     *
     * @param array $post_data
     * @param array $postarr
     * @return array
     */
    public function checkImage($post_data, $postarr) {
        if (!isset($post_data['post_content']) || !isset($postarr['ID'])) {
            return $post_data;
        }

        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        //if the option to save the images locally is set on
        if (SQ_Classes_Tools::getOption('sq_local_images')) {
            @set_time_limit(90);

            $urls = array();
            if (function_exists('preg_match_all')) {
                @preg_match_all('/<img[^>]*src=[\'"]([^\'"]+)[\'"][^>]*>/i', stripslashes($post_data['post_content']), $out);

                if (!empty($out)) {
                    if (!is_array($out[1]) || count($out[1]) == 0)
                        return $post_data;

                    if (get_bloginfo('wpurl') <> '') {
                        $domain = parse_url(get_bloginfo('wpurl'));

                        foreach ($out[1] as $row) {
                            if (strpos($row, '//') !== false &&
                                strpos($row, $domain['host']) === false
                            ) {
                                if (!in_array($row, $urls)) {
                                    $urls[] = $row;
                                }
                            }
                        }
                    }
                }
            }

            if (!is_array($urls) || (is_array($urls) && count($urls) == 0)) {
                return $post_data;
            }

            $urls = @array_unique($urls);

            $time = microtime(true);
            foreach ($urls as $url) {
                if ($file = $this->model->upload_image($url)) {
                    if (!file_is_valid_image($file['file']))
                        continue;

                    $local_file = $file['url'];
                    if ($local_file !== false) {
                        $post_data['post_content'] = str_replace($url, $local_file, $post_data['post_content']);

                        if (!$this->model->findAttachmentByUrl(basename($url))) {
                            $attach_id = wp_insert_attachment(array(
                                'post_mime_type' => $file['type'],
                                'post_title' => SQ_Classes_Tools::getValue('sq_keyword', preg_replace('/\.[^.]+$/', '', $file['filename'])),
                                'post_content' => '',
                                'post_status' => 'inherit',
                                'guid' => $local_file
                            ), $file['file'], $postarr['ID']);

                            $attach_data = wp_generate_attachment_metadata($attach_id, $file['file']);
                            wp_update_attachment_metadata($attach_id, $attach_data);
                        }
                    }
                }

                if (microtime(true) - $time >= 20) {
                    break;
                }

            }


        }

        return $post_data;
    }


    /**
     * Check the SEO from Squirrly Live Assistant
     *
     * @param array $post_data
     * @param array $postarr
     * @return array
     */
    public function checkSeo($post_data, $postarr) {
        if (!isset($post_data['post_content']) || !isset($postarr['ID'])) {
            return $post_data;
        }

        $args = array();

        $seo = SQ_Classes_Tools::getValue('sq_seo', '');

        if (is_array($seo) && count($seo) > 0)
            $args['seo'] = implode(',', $seo);

        $args['keyword'] = SQ_Classes_Tools::getValue('sq_keyword', '');

        $args['status'] = $post_data['post_status'];
        $args['permalink'] = get_permalink($postarr['ID']);
        $args['author'] = $post_data['post_author'];
        $args['post_id'] = $postarr['ID'];

        if (SQ_Classes_Tools::getOption('sq_force_savepost')) {
            SQ_Classes_Action::apiCall('sq/seo/post', $args, 10);
        } else {
            $process = array();
            if (get_option('sq_seopost') !== false) {
                $process = json_decode(get_option('sq_seopost'), true);
            }

            $process[] = $args;

            //save for later send to api
            update_option('sq_seopost', json_encode($process));
            wp_schedule_single_event(time(), 'sq_processApi');

            //If the queue is too big ... means that the cron is not working
            if (count($process) > 5) SQ_Classes_Tools::saveOptions('sq_force_savepost', 1);
        }

        //Save the keyword for this post
        if ($json = $this->model->getKeyword($postarr['ID'])) {
            $json->keyword = addslashes(SQ_Classes_Tools::getValue('sq_keyword'));
            $this->model->saveKeyword($postarr['ID'], $json);
        } else {
            $args = array();
            $args['keyword'] = addslashes(SQ_Classes_Tools::getValue('sq_keyword'));
            $this->model->saveKeyword($postarr['ID'], json_decode(json_encode($args)));
        }

        //Save the snippet in case is edited in backend and not saved
        SQ_Classes_ObjController::getClass('SQ_Controllers_FrontMenu')->saveSEO();

        //check for custom SEO
        $this->_checkBriefcaseKeywords($postarr['ID']);

        return $post_data;
    }

    /**
     * Called when Post action is triggered
     *
     * @return void
     */
    public function action() {
        parent::action();

        switch (SQ_Classes_Tools::getValue('action')) {
            case 'sq_save_ogimage':
                if (!empty($_FILES['ogimage'])) {
                    $return = $this->model->addImage($_FILES['ogimage']);
                }
                if (isset($return['file'])) {
                    $return['filename'] = basename($return['file']);
                    $local_file = str_replace($return['filename'], urlencode($return['filename']), $return['url']);
                    $attach_id = wp_insert_attachment(array(
                        'post_mime_type' => $return['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', $return['filename']),
                        'post_content' => '',
                        'post_status' => 'inherit',
                        'guid' => $local_file
                    ), $return['file'], SQ_Classes_Tools::getValue('post_id'));

                    $attach_data = wp_generate_attachment_metadata($attach_id, $return['file']);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }
                SQ_Classes_Tools::setHeader('json');
                echo json_encode($return);
                SQ_Classes_Tools::emptyCache();

                break;
            case 'sq_get_keyword':
                SQ_Classes_Tools::setHeader('json');
                if (SQ_Classes_Tools::getIsset('post_id')) {
                    echo json_encode($this->model->getKeywordsFromPost(SQ_Classes_Tools::getValue('post_id')));
                } else {
                    echo json_encode(array('error' => true));
                }
                SQ_Classes_Tools::emptyCache();
                break;
        }
        exit();
    }

    /**
     * Save the keywords from briefcase into the meta keywords if there are no keywords saved
     * @param $post_id
     */
    private function _checkBriefcaseKeywords($post_id) {
        if (SQ_Classes_Tools::getIsset('sq_hash')) {
            $keywords = SQ_Classes_Tools::getValue('sq_briefcase_keyword', array());
            if (!empty($keywords)) {
                $sq_hash = SQ_Classes_Tools::getValue('sq_hash', md5($post_id));
                $url = SQ_Classes_Tools::getValue('sq_url', get_permalink($post_id));
                $sq = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->getSqSeo($sq_hash);

                if ($sq->doseo && $sq->keywords == '') {
                    $sq->keywords = join(',', $keywords);

                    SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->db_insert(
                        $url,
                        $sq_hash,
                        (int)$post_id,
                        maybe_serialize($sq->toArray()),
                        gmdate('Y-m-d H:i:s')
                    );
                }
            }
        }
    }

    public function hookFooter() {
        if (!defined('DISABLE_WP_CRON') || DISABLE_WP_CRON) {
            global $pagenow;
            if (in_array($pagenow, array('post.php', 'post-new.php'))) {
                SQ_Classes_ObjController::getClass('SQ_Controllers_Cron')->processSEOPostCron();
            }
        }
    }

}
