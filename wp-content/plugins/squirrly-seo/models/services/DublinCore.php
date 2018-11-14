<?php

class SQ_Models_Services_DublinCore extends SQ_Models_Abstract_Seo {

    public function __construct() {
        parent::__construct();

        if ($this->_post->sq->doseo) {
            add_filter('sq_dublin_core', array($this, 'generateMeta'));
            add_filter('sq_dublin_core', array($this, 'packMeta'), 99);
        } else {
            add_filter('sq_dublin_core', array($this, 'returnFalse'));
        }

    }

    /**
     * Get all metas for Dublin Core
     * @param string $metas
     * @return string
     */
    public function generateMeta($metas = array()) {
        $date = null;
        if (get_bloginfo('language') <> '') {
            $metas['dc.language'] = get_bloginfo('language');
            $metas['dc.language.iso'] = str_replace('-', '_', get_bloginfo('language'));
        }
        if (!$name = $this->getAuthor('display_name')) {
            $name = get_bloginfo('name');
        }

        if ($name <> '') {
            $metas['dc.publisher'] = $name;
        }
        if ($this->_post->sq->title <> '') {
            $metas['dc.title'] = $this->clearTitle($this->_post->sq->title);
        }
        if ($this->_post->sq->description <> '') {
            $metas['dc.description'] = $this->clearDescription($this->_post->sq->description);
        }

        if ($this->_post->post_type == 'home') {
            $metas['dc.date.issued'] = date('Y-m-d', strtotime(get_lastpostmodified('gmt')));
        } elseif ($this->_post->post_date <> '') {
            $metas['dc.date.issued'] = date('Y-m-d', strtotime($this->_post->post_date));
        }

        if (isset($this->_post->post_modified) && $this->_post->post_modified <> '') {
            $og['dc.date.updated'] = $this->_post->post_modified;
        }

        return $metas;
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

    /**
     * Pack the Dublin Core
     * @param array $metas
     * @return bool|string
     */
    public function packMeta($metas = array()) {
        if (!empty($metas)) {
            foreach ($metas as $key => &$meta) {
                $meta = sprintf('<meta name="%s" content="%s" />', $key, $meta);
            }

            return "\n" . join("\n", array_values($metas));
        }

        return false;
    }

}