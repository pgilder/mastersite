<?php

class SQ_Models_Services_Noindex extends SQ_Models_Abstract_Seo {

    public function __construct() {
        parent::__construct();
        if ($this->_post->sq->doseo) {
            add_filter('sq_noindex', array($this, 'generateNoindex'));
            add_filter('sq_noindex', array($this, 'packNoindex'), 99);
        } else {
            add_filter('sq_noindex', array($this, 'returnFalse'));
        }
    }

    public function generateNoindex($robots = array()) {
        if ((int)$this->_post->sq->noindex == 1) {
            $robots[] = 'noindex';
        }
        if ((int)$this->_post->sq->nofollow == 1) {
            $robots[] = 'nofollow';
        } elseif (!empty($robots)) {
            $robots[] = 'follow';
        }

        return $robots;
    }

    public function packNoindex($robots = array()) {
        if (!empty($robots)) {
            return sprintf("<meta name=\"robots\" content=\"%s\">", join(',', $robots));
        }

        return false;
    }

}