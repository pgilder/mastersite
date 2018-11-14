<?php

class SQ_Controllers_FrontMenu extends SQ_Classes_FrontController {

    public function action() {
        parent::action();
        switch (SQ_Classes_Tools::getValue('action')) {
            case 'sq_saveseo':
                //Save the SEO settings
                $json = $this->saveSEO();

                if (SQ_Classes_Tools::isAjax()) {
                    SQ_Classes_Tools::setHeader('json');
                    echo json_encode($json);
                    exit();
                }
                break;
        }
    }

    public function saveSEO(){
        $json = array();
        if (SQ_Classes_Tools::getIsset('sq_hash')) {
            $sq_hash = SQ_Classes_Tools::getValue('sq_hash', false);
            $post_id = SQ_Classes_Tools::getValue('post_id', 0);
            $term_taxonomy_id = SQ_Classes_Tools::getValue('term_taxonomy_id', 0);
            $taxonomy = SQ_Classes_Tools::getValue('taxonomy', 'category');

            if (!current_user_can('manage_options')) {
                if (!current_user_can('edit_post', $post_id)) {
                    $json['error'] = 1;
                    $json['error_message'] = __("You don't have enough pemission to edit this article", _SQ_PLUGIN_NAME_);
                    exit();
                }
            }

            $url = SQ_Classes_Tools::getValue('sq_url', false);

            $sq = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->getSqSeo($sq_hash);

            $sq->doseo = SQ_Classes_Tools::getValue('sq_doseo', 0);

            $sq->title = SQ_Classes_Tools::getValue('sq_title', false);
            $sq->description = SQ_Classes_Tools::getValue('sq_description', false);
            $sq->keywords = SQ_Classes_Tools::getValue('sq_keywords', array());
            $sq->canonical = SQ_Classes_Tools::getValue('sq_canonical', false);
            $sq->noindex = SQ_Classes_Tools::getValue('sq_noindex', 0);
            $sq->nofollow = SQ_Classes_Tools::getValue('sq_nofollow', 0);
            $sq->nositemap = SQ_Classes_Tools::getValue('sq_nositemap', 0);

            $sq->og_title = SQ_Classes_Tools::getValue('sq_og_title', false);
            $sq->og_description = SQ_Classes_Tools::getValue('sq_og_description', false);
            $sq->og_author = SQ_Classes_Tools::getValue('sq_og_author', false);
            $sq->og_type = SQ_Classes_Tools::getValue('sq_og_type', 'website');
            $sq->og_media = SQ_Classes_Tools::getValue('sq_og_media', false);

            $sq->tw_title = SQ_Classes_Tools::getValue('sq_tw_title', false);
            $sq->tw_description = SQ_Classes_Tools::getValue('sq_tw_description', false);
            $sq->tw_media = SQ_Classes_Tools::getValue('sq_tw_media', false);


            //Prevent broken url in canonical link
            if(strpos($sq->canonical,'//') === false){
                $sq->canonical = false;
            }
            //empty the cache from cache plugins
            //SQ_Classes_Tools::emptyCache();

            if (SQ_Classes_ObjController::getClass('SQ_Models_BlockSettingsSeo')->db_insert(
                $url,
                $sq_hash,
                (int)$post_id,
                maybe_serialize($sq->toArray()),
                gmdate('Y-m-d H:i:s')
            )
            ) {
                $json['saved'] = $sq_hash;

                if (SQ_Classes_Tools::isAjax()) {
                    if ($post_id > 0) {
                        if (SQ_Classes_ObjController::getClass("SQ_Controllers_Menu")->setPostByID($post_id)) {
                            $json['html'] = SQ_Classes_ObjController::getClass("SQ_Controllers_Menu")->getView('FrontMenu');
                        }
                    } elseif ($term_taxonomy_id > 0) {
                        if (SQ_Classes_ObjController::getClass("SQ_Controllers_Menu")->setPostByTaxID($term_taxonomy_id, $taxonomy)) {
                            $json['html'] = SQ_Classes_ObjController::getClass("SQ_Controllers_Menu")->getView('FrontMenu');
                        }
                    }
                }
            } else {
                $json['error'] = 1;
                $json['error_message'] = 'Could not Insert the information';
                SQ_Classes_Tools::createTable();
            };

        } else {
            $json['error'] = 1;
            $json['error_message'] = 'Wrong number of parameters';

        }

        return $json;
    }
}