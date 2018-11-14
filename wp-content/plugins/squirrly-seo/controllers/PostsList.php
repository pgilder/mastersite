<?php

class SQ_Controllers_PostsList extends SQ_Classes_FrontController {

    /** @var array Posts types in */
    private $types = array();

    /** @var integer Set the column index for Squirrly */
    private $pos = 5;

    /** @var string Set the column name for Squirrly */
    private $column_id = 'sq_rank_column';

    /** @var boolean Is post list colled */
    private $is_list = false;
    private $posts = array();


    /**
     * Called in SQ_Controllers_Menu > hookMenu
     */
    public function init() {
        $this->types = array_map(array($this, '_addPostsType'), SQ_Classes_Tools::getOption('sq_post_types'));
    }

    /**
     * Create the column and filter for the Posts List
     *
     */
    public function hookInit() {
        if (SQ_Classes_Tools::getOption('sq_api') == '') {
            return;
        }

        $browser = SQ_Classes_Tools::getBrowserInfo();

        if ($browser['name'] == 'IE' && (int)$browser['version'] < 9 && (int)$browser['version'] > 0)
            return;

        foreach ($this->types as $type) {
            add_filter('manage_' . $type . '_columns', array($this, 'add_column'), 10, 1);
            add_action('manage_' . $type . '_custom_column', array($this, 'add_row'), 10, 2);
        }

        //Update post status
        add_action('before_delete_post', array($this->model, 'hookUpdateStatus'));
        add_action('untrashed_post', array($this->model, 'hookUpdateStatus'));
        add_action('trashed_post', array($this->model, 'hookUpdateStatus'));
    }

    protected function _addPostsType($type) {
        return $type . '_posts';
    }

    public function setPosts($posts) {
        if (!empty($posts)) {
            $this->posts = $posts;
            $this->is_list = true;
        }
        return $this;
    }

    /**
     * Hook the Wordpress header
     */
    public function loadHead() {
        parent::hookHead();
        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia(_SQ_THEME_URL_ . '/css/postslist.css');
    }

    /**
     * Add the Squirrly column in the Post List
     *
     * @param array $columns
     * @return array
     */
    public function add_column($columns) {
        $this->loadHead(); //load the js only for post list
        $this->is_list = true;

        return $this->insert($columns, array($this->column_id => __('Squirrly') . $this->getScripts()), $this->pos);
    }

