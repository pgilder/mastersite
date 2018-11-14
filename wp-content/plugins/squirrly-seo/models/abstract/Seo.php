<?php

abstract class SQ_Models_Abstract_Seo {
    protected $_post;
    protected $_patterns;
    protected $_sq_use;

    public function __construct() {
        //SQ_Classes_Tools::dump("apply post");
        $this->_post = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->getPost();
        if (class_exists('SQ_Classes_Tools')) {
            $this->_sq_use = SQ_Classes_Tools::getOption('sq_use');
        }
    }

    /**************************** CLEAR THE VALUES *************************************/
    /***********************************************************************************/
    /**
     * Clear and format the title for all languages
     * @param $title
     * @return string
     */
    public function clearTitle($title) {
        return SQ_Classes_Tools::clearTitle($title);
    }

    /**
     * Clear and format the descrition for all languages
     * @param $description
     * @return mixed|string
     */
    public function clearDescription($description) {
        return SQ_Classes_Tools::clearDescription($description);
    }

    public function clearKeywords($keywords) {
        if ($keywords <> '') {
            return SQ_Classes_Tools::clearTitle($keywords);
        }
        return $keywords;
    }

    /**
     * Get the image from post
     *
     * @return array
     * @param integer $post_id Custom post is
     * @param boolean $all take all the images or stop at the first one
     * @return array
     */
    public function getPostImages($post_id = null, $all = false) {
        $images = array();

        //for sitemap calls
        if (isset($post_id)) {
            $this->_post = SQ_Classes_ObjController::getDomain('SQ_Models_Domain_Post');
            $this->_post->ID = (int)$post_id;
        }

        if ((int)$this->_post->ID == 0) {
            return $images;
        }

        if (wp_attachment_is_image($this->_post->ID)) {
            $attachment = get_post($this->_post->ID);
            $this->_post->post_title = ((isset($attachment->post_title) && strlen($attachment->post_title) > 10) ? $attachment->post_title : '');
            $this->_post->post_excerpt = (isset($attachment->post_excerpt) ? $attachment->post_excerpt : isset($attachment->post_content) ? $attachment->post_content : '');
        } elseif (has_post_thumbnail($this->_post->ID)) {
            $attachment = get_post(get_post_thumbnail_id($this->_post->ID));
            $this->_post->post_title = ((isset($attachment->post_title) && strlen($attachment->post_title) > 10) ? $attachment->post_title : '');
            $this->_post->post_excerpt = (isset($attachment->post_excerpt) ? $attachment->post_excerpt : isset($attachment->post_content) ? $attachment->post_content : '');
        }

        if (isset($attachment->ID)) {
            $url = wp_get_attachment_image_src($attachment->ID, 'full');

            $images[] = array(
                'src' => esc_url($url[0]),
                'title' => $this->clearTitle($this->_post->post_title),
                'description' => $this->clearDescription($this->_post->post_excerpt),
                'width' => $url[1],
                'height' => $url[2],
            );
        }

        if ($all || empty($images)) {
            if (isset($this->_post->post_content)) {
                preg_match('/<img[^>]*src="([^"]*)"[^>]*>/i', $this->_post->post_content, $match);

                if (!empty($match)) {
                    preg_match('/alt="([^"]*)"/i', $match[0], $alt);

                    if (strpos($match[1], '//') === false) {
                        $match[1] = get_bloginfo('url') . $match[1];
                    }

                    $images[] = array(
                        'src' => esc_url($match[1]),
                        'title' => $this->clearTitle(!empty($alt[1]) ? $alt[1] : ''),
                        'description' => '',
                        'width' => '500',
                        'height' => null,
                    );
                }
            }
        }


        return $images;
    }

    /**
     * @return mixed
     */
    public function getImageType($url = '') {

        if ($url == '' || strpos($url, '.') === false) {
            return false;
        }

        $array = explode('.', $url);
        $extension = end($array);

        $types = array('gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png' => 'image/png',
            'bmp' => 'image/bmp',
            'tiff' => 'image/tiff');

        if (array_key_exists($extension, $types)) {
            return $types[$extension];
        }

        return false;
    }

    /**
     * Get the video from content
     * @param integer $post_id Custom post is
     * @return array
     */
    public function getPostVideos($post_id = null) {
        $videos = array();

        //for sitemap calls
        if (isset($post_id)) {
            $this->_post = SQ_Classes_ObjController::getDomain('SQ_Models_Domain_Post');
            $this->_post->ID = (int)$post_id;
        }

        if ((int)$this->_post->ID == 0) {
            return $videos;
        }

        if (isset($this->_post->post_content)) {
            preg_match('/(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed)\/)([^\?&\"\'>\s]+)/si', $this->_post->post_content, $match);

            if (isset($match[0])) {
                if (strpos($match[0], '//') !== false && strpos($match[0], 'http') === false) {
                    $match[0] = 'http:' . $match[0];
                }
                $videos[] = esc_url($match[0]);
            }

            preg_match('/(?:http(?:s)?:\/\/)?(?:fwd4\.wistia\.com\/(?:medias)\/)([^\?&\"\'>\s]+)/si', $this->_post->post_content, $match);

            if (isset($match[0])) {
                $videos[] = esc_url('http://fast.wistia.net/embed/iframe/' . $match[1]);
            }

            preg_match('/class=["|\']([^"\']*wistia_async_([^\?&\"\'>\s]+)[^"\']*["|\'])/si', $this->_post->post_content, $match);

            if (isset($match[0])) {
                $videos[] = esc_url('http://fast.wistia.net/embed/iframe/' . $match[2]);
            }

            preg_match('/src=["|\']([^"\']*(.mpg|.mpeg|.mp4|.mov|.wmv|.asf|.avi|.ra|.ram|.rm|.flv)["|\'])/i', $this->_post->post_content, $match);

            if (isset($match[1])) {
                $videos[] = esc_url($match[1]);
            }
        }

        return $videos;
    }

    /**
     * Check if is the homepage
     *
     * @return bool
     */
    public function isHomePage() {
        return SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->isHomePage();
    }

    public function getPost() {
        return SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->getPost();
    }

    public function returnFalse() {
        return false;
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
}