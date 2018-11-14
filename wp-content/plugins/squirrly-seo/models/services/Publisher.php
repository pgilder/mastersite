<?php

class SQ_Models_Services_Publisher extends SQ_Models_Abstract_Seo {


    public function __construct() {
        parent::__construct();
        if ($this->_post->sq->doseo) {
            add_filter('sq_publisher', array($this, 'generatePublisher'));
            add_filter('sq_publisher', array($this, 'packPublisher'), 99);
        } else {
            add_filter('sq_publisher', array($this, 'returnFalse'));
        }
    }

    public function generatePublisher($publisher = array()) {
        if ($this->_post->post_type == 'post' || $this->_post->post_type == 'product') {
            if (SQ_Classes_Tools::getOption('sq_auto_facebook') && isset($this->_post->socials->facebook_site) && $this->_post->socials->facebook_site <> '') {
                $publisher['article:publisher'] = $this->_post->socials->facebook_site;
            }
            if (SQ_Classes_Tools::getOption('sq_auto_facebook') ) {
                if(isset($this->_post->sq->og_author) && $this->_post->sq->og_author <> '') {
                    $authors = explode(',', $this->_post->sq->og_author);
                    foreach ($authors as $author) {
                        if ($author <> '') {
                            $publisher['article:author'][] = $author;
                        }
                    }
                }
            } else {
                $publisher['article:author'] = $this->getAuthor('display_name');
            }
        }

        return $publisher;
    }


    /**
     * Get the author
     * @param string $what
     * @return bool|mixed|string
     */
    protected function getAuthor($what = 'user_nicename') {
        if (!isset($this->author)) {
            if (is_author()) {
                $this->author = get_userdata(get_query_var('author'));
            } elseif (is_single() && isset($this->_post->post_author)) {
                $this->author = get_userdata((int)$this->_post->post_author)->data;
            }
        }

        if (isset($this->author)) {

            if ($what == 'user_url' && $this->author->$what == '') {
                return get_author_posts_url($this->author->ID, $this->author->user_nicename);
            }
            if (isset($this->author->$what)) {
                return $this->author->$what;
            }
        }

        return false;
    }

    public function packPublisher($publisher = array()) {
        if (!empty($publisher)) {
            foreach ($publisher as $key => &$value) {
                if (is_array($value)) {
                    $str = '';
                    foreach ($value as $subvalue) {
                        $str .= '<meta property="' . $key . '" content="' . $subvalue . '" />' . ((count($value) > 1) ? "\n" : '');
                    }
                    $value = $str;
                } else {
                    $value = '<meta property="' . $key . '" content="' . $value . '" />';
                }
            }
            return "\n" . join("\n", array_values($publisher));
        }

        return false;
    }
}