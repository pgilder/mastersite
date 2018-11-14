<?php

class SQ_Models_Domain_Post extends SQ_Models_Abstract_Domain {

    protected $_ID;
    protected $_term_taxonomy_id;
    protected $_taxonomy;
    protected $_post_type;
    protected $_url; //set the canonical link for this post type
    protected $_hash;
    protected $_sq;
    protected $_sq_adm;
    protected $_socials;
    //
    protected $_patterns;
    //
    protected $_post_name;
    protected $_guid;
    protected $_post_author;
    protected $_post_date;
    protected $_post_title;
    protected $_post_excerpt;
    protected $_post_attachment;
    protected $_post_content;
    protected $_post_status;
    protected $_post_password;

    protected $_post_parent;
    protected $_post_modified;
    protected $_category;
    protected $_category_description;
    protected $_noindex;

    protected $_debug;

    public function getSocials() {
        if (!isset($this->_socials)) {
            $this->_socials = json_decode(json_encode(SQ_Classes_Tools::getOption('socials')));
        }

        return $this->_socials;
    }

    public function getSq() {
        if (!isset($this->_sq) && isset($this->_post_type) && $this->_post_type <> '') {
            //Get the saved sq settings
            $this->_sq = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->getSqSeo($this->_hash);
            if (!empty($this->_sq)) {
                $patterns = SQ_Classes_Tools::getOption('patterns');
                //print_R($patterns);
                if (!empty($patterns) && $sq_array = $this->_sq->toArray()) {
                    if (!empty($sq_array))
                        foreach ($sq_array as $key => $value) {
                            if ($key == 'sep' || $value == '') {
                                if (SQ_Classes_Tools::getOption('sq_auto_pattern')) {
                                    if (isset($patterns[$this->_post_type])) {
                                        if (isset($patterns[$this->_post_type][$key])) {
                                            $this->_sq->$key = $patterns[$this->_post_type][$key];
                                        }
                                    } else {
                                        if (isset($patterns['custom'][$key])) {
                                            $this->_sq->$key = $patterns['custom'][$key];
                                        }
                                    }
                                } else {
                                    //If there are no patterns and no custom field, get the post values
                                    switch ($key) {
                                        case 'title':
                                            $this->_sq->title = $this->post_title;
                                            break;
                                        case 'description':
                                            $this->_sq->description = $this->post_excerpt;
                                            break;
                                    }
                                }
                            }
                        }
                }
            }

        }
        return $this->_sq;
    }

    public function getSq_adm() {

        if (!isset($this->_sq_adm) && isset($this->_post_type) && $this->_post_type <> '') {
            if (is_user_logged_in()) {
                $this->_sq_adm = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->getSqSeo($this->_hash, $this->ID);

                if (!empty($this->_sq_adm)) {
                    $patterns = SQ_Classes_Tools::getOption('patterns');
                    //print_R($this);
                    if (!empty($patterns) && $sq_array = $this->_sq_adm->toArray()) {
                        if (!empty($sq_array))
                            foreach ($sq_array as $key => $value) {
                                if ($value == '') {
                                    if (SQ_Classes_Tools::getOption('sq_auto_pattern')) {

                                        if (isset($patterns[$this->_post_type])) {
                                            $this->_sq_adm->patterns = json_decode(json_encode($patterns[$this->_post_type]));
                                        } else {
                                            $this->_sq_adm->patterns = json_decode(json_encode($patterns['custom']));
                                        }
                                    }
                                }
                            }
                    }

                }
            }
        }
        return $this->_sq_adm;
    }

    public function getID() {
        return $this->_ID;
    }

    public function importSEO() {
        if (isset($this->_ID) && (int)$this->_ID > 0) {
            $platforms = apply_filters('sq_importList', false);
            $import = array();

            if (!empty($platforms)) {
                foreach ($platforms as $path => &$metas) {
                    if ($metas = SQ_Classes_ObjController::getClass('SQ_Models_Admin')->getDBSeo($this->_ID, $metas)) {
                        if (strpos($metas, '%%') !== false) {
                            $metas = preg_replace('/%%([^\%]+)%%/', '{{$1}}', $metas);
                        }
                        $import[SQ_Classes_ObjController::getClass('SQ_Models_Admin')->getName($path)] = $metas;
                    }
                }
            }

            return $import;
        }
    }

    public function getPost_attachment() {
        if (!isset($this->_post_attachment) && isset($this->_ID) && (int)$this->_ID > 0) {
            if (has_post_thumbnail($this->_ID)) {
                $attachment = get_post(get_post_thumbnail_id($this->_ID));
                if (isset($attachment->ID)) {
                    $url = wp_get_attachment_image_src($attachment->ID, 'full');
                    $this->_post_attachment = esc_url($url[0]);
                }
            }
        }

        return $this->_post_attachment;
    }

}
