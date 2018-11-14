<?php

/**
 * The class handles the theme part in WP
 */
class SQ_Classes_DisplayController {

    private static $cache;

    public function init() {
        /* Load the global CSS file */
        self::loadMedia('global');
    }

    /**
     * echo the css link from theme css directory
     *
     * @param string $uri The name of the css file or the entire uri path of the css file
     * @param string $params : trigger, media
     *
     * @return string
     */
    public static function loadMedia($uri = '', $params = array('trigger' => false, 'media' => 'all')) {
        if (SQ_Classes_ObjController::getClass('SQ_Classes_Action')->_isAjax()) {
            return;
        }

        $css_uri = '';
        $js_uri = '';

        if (!isset($params['media'])) {
            $params['media'] = 'all';
        }

        if (isset(self::$cache[$uri]))
            return;
        self::$cache[$uri] = true;

        /* if is a custom css file */
        if (strpos($uri, '//') === false) {
            if (strpos($uri, '.') !== false) {
                $name = strtolower(_SQ_NAMESPACE_ . substr($uri, 0, strpos($uri, '.')));
            } else {
                $name = strtolower(_SQ_NAMESPACE_ . $uri);
            }
            if (strpos($uri, '.css') !== false && file_exists(_SQ_THEME_DIR_ . 'css/' . strtolower($uri))) {
                $css_uri = _SQ_THEME_URL_ . 'css/' . strtolower($uri);
            }
            if (strpos($uri, '.js') !== false && file_exists(_SQ_THEME_DIR_ . 'js/' . strtolower($uri))) {
                $js_uri = _SQ_THEME_URL_ . 'js/' . strtolower($uri);
            }

            if (file_exists(_SQ_THEME_DIR_ . 'css/' . strtolower($uri) . (SQ_DEBUG ? '' : '.min') . '.css')) {
                $css_uri = _SQ_THEME_URL_ . 'css/' . strtolower($uri) . (SQ_DEBUG ? '' : '.min') . '.css';
            }
            if (file_exists(_SQ_THEME_DIR_ . 'js/' . strtolower($uri) . (SQ_DEBUG ? '' : '.min') . '.js')) {
                $js_uri = _SQ_THEME_URL_ . 'js/' . strtolower($uri) . (SQ_DEBUG ? '' : '.min') . '.js';
            }
        } else {
            $name = strtolower(basename($uri));
            if (strpos($uri, '.css') !== FALSE)
                $css_uri = $uri;
            elseif (strpos($uri, '.js') !== FALSE) {
                $js_uri = $uri;
            }
        }


        if ($css_uri <> '') {
            if (!wp_style_is($name)) {
                wp_enqueue_style($name, $css_uri, null, SQ_VERSION_ID, $params['media']);
                if (is_admin() || (isset($params['trigger']) && $params['trigger'] === true)) { //load CSS for admin or on triggered
                    wp_print_styles(array($name));
                }
            }


        }

        if ($js_uri <> '') {
            if (!wp_script_is($name)) {
                wp_enqueue_script($name, $js_uri, null, SQ_VERSION_ID);
                if (is_admin() || isset($params['trigger']) && $params['trigger'] === true) {
                    wp_print_scripts(array($name));
                }
            }

        }
    }


    /**
     * return the block content from theme directory
     *
     * @param $block
     * @param $view
     * @return bool|string
     */
    public function getView($block, $view) {
        if (file_exists(_SQ_THEME_DIR_ . $block . '.php')) {
            ob_start();
            include(_SQ_THEME_DIR_ . $block . '.php');
            return ob_get_clean();
        }

        return false;
    }
}
