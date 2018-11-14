<?php

/**
 * Set the ajax action and call for wordpress
 */
class SQ_Classes_Action extends SQ_Classes_FrontController {

    /** @var array with all form and ajax actions */
    var $actions = array();

    /** @var array from core config */
    private static $config;


    public function _isAjax() {
        return (defined('DOING_AJAX') && DOING_AJAX);
    }

    /**
     * The hookAjax is loaded as custom hook in hookController class
     *
     * @return void
     */
    public function hookInit() {

        /* Only if ajax */
        if ($this->_isAjax()) {
            $this->actions = array();
            $this->getActions(((isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : ''))));
        }
    }

    /**
     * The hookSubmit is loaded when action si posted
     *
     * @return void
     */
    public function hookMenu() {
        /* Only if post */
        if (!$this->_isAjax()) {
            $this->actions = array();
            $this->getActions(((isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : ''))));
        }
    }

    /**
     * The hookHead is loaded as admin hook in hookController class for script load
     * Is needed for security check as nonce
     *
     * @return void
     */
    public function hookHead() {

        echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>
              <script type="text/javascript">
                  var sqQuery = {
                    "adminurl": "' . admin_url() . '",
                    "ajaxurl": "' . admin_url('admin-ajax.php') . '",
                    "adminposturl": "' . admin_url('post.php') . '",
                    "adminlisturl": "' . admin_url('edit.php') . '",
                    "nonce": "' . wp_create_nonce(_SQ_NONCE_ID_) . '"
                  }
              </script>';
    }

    public function hookFronthead() {
        if (is_user_logged_in()) {
            echo '<script type="text/javascript">
                  var sqQuery = {
                    "adminurl": "' . admin_url() . '",
                    "ajaxurl": "' . admin_url('admin-ajax.php') . '",
                    "nonce": "' . wp_create_nonce(_SQ_NONCE_ID_) . '"
                  }
              </script>';
        }
    }

    /**
     * Get all actions from config.json in core directory and add them in the WP
     *
     * @return void
     */
    public function getActions($cur_action) {
        //Let only the logged users to access the actions
        if (is_admin() || is_network_admin()) {
            /* if config allready in cache */
            if (!isset(self::$config)) {
                $config_file = _SQ_CORE_DIR_ . 'config.json';
                if (!file_exists($config_file)) {
                    return;
                }

                /* load configuration blocks data from core config files */
                self::$config = json_decode(file_get_contents($config_file), 1);
            }

            if (is_array(self::$config))
                foreach (self::$config['blocks']['block'] as $block) {
                    if (isset($block['active']) && $block['active'] == 1) {
                        /* if there is a single action */
                        if (isset($block['actions']['action']))
                            if (isset($block['admin']) &&
                                (($block['admin'] == 1 && is_user_logged_in()) ||
                                    $block['admin'] == 0)
                            ) {
                                /* if there are more actions for the current block */
                                if (!is_array($block['actions']['action'])) {
                                    /* add the action in the actions array */
                                    if ($block['actions']['action'] == $cur_action)
                                        $this->actions[] = array('class' => $block['name']);
                                } else {
                                    /* if there are more actions for the current block */
                                    foreach ($block['actions']['action'] as $action) {
                                        /* add the actions in the actions array */
                                        if ($action == $cur_action)
                                            $this->actions[] = array('class' => $block['name']);
                                    }
                                }
                            }

                    }
                }


            /* add the actions in WP */
            foreach ($this->actions as $actions) {
                SQ_Classes_ObjController::getClass($actions['class'])->action();
            }
        }

    }

    /**
     * Call the Squirrly Cloud Server
     * @param string $module
     * @param array $args
     * @return json | string
     */
    public static function apiCall($module, $args = array(), $timeout = 10) {
        $parameters = "";
        $scheme = "http:";

        if (SQ_Classes_Tools::getOption('sq_api') == '' && $module <> 'sq/login' && $module <> 'sq/register') {
            return false;
        }

        $extra = array(
            'user_url' => home_url(),
            'lang' => (defined('WPLANG') ? WPLANG : 'en_US'),
            'versq' => SQ_VERSION_ID,
            'verwp' => WP_VERSION_ID,
            'verphp' => PHP_VERSION_ID,
            'token' => SQ_Classes_Tools::getOption('sq_api'));


        if (is_array($args)) {
            $args = array_merge($args, $extra);
        } else {
            $args = $extra;
        }

        foreach ($args as $key => $value) {
            if ($value <> '') {
                $parameters .= ($parameters == "" ? "" : "&") . $key . "=" . urlencode($value);
            }
        }

        /* If the call is for login on register then use base64 is exists */
        if ($module == 'sq/login' || $module == 'sq/register') {
            if (function_exists('base64_encode')) {
                $parameters = 'q=' . base64_encode($parameters);
            }
        }

        if ($module <> "") {
            $module .= "/";
        }
        //call it with http to prevent curl issues with ssls
        $url = self::cleanUrl($scheme . _SQ_API_URL_ . $module . "?" . $parameters);
        try {
            //echo $url;exit();
            return SQ_Classes_Tools::sq_remote_get($url, array(), array('timeout' => $timeout));
        } catch (Exception $e) {
            return '';
        }

    }

    /**
     * Clear the url before the call
     * @param string $url
     * @return string
     */
    private static function cleanUrl($url) {
        return str_replace(array(' '), array('+'), $url);
    }

    public static function apiSaveSettings() {
        self::apiCall('sq/user/settings', array('settings' => json_encode(SQ_Classes_Tools::getBriefOptions())), 10);
    }

}
