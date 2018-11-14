<?php

class SQ_Models_Domain_Patterns extends SQ_Models_Abstract_Domain {

    protected $_id; //Replaced with the post/page ID
    protected $_post_type; //Replaced with the post/page type
    protected $_guid; //Replaced with the post/page slug

    public function setId($id) {
        $this->_id = $id;
    }

    /*********************************************************************************/
    protected $_date; //Replaced with the date of the post/page

    public function setPost_date($value) {
        $this->_date = $value;
    }

    //
    protected $_title; //Replaced with the title of the post/page

    public function setPost_title($value) {
        if ($value <> '') {
            $this->_title = $value;
        }
    }

    //
    protected $_post_parent;
    protected $_parent_title; //Replaced with the title of the parent page of the current page

    public function getParent_title() {
        if (isset($this->_post_parent) && (int)$this->_post_parent > 0) {
            if ($post = $this->_getPost($this->_post_parent)) {
                $this->_parent_title = $post->post_title;
            }
        }

        return $this->_parent_title;
    }

    protected $_sitename; //The site's name

    public function getSitename() {
        return get_bloginfo('name');
    }

    //
    protected $_sitedesc; //The site's tag line / description

    public function getSitedesc() {
        return $this->truncate(get_bloginfo('description'), 100, $this->getDescription_maxlength());
    }

    //
    protected $_excerpt; //Replaced with the post/page excerpt (or auto-generated if it does not exist)

    public function setPost_excerpt($value) {
        if ($value <> '') {
            $this->_excerpt = $value;
        }
    }

    protected $_excludedText = array('This is a Page excerpt. It will be displayed for search results');

    public function getExcerpt() {
        if (in_array($this->_excerpt, $this->_excludedText)) {
            $this->_excerpt = null;
        }

        if (!isset($this->_excerpt)) {
            if ($post = $this->currentpost) {
                SQ_Classes_Tools::dump($post);

                if (trim($post->post_excerpt) <> '' && !in_array($post->post_excerpt, $this->_excludedText)) {
                    $this->_excerpt = $post->post_excerpt;
                } elseif ($post->post_content <> '') {
                    $this->_excerpt = $this->truncate($post->post_content, 100, $this->getDescription_maxlength());
                }
            }
        }
        return $this->_excerpt;
    }

    protected $_post_description; //Replaced with the post/page content description (or auto-generated if it does not exist)

    public function getPost_description() {
        if ($post = $this->currentpost) {
            SQ_Classes_Tools::dump($post);

            if ($post->post_content <> '') {
                $this->_excerpt = $this->truncate($post->post_content, 100, $this->getDescription_maxlength());
            }
        }
        return $this->_excerpt;
    }

    protected $_excerpt_only; //Replaced with the post/page excerpt (without auto-generation)

    public function getExcerpt_only() {
        if (in_array($this->_excerpt_only, $this->_excludedText)) {
            $this->_excerpt_only = null;
        }


        if (!isset($this->_excerpt_only) && isset($this->_id)) {
            $this->_excerpt_only = get_the_excerpt($this->_id);
        }
        return $this->_excerpt_only;
    }

    //
    protected $_category; //Replaced with the post categories (comma separated)

    public function getCategory() {
        if (!isset($this->_category)) {
            $this->_category = $this->_title;
        }
        return $this->_category;
    }

    protected $_primary_category; //Replaced with the primary category of the post/page

    public function getPrimary_category() {
        if (!isset($this->_primary_category)) {
            if ($category = get_category_by_slug($this->_guid)) {
                $args = array(
                    'type' => 'post',
                    'child_of' => $category->term_id,
                    'orderby' => 'name',
                    'order' => 'ASC', // or any order you like
                    'hide_empty' => FALSE,
                    'hierarchical' => 1,
                    'taxonomy' => 'category',
                );
                if ($child_categories = get_categories($args)) {
                    if (!empty($child_categories)) {
                        $this->_primary_category = $child_categories[0]->name;
                    }
                }
            }

        }
        return $this->_primary_category;
    }

    //
    protected $_category_description; //Replaced with the category description

    public function getCategory_description() {
        if (!isset($this->_category_description)) {
            $this->_category_description = $this->_excerpt;
        }

        return $this->truncate($this->_category_description, 100, $this->getDescription_maxlength());
    }

    protected $_tag; //Replaced with the current tag/tags

    public function getTag() {
        if (!isset($this->_tag)) {
            $this->_tag = $this->_title;
        }
        return $this->_tag;
    }

    protected $_tag_description; //Replaced with the tag description

    public function getTag_description() {
        if (!isset($this->_tag_description)) {
            $this->_tag_description = $this->_excerpt;
        }

        return $this->truncate($this->_tag_description, 100, $this->getDescription_maxlength());
    }

    protected $_term_title; //Replaced with the term name

    public function getTerm_title() {
        if (!isset($this->_term_title)) {
            $this->_term_title = $this->_title;
        }

        return $this->truncate($this->_term_title, 10, $this->getTitle_maxlength());
    }

    protected $_term_description; //Replaced with the term description

    public function getTerm_description() {
        if (!isset($this->_term_description)) {
            $this->_term_description = $this->_excerpt;
        }

        return $this->truncate($this->_term_description, 100, $this->getDescription_maxlength());
    }

    //
    protected $_searchphrase; //Replaced with the current search phrase

    public function getSearchphrase() {
        if (!isset($this->_searchphrase)) {
            $search = get_query_var('s');
            if ($search !== '') {
                $this->_searchphrase = esc_html($search);
            }
        }
        return $this->_searchphrase;
    }

