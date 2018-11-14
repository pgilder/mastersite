<?php

/**
 * Keyword Research settings
 */
class SQ_Core_BlockSerpKeywords extends SQ_Classes_BlockController {

    public $tabs;
    public $keywords = array();
    public $index;
    public $error;
    /** @var SQ_Models_SerpCheckerTable */
    public $listTable;

    public function init() {
        $this->listTable = SQ_Classes_ObjController::getClass('SQ_Models_SerpCheckerTable');

        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia('switchery');
        $per_page = $this->listTable->get_items_per_page('edit_post_per_page');
        $paged = (int)SQ_Classes_Tools::getValue('paged', 1);

        $args = array();
        $args['paged'] = $paged;
        $args['per_page'] = $per_page;
        $args['ignored'] = (int)SQ_Classes_Tools::getOption('sq_google_show_ignored');
        $json = json_decode(SQ_Classes_Action::apiCall('sq/rank-checker/get-keywords', $args));

        if (isset($json->keywords)) {
            if(!empty($json->keywords)) {
                $this->listTable->set_pagination_args(array(
                    'total_items' => $json->total,
                    'total_pages' => ceil($json->total / $per_page),
                    'per_page' => $per_page
                ));

                $this->index = (($paged - 1) * $per_page);
                $this->keywords = $json->keywords;
            }else{
                $this->keywords = array();
                $this->listTable->set_pagination_args(array(
                    'total_items' => 0,
                    'total_pages' => 0,
                    'per_page' => $per_page
                ));

                $this->error = __('No keyword found yet.', _SQ_PLUGIN_NAME_);
            }
        }else{
            $this->keywords = array();
            $this->error = __("No connection with Squirrly Server", _SQ_PLUGIN_NAME_);
        }
        return $this;
    }

    /**
     * Called when action is triggered
     *
     * @return void
     */
    public function action() {
        parent::action();
        SQ_Classes_Tools::setHeader('json');
        switch (SQ_Classes_Tools::getValue('action')) {
            case 'sq_serp_showignore':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to activate this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                SQ_Classes_Tools::saveOptions('sq_google_show_ignored', (int)SQ_Classes_Tools::getValue('sq_show_ignored', 0));
                return;
            case 'sq_serp_ignore':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to activate this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $keyword = (string)SQ_Classes_Tools::getValue('keyword', '');
                $active = (int)SQ_Classes_Tools::getValue('active', 1);

                if ($keyword <> '') {
                    //delete all local keyword ranks
                    SQ_Classes_ObjController::getClass('SQ_Models_SerpChecker')->deleteKeyword($keyword);

                    //set ignore on API
                    $args = array();
                    $args['keyword'] = $keyword;
                    $args['active'] = $active;
                    echo SQ_Classes_Action::apiCall('sq/rank-checker/ignore-keyword', $args);
                } else {
                    echo json_encode(array('error' => true));
                }

                break;
            case 'sq_serp_articlesearch':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to activate this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $string = SQ_Classes_Tools::getValue('sSearch', '');
                $start = SQ_Classes_Tools::getValue('iDisplayStart', 0);
                $show = SQ_Classes_Tools::getValue('iDisplayLength', 10);

                $data = array();

                $posts = SQ_Classes_ObjController::getClass('SQ_Models_SerpChecker')->searchPost($string, array(), $start, $show);
                if (!empty($posts)) {
                    foreach ($posts as $item) {
                        $data[] = array('<span class="sq_item" id="' . $item->ID . '">' . $item->post_title . '</span>');
                    }
                }
                echo json_encode(array('data' => $data));

                break;

            case 'sq_serp_addkeyword':
                $keyword = SQ_Classes_Tools::getValue('keyword', null);
                $post_url = SQ_Classes_Tools::getValue('post_url', '');
                $post_id = url_to_postid($post_url);

                if (isset($keyword) && (int)$post_id > 0) {
                    $args = array();
                    $args['post_id'] = (int)$post_id;
                    $args['permalink'] = get_permalink((int)$post_id);
                    $args['country'] = SQ_Classes_ObjController::getClass('SQ_Controllers_SerpChecker')->getCountry();
                    $args['language'] = SQ_Classes_ObjController::getClass('SQ_Controllers_SerpChecker')->getLanguage();
                    $args['keywords'] = json_encode(array($keyword));
                    SQ_Classes_Action::apiCall('sq/rank-checker/save-keywords', $args);
                    echo json_encode(array('sent' => true));
                } else {
                    echo json_encode(array('error' => __('Could not find the Article in your Website', _SQ_PLUGIN_NAME_)));
                }
                break;
        }
        exit();
    }


}
