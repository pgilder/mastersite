<?php

class SQ_Controllers_SerpChecker extends SQ_Classes_FrontController {

    public $checkin;
    /** @var SQ_Models_SerpChecker */
    public $model;
    /** @var SQ_Models_SerpCheckerTable */
    public $listTable;
    //
    protected $_tabs;
    protected $_tab;

    public function init() {
        $this->tabs = array();

        SQ_Classes_Tools::saveOptions('sq_analytics', 1); //Save analytics viewed

        if (SQ_Classes_Tools::getOption('sq_api') <> '') {
            $this->checkin = json_decode(SQ_Classes_Action::apiCall('sq/rank-checker/checkin'));
            if (isset($this->checkin->error)) {
                SQ_Classes_Tools::saveOptions('sq_google_serp_active', 0);
                SQ_Classes_ObjController::getClass('SQ_Classes_Error')->setError(sprintf(__('To get back to the Advanced Analytics and see rankings for all the keywords in Briefcase upgrade to %sBusiness Plan%s.'), '<a href="' . SQ_Classes_Tools::getBusinessLink() . '" target="_blank">', '</a>'), 'error');
            } else {
                if (isset($this->checkin->trial) && $this->checkin->trial) {
                    SQ_Classes_Tools::saveOptions('sq_google_serp_trial', 1);
                } else {
                    SQ_Classes_Tools::saveOptions('sq_google_serp_trial', 0);
                }
            }

            SQ_Classes_ObjController::getClass('SQ_Classes_Error')->hookNotices();

            //Prepare the table with records
            $this->listTable = SQ_Classes_ObjController::getClass('SQ_Models_SerpCheckerTable');
            $this->listTable->prepare_items();

            //Set the Tabs
            $this->_tabs['keywords'] = 'Top Keywords';
            $this->_tab = SQ_Classes_Tools::getValue('tab', false);

            SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia('bootstrap');
            SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia('serpchecker');

            if ($this->_tab <> '') {
                foreach ($this->_tabs as $slug => $value) {
                    if ($this->_tab == $slug) {
                        echo SQ_Classes_ObjController::getClass('SQ_Core_BlockSerp' . ucfirst($slug))->init()->getView();
                        return;
                    }
                }
            }
        }

        return parent::init();
    }

    public function action() {
        parent::action();

        switch (SQ_Classes_Tools::getValue('action')) {
            case 'sq_serp_process':
                SQ_Classes_Tools::setHeader('json');
                $done = false;

                $paged = (int)get_transient('sq_progress');
                if (!$this->getAllRanks(($paged + 1), 100, false)) {
                    exit(json_encode(array('progress' => (min($paged + 1, 9.9) * 10))));
                } else {
                    exit(json_encode(array('progress' => 100)));
                }

                break;

            case 'sq_serp_refresh':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to activate this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                SQ_Classes_Tools::setHeader('json');

                $this->getAllRanks();

                exit(json_encode(array('refreshed' => true)));

                break;

            case 'sq_serp_refresh_post':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to activate this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                SQ_Classes_Tools::setHeader('json');
                $this->listTable = SQ_Classes_ObjController::getClass('SQ_Models_SerpCheckerTable');

                $post_id = SQ_Classes_Tools::getValue('id', false);
                $keyword = SQ_Classes_Tools::getValue('keyword', false);
                if ($post_id && $keyword) {
                    $args = array();
                    $args['post_id'] = $post_id;
                    $args['keyword'] = $keyword;
                    $json = json_decode(SQ_Classes_Action::apiCall('sq/rank-checker/process-single', $args));

                    if (isset($json->rank) && !empty($json->rank)) {
                        $this->model->saveKeyword($post_id, $json->rank);

                        $rank = current($json->rank);
                        $date_format = get_option('date_format');
                        $time_format = get_option('time_format');
                        $timezone = (int)get_option('gmt_offset');
                        $rank->datetime = date($date_format . ' ' . $time_format, strtotime($rank->datetime) + $timezone * HOUR_IN_SECONDS);

                        exit(json_encode(array('rank' => $this->listTable->getRankText($rank->rank, $rank->change), 'datetime' => __('Last checked', _SQ_PLUGIN_NAME_) . ': ' . $rank->datetime)));
                    } else {
                        $rank = $this->model->getKeyword($post_id);

                        $rank = current($json->rank);
                        $date_format = get_option('date_format');
                        $time_format = get_option('time_format');
                        $timezone = (int)get_option('gmt_offset');
                        $rank->datetime = date($date_format . ' ' . $time_format, strtotime($rank->datetime) + $timezone * HOUR_IN_SECONDS);

                        exit(json_encode(array('rank' => $this->listTable->getRankText($rank->rank, $rank->change), 'datetime' => __('Last checked', _SQ_PLUGIN_NAME_) . ': ' . $rank->datetime)));
                    }
                }

                exit(json_encode(array('error' => __('Invalid Request', _SQ_PLUGIN_NAME_))));

                break;
            case 'sq_serp_purgeall':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to activate this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                SQ_Classes_ObjController::getClass('SQ_Models_SerpChecker')->purgeAllMeta(array('key' => '_src_processed'));
                SQ_Classes_ObjController::getClass('SQ_Models_SerpChecker')->purgeAllMeta(array('key' => '_src_keyword'));
                $this->getAllRanks();

                SQ_Classes_Error::setMessage(__('Removed successfully! The ranks were updated from Squirry Server'));
                break;

        }
    }