    //
    protected $_sep; //The separator defined in your theme's wp_title tag

    public function setSep($sep = null) {
        if (isset($sep)) {
            $this->_sep = $sep;
        }
    }

    public function getSep() {
        if (!isset($this->_sep)) {
            $this->_sep = '-';
        }

        $seps = json_decode(SQ_ALL_SEP, true);

        if (isset($seps[$this->_sep])) {
            return $seps[$this->_sep];
        } else {
            return $this->_sep;
        }
    }

    /*********************************************************************************/
    //
    protected $_page; //Replaced with the current page number with context (i.e. page 2 of 4)

    public function getPage() {
        if (is_paged()) {
            return $this->sep . ' ' . __('Page', _SQ_PLUGIN_NAME_) . ' ' . (int)get_query_var('paged') . ' ' .
                __('of', _SQ_PLUGIN_NAME_) . ' ' . $this->pagetotal;
        }
        return '';
    }

    //
    protected $_pagetotal; //Replaced with the current page total

    public function getPagetotal() {
        global $wp_query;
        if (isset($wp_query->max_num_pages))
            return (int)$wp_query->max_num_pages;

        return '';
    }

    protected $_pagenumber; //Replaced with the current page number

    //
    public function getPagenumber() {
        if (is_paged()) {
            return (int)get_query_var('paged');
        }
        return '';
    }

    //
    protected $_pt_single; //Replaced with the post type single label
    protected $_pt_plural; //Replaced with the post type plural label
    protected $_modified; //Replaced with the post/page modified time

    public function setPost_modified($value) {
        if ($value <> '') {
            $this->_modified = $value;
        }
    }


    protected $_name; //Replaced with the post/page author's 'nicename'

    public function setPost_author($value) {
        if ($value <> '') {
            $this->_name = $value;
        }
    }

    protected $_user_description; //Replaced with the post/page author's 'Biographical Info'

    public function getUser_description() {
        if (!isset($this->_user_description)) {
            $this->_user_description = $this->_excerpt;
        }

        return $this->truncate($this->_user_description, 100, $this->getDescription_maxlength());
    }

    protected $_userid; //Replaced with the post/page author's userid
    protected $_currenttime; //Replaced with the current time

    public function getCurrenttime() {
        return date(get_option('time_format'));
    }

    protected $_currentdate; //Replaced with the current date

    public function getCurrentdate() {
        return date(get_option('date_format'));
    }

    protected $_currentday; //Replaced with the current day

    public function getCurrentday() {
        return date('d');
    }

    protected $_currentmonth; //Replaced with the current month

    public function getCurrentmonth() {
        return date('F');
    }

    protected $_currentyear; //Replaced with the current year

    public function getCurrentyear() {
        return date('Y');
    }

    protected $_caption; //Attachment caption
    protected $_keyword; //Replaced with the posts focus keyword
    protected $_focuskw; //Same as keyword

    public function getKeyword() {
        if (!isset($this->_keyword)) {
            if (isset($this->id) && (int)$this->id > 0) {
                if ($json = SQ_Classes_ObjController::getClass('SQ_Models_Post')->getKeyword($this->id)) {
                    $this->_focuskw = $this->_keyword = SQ_Classes_Tools::i18n($json->keyword);
                    return $this->_keyword;
                }
            }
            $this->_keyword = '';
        }

        return $this->_keyword;
    }

    public function getFocuskw() {
        if (!isset($this->_focuskw)) {
            if (isset($this->id) && (int)$this->id > 0) {
                if ($json = SQ_Classes_ObjController::getClass('SQ_Models_Post')->getKeyword($this->id)) {
                    $this->_focuskw = $this->_keyword = SQ_Classes_Tools::i18n($json->keyword);
                    return $this->_focuskw;
                }
            }
            $this->_focuskw = '';
        }

        return $this->_focuskw;
    }

    protected $_term404; //Replaced with the slug which caused the 404
    /*********************************************************************************/

    ///////////
    public function getPatterns() {
        $patterns = array();
        foreach ($this->_getProperties() as $property => $value) {
            $patterns[$property] = '{{' . $property . '}}';
        }

        return $patterns;
    }

    protected $_currentpost;

    public function getCurrentPost() {
        if (!isset($this->_currentpost)) {
            if (isset($this->id) && (int)$this->id > 0) {
                $this->_currentpost = $this->_getPost($this->id);
            }
        }

        return $this->_currentpost;
    }

    private function _getPost($id = null) {
        $post = false;

        if (isset($id)) {
            if (isset($this->id) && (int)$this->id > 0) {
                $post = get_post($id);
            }
        }

        return $post;
    }

    public function truncate($text, $min = 100, $max = 110) {
        if ($text <> '' && strlen($text) > $max) {
            if (function_exists('strip_tags')) {
                $text = strip_tags($text);
            }
            $text = str_replace(']]>', ']]&gt;', $text);
            $text = @preg_replace('/\[(.+?)\]/is', '', $text);
            $text = strip_tags($text);

            if ($max < strlen($text)) {
                while ($text[$max] != ' ' && $max > $min) {
                    $max--;
                }
            }
            $text = substr($text, 0, $max);
            return trim(stripcslashes($text));
        }

        return $text;
    }

    public function getTitle_maxlength() {
        $metas = SQ_Classes_Tools::getOption('sq_metas');
        return $metas['title_maxlength'];
    }

    public function getDescription_maxlength() {
        $metas = SQ_Classes_Tools::getOption('sq_metas');
        return $metas['description_maxlength'];
    }

}
