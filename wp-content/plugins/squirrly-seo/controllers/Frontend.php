<?php

class SQ_Controllers_Frontend extends SQ_Classes_FrontController {

    /** @var  SQ_Models_Frontend */
    public $model;

    public function __construct() {
        if (SQ_Classes_Tools::isAjax()) return;
        parent::__construct();
        //For favicon and Robots
        $this->hookCheckFiles();

        //Hook the buffer on both actions in case one fails
        if (!defined('CE_FILE')) { //compatible with other cache plugins
            add_action('plugins_loaded', array($this, 'hookBuffer'), 9);
        }
        add_action('template_redirect', array($this, 'hookBuffer'), 1);

        //Set the post so that Squirrly will know which one to process
        if(defined('BP_REQUIRED_PHP_VERSION')) {
            add_action('template_redirect', array($this->model, 'setPost'), 10);
        }else{
            add_action('template_redirect', array($this->model, 'setPost'), 9);
        }
        /* Check if sitemap is on and Load the Sitemap */
        if (SQ_Classes_Tools::getOption('sq_auto_sitemap')) SQ_Classes_ObjController::getClass('SQ_Controllers_Sitemaps');

        /* Load the Feed Style  */
        if (SQ_Classes_Tools::getOption('sq_auto_feed')) SQ_Classes_ObjController::getClass('SQ_Controllers_Feed');

        /* Fix the Links for feed*/
        if (SQ_Classes_Tools::getOption('sq_url_fix')) add_action('the_content', array($this, 'fixFeedLinks'), 11);

    }

    /**
     * HOOK THE BUFFER
     */
    public function hookBuffer() {
        //remove the action is already hocked in plugins_loaded
        if (!did_action('template_redirect')) {
            remove_action('template_redirect', array($this, 'hookBuffer'), 1);
        }

        if ($this->isSquirrlySeoEnabled()) {
            global $wp_super_cache_late_init;
            if (isset($wp_super_cache_late_init) && $wp_super_cache_late_init == 1 && !did_action('init')) {
                //add an action after Super cache late login is started
                add_action('init', array($this->model, 'startBuffer'), PHP_INT_MAX);
            } elseif (SQ_Classes_Tools::getOption('sq_laterload') && !did_action('template_redirect')) {
                add_action('template_redirect', array($this->model, 'startBuffer'), PHP_INT_MAX);
            } else {
                $this->model->startBuffer();
            }

            if (defined('WP_ROCKET_VERSION')) {
                add_filter('rocket_buffer', array($this->model, 'getBuffer'), PHP_INT_MAX);
            }

            add_action('shutdown', array($this->model, 'getBuffer'));
        }
    }


    /**
     * Called after plugins are loaded
     */
    public function hookCheckFiles() {
        //Check for sitemap and robots
        if ($basename = $this->isFile($_SERVER['REQUEST_URI'])) {
            if (SQ_Classes_Tools::getOption('sq_auto_robots') == 1) {
                if ($basename == "robots.txt") {
                    SQ_Classes_ObjController::getClass('SQ_Models_Services_Robots');
                    apply_filters('sq_robots', false);
                    exit();
                }
            }

            if (SQ_Classes_Tools::getOption('favicon') <> '') {
                if ($basename == "favicon.icon") {
                    SQ_Classes_Tools::setHeader('ico');
                    @readfile(_SQ_CACHE_DIR_ . SQ_Classes_Tools::getOption('favicon'));
                    exit();
                } elseif ($basename == "touch-icon.png") {
                    SQ_Classes_Tools::setHeader('png');
                    @readfile(_SQ_CACHE_DIR_ . SQ_Classes_Tools::getOption('favicon'));
                    exit();
                } else {
                    $appleSizes = preg_split('/[,]+/', _SQ_MOBILE_ICON_SIZES);
                    foreach ($appleSizes as $appleSize) {
                        if ($basename == "touch-icon$appleSize.png") {
                            SQ_Classes_Tools::setHeader('png');
                            @readfile(_SQ_CACHE_DIR_ . SQ_Classes_Tools::getOption('favicon') . $appleSize);
                            exit();
                        }
                    }
                }
            }

        }

    }


    /**
     * Hook the Header load
     */
    public function hookFronthead() {
        if (!defined('SQ_NOCSS') || (defined('SQ_NOCSS') && !SQ_NOCSS)) {
            if (!SQ_Classes_Tools::isAjax()) {
                SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia(_SQ_THEME_URL_ . 'css/frontend' . (SQ_DEBUG ? '' : '.min') . '.css');
            }
        }
    }

    /**
     * Change the image path to absolute when in feed
     * @param string $content
     *
     * @return string
     */
    public function fixFeedLinks($content) {
        if (is_feed()) {
            $find = $replace = $urls = array();

            @preg_match_all('/<img[^>]*src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $out);
            if (is_array($out)) {
                if (!is_array($out[1]) || empty($out[1]))
                    return $content;

                foreach ($out[1] as $row) {
                    if (strpos($row, '//') === false) {
                        if (!in_array($row, $urls)) {
                            $urls[] = $row;
                        }
                    }
                }
            }

            @preg_match_all('/<a[^>]*href=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $out);
            if (is_array($out)) {
                if (!is_array($out[1]) || empty($out[1]))
                    return $content;

                foreach ($out[1] as $row) {
                    if (strpos($row, '//') === false) {
                        if (!in_array($row, $urls)) {
                            $urls[] = $row;
                        }
                    }
                }
            }
            if (!is_array($urls) || (is_array($urls) && empty($urls))) {
                return $content;
            }

            $urls = array_unique($urls);
            foreach ($urls as $url) {
                $find[] = "'" . $url . "'";
                $replace[] = "'" . esc_url(home_url() . $url) . "'";
                $find[] = '"' . $url . '"';
                $replace[] = '"' . esc_url(home_url() . $url) . '"';
            }
            if (!empty($find) && !empty($replace)) {
                $content = str_replace($find, $replace, $content);
            }
        }
        return $content;
    }

    public function hookFrontfooter() {
        //Memory Debug
        if (SQ_Classes_Tools::getValue('sq_debug') === 'on') {
            SQ_Classes_Tools::dump(SQ_Classes_Tools::getMaxMemory(), SQ_Classes_Tools::getUsedMemory());
        }

        if ($this->isSquirrlySeoEnabled()) {
            echo $this->model->getFooter();
        }
    }

    public function isSquirrlySeoEnabled() {
        return (apply_filters('sq_use', SQ_Classes_Tools::getOption('sq_use')) == 1);
    }

    public function isFile($url = null) {
        if (isset($url) && $url <> '') {
            $url = basename($url);
            if (strpos($url, '?') <> '') {
                $url = substr($url, 0, strpos($url, '?'));
            }

            $files = array('ico', 'icon', 'txt', 'jpg', 'jpeg', 'png', 'bmp', 'gif',
                'css', 'scss', 'js',
                'pdf', 'doc', 'docx', 'csv', 'xls', 'xslx',
                'mp4', 'mpeg',
                'zip', 'rar');

            if (strrpos($url, '.') !== false) {
                $ext = substr($url, strrpos($url, '.') + 1);
                if (in_array($ext, $files)) {
                    return $url;
                }
            }
        }

        return false;

    }
}
