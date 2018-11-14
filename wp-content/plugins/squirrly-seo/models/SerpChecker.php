<?php

class SQ_Models_SerpChecker {
    public function __construct() {
        $this->exclude = array('attachment', 'revision', 'nav_menu_item', 'product_variation', 'shop_order', 'shop_order_refund', 'shop_coupon', 'shop_webhook');
    }

    /******************************************************************************/
    public function getPostTypes() {
        $types = get_post_types();
        foreach (array('revision', 'nav_menu_item') as $exclude) {
            if (in_array($exclude, $types)) {
                unset($types[$exclude]);
            }
        }

        foreach ($types as $type) {
            $type_data = get_post_type_object($type);

            if (isset($type_data->public) && !$type_data->public) {
                unset($types[$type]);
            }
        }

        return $types;
    }


    /**
     * check if there are keywords saved
     * @global object $wpdb
     * @return integer|false
     */
    public function getPostWithKeywords() {
        global $wpdb;

        if ($rows = $wpdb->get_results("SELECT  `meta_id`, `post_id`, `meta_value`
                FROM `" . $wpdb->postmeta . "`
                WHERE (`meta_key` = '_src_keyword')")
        ) {
            SQ_Classes_Tools::dump($rows);
            return array_map(array($this, '_decodePostByMeta'), $rows);
        }

        return false;
    }

    private function _decodePostByMeta(&$row) {
        $row->meta_value = json_decode($row->meta_value);
        return $row;
    }


    /**
     * get the keyword
     * @global object $wpdb
     * @param integer $post_id
     * @return boolean
     */
    public function getKeyword($post_id) {
        global $wpdb;

        if ($row = $wpdb->get_row("SELECT `post_id`, `meta_value`
                       FROM `" . $wpdb->postmeta . "`
                       WHERE (`meta_key` = '_src_keyword' AND `post_id`=" . (int)$post_id . ")
                       ORDER BY `meta_id` DESC")
        ) {

            $this->_decodePostByMeta($row);
            return $row->meta_value;
        }

        return false;
    }


    /********************** RANKING *****************************/
    public function searchPost($q, $exclude = array(), $start = 0, $nbr = 10) {
        global $wpdb;
        $responce = array();
        if (sizeof($exclude) > 1) {
            $exclude = join(',', $exclude);
        } else
            $exclude = (int)$exclude;

        $q = trim($q, '"');
        /* search in wp database */
        $posts = $wpdb->get_results("SELECT ID, post_title, post_date_gmt, post_content, post_type FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND (post_title LIKE '%$q%' OR post_content LIKE '%$q%') AND ID not in ($exclude) ORDER BY post_title LIMIT " . $start . ',' . ($start + $nbr));


        if ($posts) {
            return $posts;
        }
        return false;
    }

    public function getQueuePost($limit = 100) {
        $post_types = $this->getPostTypes();
        foreach ($this->exclude as $exclude) {
            if (in_array($exclude, $post_types)) {
                unset($post_types[$exclude]);
            }
        }

        $args = array(
            'posts_per_page' => $limit,
            'post_type' => $post_types,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => '_src_processed',
                    'compare' => 'NOT EXISTS'
                )
            )
        );

        return get_posts($args);
    }

