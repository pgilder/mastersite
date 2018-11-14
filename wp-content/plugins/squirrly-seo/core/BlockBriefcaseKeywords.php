<?php

/**
 * Keyword Research settings
 */
class SQ_Core_BlockBriefcaseKeywords extends SQ_Classes_BlockController {

    public $tabs;
    public $keywords = array();
    public $labels = array();
    public $countries; //string with li of all countries
    //
    public $index;
    public $error;
    /** @var SQ_Models_SerpCheckerTable */
    public $listTable;
    public $checkin;

    public function init() {
        $this->listTable = SQ_Classes_ObjController::getClass('SQ_Models_SerpCheckerTable');
        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia('serpchecker.css');
        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia('switchery');

        $menu = SQ_Classes_Tools::getOption('menu');

        //Not yet available
        if (SQ_Classes_Tools::getOption('sq_google_serp_active') == 0) {
            $this->checkin = json_decode(SQ_Classes_Action::apiCall('sq/rank-checker/checkin'));
            if (isset($this->checkin->active) && $this->checkin->active) {
                if (isset($this->checkin->trial) && $this->checkin->trial) {
                    SQ_Classes_Tools::saveOptions('sq_google_serp_trial', 1);
                } else {
                    SQ_Classes_Tools::saveOptions('sq_google_serp_trial', 0);
                }
                SQ_Classes_Tools::saveOptions('sq_google_serp_active', 1);
                SQ_Classes_ObjController::getClass('SQ_Classes_Error')->setError(sprintf(__('%sYou activated the  Business Plan with Advanced Analytics. %sStart Here%s %s'), '<strong style="font-size: 16px;">', '<a href="' . admin_url('admin.php?page=sq_posts') . '">', '</a>', '</strong>'), 'success');
            } elseif (isset($this->checkin->error) && $this->checkin->error == "subscription_notfound" && !SQ_Classes_Tools::getOption('sq_google_serp_active')) {
                if ($menu['show_ads']) {
                    SQ_Classes_ObjController::getClass('SQ_Classes_Error')->setError(sprintf(__('%sStart a FREE Trial of the Business Plan with Advanced Analytics for 7 days. No credit card required. %sSee details%s %s'), '<strong style="font-size: 16px;">', '<a href="' . _SQ_DASH_URL_ . 'login/?token=' . SQ_Classes_Tools::getToken(). '&redirect_to=' . _SQ_DASH_URL_ . 'user/plans?pid=31" target="_blank">', '</a>', '</strong>'), 'trial');
                }
            }
        }


        $per_page = $this->listTable->get_items_per_page('edit_post_per_page');
        $paged = (int)SQ_Classes_Tools::getValue('paged', 1);
        $search = (string)SQ_Classes_Tools::getValue('skeyword', '');
        $labels = SQ_Classes_Tools::getValue('slabel', array());

        $args = array();
        $args['json'] = true;
        $args['paged'] = $paged;
        $args['search'] = $search;
        if(!empty($labels)) {
            $args['label'] = join(',', $labels);
        }
        $args['per_page'] = $per_page;
        $json = json_decode(SQ_Classes_Action::apiCall('sq/briefcase/get', $args));

        if (isset($json->keywords) && !empty($json->keywords)) {
            $this->listTable->set_pagination_args(array(
                'total_items' => $json->total,
                'total_pages' => ceil($json->total / $per_page),
                'per_page' => $per_page
            ));

            $this->index = (($paged - 1) * $per_page);
            $this->keywords = $json->keywords;
        } else {
            $this->error = __('No keyword found in the briefcase.', _SQ_PLUGIN_NAME_);
        }

        $this->labels = $json->labels;
        $this->countries = $json->countries;

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia('bootstrap');
        SQ_Classes_ObjController::getClass('SQ_Classes_Error')->hookNotices();

        return parent::init();
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
            case 'sq_briefcase_addkeyword':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to manage this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $keyword = (string)SQ_Classes_Tools::getValue('keyword', '');
                $labels = SQ_Classes_Tools::getValue('labels', array());

                if ($keyword <> '') {
                    $args = array();
                    $args['keyword'] = $keyword;
                    if (!empty($labels)) {
                        foreach ($labels as $label) {
                            $args['label'] = $label;
                            SQ_Classes_Action::apiCall('sq/briefcase/add', $args);
                        }
                    } else {
                        SQ_Classes_Action::apiCall('sq/briefcase/add', $args);

                    }

                    echo json_encode(array('saved' => __('Saved!', _SQ_PLUGIN_NAME_)));
                } else {
                    echo json_encode(array('error' => __('Invalid Keyword!', _SQ_PLUGIN_NAME_)));
                }
                break;
            case 'sq_briefcase_deletekeyword':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to manage this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $id = (int)SQ_Classes_Tools::getValue('id', 0);

                if ($id > 0) {
                    //set ignore on API
                    $args = array();
                    $args['id'] = $id;
                    SQ_Classes_Action::apiCall('sq/briefcase/delete', $args);
                    echo json_encode(array('deleted' => __('Deleted!', _SQ_PLUGIN_NAME_)));
                } else {
                    echo json_encode(array('error' => __('Invalid params!', _SQ_PLUGIN_NAME_)));
                }
                break;
            case 'sq_briefcase_doresearch':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to manage this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $keyword = (string)SQ_Classes_Tools::getValue('keyword', '');
                $country = (string)SQ_Classes_Tools::getValue('country', 'us');

                if ($keyword <> '' ) {
                    $args = array();
                    $args['q'] = $keyword;
                    $args['lang'] = 'en_US';
                    $args['country'] = $country;
                    $response = json_decode(SQ_Classes_Action::apiCall('sq/kr/brief', $args));
                    if ($response->error == 'limit_exceeded') {
                        $response->error = '<div class="sq_limit_exceeded">' . __('Keyword Research limit exceeded.', _SQ_PLUGIN_NAME_) . '<br>  <a href="' . _SQ_DASH_URL_ . 'login/?token=' . SQ_Classes_Tools::getOption('sq_api') . '&redirect_to=' . _SQ_DASH_URL_ . 'user/plans?pid=102" target="_blank">' . __('Add 20 Keyword Researches', _SQ_PLUGIN_NAME_) . '</a></div>';
                    }
                    echo json_encode($response);
                } else {
                    echo json_encode(array('error' => __('Invalid Keyword!', _SQ_PLUGIN_NAME_)));
                }
                break;
            case 'sq_briefcase_article':

                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to manage this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $keyword = (string)SQ_Classes_Tools::getValue('keyword', '');

                if ($keyword <> '') {
                    $args = array();
                    $args['keyword'] = $keyword;
                    $articles = json_decode(SQ_Classes_Action::apiCall('sq/briefcase/articles', $args));

                    $rows = array();
                    if ($articles && !empty($articles)) {
                        foreach ($articles as $article) {
                            if ($post = get_post($article->id)) {
                                $link = get_permalink($post->ID);

                                if (SQ_Classes_Tools::getOption('sq_google_serp_active')) {
                                    $listTable = SQ_Classes_ObjController::getClass('SQ_Models_SerpCheckerTable');
                                    $check_rank = $listTable->getRankText($article->position, 0);
                                } else {
                                    $check_rank = '<button class="btn btn-default sq_research_selectit" onclick="location.href = \'' . SQ_Classes_Tools::getBusinessLink() . '\'">' . __('Check Ranks', _SQ_PLUGIN_NAME_) . '</button>';
                                }
                                $date_format = get_option('date_format');
                                $time_format = get_option('time_format');
                                $timezone = (int)get_option('gmt_offset');
                                $datetime = date($date_format . ' ' . $time_format, strtotime($article->datetime) + $timezone * HOUR_IN_SECONDS);
                                $rows[] = '<tr>
                                            <td><span style="font-size: 15px; color: #333">' . $post->post_title . '</span><br /><span style="font-size: 11px;"><a href="' . $link . '" target="_blank" >' . $link . '</a></span></td>
                                            <td align="right" id="sq_rank_value' . $post->ID . '" title="' . __('Last checked', _SQ_PLUGIN_NAME_) . ': ' . $datetime . '">' . $check_rank . '</td>
                                            <td align="right">' . (int)$article->optimized . '%' . '</td>
                                            <td align="center">
                                                <button class="btn btn-default sq_research_selectit" onclick="location.href = \'' . admin_url('post.php?post=' . $post->ID . '&action=edit') . '\'">' . __('Edit', _SQ_PLUGIN_NAME_) . '</button>
                                                ' . sprintf('<button class="btn btn-default sq_rank_refresh" data-id="' . $post->ID . '" data-keyword="' . htmlspecialchars($keyword) . '">%s</button>', __('Get Rank', _SQ_PLUGIN_NAME_)) . '
                                            </td>
                                        </tr>';
                            } else {
                                $rows[] = '<tr>
                                            <td><span style="font-size: 11px; color: #aaa">' . __('Deleted Post', _SQ_PLUGIN_NAME_) . '</span></td>
                                            <td align="right"></td>
                                            <td align="right">' . (int)$article->optimized . '%' . '</td>
                                            <td></td>
                                        </tr>';
                            }
                        }

                        $table = sprintf('<table class="subtable">
                                            <tr>
                                                <th>' . __('Article title', _SQ_PLUGIN_NAME_) . '</th>
                                                <th style="width: 150px; text-align: right;" align="right">' . __('Google Rank', _SQ_PLUGIN_NAME_) . '</th>
                                                <th style="width: 150px; text-align: right;">' . __('Optimized', _SQ_PLUGIN_NAME_) . '</th>
                                                <th style="width: 150px; text-align: center;">' . __('Option', _SQ_PLUGIN_NAME_) . '</th>
                                            </tr>
                                            %s
                                        </table>', join('', $rows));
                    } else {
                        $table = '<table class="subtable"><tr><td colspan="6" align="center" style="color: red">' . __('There are no articles found', _SQ_PLUGIN_NAME_) . '</td></tr></table>';
                    }


                    echo json_encode(array('articles' => $table));
                } else {
                    echo json_encode(array('error' => __('Invalid Keyword!', _SQ_PLUGIN_NAME_)));
                }
                break;

            /*****************************/

            case 'sq_briefcase_addlabel':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to manage this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $name = (string)SQ_Classes_Tools::getValue('name', 0);
                $color = (string)SQ_Classes_Tools::getValue('color', '#ffffff');

                if ($name <> '' && $color <> '') {
                    $args = array();
                    $args['name'] = $name;
                    $args['color'] = $color;
                    SQ_Classes_Action::apiCall('sq/briefcase/add-label', $args);

                    echo json_encode(array('saved' => __('Saved!', _SQ_PLUGIN_NAME_)));
                } else {
                    echo json_encode(array('error' => __('Invalid Label or Color!', _SQ_PLUGIN_NAME_)));
                }
                break;
            case 'sq_briefcase_editlabel':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to manage this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $id = (string)SQ_Classes_Tools::getValue('id', 0);
                $name = (string)SQ_Classes_Tools::getValue('name', 0);
                $color = (string)SQ_Classes_Tools::getValue('color', '#ffffff');

                if ((int)$id > 0 && $name <> '' && $color <> '') {
                    $args = array();
                    $args['id'] = $id;
                    $args['name'] = $name;
                    $args['color'] = $color;
                    SQ_Classes_Action::apiCall('sq/briefcase/edit-label', $args);

                    echo json_encode(array('saved' => __('Saved!', _SQ_PLUGIN_NAME_)));
                } else {
                    echo json_encode(array('error' => __('Invalid params!', _SQ_PLUGIN_NAME_)));
                }
                break;
            case 'sq_briefcase_deletelabel':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to manage this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $id = (int)SQ_Classes_Tools::getValue('id', 0);

                if ($id > 0) {
                    //set ignore on API
                    $args = array();
                    $args['id'] = $id;
                    SQ_Classes_Action::apiCall('sq/briefcase/delete-label', $args);
                    echo json_encode(array('deleted' => __('Deleted!', _SQ_PLUGIN_NAME_)));
                } else {
                    echo json_encode(array('error' => __('Invalid params!', _SQ_PLUGIN_NAME_)));
                }
                break;
            case 'sq_briefcase_keywordlabel':
                if (!current_user_can('edit_posts')) {
                    echo json_encode(array('error' => __("You don't have enough pemission to manage this feature", _SQ_PLUGIN_NAME_)));
                    exit();
                }

                $keyword_id = SQ_Classes_Tools::getValue('keyword_id', 0);
                $labels = SQ_Classes_Tools::getValue('labels', array());

                if ((int)$keyword_id > 0) {
                    $args = array();
                    $args['id'] = $keyword_id;
                    if(is_array($labels) && !empty($labels)) {
                        $args['labels'] = join(',', $labels);
                        SQ_Classes_Action::apiCall('sq/briefcase/add-briefcase-labels', $args);
                    }else{
                        SQ_Classes_Action::apiCall('sq/briefcase/delete-briefcase-labels', $args);

                    }
                    echo json_encode(array('saved' => __('Saved!', _SQ_PLUGIN_NAME_)));
                } else {
                    echo json_encode(array('error' => __('Invalid Keyword!', _SQ_PLUGIN_NAME_)));
                }
                break;

        }

        exit();

    }


}