    /**
     * Add row in Post list
     *
     * @param object $column
     * @param integer $post_id
     */
    public function add_row($column, $post_id) {
        $cached = false;

        if ($column == $this->column_id) {
            if (isset($_COOKIE[$this->column_id . $post_id]) && $_COOKIE[$this->column_id . $post_id] <> '') {
                $cached = true;
            } else {
                if (get_post_status($post_id) == 'publish')
                    array_push($this->posts, $post_id);
            }

            echo '<div class="' . $this->column_id . '_row" ref="' . $post_id . '">' . (($cached) ? $_COOKIE[$this->column_id . $post_id] : 'loading ...') . '</div>';

            if ($frontend = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')) {
                $title = $frontend->getAdvancedMeta($post_id, 'title');
                $description = $frontend->getAdvancedMeta($post_id, 'description');
                echo '<script type="text/javascript">
                    jQuery(\'#post-' . $post_id . '\').find(\'.row-title\').before(\'' . (($description <> '') ? '<span class="sq_rank_custom_meta sq_rank_customdescription sq_rank_sprite" title="' . __('Custom description: ', _SQ_PLUGIN_NAME_) . ' ' . addslashes($description) . '"></span>' : '') . ' ' . (($title <> '') ? '<span class="sq_rank_custom_meta sq_rank_customtitle sq_rank_sprite" title="' . __('Custom title: ', _SQ_PLUGIN_NAME_) . ' ' . addslashes($title) . '"></span>' : '') . '\');
               </script>';
            }
        }
    }

    /**
     * Hook the Footer
     *
     */
    public function hookFooter() {
        if (!$this->is_list)
            return;

        $posts = '';
        foreach ($this->posts as $post) {
            $posts .= '"' . $post . '",';
        }
        if (strlen($posts) > 0)
            $posts = substr($posts, 0, strlen($posts) - 1);

        echo '<script type="text/javascript">
                    var sq_posts = new Array(' . $posts . ');
                    //Show set complete
                    if (jQuery(".sq_helpnotice").length > 0)
                        jQuery(".sq_helpnotice").slideDown();
              </script>';

        $this->setVars();
    }

    /**
     * Set the javascript variables
     */
    public function setVars() {
        echo '<script type="text/javascript">
                    var __sq_article_rank = "' . __('SEO Analytics, by Squirrly', _SQ_PLUGIN_NAME_) . '";
                    var __sq_refresh = "' . __('Update', _SQ_PLUGIN_NAME_) . '"

                    var __sq_dashurl = "' . _SQ_STATIC_API_URL_ . '";
                    var __token = "' . SQ_Classes_Tools::getOption('sq_api') . '";
                    var __sq_ranknotpublic_text = "' . __('Not Public', _SQ_PLUGIN_NAME_) . '";
                    var __sq_couldnotprocess_text = "' . __('Could not process', _SQ_PLUGIN_NAME_) . '";
              </script>';
    }

    public function getScripts() {
        return '<script type="text/javascript">
                //load the rank from squirrly
                if (typeof sq_script === "undefined"){
                    var sq_script = document.createElement(\'script\');
                    sq_script.src = "' . _SQ_STATIC_API_URL_ . SQ_URI . '/js/sq_rank' . (SQ_DEBUG ? '' : '.min') . '.js?ver=' . SQ_VERSION_ID . '";
                    var site_head = document.getElementsByTagName ("head")[0] || document.documentElement;
                    site_head.insertBefore(sq_script, site_head.firstChild);
                }
               google.load("visualization", "1", {packages: ["corechart"]});
               function drawChart(id, values, reverse) {
                    var data = google.visualization.arrayToDataTable(values);

                    var options = {

                        curveType: "function",
                        title: "",
                        chartArea:{width:"100%",height:"100%"},
                        enableInteractivity: "true",
                        tooltip: {trigger: "auto"},
                        pointSize: "0",
                        legend: "none",
                        backgroundColor: "transparent",
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
     * Push the array to a specific index
     * @param array $src
     * @param array $in
     * @param integer $pos
     * @return array
     */
    public function insert($src, $in, $pos) {
        $array = array();
        if (is_int($pos))
            $array = array_merge(array_slice($src, 0, $pos), $in, array_slice($src, $pos));
        else {
            foreach ($src as $k => $v) {
                if ($k == $pos)
                    $array = array_merge($array, $in);
                $array[$k] = $v;
            }
        }
        return $array;
    }

    /**
     * Hook Get/Post action
     * @return string
     */
    public function action() {
        switch (SQ_Classes_Tools::getValue('action')) {
            case 'inline-save':
                check_ajax_referer('inlineeditnonce', '_inline_edit');
                if (isset($_POST['post_ID']) && ($post_id = (int)$_POST['post_ID']) && isset($_POST['_status']) && $_POST['_status'] <> '') {
                    $args = array();
                    $args['status'] = $_POST['_status'];
                    $args['post_id'] = $post_id;
                    SQ_Classes_Action::apiCall('sq/seo/update', $args, 10);
                }

                return;
        }

        parent::action();
        switch (SQ_Classes_Tools::getValue('action')) {
            case 'sq_posts_rank':
                SQ_Classes_Tools::setHeader('json');
                $args = array();
                $posts = SQ_Classes_Tools::getValue('posts');
                if (is_array($posts) && !empty($posts)) {
                    $posts = SQ_Classes_Tools::getValue('posts');
                    $args['posts'] = join(',', $posts);

                    $response = json_decode(SQ_Classes_Action::apiCall('sq/user-analytics/total', $args, 20));
                }
                if (isset($response) && is_object($response)) {
                    $response = $this->model->getTotal($response);
                    exit(json_encode($response));
                }
                exit(json_encode(array('posts' => array())));
                break;
            case 'sq_post_rank':
                SQ_Classes_Tools::setHeader('json');
                $args = array();
                $rank = null;
                $this->model->post_id = (int)SQ_Classes_Tools::getValue('post');
                $args['post_id'] = $this->model->post_id;

                if ($json = SQ_Classes_ObjController::getClass('SQ_Models_Post')->getKeyword($this->model->post_id)) {
                    if (isset($json->rank)) {
                        $rank = $json->rank;
                    } else {
                        $rank = get_transient('sq_rank' . $this->model->post_id);
                    }

                    if (isset($rank) && $rank !== false) {
                        $ranking = SQ_Classes_ObjController::getClass('SQ_Classes_Ranking');
                        $args['rank'] = (string)$rank;
                        $args['country'] = $ranking->getCountry();
                        $args['language'] = $ranking->getLanguage();
                    }
                }

                $response = json_decode(SQ_Classes_Action::apiCall('sq/user-analytics/detail', $args, 20));

                if (!is_object($response)) {
                    exit(json_encode(array('error' => $response)));
                } else {
                    //SQ_Classes_Tools::dump($response);
                    $analytics = SQ_Classes_ObjController::getClass('SQ_Core_BlockAnalytics');
                    $analytics->flush = false;
                    $analytics->post_id = $this->model->post_id;
                    $analytics->audit = $this->model->getAnalytics($response, $this->model->post_id);

                    $response = $analytics->init();
                    if (SQ_Classes_Tools::getValue('sq_debug') === 'on') {
                        exit();
                    }
                    exit(json_encode($response));
                }
                break;
            case 'sq_recheck':
                SQ_Classes_Tools::setHeader('json');
                if (SQ_Classes_Tools::getValue('sq_debug') === 'on' || get_transient('google_blocked') === false) {
                    $this->model->post_id = (int)SQ_Classes_Tools::getValue('post_id');
                    if ($json = SQ_Classes_ObjController::getClass('SQ_Models_Post')->getKeyword($this->model->post_id)) {
                        $oldrank = (isset($json->rank) ? $json->rank : -1);
                        if (get_transient('sq_rank' . $this->model->post_id) !== false) {
                            delete_transient('sq_rank' . $this->model->post_id);
                        }

                        $this->checkKeyword($json->keyword, true);
                        $json = SQ_Classes_ObjController::getClass('SQ_Models_Post')->getKeyword($this->model->post_id);
                        if ($json->rank === false) {
                            exit(json_encode(array('error' => true)));
                        } else {
                            if ($json->rank == -2) {
                                $json->rank = $oldrank;
                            }

                            if ($json->rank == -1) {
                                $value = sprintf(__('Not in top 100 for: %s'), '<br />' . $json->keyword);
                            } elseif ($json->rank == 0) {
                                $value = __('The URL is Indexed', _SQ_PLUGIN_NAME_);
                            } elseif ($json->rank > 0) {
                                $value = '<strong style="display:block; font-size: 120%; width: 100px; margin: 0 auto; text-align:right;">' . sprintf(__('%s'), $json->rank) . '</strong>' . ((isset($json->country)) ? ' (' . $json->country . ')' : '');
                            }
                            exit(json_encode(array('rank' => $value)));
                        }
                    }

                    exit(json_encode(array('error' => true)));
                } else {
                    $this->model->post_id = (int)SQ_Classes_Tools::getValue('post_id');
                    if ($json = SQ_Classes_ObjController::getClass('SQ_Models_Post')->getKeyword($this->model->post_id)) {
                        if ($json->rank === false) {
                            exit(json_encode(array('error' => true)));
                        } else {
                            if ($json->rank == -1) {
                                $value = sprintf(__('Not in top 100 for: %s'), '<br />' . $json->keyword);
                            } elseif ($json->rank == 0) {
                                $value = __('The URL is Indexed', _SQ_PLUGIN_NAME_);
                            } elseif ($json->rank > 0) {
                                $value = '<strong style="display:block; font-size: 120%; width: 100px; margin: 0 auto; text-align:right;">' . sprintf(__('%s'), $json->rank) . '</strong>' . ((isset($json->country)) ? ' (' . $json->country . ')' : '');
                            }
                            exit(json_encode(array('rank' => $value)));
                        }
                    }
                    exit(json_encode(array('error' => true)));
                }
                break;
        }
    }

    /**
     * Check and save the Keyword SERP
     *
     * @param type $keyword
     * @return type
     */
    private function checkKeyword($keyword, $force = false) {
        $rank = null;

        if ($keyword == '')
            return;

        $ranking = SQ_Classes_ObjController::getClass('SQ_Classes_Ranking');
        if (is_object($ranking)) {
            $rank = get_transient('sq_rank' . $this->model->post_id);
            //if the rank is not in transient
            if ($rank === false) {
                //get the keyword from database
                $json = SQ_Classes_ObjController::getClass('SQ_Models_Post')->getKeyword($this->model->post_id);
                if ($force === false && isset($json->rank)) {
                    $rank = $json->rank;
                    //add it to transient
                    set_transient('sq_rank' . $this->model->post_id, $rank, (60 * 60 * 24 * 1));
                } else {
                    $rank = $ranking->processRanking($this->model->post_id, $keyword);

                    if ($rank == -1) {
                        sleep(mt_rand(5, 10));
                        //if not indexed with the keyword then find the url
                        if ($ranking->processRanking($this->model->post_id, get_permalink($this->model->post_id)) > 0) { //for permalink index set 0
                            $rank = 0;
                        }
                    }
                    if ($rank !== false && $rank >= -1) {
                        $args = array();
                        $args['keyword'] = $keyword;
                        $args['rank'] = $rank;
                        $args['country'] = $ranking->getCountry();
                        $args['language'] = $ranking->getLanguage();
                        SQ_Classes_ObjController::getClass('SQ_Models_Post')->saveKeyword($this->model->post_id, json_decode(json_encode($args)));
                    }
                    //add it to transient
                    set_transient('sq_rank' . $this->model->post_id, $rank, (60 * 60 * 24 * 1));
                }
            }

            //save the rank if there is no error
            if ($rank !== false && $rank >= -1) {
                $args = array();
                $args['post_id'] = $this->model->post_id;
                $args['rank'] = (string)$rank;
                $args['country'] = $ranking->getCountry();
                $args['language'] = $ranking->getLanguage();
                SQ_Classes_Action::apiCall('sq/user-analytics/saveserp', $args);
            }
        }
        return $rank;
    }

}