    public function countQueuePost($args = array()) {
        $post_types = $this->getPostTypes();
        foreach ($this->exclude as $exclude) {
            if (in_array($exclude, $post_types)) {
                unset($post_types[$exclude]);
            }
        }

        $args = array_merge(array(
            'post_type' => $post_types,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_src_processed',
                    'compare' => 'NOT EXISTS'
                )
            )
        ), $args);

        $posts = new WP_Query($args);
        return $posts->found_posts;
    }

    public function getRankQueuePost($old = 1, $limit = 10) {
        $post_types = $this->getPostTypes();
        foreach ($this->exclude as $exclude) {
            if (in_array($exclude, $post_types)) {
                unset($post_types[$exclude]);
            }
        }

        $args = array(
            'posts_per_page' => $limit,
            'post_type' => $post_types,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'relation' => 'AND',
                    array(
                        'key' => '_src_processed',
                        'compare' => 'EXISTS'
                    ),
                    array(
                        'key' => '_src_keyword',
                        'compare' => 'NOT EXISTS'
                    )
                )
            )
        );

        return get_posts($args);
    }

    public function getRankDonePost($limit = 10) {
        $post_types = $this->getPostTypes();
        foreach ($this->exclude as $exclude) {
            if (in_array($exclude, $post_types)) {
                unset($post_types[$exclude]);
            }
        }

        $args = array(
            'posts_per_page' => $limit,
            'post_type' => $post_types,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => '_src_keyword',
                    'compare' => 'EXISTS'
                )
            )
        );

        return get_posts($args);
    }

    public function countAllPost($args = array()) {
        $post_types = $this->getPostTypes();
        foreach ($this->exclude as $exclude) {
            if (in_array($exclude, $post_types)) {
                unset($post_types[$exclude]);
            }
        }

        $args = array_merge(array(
            'post_type' => $post_types,
            'post_status' => 'publish'
        ), $args);

        $posts = new WP_Query($args);
        return $posts->found_posts;
    }

    public function countDonePost($args = array()) {
        $post_types = $this->getPostTypes();
        foreach ($this->exclude as $exclude) {
            if (in_array($exclude, $post_types)) {
                unset($post_types[$exclude]);
            }
        }

        $args = array_merge(array(
            'post_type' => $post_types,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_src_processed',
                    'compare' => 'EXISTS'
                )
            )
        ), $args);

        $posts = new WP_Query($args);
        return $posts->found_posts;
    }


