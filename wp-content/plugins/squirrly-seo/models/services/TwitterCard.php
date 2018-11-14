<?php

class SQ_Models_Services_TwitterCard extends SQ_Models_Abstract_Seo {

    public function __construct() {
        parent::__construct();

        if ($this->_post->sq->doseo) {
            add_filter('sq_twitter_card', array($this, 'generateTwitterCard'), 10);
            add_filter('sq_twitter_card', array($this, 'packTwitterCard'), 99);
        } else {
            add_filter('sq_twitter_card', array($this, 'returnFalse'));
        }

    }

    public function generateTwitterCard($tw = array()) {

        if (SQ_Classes_Tools::getOption('sq_auto_twitter')) {
            if ($this->_post->url <> '') {
                $tw['twitter:url'] = urldecode(esc_url($this->_post->url));
            }

            if ($this->_post->sq->tw_title <> '') {
                $tw['twitter:title'] = $this->clearTitle($this->_post->sq->tw_title);
            } else {
                $tw['twitter:title'] = $this->clearTitle($this->_post->sq->title);
            }

            if ($this->_post->sq->tw_description <> '') {
                $tw['twitter:description'] = $this->clearDescription($this->_post->sq->tw_description);
            } else {
                $tw['twitter:description'] = $this->clearDescription($this->_post->sq->description);
            }


            if ($this->_post->sq->tw_media <> '') {
                $tw['twitter:image'] = $this->_post->sq->tw_media;
            } else {
                $this->_setMedia($tw);
            }

            if($account = $this->_getTwitterAccount()) {
                $tw['twitter:creator'] = $account;
                $tw['twitter:site'] = $account;
            }

            $tw['twitter:domain'] = get_bloginfo('title');
            $tw['twitter:card'] = $this->_post->socials->twitter_card_type;

        }

        return $tw;
    }

    protected function _setMedia(&$tw) {
        $images = $this->getPostImages();
        if (!empty($images)) {
            $image = current($images);
            if (isset($image['src'])) {
                $tw['twitter:image'] = $image['src'];
            }
        }
    }

    protected function _getTwitterAccount() {
        $account = SQ_Classes_Tools::getOption('sq_tw_account');
        if ($account == '') {
            if (class_exists('SQ_Classes_Tools')) {
                $account = SQ_Classes_Tools::getOption('sq_twitter_account');
            }
        }

        if ($account <> '') {
            if (strpos($account, 'twitter.com') !== false) {
                preg_match('/twitter.com\/([@1-9a-z_-]+)/i', $account, $result);
                if (isset($result[1]) && !empty($result[1])) {
                    return '@' . str_replace('@', '', $result[1]);
                }
            } else {
                preg_match('/([@1-9a-z_-]+)/i', $account, $result);
                if (isset($result[1]) && !empty($result[1])) {
                    return '@' . str_replace('@', '', $result[1]);
                }
            }
        } else {
            return '';
        }
        return false;
    }


    public function packTwitterCard($tw = array()) {
        if (!empty($tw)) {
            foreach ($tw as $key => &$value) {
                $value = '<meta property="' . $key . '" content="' . $value . '" />';
            }
            return "\n" . join("\n", array_values($tw));
        }

        return false;
    }

}