    public function hookHead() {
        return '';
    }

    public function getNavigationTop() {
        return $this->listTable->display_tablenav('top');
    }

    public function getNavigationBottom() {
        return $this->listTable->display_tablenav('bottom');
    }

    public function getHeaderColumns() {
        return $this->listTable->print_column_headers();
    }

    public function getRows() {
        return $this->listTable->display_rows();
    }

    /*
    * Set the javascript variables
    */
    public function setVars() {
        echo '<script type="text/javascript">
                    var __sq_refresh = "' . __('Update', _SQ_PLUGIN_NAME_) . '"

                    var __sq_dashurl = "' . _SQ_DASH_URL_ . '";
                    var __token = "' . SQ_Classes_Tools::getOption('sq_api') . '";
                    var __sq_ranknotpublic_text = "' . __('Not Public', _SQ_PLUGIN_NAME_) . '";
                    var __sq_couldnotprocess_text = "' . __('Could not process', _SQ_PLUGIN_NAME_) . '";
              </script>';
    }

    public function getScripts() {
        return '<script type="text/javascript">
               google.load("visualization", "1", {packages: ["corechart"]});
               function drawChart(id, values, reverse) {
                    var data = google.visualization.arrayToDataTable(values);

                    var options = {

                        curveType: "function",
                        title: "",
                        chartArea:{width:"100%",height:"100%"},
                        enableInteractivity: "true",
                        tooltip: {trigger: "auto"},
                        pointSize: "2",
                        colors: ["#55b2ca"],
                        hAxis: {
                          baselineColor: "transparent",
                           gridlineColor: "transparent",
                           textPosition: "none"
                        } ,
                        vAxis:{
                          direction: ((reverse) ? -1 : 1),
                          baselineColor: "transparent",
                          gridlineColor: "transparent",
                          textPosition: "none"
                        }
                    };

                    var chart = new google.visualization.LineChart(document.getElementById(id));
                    chart.draw(data, options);
                    return chart;
                }
          </script>';
    }


    /**
     * Get the specified country in Squirrly
     * @return mixed|string
     */
    public function getCountry() {
        if (SQ_Classes_Tools::getOption('sq_google_country') <> '') {
            return SQ_Classes_Tools::getOption('sq_google_country');
        }
        return 'com';
    }

    /**
     * Get the google language from settings
     * @return string
     */
    public function getLanguage() {
        if (SQ_Classes_Tools::getOption('sq_google_language') <> '') {
            return SQ_Classes_Tools::getOption('sq_google_language');
        }
        return 'en';
    }


    /**
     * Do google rank with cron
     *
     * @param int $limit
     */
    public function processCron($limit = 50, $force_ranks = false) {
        set_time_limit(30);
        /* Load the Submit Actions Handler */
        SQ_Classes_ObjController::getClass('SQ_Classes_Tools');
        SQ_Classes_ObjController::getClass('SQ_Classes_Action');

        //Get saved ranks from API SERVER
        $last_check = (int)SQ_Classes_Tools::getOption('sq_google_last_checked');

        //If force ranks or every day
        if ($force_ranks || $last_check < strtotime('-6 hours')) {
            $this->getAllRanks();
        }

//        try {
//            //Find more posts with keywords and send them to API
//            SQ_Classes_Tools::dump('Process queued posts');
//            if ($posts = $this->model->getQueuePost($limit)) {
//                if (!empty($posts))
//                    foreach ($posts as $post) {
//                        $this->sendPostToApi($post);
//                    }
//            }
//        } catch (Exception $e) {
//
//        }


    }