//////////////////////////////////////////
    public function getKeywordsFromPost($post) {
        $keywords = array();

        //Get the squirrly keyword
        if ($json = $this->getSQKeyword($post->ID)) {
            if (isset($json->keyword)) {
                return $json->keyword;
            }
        }


        //If there are keywords in other plugins
        $this->getOtherPluginsKeywords($post->ID, $keywords);

        if (empty($keywords)) {
            //Get the tags from post
            $tags = wp_get_post_tags($post->ID);
            if (!empty($tags)) {
                foreach ($tags as $key => $keyword) {
                    if (str_word_count($keyword->name) >= 2) {
                        $keywords[] = SQ_Classes_Tools::i18n($keyword->name);
                    }
                }
            }
        }

        //sort based on repedeat keywords
        $acv = array_count_values($keywords);
        arsort($acv);
        $keywords = array_keys($acv);

        if (!empty($keywords)) {
            return current($keywords);
        } else {
            return false;
        }
    }

    public function getSQKeyword($post_id) {
        global $wpdb;

        if ($row = $wpdb->get_row("SELECT `post_id`, `meta_value`
                       FROM `" . $wpdb->postmeta . "`
                       WHERE (`meta_key` = '_sq_post_keyword' AND `post_id`=" . (int)$post_id . ")
                       ORDER BY `meta_id` DESC")
        ) {

            return json_decode($row->meta_value);
        }

        return false;
    }


    /**
     * Check if other plugin are/were installed and don't change the SEO
     *
     * @param $post_id
     * @param $keywords
     * @return array
     */
    public function getOtherPluginsKeywords($post_id, &$keywords) {
        global $wpdb;

        $fields = array(
            '_aioseop_keywords',
            '_yoast_wpseo_focuskw',
        );


        if ($rows = $wpdb->get_results("SELECT `meta_key`, `meta_value`
                       FROM `" . $wpdb->postmeta . "`
                       WHERE `post_id`=" . (int)$post_id)
        ) {
            if (!empty($posts))
                foreach ($rows as $row) {
                    if (array_key_exists($row->meta_key, $fields)) {
                        if ($row->meta_value <> '') {
                            $keywords[] = $row->meta_value;
                        }
                    }
                }
        }

        /////////////
        return $keywords;
    }


    /////////////////////////////////////////////////////

    /**
     * Save the post keyword
     * @param integer $post_id
     * @param object $args
     */
    public function saveProcessed($post_id) {
        $metas[] = array('key' => '_src_processed',
            'value' => time());

        SQ_Classes_Tools::dump($metas);
        //save/update a post process
        SQ_Classes_ObjController::getClass('SQ_Models_Post')->savePostMeta($post_id, $metas);
        return true;
    }

    public function countKeywords() {
        global $wpdb;

        if ($row = $wpdb->get_row("SELECT count(`post_id`) as count
                       FROM `" . $wpdb->postmeta . "`
                       WHERE (`meta_key` = '_src_keyword' )
                       ORDER BY `post_id` DESC")
        ) {
            return json_decode($row->count);
        }

        return false;
    }

    public function getKeywords() {
        global $wpdb;

        if ($rows = $wpdb->get_results("SELECT  `post_id`, `meta_value`
                       FROM `" . $wpdb->postmeta . "`
                       WHERE (`meta_key` = '_src_keyword' )
                       ORDER BY `post_id` DESC")
        ) {
            $posts = array();
            if (!empty($rows))
                foreach ($rows as $row) {
                    $posts[$row->post_id] = json_decode($row->meta_value);
                }

            return $posts;
        }

        return false;
    }


    /**
     * Save the post keyword
     * @param integer $post_id
     * @param object $args
     */
    public function saveKeyword($post_id, $args) {

        if (!empty($args)) {
            //Add or Update the keyword from API
            $this->savePostMetaKeyword($post_id, $args);
        }

        return json_encode($args);
    }

    public function deleteKeyword($keyword) {
        global $wpdb;

        if ($post_metas = $wpdb->get_results("SELECT  `meta_id`, `post_id`, `meta_value`
                FROM `" . $wpdb->postmeta . "`
                WHERE (`meta_key` = '_src_keyword')")
        ) {
            $posts = array_map(array($this, 'getPostByMeta'), $post_metas);

            foreach ($posts as $post) {
                if ($post->meta_value->keyword == $keyword) {
                    $this->deleteMeta($post->meta_id);
                }
            }
        }

        return false;
    }

    public function purgeMeta($post_id, $meta) {
        global $wpdb;
        $sql = "DELETE FROM `" . $wpdb->postmeta . "` WHERE `meta_key` = '" . $meta['key'] . "' AND `post_id`=" . (int)$post_id;
        $wpdb->query($sql);
    }

    public function purgeAllMeta($meta) {
        global $wpdb;
        $sql = "DELETE FROM `" . $wpdb->postmeta . "` WHERE `meta_key` = '" . $meta['key'] . "'";
        $wpdb->query($sql);
    }


    public function getPostByMeta($post_meta) {
        $post_meta->meta_value = json_decode($post_meta->meta_value);
        return $post_meta;
    }

    /**
     * Save/Update Meta Keyword in database
     *
     * @param integer $post_id
     * @param array $args {"keyword":"test article","rank":"11","change":-1,"country":"ro","language":"en","datetime":"2017-11-21 09:22:04"
     * @return array|false
     */
    public function savePostMetaKeyword($post_id, $args = array()) {
        global $wpdb;

        if ((int)$post_id == 0 || empty($args))
            return false;

        foreach ($args as $serp) {
            $keyword = (isset($serp->keyword) ? $serp->keyword : '');

            if ($serp->change > 0 && SQ_Classes_Tools::getOption('sq_analytics')) {
                SQ_Classes_Tools::saveOptions('sq_analytics', 0); //set analytics to not viewed yet and show notification
            }
            if ($keyword <> '') {
                $sql = "SELECT `meta_id`, `meta_value`
                    FROM `" . $wpdb->postmeta . "`
                    WHERE `meta_key` = '_src_keyword' AND `post_id`=" . (int)$post_id;

                if ($records = $wpdb->get_results($sql)) {
                    foreach ($records as $record) {
                        $dbkeyword = '';
                        if (!empty($record->meta_value)) {
                            $json = json_decode($record->meta_value);
                            if (isset($json->keyword)) {
                                $dbkeyword = $json->keyword;
                            }
                        }

                        if ($keyword == $dbkeyword) {
                            if($json->datetime < date('Y-m-d 00:00:00')) {
                                $sql = "UPDATE `" . $wpdb->postmeta . "`
                               SET `meta_value` = '" . addslashes(json_encode($serp)) . "'
                               WHERE `meta_id` = '" . $record->meta_id . "'";

                                $wpdb->query($sql);
                            }

                            continue 2; //go to the next keyword
                        }
                    }
                }
                $sql = "INSERT INTO `" . $wpdb->postmeta . "`
                    (`post_id`,`meta_key`,`meta_value`)
                    VALUES (" . (int)$post_id . ",'_src_keyword','" . addslashes(json_encode($serp)) . "')";

                $wpdb->query($sql);
            }
        }

        return $args;
    }


    public function deleteMeta($meta_id) {
        global $wpdb;
        $sql = "DELETE FROM `" . $wpdb->postmeta . "` WHERE `meta_id` = '" . $meta_id . "'";
        $wpdb->query($sql);
    }
}