    /**
     * Send the post to API for SERP Check
     * @param $post
     */
    public function sendPostToApi($post) {
        global $wp_query;

        if ($wp_query) {
            if ($keyword = $this->model->getKeywordsFromPost($post)) {
                $args = array();

                $args['post_id'] = $post->ID;
                $args['permalink'] = get_permalink($post->ID);
                $args['country'] = $this->getCountry();
                $args['language'] = $this->getLanguage();
                $args['keywords'] = json_encode(array($keyword));

                SQ_Classes_Action::apiCall('sq/rank-checker/save-keywords', $args);
            }

            //save post verified and exclude from queue
            $this->model->saveProcessed($post->ID);

        }

    }

    /**
     * Get all ranks from API Server
     */
    public function getAllRanks($paged = 1, $per_page = 20, $loop = true, $cnt = 0) {
        set_time_limit(90);
        $args = array('paged' => $paged, 'per_page' => $per_page);
        set_transient('sq_progress', $paged, 60);

        $json = json_decode(SQ_Classes_Action::apiCall('sq/rank-checker/get-ranks', $args));
        if (isset($json->ranks)) {
            if (!empty($json->ranks)) {
                foreach ($json->ranks as $post_id => $rank) {
                    if ($post_id == 0) {
                        foreach ($rank as $row) {
                            if($post_id = url_to_postid($row->permalink)) {
                                $status = get_post_status($post_id);
                                if ($status <> 'publish') {
                                    //delete the records for this post to insert all the keywords fresh
                                    $this->model->purgeMeta($post_id, array('key' => '_src_keyword'));
                                    continue;
                                } else {
                                    $this->model->saveKeyword($post_id, $rank);
                                }
                            }
                            //prevent timeout
                            if(number_format(microtime(true) - REQUEST_TIME, 0) >= 30){
                                $loop = false;
                                break;
                            }
                        }
                    } else {
                        $status = get_post_status($post_id);
                        if ($status <> 'publish') {
                            $args = array();
                            $args['status'] = ($status ? $status : 'deleted');
                            $args['post_id'] = $post_id;
                            //Make sure the API has the correct status of the post
                            SQ_Classes_Action::apiCall('sq/seo/update', $args, 10);

                            //delete the records for this post to insert all the keywords fresh
                            $this->model->purgeMeta($post_id, array('key' => '_src_keyword'));
                            continue;
                        } else {
                            $this->model->saveKeyword($post_id, $rank);
                        }
                    }
                    //prevent timeout
                    if(number_format(microtime(true) - REQUEST_TIME, 30) >= 85){
                        $loop = false;
                        break;
                    }
                }
            }
        }

        if ($cnt < 20 && $loop) { //prevent infinite loops
            if (isset($json->done) && !$json->done) {
                $this->getAllRanks(($paged + 1), $per_page, $loop, ($cnt + 1));
            }
        }

        //Save the summary if received
        if (isset($json->info) && isset($json->info->keywords)) {
            SQ_Classes_Tools::saveOptions('sq_google_last_info', json_encode($json->info));
        }

        if (isset($json->done) && $json->done) {
            SQ_Classes_Tools::saveOptions('sq_google_last_checked', time());

            return true;
        }

        return false;

    }


    /**
     * Get all ranks from API Server
     *
     * public function getChangedRanks($paged = 1, $per_page = 100, $cnt = 0) {
     * $args = array('paged' => $paged, 'per_page' => $per_page);
     * $json = json_decode(SQ_Classes_Action::apiCall('sq/rank-checker/get-changes', $args));
     *
     * if (isset($json->ranks) && !empty($json->ranks)) {
     * foreach ($json->ranks as $post_id => $rank) {
     * $this->model->saveKeyword($post_id, $rank);
     * }
     * }
     *
     * if ($cnt < 20) { //prevent infinite loops
     * if (isset($json->done) && !$json->done) {
     * $this->getChangedRanks(($paged + 1), $per_page, ($cnt + 1));
     * }
     * }
     *
     * SQ_Classes_Tools::saveOptions('sq_google_last_checked', time());
     *
     * //        print_R($json->info);
     * //        exit();
     * //Save the summary if received
     * if (isset($json->info) && !empty($json->info->keywords)) {
     * SQ_Classes_Tools::saveOptions('sq_google_last_info', json_encode($json->info));
     * }
     * }
     */
    public function getKeywordsFound() {
        return $this->model->countKeywords();
    }

}
